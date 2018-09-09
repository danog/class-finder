<?php

namespace TestApp1;

require_once __DIR__ . '/../vendor/autoload.php';

use HaydenPierce\ClassFinder\ClassFinder;

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
            )
        );
    }
}