<?php

namespace TestApp1;

require_once __DIR__ . '/../vendor/autoload.php';

use HaydenPierce\ClassFinder\ClassFinder;

class PSR4Test extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        // Reset ClassFinder back to normal.
        ClassFinder::setAppRoot(null);
    }

    /**
     * @dataProvider getClassesInNamespaceDataProvider
     */
    public function testGetClassesInNamespace($namespace, $expected, $message)
    {
        try {
            $classes = ClassFinder::getClassesInNamespace($namespace);
        } catch (\Exception $e) {
            $this->assertFalse(true, 'An exception occurred: ' . $e->getMessage());
            $classes = array();
        }

        $this->assertEquals($expected, $classes, $message);
    }

    public function getClassesInNamespaceDataProvider()
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
                'ClassFinder should return an empty array if the namespace is known, but contains no classes.'
            )
        );
    }

    /**
     * @dataProvider getClassesInNamespaceRecursivelyDataProvider
     */
    public function testGetClassesInNamespaceRecursively($namespace, $expected, $message)
    {
        try {
            $classes = ClassFinder::getClassesInNamespace($namespace);
        } catch (\Exception $e) {
            $this->assertFalse(true, 'An exception occurred: ' . $e->getMessage());
            $classes = array();
        }

        $this->assertEquals($expected, $classes, $message);
    }

    public function getClassesInNamespaceRecursivelyDataProvider()
    {
        return array(
            array(
                'TestApp1\Foo',
                array(
                    'TestApp1\Foo\Bar',
                    'TestApp1\Foo\Baz',
                    'TestApp1\Foo\Foo',
                    'TestApp1\Foo\Loo\Lar',
                    'TestApp1\Foo\Loo\Laz',
                    'TestApp1\Foo\Loo\Loo'
                ),
                'ClassFinder should be able to find 1st party classes recursively, multiple namespaces deep.'
            ),
            array(
                array(
                    'TestApp1\Foo\Loo\Lar',
                    'TestApp1\Foo\Loo\Laz',
                    'TestApp1\Foo\Loo\Loo'
                ),
                'ClassFinder should not turn up other classes when running in recursive mode.'
            ),
            array(
                'TestApp1\Multi',
                array(
                    'TestApp1\Multi\Uij',
                    'TestApp1\Multi\Yij',
                    'TestApp1\Multi\Uik',
                    'TestApp1\Multi\Yik',
                    'TestApp1\Multi\Yop\Rik',
                    'TestApp1\Multi\Yop\Tik',
                    'TestApp1\Multi\Yop\Eij',
                    'TestApp1\Multi\Yop\Rij'
                ),
                'ClassFinder should be able to find 1st party classes recursively when a provided namespace root maps to multiple directories (Example: "HaydenPierce\\SandboxAppMulti\\": ["multi/Bop", "multi/Bot"] )'
            ),
            array(
                'HaydenPierce',
                array(
                    'HaydenPierce\SandboxApp\Foy',
                    'HaydenPierce\SandboxApp\Foo\Bar\Barc',
                    'HaydenPierce\SandboxApp\Foo\Bar\Barp',
                    'HaydenPierce\SandboxAppMulti\Zip',
                    'HaydenPierce\SandboxAppMulti\Zop',
                    'HaydenPierce\SandboxAppMulti\Zap',
                    'HaydenPierce\SandboxAppMulti\Zit'
                ),
                'ClassFinder should be able to find 3rd party classes'
            ),
            'TestApp1\Foo\Loo'
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
}