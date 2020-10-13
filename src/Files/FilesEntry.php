<?php

namespace danog\ClassFinder\Files;

use danog\ClassFinder\ClassFinder;

class FilesEntry
{
    /** @var string */
    private $file;

    /** @var string */
    private $php;

    /**
     * @param string $fileToInclude
     * @param string $php
     */
    public function __construct($fileToInclude, $php)
    {
        $this->file = $this->normalizePath($fileToInclude);
        $this->php = $php;
    }

    /**
     * @param string $namespace
     * @return bool
     */
    public function knowsNamespace($namespace)
    {
        $classes = $this->getClassesInFile(ClassFinder::ALLOW_ALL);

        foreach($classes as $class) {
            if (strpos($class, $namespace) !== false) {
                return true;
            };
        }

        return false;
    }

    /**
     * Gets a list of classes that belong to the given namespace
     * @param string $namespace
     * @param int    $options
     * @return string[]
     */
    public function getClasses($namespace, $options)
    {
        $classes = $this->getClassesInFile($options);

        return array_values(array_filter($classes, function($class) use ($namespace) {
            $classNameFragments = explode('\\', $class);
            array_pop($classNameFragments);
            $classNamespace = implode('\\', $classNameFragments);

            $namespace = trim($namespace, '\\');

            return $namespace === $classNamespace;
        }));
    }

    /**
     * Execute PHP code and return retuend value
     *
     * @param string $script
     * @return mixed
     */
    private function execReturn($script)
    {
        exec($this->php . " -r \"$script\"", $output);
        $classes = 'return ' . implode('', $output) . ';';
        return eval($classes);
    }

    /**
     * Dynamically execute files and check for defined classes.
     *
     * This is where the real magic happens. Since classes in a randomly included file could contain classes in any namespace,
     * (or even multiple namespaces!) we must execute the file and check for newly defined classes. This has a potential
     * downside that files being executed will execute their side effects - which may be undesirable. However, Composer
     * will require these files anyway - so hopefully causing those side effects isn't that big of a deal.
     *
     * @return array
     */
    private function getClassesInFile($options)
    {
        // get_declared_*() returns a bunch of classes|interfaces|traits that are built into PHP. So we need a control here.
        list($initialInterfaces, 
            $initialClasses, 
            $initialTraits,
            $initialFuncs
        ) = $this->execReturn("var_export(array(get_declared_interfaces(), get_declared_classes(), get_declared_traits(), get_defined_functions()['user']));");

        // This brings in the new classes. so $classes here will include the PHP defaults and the newly defined classes
        list($allInterfaces,
            $allClasses,
            $allTraits,
            $allFuncs
        ) = $this->execReturn("require_once '{$this->file}'; var_export(array(get_declared_interfaces(), get_declared_classes(), get_declared_traits(), get_defined_functions()['user']));");

        $interfaces = array_diff($allInterfaces, $initialInterfaces);
        $classes = array_diff($allClasses, $initialClasses);
        $traits = array_diff($allTraits, $initialTraits);
        $funcs = array_diff($allFuncs, $initialFuncs);

        $final = array();
        if ($options & ClassFinder::ALLOW_CLASSES) {
            $final = $classes;
        }
        if ($options & ClassFinder::ALLOW_INTERFACES) {
            $final = array_merge($final, $interfaces);
        }
        if ($options & ClassFinder::ALLOW_TRAITS) {
            $final = array_merge($final, $traits);
        }
        if ($options & ClassFinder::ALLOW_FUNCTIONS) {
            $final = array_merge($final, $funcs);
        }
        return $final;
    }

    /**
     * TODO: Similar to PSR4Namespace::normalizePath. Maybe we refactor?
     * @param string $path
     * @return string
     */
    private function normalizePath($path)
    {
        $path = str_replace('\\', '/', $path);
        return $path;
    }
}
