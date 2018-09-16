<?php

namespace HaydenPierce\ClassFinder\UnitTest\Classmap;

use HaydenPierce\ClassFinder\Classmap\ClassmapEntry;

class ClassmapEntryTest extends \PHPUnit_Framework_TestCase
{
    public function testKnowsNamespace()
    {
        $entry = new ClassmapEntry("MyClassmap\Foo\Bar");

        $this->assertTrue($entry->knowsNamespace("MyClassmap"));
        $this->assertTrue($entry->knowsNamespace("MyClassmap\Foo"));
        $this->assertTrue($entry->knowsNamespace("MyClassmap\Foo\Bar"));

        $this->assertFalse($entry->knowsNamespace("MyClassmap\Bar"));
        $this->assertFalse($entry->knowsNamespace("MyClassmap\Foo\Bar\Baz"));
    }

    public function testMatches()
    {
        $entry = new ClassmapEntry("MyClassmap\Foo\Bar");

        $this->assertTrue($entry->matches("MyClassmap\Foo"));

        $this->assertFalse($entry->matches("MyClassmap"), "Providing only a single segment of a namespace should not be a match.");
        $this->assertFalse($entry->matches("MyClassmap\Foo\Bar"), "Providing the fully qualified classname doesn't match because only the class's namespace should match.");
        $this->assertFalse($entry->matches("MyClassmap\Bar"));
        $this->assertFalse($entry->matches("MyClassmap\Foo\Bar\Baz"));
    }
}