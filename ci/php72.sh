echo "composer install --working-dir=$1/test/app1 --quiet"

composer install --working-dir=$1/test/app1 --quiet
composer install --working-dir=$1 --quiet
php --version
php /builds/hpierce1102/ClassFinder/vendor/bin/phpunit --testsuite all