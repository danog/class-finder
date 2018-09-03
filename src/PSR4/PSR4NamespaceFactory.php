<?php
namespace HaydenPierce\ClassFinder\PSR4;

use HaydenPierce\ClassFinder\AppConfig;
use HaydenPierce\ClassFinder\Exception\ClassFinderException;

class PSR4NamespaceFactory
{
    /** @var AppConfig */
    private $appConfig;

    public function __construct(AppConfig $appConfig)
    {
        $this->appConfig = $appConfig;
    }

    /**
     * @return array
     */
    public function getPSR4Namespaces()
    {
        $namespaces = $this->getUserDefinedPSR4Namespaces();
        $vendorNamespaces = require($this->appConfig->getAppRoot() . 'vendor/composer/autoload_psr4.php');

        $namespaces = array_merge($vendorNamespaces, $namespaces);

        // There's some wackiness going on here for PHP 5.3 compatibility.
        $names = array_keys($namespaces);
        $directories = array_values($namespaces);
        $self = $this;
        $namespaces = array_map(function($index) use ($self, $names, $directories) {
            return $self->createNamespace($names[$index], $directories[$index]);
        },range(0, count($namespaces) - 1));

        return $namespaces;
    }

    /**
     * @return array
     */
    private function getUserDefinedPSR4Namespaces()
    {
        $appRoot = $this->appConfig->getAppRoot();

        $composerJsonPath = $appRoot . 'composer.json';
        $composerConfig = json_decode(file_get_contents($composerJsonPath));

        //Apparently PHP doesn't like hyphens, so we use variable variables instead.
        $psr4 = "psr-4";
        return (array)$composerConfig->autoload->$psr4;
    }

    /**
     * Creates a namespace from composer_psr4.php and composer.json's autoload.psr4 items
     * @param $namespace
     * @param $directories
     * @throws ClassFinderException
     * @return PSR4Namespace
     */
    public function createNamespace($namespace, $directories)
    {
        if (is_string($directories)) {
            // This is an acceptable format according to composer.json
            $directories = array($directories);
        } elseif (is_array($directories)) {
            // composer_psr4.php seems to put everything in this format
        } else {
            throw new ClassFinderException('Unknown PSR4 definition.');
        }

        $self = $this;
        $directories = array_map(function($directory) use ($self) {
            if ($self->isAbsolutePath($directory)) {
                return $directory;
            } else {
                return $self->appConfig->getAppRoot() . $directory;
            }
        }, $directories);

        $directories = array_map(function($directory) {
            return realpath($directory);
        }, $directories);

        return new PSR4Namespace($namespace, $directories);
    }

    /**
     * Mostly this answer https://stackoverflow.com/a/38022806/3000068
     * A few changes: Changed exceptions to be ClassFinderExceptions, removed some ctype dependencies,
     * updated the root prefix regex to handle Window paths better.
     * @param $path string
     * @throws ClassFinderException
     * @return bool
     */
    private function isAbsolutePath($path) {
        if (!is_string($path)) {
            $mess = sprintf('String expected but was given %s', gettype($path));
            throw new ClassFinderException($mess);
        }

        // Optional wrapper(s).
        $regExp = '%^(?<wrappers>(?:[[:print:]]{2,}://)*)';
        // Optional root prefix.
        $regExp .= '(?<root>(?:[[:alpha:]]:[/\\\\]|/)?)';
        // Actual path.
        $regExp .= '(?<path>(?:[[:print:]]*))$%';
        $parts = [];
        if (!preg_match($regExp, $path, $parts)) {
            $mess = sprintf('Path is NOT valid, was given %s', $path);
            throw new ClassFinderException($mess);
        }
        if ('' !== $parts['root']) {
            return true;
        }
        return false;
    }
}