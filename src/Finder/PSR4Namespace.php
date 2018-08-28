<?php
namespace HaydenPierce\ClassFinder\Finder;

use HaydenPierce\ClassFinder\Exception\ClassFinderException;

class PSR4Namespace
{
    private $namespace;
    private $directories;

    public function __construct($namespace, $directories)
    {
        $this->namespace = $namespace;
        $this->directories = $directories;
    }

    public function matches($namespace)
    {
        $namespaceFragments = explode('\\', $namespace);
        $undefinedNamespaceFragments = [];

        while($namespaceFragments) {
            $possibleNamespace = implode('\\', $namespaceFragments) . '\\';

            if($this->namespace === $possibleNamespace){
                return true;
            }

            array_unshift($undefinedNamespaceFragments, array_pop($namespaceFragments));
        }

        return false;
    }

    public function findClasses($namespace)
    {
        // TODO: revisit this. It probably doesn't work.
        $relativePath = substr($namespace, strlen($this->namespace));

        $directories = array_reduce($this->directories, function($carry, $directory) use ($relativePath, $namespace){
            $realDirectory = realpath($directory . '/' . $relativePath);
            if ($realDirectory !== false) {
                return array_merge($carry, array($realDirectory));
            } else {
                throw new ClassFinderException(sprintf("Unknown namespace '%s'. Checked for files in %s, but that directory did not exist. See %s for details.",
                    $namespace,
                    $realDirectory,
                    'https://gitlab.com/hpierce1102/ClassFinder/blob/master/docs/exceptions/unknownSubNamespace.md'
                ));
            }
        }, array());

        $arraysOfClasses = array_map(function($directory) {
            return scandir($directory);
        }, $directories);

        $potentialClassFiles = array_reduce($arraysOfClasses, function($carry, $arrayOfClasses) {
            return array_merge($carry, $arrayOfClasses);
        }, array());

        $potentialClasses = array_map(function($file) use ($namespace){
            return $namespace . '\\' . str_replace('.php', '', $file);
        }, $potentialClassFiles);

        return array_filter($potentialClasses, 'class_exists');
    }
}