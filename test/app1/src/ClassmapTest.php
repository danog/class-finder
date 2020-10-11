<?php

namespace TestApp1;

require_once __DIR__ . '/../vendor/autoload.php';

use danog\ClassFinder\ClassFinder;

class ClassmapTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        // Reset ClassFinder back to normal.
        ClassFinder::setAppRoot(null);
    }
    /**
     * @dataProvider classFinderDataProvider
     */
    public function testClassFinder($namespace, $expected, $message)
    {
        try {
            $classes = ClassFinder::getClassesInNamespace($namespace);
        } catch (Exception $e) {
            $this->assertFalse(true, 'An exception occurred: ' . $e->getMessage());
            $classes = array();
        }

        $this->assertEquals($expected, $classes, $message);
    }

    public function classFinderDataProvider()
    {
        return array(
            array(
                'TestApp1\ClassmapClasses',
                array(
                    'TestApp1\ClassmapClasses\Bik',
                    'TestApp1\ClassmapClasses\Bil',
                    'TestApp1\ClassmapClasses\Bir',
                    'TestApp1\ClassmapClasses\Mik',
                    'TestApp1\ClassmapClasses\Mil',
                    'TestApp1\ClassmapClasses\Mir',
                    'TestApp1\ClassmapClasses\Tik',
                    'TestApp1\ClassmapClasses\Til',
                    'TestApp1\ClassmapClasses\Tir'
                ),
                'Classfinder should be able to load classes based on a classmap.'
            ),
            array(
                'danog\Classmap',
                array(
                    'danog\Classmap\Classmap2ClassmapINC',
                    'danog\Classmap\Classmap2ClassmapPHP',
                    'danog\Classmap\Classmap3ClassesPHP',
                    'danog\Classmap\ClassmapClassmap2PHP'
                ),
                'Classfinder should be able to load classes based on a classmap from 3rd party packages.'
            ),
            array(
                'danog\Classmap2',
                array(
                    'danog\Classmap2\Classmap2ClassmapINC',
                    'danog\Classmap2\Classmap2ClassmapPHP',
                    'danog\Classmap2\Classmap3ClassesPHP',
                    'danog\Classmap2\ClassmapClassmap2PHP'
                ),
                'Classfinder should be able to handle multiple namespaces in a single file for a classmap.'
            )
        );
    }

    /**
     * @dataProvider classesInNamespaceRecursivelyDataProvider
     */
    public function testClassesInNamespaceRecursively($namespace, $expected, $message)
    {
        ClassFinder::disablePSR4Support();

        try {
            $classes = ClassFinder::getClassesInNamespace($namespace, ClassFinder::RECURSIVE_MODE);
        } catch (Exception $e) {
            $this->assertFalse(true, 'An exception occurred: ' . $e->getMessage());
            $classes = array();
        }

        ClassFinder::enablePSR4Support();

        $this->assertEquals($expected, $classes, $message);
    }

    public function classesInNamespaceRecursivelyDataProvider()
    {
        return array(
            array(
                'TestApp1\ClassmapClasses',
                array(
                    'TestApp1\ClassmapClasses\Bik',
                    'TestApp1\ClassmapClasses\Bil',
                    'TestApp1\ClassmapClasses\Bir',
                    'TestApp1\ClassmapClasses\Mik',
                    'TestApp1\ClassmapClasses\Mil',
                    'TestApp1\ClassmapClasses\Mir',
                    'TestApp1\ClassmapClasses\NestedClasses\NestedClass1',
                    'TestApp1\ClassmapClasses\NestedClasses\NestedClass2',
                    'TestApp1\ClassmapClasses\NestedClasses\NestedClass3',
                    'TestApp1\ClassmapClasses\Tik',
                    'TestApp1\ClassmapClasses\Til',
                    'TestApp1\ClassmapClasses\Tir'
                ),
                'Classfinder should be able to load classes recursively based on a classmap.'
            ),
            array(
                'danog',
                array(
                    'danog\Classmap2\Classmap2ClassmapINC',
                    'danog\Classmap2\Classmap2ClassmapPHP',
                    'danog\Classmap2\Classmap3ClassesPHP',
                    'danog\Classmap2\ClassmapClassmap2PHP',
                    'danog\Classmap\Classmap2ClassmapINC',
                    'danog\Classmap\Classmap2ClassmapPHP',
                    'danog\Classmap\Classmap3ClassesPHP',
                    'danog\Classmap\ClassmapClassmap2PHP',
                ),
                'Classfinder should be able to load third party classes recursively based on a classmap.'
            )
        );
    }
}