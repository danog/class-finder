<?php
namespace HaydenPierce\ClassFinder\Files;

class FilesEntry
{
    private $file;

    public function __construct($fileToInclude)
    {
        $this->file = $fileToInclude;
    }

    public function knowsNamespace($namespace)
    {
        // TODO.
    }

    /**
     * @param $namespace
     * @return bool
     */
    public function matches($namespace)
    {
        // TODO.
    }
}