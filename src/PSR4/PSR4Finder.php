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
     * @return bool|string
     * @throws ClassFinderException
     */
    public function findClasses($namespace)
    {
        $bestNamespace = $this->findBestPSR4Namespace($namespace);

        if ($bestNamespace instanceof PSR4Namespace) {
            return $bestNamespace->findClasses($namespace);
        } else {
            throw new ClassFinderException(sprintf("Unknown namespace '%s'. You should add the namespace prefix to composer.json. See '%s' for details.",
                $namespace,
                'https://gitlab.com/hpierce1102/ClassFinder/blob/master/docs/exceptions/unregisteredRoot.md'
            ));
        }
    }

    /**
     * @param $namespace
     * @return PSR4Namespace
     */
    private function findBestPSR4Namespace($namespace)
    {
        $composerNamespaces = $this->factory->getPSR4Namespaces();

        $carry = new \stdClass();
        $carry->highestMatchingSegments = 0;
        $carry->bestNamespace = null;

        /** @var PSR4Namespace $bestNamespace */
        $bestNamespace = array_reduce($composerNamespaces, function ($carry, PSR4Namespace $potentialNamespace) use ($namespace) {
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
