<?php
namespace HaydenPierce\ClassFinder;

class PSR4Finder implements FinderInterface
{
    private $config;

    public function __construct(AppConfig $config)
    {
        $this->config = $config;
    }
    /**
     * @return array
     * @throws \Exception
     */
    public function getDefinedNamespaces()
    {
        $appRoot = $this->config->getAppRoot();

        $composerJsonPath = $appRoot. 'composer.json';
        $composerConfig = json_decode(file_get_contents($composerJsonPath));

        //Apparently PHP doesn't like hyphens, so we use variable variables instead.
        $psr4 = "psr-4";
        return (array) $composerConfig->autoload->$psr4;
    }

    /**
     * @param $namespace
     * @return bool|string
     * @throws \Exception
     */
    public function getNamespaceDirectory($namespace)
    {
        $appRoot = $this->config->getAppRoot();

        $composerNamespaces = $this->config->getDefinedNamespaces();

        $namespaceFragments = explode('\\', $namespace);
        $undefinedNamespaceFragments = [];

        while($namespaceFragments) {
            $possibleNamespace = implode('\\', $namespaceFragments) . '\\';

            if(array_key_exists($possibleNamespace, $composerNamespaces)){
                $resolvedDirectory = $appRoot . $composerNamespaces[$possibleNamespace] . implode('/', $undefinedNamespaceFragments);
                $realDirectory = realpath($resolvedDirectory);
                if ($realDirectory !== false) {
                    return $realDirectory;
                } else {
                    throw new ClassFinderException(sprintf("Unknown namespace '%s'. Checked for files in %s, but that directory did not exist. See %s for details.",
                        $namespace,
                        $resolvedDirectory,
                        'https://gitlab.com/hpierce1102/ClassFinder/blob/master/docs/exceptions/unknownSubNamespace.md'
                    ));
                }
            }

            array_unshift($undefinedNamespaceFragments, array_pop($namespaceFragments));
        }

        throw new ClassFinderException(sprintf("Unknown namespace '%s'. You should add the namespace prefix to composer.json. See '%s' for details.",
            $namespace,
            'https://gitlab.com/hpierce1102/ClassFinder/blob/master/docs/exceptions/unregisteredRoot.md'
        ));
    }
}
