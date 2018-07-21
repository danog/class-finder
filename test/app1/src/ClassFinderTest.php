<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

use HaydenPierce\ClassFinder\ClassFinder;

class ClassFinderTest extends \PHPUnit\Framework\TestCase
{
    public function testClassFinder()
    {
        ClassFinder::$appRoot = realpath(__DIR__ . '/../') . '/';

        try {
            $classes = ClassFinder::getClassesInNamespace('TestApp1\Foo');
        } catch (Exception $e) {
            $this->assertFalse(true, 'An exception occurred: ' . $e->getMessage());
            $classes = array();
        }

        $this->assertEquals(array(

        ), $classes);
    }
}