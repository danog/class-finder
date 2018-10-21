<?php

namespace TestApp1;

require_once __DIR__ . '/../vendor/autoload.php';

use HaydenPierce\ClassFinder\ClassFinder;

class FilesTest extends \PHPUnit_Framework_TestCase
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
            ClassFinder::enableFilesSupport();
            $classes = ClassFinder::getClassesInNamespace($namespace);
        } catch (\Exception $e) {
            $this->assertFalse(true, 'An exception occurred: ' . $e->getMessage());
            $classes = array();
        }

        $this->assertEquals($expected, $classes, $message);
    }

    public function classFinderDataProvider()
    {
        return array(
            array(
                'TestApp1\FilesClasses',
                array(
                    'TestApp1\FilesClasses\Bam',
                    'TestApp1\FilesClasses\Wam',
                    'TestApp1\FilesClasses\Fam',
                    'TestApp1\FilesClasses\Cam',
                    'TestApp1\FilesClasses\Lam',
                ),
                'ClassFinder should be able to find 1st party classes included from `files` listed in composer.json.'
            )
        );
    }
}