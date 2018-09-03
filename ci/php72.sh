composer install --working-dir=/builds/hpierce1102/ClassFinder/test/app1 --quiet
composer install --working-dir=/builds/hpierce1102/ClassFinder --quiet
php --version
php /builds/hpierce1102/ClassFinder/vendor/bin/phpunit /builds/hpierce1102/ClassFinder/test/app1/src/ClassFinderTest.php