<?php
namespace HaydenPierce\ClassFinder;

use HaydenPierce\ClassFinder\Exception\ClassFinderException;
use HaydenPierce\ClassFinder\PSR4\PSR4Finder;
use HaydenPierce\ClassFinder\PSR4\PSR4NamespaceFactory;

class ClassFinder
{
    /** @var AppConfig */
    private static $config;

    /** @var PSR4Finder */
    private static $psr4;

    private static function initialize()
    {
        if (!(self::$config instanceof AppConfig)) {
            self::$config = new AppConfig();
        }

        if (!(self::$psr4 instanceof PSR4Finder)) {
            $PSR4Factory = new PSR4NamespaceFactory(self::$config);
            self::$psr4 = new PSR4Finder($PSR4Factory);
        }
    }

    /**
     * @param $namespace
     * @return array
     * @throws \Exception
     */
    public static function getClassesInNamespace($namespace)
    {
        self::initialize();

        $isNamespaceKnown = self::$psr4->isNamespaceKnown($namespace);
        if (!$isNamespaceKnown) {
            throw new ClassFinderException(sprintf("Unknown namespace '%s'. You should add the namespace to composer.json. See '%s' for details.",
                $namespace,
                'https://gitlab.com/hpierce1102/ClassFinder/blob/master/docs/exceptions/unregisteredRoot.md'
            ));
        }

        $classes = self::$psr4->findClasses($namespace);

        return array_values($classes);
    }

    public static function setAppRoot($appRoot)
    {
        self::initialize();
        self::$config->setAppRoot($appRoot);
    }
}