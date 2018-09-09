<?php
namespace HaydenPierce\ClassFinder\Classmap;

class ClassmapEntry
{
    private $className;

    public function __construct($fullyQualifiedClassName)
    {
        $this->className = $fullyQualifiedClassName;
    }

    public function knowsNamespace($namespace)
    {
        return strpos($this->className, $namespace) !== false;
    }

    /**
     * Checks if the class is a DIRECT child of the given namespace. Currently, no other finders support "recursively"
     * discovering classes, so the Classmap module will not be the exception to that rule.
     *
     * @param $namespace
     * @return bool
     */
    public function matches($namespace)
    {
        $classNameFragments = explode('\\', $this->getClassName());
        array_pop($classNameFragments);
        $classNamespace = implode('\\', $classNameFragments);

        $namespace = trim($namespace, '\\');

        return $namespace === $classNamespace;
    }

    public function getClassName()
    {
        return $this->className;
    }

}