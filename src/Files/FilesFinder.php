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

        $matchingEntries = array_filter($filesEntries, function(FilesEntry $entry) use ($namespace) {
            return $entry->matches($namespace);
        });

        return array_map(function(FilesEntry $entry) {
            return $entry->getClassName();
        }, $matchingEntries);
    }
}
