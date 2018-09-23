<?php
namespace HaydenPierce\ClassFinder\PSR4;

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

    public function knowsNamespace($namespace)
    {
        $numberOfSegments = count(explode('\\', $namespace));
        $matchingSegments = $this->countMatchingNamespaceSegments($namespace);

        if ($matchingSegments === 0) {
            // Provided namespace doesn't map to anything registered.
            return false;
        } elseif ($numberOfSegments <= $matchingSegments) {
            // This namespace is a superset of the provided namespace. Namespace is known.
            return true;
        } else {
            // This namespace is a subset of the provided namespace. We must resolve the remaining segments to a directory.
            $relativePath = substr($namespace, strlen($this->namespace));
            foreach ($this->directories as $directory) {
                $path = $this->normalizePath($directory, $relativePath);
                if (is_dir($path)) {
                    return true;
                }
            }

            return false;
        }
    }

    /**
     * Determines how many namespace segments match the internal namespace. This is useful because multiple namespaces
     * may technically match a registered namespace root, but one of the matches may be a better match. Namespaces that
     * match, but are not _the best_ match are incorrect matches. TestApp1\\ is **not** the best match when searching for
     * namespace TestApp1\\Multi\\Foo if TestApp1\\Multi was explicitly registered.
     *
     * PSR4Namespace $a;
     * $a->namespace = "TestApp1\\";
     * $a->countMatchingNamespaceSegments("TestApp1\\Multi") -> 1, TestApp1 matches.
     *
     * PSR4Namespace $b;
     * $b->namespace = "TestApp1\\Multi";
     * $b->countMatchingNamespaceSegments("TestApp1\\Multi") -> 2, TestApp1\\Multi matches
     *
     * PSR4Namespace $c;
     * $c->namespace = "HaydenPierce\\Foo\\Bar";
     * $c->countMatchingNamespaceSegments("TestApp1\\Multi") -> 0, No matches.
     *
     * @param $namespace
     * @return int
     */
    public function countMatchingNamespaceSegments($namespace)
    {
        $namespaceFragments = explode('\\', $namespace);
        $undefinedNamespaceFragments = array();

        while($namespaceFragments) {
            $possibleNamespace = implode('\\', $namespaceFragments) . '\\';

            if(strpos($this->namespace, $possibleNamespace) !== false){
                return count(explode('\\', $possibleNamespace)) - 1;
            }

            array_unshift($undefinedNamespaceFragments, array_pop($namespaceFragments));
        }

        return 0;
    }

    public function isAcceptableNamespace($namespace)
    {
        $namespaceSegments = count(explode('\\', $this->namespace)) - 1;
        $matchingSegments = $this->countMatchingNamespaceSegments($namespace);
        return $namespaceSegments === $matchingSegments;
    }

    public function findClasses($namespace)
    {
        $relativePath = substr($namespace, strlen($this->namespace));

        $self = $this;
        $directories = array_reduce($this->directories, function($carry, $directory) use ($relativePath, $namespace, $self){
            $path = $self->normalizePath($directory, $relativePath);
            $realDirectory = realpath($path);
            if ($realDirectory !== false) {
                return array_merge($carry, array($realDirectory));
            } else {
                return $carry;
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

        return array_filter($potentialClasses, function($potentialClass) {
            if (function_exists($potentialClass)) {
                // For some reason calling class_exists() on a namespace'd function raises a Fatal Error (tested PHP 7.0.8)
                // Example: DeepCopy\deep_copy
                return false;
            } else {
                return class_exists($potentialClass);
            }
        });
    }

    /**
     * Creates a file path based on an absolute path to a directory and a relative path in a way
     * that will be compatible with both Linux and Windows. This method is also extracted so that
     * it can be turned into a vfs:// stream URL for unit testing.
     * @param $directory
     * @param $relativePath
     * @return mixed
     */
    public function normalizePath($directory, $relativePath)
    {
        $path = str_replace('\\', '/', $directory . '/' . $relativePath);
        return $path;
    }
}