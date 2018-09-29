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
        $filesEntries = $this->factory->getfilesEntries();

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

        $matchingEntries = array_filter($filesEntries, function(filesEntry $entry) use ($namespace) {
            return $entry->matches($namespace);
        });

        return array_map(function(filesEntry $entry) {
            return $entry->getClassName();
        }, $matchingEntries);
    }
}
