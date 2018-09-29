<?php
namespace HaydenPierce\ClassFinder\Files;

use HaydenPierce\ClassFinder\AppConfig;

class FilesEntryFactory
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
    public function getFilesEntries()
    {
        $files = array();

        $filesKeys = array_keys($files);
        return array_map(function($index) use ($filesKeys){
            return new FilesEntry($filesKeys[$index]);
        }, range(0, count($files) - 1));
    }
}