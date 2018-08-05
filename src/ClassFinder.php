<?php
namespace HaydenPierce\ClassFinder;

class ClassFinder
{
    public static $appRoot;

    /**
     * @throws \Exception
     */
    private static function findAppRoot()
    {
        if (self::$appRoot) {
            $appRoot = self::$appRoot;
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

        if (!file_exists($appRoot . '/composer.json')) {
            throw new ClassFinderException(sprintf("Could not locate composer.json. You can get around this by setting ClassFinder::\$appRoot manually. See '%s' for details.",
                'https://gitlab.com/hpierce1102/ClassFinder' // TODO: write documentation and update this link.
                ));
        } else {
            self::$appRoot = $appRoot;
            return self::$appRoot;
        }

    }

    /**
     * @param $namespace
     * @return array
     * @throws \Exception
     */
    public static function getClassesInNamespace($namespace)
    {
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
     * @return array
     * @throws \Exception
     */
    private static function getDefinedNamespaces()
    {
        $appRoot = self::findAppRoot();

        $composerJsonPath = $appRoot. 'composer.json';
        $composerConfig = json_decode(file_get_contents($composerJsonPath));

        //Apparently PHP doesn't like hyphens, so we use variable variables instead.
        $psr4 = "psr-4";
        return (array) $composerConfig->autoload->$psr4;
    }

    /**
     * @param $namespace
     * @return bool|string
     * @throws \Exception
     */
    private static function getNamespaceDirectory($namespace)
    {
        $appRoot = self::findAppRoot();

        $composerNamespaces = self::getDefinedNamespaces();

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
}