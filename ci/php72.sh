composer install --working-dir=$1/test/app1 --quiet || exit 1
composer install --working-dir=$1/test/app2 --quiet || exit 1
composer install --working-dir=$1 --quiet || exit 1
php --version
php $1/vendor/bin/phpunit --testsuite all
php $1/vendor/bin/phpunit --testsuite noAutoload