ClassFinder
===========

A dead simple utility to identify classes in a given namespace.

This package is an improved implementation of an [answer on Stack Overflow](https://stackoverflow.com/a/40229665/3000068)
and provides additional features with less configuration required.

Requirements
------------

* Application is using Composer.
* Classes can be autoloaded with Composer.
* PHP >= 5.3.0

Installing
----------

Installing is done by requiring it with Composer.

```
$ composer require haydenpierce/class-finder
```

No other installation methods are currently supported.

Supported Autoloading Methods
--------------------------------

| Method     | Supported | with `ClassFinder::RECURSIVE_MODE` |
| ---------- | --------- | ---------------------------------- |
| PSR-4      | ✔️     | ✔️                               |
| PSR-0      | ❌️*    | ❌️*                              |
| Classmap   | ✔️     | ✔️                               |
| Files      | ✔️^    | ❌️**                             |

\^ Experimental.

\* Planned.

\** Not planned. Open an issue if you need this feature.

Examples
--------

**Standard Mode**

```
<?php

require_once __DIR__ . '/vendor/autoload.php';

$classes = ClassFinder::getClassesInNamespace('TestApp1\Foo');

/**
 * array(
 *   'TestApp1\Foo\Bar',
 *   'TestApp1\Foo\Baz',
 *   'TestApp1\Foo\Foo'
 * )
 */
var_dump($classes);
```

**Recursive Mode** *(in v0.3-beta)*

```
<?php

require_once __DIR__ . '/vendor/autoload.php';

$classes = ClassFinder::getClassesInNamespace('TestApp1\Foo', ClassFinder::RECURSIVE_MODE);

/**
 * array(
 *   'TestApp1\Foo\Bar',
 *   'TestApp1\Foo\Baz',
 *   'TestApp1\Foo\Foo',
 *   'TestApp1\Foo\Box\Bar',
 *   'TestApp1\Foo\Box\Baz',
 *   'TestApp1\Foo\Box\Foo',
 *   'TestApp1\Foo\Box\Lon\Bar',
 *   'TestApp1\Foo\Box\Lon\Baz',
 *   'TestApp1\Foo\Box\Lon\Foo',
 * )
 */
var_dump($classes);
```
 
Documentation
-------------

[Changelog](docs/changelog.md)

**Exceptions**:

* [Files could not locate PHP](docs/exceptions/filesCouldNotLocatePHP.md)
* [Files exec not available](docs/exceptions/filesExecNotAvailable.md)
* [Missing composer.json](docs/exceptions/missingComposerConfig.md)
* [Unknown namespace](docs/exceptions/unknownNamespace.md)

**Internals**

* [How Testing Works](docs/testing.md)
* [Continuous Integration Notes](docs/ci.md)

Roadmap
-------

> **WARNING**: Before 1.0.0, expect that bug fixes _will not_ be backported to older versions. Backwards incompatible changes
may be introduced in minor 0.X.Y versions, where X changes.

0.0.1 - First party `psr4` classes

0.1.0 - Third party `psr4` classes

0.2.0 - `classmap` support.

0.3.0 - `files` support

0.4.0 - `psr0` support

0.5.0 - Additional features: 

Various ideas:

* ~~`ClassFinder::getClassesInNamespace('TestApp1\Foo', ClassFinder::RECURSIVE_MODE)`. 
Providing classes multiple namespaces deep.~~ (included v0.3-beta)

* `ClassFinder::getClassesImplementingInterface('TestApp1\Foo', 'TestApp1\FooInterface', ClassFinder::RECURSIVE_MODE)`.
Filtering classes to only classes that implement a namespace.

* `ClassFinder::debugRenderReport('TestApp1\Foo\Baz')` 
Guidance for solving "class not found" errors resulting from typos in namespaces, missing directories, etc. Would print
an HTML report. Not intended for production use, but debugging.

1.0.0 - Better compliance with semantic versioning.
