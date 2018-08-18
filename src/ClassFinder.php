<?php
namespace HaydenPierce\ClassFinder;

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
            self::$psr4 = new PSR4Finder(self::$config);
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

        $files = scandir(self::$psr4->getNamespaceDirectory($namespace));

        $classes = array_map(function($file) use ($namespace){
            return $namespace . '\\' . str_replace('.php', '', $file);
        }, $files);

        $classes = array_filter($classes, function($possibleClass){
            return class_exists($possibleClass);
        });

        return array_values($classes);
    }

    public static function setAppRoot($appRoot)
    {
        self::initialize();
        self::$config->setAppRoot($appRoot);
    }
}