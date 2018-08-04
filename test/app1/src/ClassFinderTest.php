<?php

namespace TestApp1;

require_once __DIR__ . '/../vendor/autoload.php';

use HaydenPierce\ClassFinder\ClassFinder;
use \PHPUnit\Framework\TestCase;


// "vendor/bin/phpunit" "./test/app1/src/ClassFinderTest.php"
class ClassFinderTest extends TestCase
{
    /**
     * @dataProvider classFinderDataProvider
     */
    public function testClassFinder($namespace, $expected)
    {
        try {
            $classes = ClassFinder::getClassesInNamespace($namespace);
        } catch (Exception $e) {
            $this->assertFalse(true, 'An exception occurred: ' . $e->getMessage());
            $classes = array();
        }

        $this->assertEquals($expected, $classes);
    }

    public function classFinderDataProvider()
    {
        return array(
            array(
                'TestApp1\Foo',
                array(
                    'TestApp1\Foo\Bar',
                    'TestApp1\Foo\Baz',
                    'TestApp1\Foo\Foo'
                )
            ),
            array(
                'TestApp1\Foo\Loo',
                array(
                    'TestApp1\Foo\Loo\Lar',
                    'TestApp1\Foo\Loo\Laz',
                    'TestApp1\Foo\Loo\Loo'
                )
            )
        );
    }

    /**
     * @expectedException HaydenPierce\ClassFinder\ClassFinderException
     * @expectedExceptionMessage Unknown namespace 'DoesNotExist\Foo\Bar'. You should add the namespace prefix to composer.json.
     */
    public function testThrowsOnUnknownNameSpace()
    {
        // The root wasn't registered in composer.json.
        // "Unknown namespace '$namespace'. You should add the namespace prefix to composer.json. See '$link' for details."
        ClassFinder::getClassesInNamespace('DoesNotExist\Foo\Bar');
    }

}