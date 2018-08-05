Unknown Namespace Root
----------------------

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
            "HaydenPierce\\": "src/"
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

> Unknown namespace 'DoesNotExist\Foo\Bar'. You should add the namespace prefix to composer.json.

ClassFinder attempts to figure out which directory it should look for classes in by piecing together a path from 
elements in your `autoload.psr-4` configuration. In this instance, we've asked for classes in the `Acme\Foo\Bar` namespace,
so ClassFinder will attempt to build a directory path there. Since none of `Acme`, `Acme\Foo`, or  `Acme\Foo\Bar` exist
in the `autoload.psr-4`, ClassFinder cannot determine a valid path.

This can be solved by adding `Acme` to the `autoload.psr-4` key like so:

```
{
    ...
    "autoload": {
        "psr-4": {
            "HaydenPierce\\": "src/",
            "Acme\\" : "src/acme"
        }
    }
    ...
}
```

This will allow ClassFinder to search `src/acme/Foo/Bar/*` for classes in the `Acme\Foo\Bar` namespace. You could also add
`Acme\Foo` to the `autoload.psr-4` config if that makes more sense.

If this information doesn't resolve the issue, please feel free to submit an issue.