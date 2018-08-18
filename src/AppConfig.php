<?php

namespace HaydenPierce\ClassFinder;

/**
 * Class AppConfig
 * @package HaydenPierce\ClassFinder
 * @internal
 */
class AppConfig
{
    public static $appRoot;

    /**
     * @throws \Exception
     */
    public static function findAppRoot()
    {
        if (self::$appRoot) {
            $appRoot = self::$appRoot;
        } else {
            $workingDirectory = str_replace('\\', '/', __DIR__);
            $workingDirectory = str_replace('/vendor/haydenpierce/class-finder/src', '', $workingDirectory);
            $directoryPathPieces = explode('/', $workingDirectory);

            $appRoot = '/';
            do {
                $path = implode('/', $directoryPathPieces) . '/composer.json';
                if (file_exists($path)) {
                    $appRoot = implode('/', $directoryPathPieces) . '/';
                } else {
                    array_pop($directoryPathPieces);
                }
            } while (is_null($appRoot) && count($directoryPathPieces) > 0);
        }

        if (!file_exists($appRoot . '/composer.json')) {
            throw new ClassFinderException(sprintf("Could not locate composer.json. You can get around this by setting ClassFinder::\$appRoot manually. See '%s' for details.",
                'https://gitlab.com/hpierce1102/ClassFinder/blob/master/docs/exceptions/missingComposerConfig.md'
            ));
        } else {
            self::$appRoot = $appRoot;
            return self::$appRoot;
        }
    }
}