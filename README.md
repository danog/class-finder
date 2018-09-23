ClassFinder
===========

A dead simple utility to identify classes in a given namespace.  This package is an improved implementation of an
 [answer on Stack Overflow](https://stackoverflow.com/a/40229665/3000068) that provides additional features with less
 configuration required.

Requirements
------------

 * Application is using Composer.
 * Classes can be autoloaded with PSR-4 or classmaps.
 * PHP >= 5.3.0

Installing
----------

Installing is done by requiring it with Composer.

```
$ composer require haydenpierce/class-finder
```

No other installation methods are currently supported.

Example
-------

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
 
Documentation
-------------

[Changelog](docs/changelog.md)

**Exceptions**:

* [Unknown namespace - (Unregistered)](docs/exceptions/unregisteredRoot.md)
* [Unknown namespace - (Registered)](docs/exceptions/unknownSubNamespace.md)
* [Missing composer.json](docs/exceptions/missingComposerConfig.md)

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

* `ClassFinder::getClassesInNamespace('TestApp1\Foo', ClassFinder::RECURSIVE_MODE)`. 
Providing classes multiple namespaces deep.

* `ClassFinder::getClassesImplementingInterface('TestApp1\Foo', 'TestApp1\FooInterface', ClassFinder::RECURSIVE_MODE)`. 
Filtering classes to only classes that implement a namespace.

* `ClassFinder::debugRenderReport('TestApp1\Foo\Baz')` 
Guidance for solving "class not found" errors resulting from typos in namespaces, missing directories, etc. Would print
an HTML report. Not intended for production use, but debugging.

1.0.0 - Better compliance with semantic versioning.