<?php

namespace HaydenPierce\ClassFinder\UnitTest\Files;
use HaydenPierce\ClassFinder\Files\FilesEntry;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

class FilesTest extends \PHPUnit_Framework_TestCase
{
    private $file;

    public function setUp()
    {
        /*
         * A temporary file is used here due to the nature of the FilesEntry. Because FilesEntry must include a PHP file
         * in a shell command, we would have to somehow include the vfs wrapper in that shell call. Since that would
         * require a change to the class under test, it's probably just easier to use a temporary file.
         */
        $this->file = tmpfile();
        fwrite($this->file, <<<EOL
<?php

namespace Foo\Bar;

class Foo {}
class Bar {}

namespace Baz;

class Boo {}

EOL
        );
    }

    public function testGetClasses()
    {
        $metaData = stream_get_meta_data($this->file);
        $tmpFilename = $metaData['uri'];

        $files = new FilesEntry($tmpFilename, $this->findPHP());

        $classes = $files->getClasses('Foo\Bar');

        $this->assertEquals(array(
            'Foo\Bar\Foo',
            'Foo\Bar\Bar'
        ), $classes, 'FilesEntry should be able to determine the classes defined in a given file.');
    }

    private function findPHP()
    {
        if (defined("PHP_BINARY")) {
            // PHP_BINARY was made available in PHP 5.4
            $php = PHP_BINARY;
        } else {
            $isHostWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
            if ($isHostWindows) {
                exec('where php', $output);
                $php = $output[0];
            } else {
                exec('which php', $output);
                $php = $output[0];
            }
        }

        return $php;
    }
}
