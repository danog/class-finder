<?php
namespace HaydenPierce\ClassFinder\Classmap;

use HaydenPierce\ClassFinder\AppConfig;
use HaydenPierce\ClassFinder\Exception\ClassFinderException;

class ClassmapEntryFactory
{
    /** @var AppConfig */
    private $appConfig;

    public function __construct(AppConfig $appConfig)
    {
        $this->appConfig = $appConfig;
    }

    /**
     * @return array
     */
    public function getClassmapEntries()
    {

    }
}