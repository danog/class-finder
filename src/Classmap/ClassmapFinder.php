<?php
namespace HaydenPierce\ClassFinder\Classmap;

use HaydenPierce\ClassFinder\FinderInterface;

class ClassmapFinder implements FinderInterface
{
    private $factory;

    public function __construct(ClassmapEntryFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param $namespace
     * @return bool|string
     * @throws ClassFinderException
     */
    public function findClasses($namespace)
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
