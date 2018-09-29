<?php
namespace HaydenPierce\ClassFinder;

use HaydenPierce\ClassFinder\Classmap\ClassmapEntryFactory;
use HaydenPierce\ClassFinder\Classmap\ClassmapFinder;
use HaydenPierce\ClassFinder\Exception\ClassFinderException;
use HaydenPierce\ClassFinder\PSR4\PSR4Finder;
use HaydenPierce\ClassFinder\PSR4\PSR4NamespaceFactory;

class ClassFinder
{
    /** @var AppConfig */
    private static $config;

    /** @var PSR4Finder */
    private static $psr4;

    /** @var ClassmapFinder */
    private static $classmap;

    private static function initialize()
    {
        if (!(self::$config instanceof AppConfig)) {
            self::$config = new AppConfig();
        }

        if (!(self::$psr4 instanceof PSR4Finder)) {
            $PSR4Factory = new PSR4NamespaceFactory(self::$config);
            self::$psr4 = new PSR4Finder($PSR4Factory);
        }

        if (!(self::$classmap instanceof ClassmapFinder)) {
            $classmapFactory = new ClassmapEntryFactory(self::$config);
            self::$classmap = new ClassmapFinder($classmapFactory);
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

        $supportedFinders = array(
            self::$psr4,
            self::$classmap
        );

        $findersWithNamespace = array_filter($supportedFinders, function(FinderInterface $finder) use ($namespace){
            return $finder->isNamespaceKnown($namespace);
        });

        if (count($findersWithNamespace) === 0) {
            throw new ClassFinderException(sprintf("Unknown namespace '%s'. See '%s' for details.",
                $namespace,
                'https://gitlab.com/hpierce1102/ClassFinder/blob/master/docs/exceptions/unknownNamespace.md'
            ));
        }

        $classes = array_reduce($findersWithNamespace, function($carry, FinderInterface $finder) use ($namespace){
            return array_merge($carry, $finder->findClasses($namespace));
        }, array());

        return array_unique($classes);
    }

    public static function setAppRoot($appRoot)
    {
        self::initialize();
        self::$config->setAppRoot($appRoot);
    }
}