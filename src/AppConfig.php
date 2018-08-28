<?php

namespace HaydenPierce\ClassFinder;

use HaydenPierce\ClassFinder\Exception\ClassFinderException;
use HaydenPierce\ClassFinder\Finder\PSR4NamespaceFactory;

/**
 * Class AppConfig
 * @package HaydenPierce\ClassFinder
 * @internal
 */
class AppConfig
{
    /** @var PSR4NamespaceFactory  */
    private $psr4NamespaceFactory;

    /** @var string */
    private $appRoot;

    public function __construct(PSR4NamespaceFactory $PSR4NamespaceFactory)
    {
        $this->appRoot = $this->findAppRoot();
        $this->psr4NamespaceFactory = $PSR4NamespaceFactory;
    }

    /**
     * @throws \Exception
     */
    private function findAppRoot()
    {
        if ($this->appRoot) {
            $appRoot = $this->appRoot;
        } else {
            $workingDirectory = str_replace('\\', '/', __DIR__);
            $workingDirectory = str_replace('/vendor/haydenpierce/class-finder/src', '', $workingDirectory);
            $directoryPathPieces = explode('/', $workingDirectory);

            $appRoot = '/';
            do {
                $path = implode('/', $directoryPathPieces) . '/composer.json';
                if (file_exists($path)) {
                    $appRoot = implode('/', $directoryPathPieces) . '/';
                } else {
                    array_pop($directoryPathPieces);
                }
            } while (is_null($appRoot) && count($directoryPathPieces) > 0);
        }

        $this->throwIfInvalidAppRoot($appRoot);

        $this->appRoot= $appRoot;
        return $this->appRoot;
    }

    /**
     * @param $appRoot
     * @throws ClassFinderException
     */
    private function throwIfInvalidAppRoot($appRoot)
    {
        if (!file_exists($appRoot . '/composer.json')) {
            throw new ClassFinderException(sprintf("Could not locate composer.json. You can get around this by setting ClassFinder::\$appRoot manually. See '%s' for details.",
                'https://gitlab.com/hpierce1102/ClassFinder/blob/master/docs/exceptions/missingComposerConfig.md'
            ));
        }
    }

    /**
     * @return array
     * @throws ClassFinderException
     */
    public function getPSR4Namespaces()
    {
        $namespaces = $this->getUserDefinedPSR4Namespaces();
        $vendorNamespaces = require($this->getAppRoot() . 'vendor/composer/autoload_psr4.php');

        $namespaces = array_merge($vendorNamespaces, $namespaces);

        // There's some wackiness going on here for PHP 5.3 compatibility.
        $names = array_keys($namespaces);
        $directories = array_values($namespaces);
        $self = $this;
        $namespaces = array_map(function($index) use ($self, $names, $directories) {
            return $self->psr4NamespaceFactory->createNamespace($names[$index], $directories[$index], $this->appRoot);
        },range(0, count($namespaces) - 1));

        return $namespaces;
    }


    /**
     * @return string
     */
    public function getAppRoot()
    {
        if ($this->appRoot === null) {
            $this->appRoot = $this->findAppRoot();
        }

        return $this->appRoot;
    }

    /**
     * @param string $appRoot
     */
    public function setAppRoot($appRoot)
    {
        $this->appRoot = $appRoot;
    }

    /**
     * @return array
     */
    private function getUserDefinedPSR4Namespaces()
    {
        $appRoot = $this->getAppRoot();
        $this->throwIfInvalidAppRoot($appRoot);

        $composerJsonPath = $appRoot . 'composer.json';
        $composerConfig = json_decode(file_get_contents($composerJsonPath));

        //Apparently PHP doesn't like hyphens, so we use variable variables instead.
        $psr4 = "psr-4";
        return (array)$composerConfig->autoload->$psr4;
    }
}