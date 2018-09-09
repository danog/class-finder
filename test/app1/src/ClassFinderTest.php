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
     * @expectedException HaydenPierce\ClassFinder\Exception\ClassFinderException
     * @expectedExceptionMessageRegExp  /Unknown namespace 'DoesNotExist'\./
     */
    public function testThrowsOnUnknownNameSpace()
    {
        ClassFinder::getClassesInNamespace('DoesNotExist');
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