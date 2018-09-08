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

    }
}
