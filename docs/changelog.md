Version 0.1.0
-------------

* Vastly improved PSR4 support
    * Loading classes from Composer packages is now supported.
    * Namespaces that map to multiple directories is now supported.
    * Fixed a bug where ClassFinder would use a more generic (and therefore _wrong_) namespace over a better one. 
    (Selecting `Acme\\`, when `Acme\\Foo` is a better choice)
* Manually overriding the AppRoot is now done with a static method instead of a static property

Mapping a namespace to multiple directories:
```
    ...
    "autoload": {
        "psr-4": {
            "Acme\\Foo\\": [ "src/", "srcButDifferent/" ]
        }
    }
    ...
```

Old overriding app root: 
```
ClassFinder::appRoot('/home/hpierce/whatevs'); 
```

New overriding app root:
```
ClassFinder::setAppRoot('/home/hpierce/whatevs'); 
```