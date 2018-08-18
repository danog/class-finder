<?php
namespace HaydenPierce\ClassFinder;

class ClassFinder
{
    /** @var AppConfig */
    private static $config;

    /**
     * @param $namespace
     * @return array
     * @throws \Exception
     */
    public static function getClassesInNamespace($namespace)
    {
        self::initialize();

        $files = scandir(self::getNamespaceDirectory($namespace));

        $classes = array_map(function($file) use ($namespace){
            return $namespace . '\\' . str_replace('.php', '', $file);
        }, $files);

        $classes = array_filter($classes, function($possibleClass){
            return class_exists($possibleClass);
        });

        return array_values($classes);
    }

    /**
     * @param $namespace
     * @return bool|string
     * @throws \Exception
     */
    private static function getNamespaceDirectory($namespace)
    {
        $appRoot = self::$config->getAppRoot();

        $composerNamespaces = self::$config->getDefinedNamespaces();

        $namespaceFragments = explode('\\', $namespace);
        $undefinedNamespaceFragments = [];

        while($namespaceFragments) {
            $possibleNamespace = implode('\\', $namespaceFragments) . '\\';

            if(array_key_exists($possibleNamespace, $composerNamespaces)){
                $resolvedDirectory = $appRoot . $composerNamespaces[$possibleNamespace] . implode('/', $undefinedNamespaceFragments);
                $realDirectory = realpath($resolvedDirectory);
                if ($realDirectory !== false) {
                    return $realDirectory;
                } else {
                    throw new ClassFinderException(sprintf("Unknown namespace '%s'. Checked for files in %s, but that directory did not exist. See %s for details.",
                        $namespace,
                        $resolvedDirectory,
                        'https://gitlab.com/hpierce1102/ClassFinder/blob/master/docs/exceptions/unknownSubNamespace.md'
                    ));
                }
            }

            array_unshift($undefinedNamespaceFragments, array_pop($namespaceFragments));
        }

        throw new ClassFinderException(sprintf("Unknown namespace '%s'. You should add the namespace prefix to composer.json. See '%s' for details.",
            $namespace,
            'https://gitlab.com/hpierce1102/ClassFinder/blob/master/docs/exceptions/unregisteredRoot.md'
        ));
    }

    public static function setAppRoot($appRoot)
    {
        self::initialize();
        self::$config->setAppRoot($appRoot);
    }

    private static function initialize()
    {
        if (!(self::$config instanceof AppConfig)) {
            self::$config = new AppConfig();
        }
    }
}