<?php
namespace HaydenPierce\ClassFinder\Finder;

use HaydenPierce\ClassFinder\AppConfig;
use HaydenPierce\ClassFinder\Exception\ClassFinderException;

class PSR4Finder implements FinderInterface
{
    private $config;

    public function __construct(AppConfig $config)
    {
        $this->config = $config;
    }

    /**
     * @param $namespace
     * @return bool|string
     * @throws ClassFinderException
     */
    public function findClasses($namespace)
    {
        $composerNamespaces = $this->config->getPSR4Namespaces();

        /** @var PSR4Namespace $bestNamespace */
        $bestNamespace = array_reduce($composerNamespaces, function($carry, PSR4Namespace $potentialNamespace) use ($namespace) {
            if ($potentialNamespace->matches($namespace)) {
                return $potentialNamespace;
            } else {
                return $carry;
            }
        }, null);

        if ($bestNamespace instanceof PSR4Namespace) {
            return $bestNamespace->findClasses($namespace);
        } else {
            throw new ClassFinderException(sprintf("Unknown namespace '%s'. You should add the namespace prefix to composer.json. See '%s' for details.",
                $namespace,
                'https://gitlab.com/hpierce1102/ClassFinder/blob/master/docs/exceptions/unregisteredRoot.md'
            ));
        }
    }
}
