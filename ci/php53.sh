composer install --working-dir=$1/test/app1 --quiet || exit 1
composer install --working-dir=$1 --quiet || exit 1
php --version
php /builds/hpierce1102/ClassFinder/vendor/bin/phpunit --testsuite all