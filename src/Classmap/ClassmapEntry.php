<?php
namespace HaydenPierce\ClassFinder\Classmap;

use HaydenPierce\ClassFinder\Exception\ClassFinderException;

class ClassmapEntry
{
    private $namespace;
    private $directories;

    public function __construct($namespace, $directories)
    {
        $this->namespace = $namespace;
        $this->directories = $directories;
    }
}