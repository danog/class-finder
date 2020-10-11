<?php

namespace TestApp2;

require_once __DIR__ . '/vendor/autoload.php';

use danog\ClassFinder\ClassFinder;

class PSR4NoAutoloadTest extends \PHPUnit_Framework_TestCase
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
                'danog\SandboxApp',
                array(
                    'danog\SandboxApp\Foy'
                ),
                'ClassFinder should be able to find 3rd party classes'
            ),
            array(
                'danog\SandboxApp\Foo\Bar',
                array(
                    'danog\SandboxApp\Foo\Bar\Barc',
                    'danog\SandboxApp\Foo\Bar\Barp'
                ),
                'ClassFinder should be able to find 3rd party classes multiple namespaces deep.'
            ),
            array(
                'danog\SandboxAppMulti',
                array(
                    'danog\SandboxAppMulti\Zip',
                    'danog\SandboxAppMulti\Zop',
                    'danog\SandboxAppMulti\Zap',
                    'danog\SandboxAppMulti\Zit'
                ),
                'ClassFinder should be able to find 3rd party classes when a provided namespace root maps to multiple directories (Example: "danog\\SandboxAppMulti\\": ["multi/Bop", "multi/Bot"] )'
            )
        );
    }

    public function testGetClassesInNamespaceRecursively()
    {
        $namespace = 'danog';
        $expected = array(
            'danog\SandboxApp\Foy',
            'danog\SandboxApp\Fob\Soz',
            'danog\SandboxApp\Foo\Larc',
            'danog\SandboxApp\Foo\Bar\Barc',
            'danog\SandboxApp\Foo\Bar\Barp',
            'danog\SandboxAppMulti\Zip',
            'danog\SandboxAppMulti\Zop',
            'danog\SandboxAppMulti\Zap',
            'danog\SandboxAppMulti\Zit'
        );
        $message = 'ClassFinder should be able to find 3rd party classes';

        ClassFinder::disableClassmapSupport();

        try {
            $classes = ClassFinder::getClassesInNamespace($namespace, ClassFinder::RECURSIVE_MODE);
        } catch (\Exception $e) {
            $this->assertFalse(true, 'An exception occurred: ' . $e->getMessage());
            $classes = array();
        }

        // ClassFinder has the ability to find itself. This ability, while intended, is incontinent for tests
        // because of the 'danog' test case. Whenever ClassFinder would be updated, we would need to update the
        // test. To prevent the flakiness, we just remove ClassFinder's classes.
        $classes = array_filter($classes, function($class) {
            return strpos($class, 'danog\ClassFinder') !== 0;
        });

        ClassFinder::enableClassmapSupport();

        $this->assertEquals($expected, $classes, $message);
    }
}