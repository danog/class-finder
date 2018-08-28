<?php
namespace HaydenPierce\ClassFinder;

use HaydenPierce\ClassFinder\Finder\PSR4Finder;
use HaydenPierce\ClassFinder\Finder\PSR4NamespaceFactory;

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

        $classes = self::$psr4->findClasses($namespace);

        return array_values($classes);
    }

    public static function setAppRoot($appRoot)
    {
        self::initialize();
        self::$config->setAppRoot($appRoot);
    }
}