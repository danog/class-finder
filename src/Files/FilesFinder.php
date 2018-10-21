<?php
namespace HaydenPierce\ClassFinder\Files;

use HaydenPierce\ClassFinder\FinderInterface;

class FilesFinder implements FinderInterface
{
    private $factory;

    public function __construct(FilesEntryFactory $factory)
    {
        $this->factory = $factory;
    }

    public function isNamespaceKnown($namespace)
    {
        $filesEntries = $this->factory->getFilesEntries();

        foreach($filesEntries as $filesEntry) {
            if ($filesEntry->knowsNamespace($namespace)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $namespace
     * @return bool|string
     */
    public function findClasses($namespace)
    {
        $filesEntries = $this->factory->getFilesEntries();

        return array_reduce($filesEntries, function($carry, FilesEntry $entry) use ($namespace){
            return array_merge($carry, $entry->getClasses($namespace));
        }, array());
    }
}
