<?php
namespace HaydenPierce\ClassFinder\Classmap;

use HaydenPierce\ClassFinder\Exception\ClassFinderException;
use HaydenPierce\ClassFinder\FinderInterface;

class ClassmapFinder implements FinderInterface
{
    private $factory;

    public function __construct(ClassmapEntryFactory $factory)
    {
        $this->factory = $factory;
    }

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
     * TODO: Add support for RECURSIVE_MODE
     * @param $namespace
     * @param $options
     * @return bool|string
     * @throws ClassFinderException
     */
    public function findClasses($namespace, $options)
    {
        $classmapEntries = $this->factory->getClassmapEntries();

        $matchingEntries = array_filter($classmapEntries, function(ClassmapEntry $entry) use ($namespace) {
            return $entry->matches($namespace);
        });

        return array_map(function(ClassmapEntry $entry) {
            return $entry->getClassName();
        }, $matchingEntries);
    }
}
