<?php

/**
 * Glossary:
 * test classes - Classes that ClassFinder will attempt to find.
 * test app - a directory structure containing a composer.json. This directory structure is intended to simulate an application
 * that can autoload classes with psr-4.
 *
 * Due to the nature of the this component, the ClassFinder class must have access to the classes in the test app.
 * For this reason, we copy in ClassFinder and ensure that it can be autoloaded with the test classes.
 */

// Find test apps

function findTestApps($rootDir) {
    $testAppPaths = scandir(__DIR__);

    $testAppPaths = array_filter($testAppPaths, function($path) {
        return substr($path, 0, 3) === 'app';
    });

    $testAppPaths = array_map(function($path) {
        return realpath(__DIR__  . '/' . $path);
    }, $testAppPaths);

    return $testAppPaths;
}

function buildDirectories($path) {
    $pathPieces = explode('/', $path);

    // Remove the file, this isn't a directory
    array_pop($pathPieces);

    // On Windows, the first element could be C:/, representing the first directory. Remove that.
    // I suspect someone's going to have an issue with this and report a bug.
    // ✨ oh well ✨
    $isFirstElementWindowsDrive = strpos($pathPieces[0], ':') !== false;
    if ($isFirstElementWindowsDrive) {
        array_shift($pathPieces);
    }

    $requiredDirectory = '/';
    foreach($pathPieces as $piece) {
        $requiredDirectory = $requiredDirectory . $piece;

        if(!is_dir($requiredDirectory)) {
            mkdir($requiredDirectory);
        }

        $requiredDirectory = $requiredDirectory . '/';
    }
}

function copyInCurrentClasses($testApp) {
    $classFinderSource = realpath(__DIR__ . '/../src/ClassFinder.php');
    $classFinderPath = $testApp . '/vendor/hpierce1102/ClassFinder.php';
    $classFinderPath = str_replace('\\', '/', $classFinderPath);

    buildDirectories($classFinderPath);

    if (file_exists($classFinderPath)) {
        unlink($classFinderPath);
    }

    copy($classFinderSource, $classFinderPath);
}

$testApps = findTestApps(__DIR__);

foreach($testApps as $testApp) {

    $autoloaderPath = $testApp . '/vendor/autoload.php';

    $autoloaderExists = false;
    if (!file_exists($autoloaderPath)) {
        echo "No autoloader detected.\n";
        echo "Running composer install for $testApp...\n";

        // TODO: Programically run composer install?
        // https://stackoverflow.com/a/45831624/3000068
        // I ran into a problem getting composer to run via shell_exec() - Stack Overflow suggests it could be xdebug related.
        // I couldn't figure out how to set an environment variable before running a command on Windows.
        // This should be capable of Windows and Unix systems.
        echo "FAILED.\n";

        echo "************\n";
        echo "* SOLUTION *\n";
        echo "************\n";
        echo "You will need to run the following command to get this up and running:\n";
        echo "composer install --working-dir=\"$testApp\"\n";
        exit;
    }

    copyInCurrentClasses($testApp);
}
