Unknown Child Namespace
-----------------------

Example composer.json:
```
{
    "name": "haydenpierce/sample-app",
    "type": "application",
    "license": "MIT",
    "authors": [
        {
            "name": "Hayden Pierce",
            "email": "hayden@haydenpierce.com"
        }
    ],
    "autoload": {
        "psr-4": {
            "Acme\\": "src/"
        }
    }
}
```

Example PHP:
```
<?php

require_once __DIR__ . '/../vendor/autoload.php';

use HaydenPierce\ClassFinder\ClassFinder;

$classes = ClassFinder::getClassesInNamespace('Acme\Foo\Bar');
```

Results in this exception:

> Unknown namespace 'TestApp1\Foo\Bar'. Checked for files in *C:\Users\HPierce\PhpstormProjects\ClassFinder\test\app1\src\Foo\Bar*, but that directory did not exist

ClassFinder attempts to figure out which directory it should look for classes in by piecing together a path from 
elements in your `autoload.psr-4` configuration. In this instance, we've asked for classes in the `Acme\Foo\Bar` namespace,
so ClassFinder will attempt to build a directory path there. ClassFinder successfully mapped *Acme* to `src/`, but upon adding
the rest of the namespace to the path, failed to find a directory.

Things to check for:

* Does the directory actually exist? 
* Does PHP have permissions to the directory?
* Is your app PSR-4 compliant?
* Is the namespace correct?

If this information doesn't resolve the issue, please feel free to submit an issue.