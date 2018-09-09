<?php

namespace HaydenPierce\ClassFinder\UnitTest;

use HaydenPierce\ClassFinder\PSR4\PSR4Namespace;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

class PSR4NamespaceTest extends \PHPUnit_Framework_TestCase
{
    /** @var vfsStreamDirectory */
    private $root;

    public function setUp()
    {
        $structure = $this->getTestStructure();
        $this->root = vfsStream::setup('root', null, $structure);
    }

    public function getTestStructure()
    {
        return array(
            'Baz' => array(
                'Foo' => array(
                    'Fooa.php' => $this->getClassFileContents('PSR4\\Foo', 'Fooa'),
                    'Foob.php' => $this->getClassFileContents('PSR4\\Foo', 'Foob')
                ),
                'Bar.php' => $this->getClassFileContents('PSR4', 'Bar'),
                'Barb.php' => $this->getClassFileContents('PSR4', 'Barb')
            )
        );
    }

    public function getClassFileContents($namespace, $className)
    {
        $template = <<<EOL
<?php 

namespace %s

class %s
{
}
EOL;

        return sprintf($template, $namespace, $className);
    }

    public function testCountMatchingNamespaceSegments()
    {
        $namespace = new PSR4Namespace('MyPSR4Root\\Foot\\', $this->root->getChild('Baz')->path());

        $this->assertEquals($namespace->countMatchingNamespaceSegments('MyPSR4Root\\Foot'), 2);
        $this->assertEquals($namespace->countMatchingNamespaceSegments('MyPSR4Root'), 1);
        $this->assertEquals($namespace->countMatchingNamespaceSegments('MyPSR4Root\\Foot\\Baz\\Foo'), 3);
        $this->assertEquals($namespace->countMatchingNamespaceSegments('MyPSR4Root\\Foot\\Baz'), 3);
        $this->assertEquals($namespace->countMatchingNamespaceSegments('Cactus'), 0);
        $this->assertEquals($namespace->countMatchingNamespaceSegments('Cactus\\Foot'), 0);
    }
}
