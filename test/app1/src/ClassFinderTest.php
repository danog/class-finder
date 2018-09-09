<?php

namespace TestApp1;

require_once __DIR__ . '/../vendor/autoload.php';

use HaydenPierce\ClassFinder\ClassFinder;

// "vendor/bin/phpunit" "./test/app1/src/ClassFinderTest.php"
class ClassFinderTest extends \PHPUnit_Framework_TestCase
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
                'TestApp1\Foo',
                array(
                    'TestApp1\Foo\Bar',
                    'TestApp1\Foo\Baz',
                    'TestApp1\Foo\Foo'
                ),
                'ClassFinder should be able to find 1st party classes.'
            ),
            array(
                'TestApp1\Foo\Loo',
                array(
                    'TestApp1\Foo\Loo\Lar',
                    'TestApp1\Foo\Loo\Laz',
                    'TestApp1\Foo\Loo\Loo'
                ),
                'ClassFinder should be able to find 1st party classes multiple namespaces deep.'
            ),
            array(
                'TestApp1\Multi',
                array(
                    'TestApp1\Multi\Uij',
                    'TestApp1\Multi\Yij',
                    'TestApp1\Multi\Uik',
                    'TestApp1\Multi\Yik'
                ),
                'ClassFinder should be able to find 1st party classes when a provided namespace root maps to multiple directories (Example: "HaydenPierce\\SandboxAppMulti\\": ["multi/Bop", "multi/Bot"] )'
            ),
            array(
                'TestApp1\Multi\Yop',
                array(
                    'TestApp1\Multi\Yop\Rik',
                    'TestApp1\Multi\Yop\Tik',
                    'TestApp1\Multi\Yop\Eij',
                    'TestApp1\Multi\Yop\Rij'
                ),
                'ClassFinder should be able to find 1st party classes when a provided namespace root maps to multiple directories multiple levels deep. (Example: "HaydenPierce\\SandboxAppMulti\\": ["multi/Bop", "multi/Bot"] )'
            ),
            array(
                'HaydenPierce\SandboxApp',
                array(
                    'HaydenPierce\SandboxApp\Foy'
                ),
                'ClassFinder should be able to find 3rd party classes'
            ),
            array(
                'HaydenPierce\SandboxApp\Foo\Bar',
                array(
                    'HaydenPierce\SandboxApp\Foo\Bar\Barc',
                    'HaydenPierce\SandboxApp\Foo\Bar\Barp'
                ),
                'ClassFinder should be able to find 3rd party classes multiple namespaces deep.'
            ),
            array(
                'HaydenPierce\SandboxAppMulti',
                array(
                    'HaydenPierce\SandboxAppMulti\Zip',
                    'HaydenPierce\SandboxAppMulti\Zop',
                    'HaydenPierce\SandboxAppMulti\Zap',
                    'HaydenPierce\SandboxAppMulti\Zit'
                ),
                'ClassFinder should be able to find 3rd party classes when a provided namespace root maps to multiple directories (Example: "HaydenPierce\\SandboxAppMulti\\": ["multi/Bop", "multi/Bot"] )'
            ),
            array(
                'TestApp1\Foo\Empty',
                array(),
                'ClassFinder should return an empty array if the namesapce is known, but contains no classes.'
            )
        );
    }

    /**
     * @expectedException HaydenPierce\ClassFinder\Exception\ClassFinderException
     * @expectedExceptionMessageRegExp  /Unknown namespace 'TestApp1\\DoesNotExist'\./
     */
    public function testThrowsOnUnknownSubNameSpace()
    {
        ClassFinder::getClassesInNamespace('TestApp1\DoesNotExist');
    }

    /**
     * @expectedException HaydenPierce\ClassFinder\Exception\ClassFinderException
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