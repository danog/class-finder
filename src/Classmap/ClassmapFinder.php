<?php

namespace danog\ClassFinder\Classmap;

use danog\ClassFinder\ClassFinder;
use danog\ClassFinder\FinderInterface;

class ClassmapFinder implements FinderInterface
{
    /** @var ClassmapEntryFactory */
    private $factory;

    public function __construct(ClassmapEntryFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param string $namespace
     * @return bool
     */
    public function isNamespaceKnown($namespace)
    {
        $classmapEntries = $this->factory->getClassmapEntries();

        foreach($classmapEntries as $classmapEntry) {
            if ($classmapEntry->knowsNamespace($namespace)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $namespace
     * @param int $options
     * @return string[]
     */
    public function findClasses($namespace, $options)
    {
        $classmapEntries = $this->factory->getClassmapEntries();

        $matchingEntries = array_filter($classmapEntries, function(ClassmapEntry $entry) use ($namespace, $options) {
            if (!$entry->matches($namespace, $options)) return false;
            $potentialClass = $entry->getClassName();
            if (function_exists($potentialClass)) {
                // For some reason calling class_exists() on a namespace'd function raises a Fatal Error (tested PHP 7.0.8)
                // Example: DeepCopy\deep_copy
                return $options & ClassFinder::ALLOW_FUNCTIONS;
            } else if (class_exists($potentialClass)) {
                return $options & ClassFinder::ALLOW_CLASSES;
             } else if (interface_exists($potentialClass, false)) {
                return $options & ClassFinder::ALLOW_INTERFACES;
             } else if (trait_exists($potentialClass, false)) {
                return $options & ClassFinder::ALLOW_TRAITS;
            }
        });

        return array_map(function(ClassmapEntry $entry) {
            return $entry->getClassName();
        }, $matchingEntries);
    }
}
