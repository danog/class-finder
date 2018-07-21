<?php

namespace TestApp1;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../vendor/hpierce1102/ClassFinder.php';

use HaydenPierce\ClassFinder\ClassFinder;
use \PHPUnit\Framework\TestCase;


// "vendor/bin/phpunit" "./test/app1/src/ClassFinderTest.php"
class ClassFinderTest extends TestCase
{
    public function testClassFinder()
    {
        try {
            $classes = ClassFinder::getClassesInNamespace('TestApp1\Foo');
        } catch (Exception $e) {
            $this->assertFalse(true, 'An exception occurred: ' . $e->getMessage());
            $classes = array();
        }

        $this->assertEquals(array(
            'TestApp1\Foo\Bar',
            'TestApp1\Foo\Baz',
            'TestApp1\Foo\Foo'
        ), $classes);
    }
}