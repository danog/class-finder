<?php
namespace HaydenPierce\ClassFinder\Finder;

interface FinderInterface
{
    public function findClasses($namespace);
}