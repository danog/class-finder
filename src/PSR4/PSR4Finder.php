<?php
namespace HaydenPierce\ClassFinder\PSR4;

use HaydenPierce\ClassFinder\Exception\ClassFinderException;
use HaydenPierce\ClassFinder\FinderInterface;

class PSR4Finder implements FinderInterface
{
    private $factory;

    public function __construct(PSR4NamespaceFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param $namespace
     * @return array
     * @throws ClassFinderException
     */
    public function findClasses($namespace)
    {
        $bestNamespace = $this->findBestPSR4Namespace($namespace);

        if ($bestNamespace instanceof PSR4Namespace) {
            return $bestNamespace->findClasses($namespace);
        } else {
            return array();
        }
    }

    public function isNamespaceKnown($namespace)
    {
        $composerNamespaces = $this->factory->getPSR4Namespaces();

        foreach($composerNamespaces as $psr4Namespace) {
            if ($psr4Namespace->knowsNamespace($namespace)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $namespace
     * @return PSR4Namespace
     */
    private function findBestPSR4Namespace($namespace)
    {
        $composerNamespaces = $this->factory->getPSR4Namespaces();

        $acceptableNamespaces = array_filter($composerNamespaces, function(PSR4Namespace $potentialNamespace) use ($namespace){
            return $potentialNamespace->isAcceptableNamespace($namespace);
        });

        $carry = new \stdClass();
        $carry->highestMatchingSegments = 0;
        $carry->bestNamespace = null;

        /** @var PSR4Namespace $bestNamespace */
        $bestNamespace = array_reduce($acceptableNamespaces, function ($carry, PSR4Namespace $potentialNamespace) use ($namespace) {
            $matchingSegments = $potentialNamespace->countMatchingNamespaceSegments($namespace);

            if ($matchingSegments > $carry->highestMatchingSegments) {
                $carry->highestMatchingSegments = $matchingSegments;
                $carry->bestNamespace = $potentialNamespace;
            }

            return $carry;
        }, $carry);

        return $bestNamespace->bestNamespace;
    }
}
