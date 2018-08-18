<?php

namespace TestApp1;

require_once __DIR__ . '/../vendor/autoload.php';

use HaydenPierce\ClassFinder\ClassFinder;
use \PHPUnit\Framework\TestCase;


// "vendor/bin/phpunit" "./test/app1/src/ClassFinderTest.php"
class ClassFinderTest extends TestCase
{
    public function setup()
    {
        // Reset ClassFinder back to normal.
        ClassFinder::setAppRoot(null);
    }
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
            ),
            // This namespace is empty, but it DOES exist. So we don't throw an exception.
            array(
                'TestApp1\Foo\Empty',
                array()
            )
        );
    }

    /**
     * @expectedException HaydenPierce\ClassFinder\ClassFinderException
     * @expectedExceptionMessage Unknown namespace 'DoesNotExist\Foo\Bar'. You should add the namespace prefix to composer.json.
     */
    public function testThrowsOnUnknownNameSpace()
    {
        // The top level namespace ("DoesNotExist") wasn't registered in composer.json.
        // "Unknown namespace '$namespace'. You should add the namespace prefix to composer.json. See '$link' for details."
        ClassFinder::getClassesInNamespace('DoesNotExist\Foo\Bar');
    }

    /**
     * @expectedException HaydenPierce\ClassFinder\ClassFinderException
     * @expectedExceptionMessageRegExp  /Unknown namespace 'TestApp1\\DoesNotExist'\. Checked for files in .*, but that directory did not exist\./
     */
    public function testThrowsOnUnknownSubNameSpace()
    {
        ClassFinder::getClassesInNamespace('TestApp1\DoesNotExist');
    }

    /**
     * @expectedException HaydenPierce\ClassFinder\ClassFinderException
     * @expectedExceptionMessage Could not locate composer.json. You can get around this by setting ClassFinder::$appRoot manually.
     */
    public function testThrowsOnMissingComposerConfig()
    {
        // ClassFinder will fail to identify a valid composer.json file.
        ClassFinder::setAppRoot("/"); // Obviously, the application isn't running directly on the OS's root.

        // "Could not locate composer.json. You can get around this by setting ClassFinder::$appRoot manually. See '$link' for details."
        ClassFinder::getClassesInNamespace('TestApp1\Foo\Loo');
    }
}