<?php
namespace HaydenPierce\ClassFinder\Finder;

use HaydenPierce\ClassFinder\Exception\ClassFinderException;

class PSR4NamespaceFactory
{
    public function __construct()
    {

    }

    /**
     * TODO: Figure out a way to stop passing in $appRoot. This will probably be a refactoring.
     * Creates a namespace from composer_psr4.php and composer.json's autoload.psr4 items
     * @param $namespace
     * @param $directories
     * @throws ClassFinderException
     */
    public function createNamespace($namespace, $directories, $appRoot)
    {
        if (is_string($directories)) {
            // This is an acceptable format according to composer.json
            $directories = array($appRoot . $directories);
        } elseif (is_array($directories)) {
            // composer_psr4.php seems to put everything in this format
        } else {
            throw new ClassFinderException('Unknown PSR4 definition.');
        }

        $directories = array_map(function($directory) {
            return realpath($directory);
        }, $directories);

        return new PSR4Namespace($namespace, $directories);
    }
}