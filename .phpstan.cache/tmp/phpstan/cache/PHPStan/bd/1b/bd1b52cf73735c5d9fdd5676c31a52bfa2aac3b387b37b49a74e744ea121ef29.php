<?php declare(strict_types = 1);

// phpinternal-PHPStan\BetterReflection\Reflection\ReflectionClass-domnamednodemap
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v1-6.70.0.3-dev-master@709e512-8.4.24',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\InternalLocatedSource',
      'data' => 
      array (
        'name' => 'DOMNamedNodeMap',
        'filename' => 'phar:///var/www/projects/exprmt/vendor/phpstan/phpstan/phpstan.phar/vendor/ondrejmirtes/better-reflection/src/SourceLocator/SourceStubber/../../../../../jetbrains/phpstorm-stubs/dom/dom_c.stub',
        'extensionName' => 'dom',
        'aliasName' => NULL,
      ),
    ),
    'namespace' => NULL,
    'name' => 'DOMNamedNodeMap',
    'shortName' => 'DOMNamedNodeMap',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * The DOMNamedNodeMap class
 * @link https://php.net/manual/en/class.domnamednodemap.php
 * @property-read int $length The number of nodes in the map. The range of valid child node indices is 0 to length - 1 inclusive.
 *
 * @template-covariant TNode of DOMNode
 * @implements IteratorAggregate<string, TNode>
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 12,
    'endLine' => 95,
    'startColumn' => 5,
    'endColumn' => 5,
    'parentClassName' => NULL,
    'implementsClassNames' => 
    array (
      0 => 'IteratorAggregate',
      1 => 'Countable',
    ),
    'traitClassNames' => 
    array (
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
      'length' => 
      array (
        'declaringClassName' => 'DOMNamedNodeMap',
        'implementingClassName' => 'DOMNamedNodeMap',
        'name' => 'length',
        'modifiers' => 1,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'int',
            'isIdentifier' => true,
          ),
        ),
        'default' => NULL,
        'docComment' => '/**
 * @var int The number of nodes in the map. The valid child node indices range from 0 to length - 1 inclusive.
 * @readonly
 * @link https://php.net/manual/en/class.domnamednodemap.php#domnamednodemap.props.length
 */',
        'attributes' => 
        array (
          0 => 
          array (
            'name' => 'JetBrains\\PhpStorm\\Internal\\PhpStormStubsElementAvailable',
            'isRepeated' => false,
            'arguments' => 
            array (
              'from' => 
              array (
                'code' => '\'8.1\'',
                'attributes' => 
                array (
                  'startLine' => 19,
                  'endLine' => 19,
                  'startTokenPos' => 29,
                  'startFilePos' => 781,
                  'endTokenPos' => 29,
                  'endFilePos' => 785,
                ),
              ),
            ),
          ),
        ),
        'startLine' => 19,
        'endLine' => 20,
        'startColumn' => 9,
        'endColumn' => 27,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
    ),
    'immediateMethods' => 
    array (
      'getNamedItem' => 
      array (
        'name' => 'getNamedItem',
        'parameters' => 
        array (
          'qualifiedName' => 
          array (
            'name' => 'qualifiedName',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
              0 => 
              array (
                'name' => 'JetBrains\\PhpStorm\\Internal\\LanguageLevelTypeAware',
                'isRepeated' => false,
                'arguments' => 
                array (
                  0 => 
                  array (
                    'code' => '[\'8.0\' => \'string\']',
                    'attributes' => 
                    array (
                      'startLine' => 33,
                      'endLine' => 33,
                      'startTokenPos' => 56,
                      'startFilePos' => 1398,
                      'endTokenPos' => 62,
                      'endFilePos' => 1416,
                    ),
                  ),
                  'default' => 
                  array (
                    'code' => '\'\'',
                    'attributes' => 
                    array (
                      'startLine' => 33,
                      'endLine' => 33,
                      'startTokenPos' => 68,
                      'startFilePos' => 1428,
                      'endTokenPos' => 68,
                      'endFilePos' => 1429,
                    ),
                  ),
                ),
              ),
            ),
            'startLine' => 33,
            'endLine' => 34,
            'startColumn' => 13,
            'endColumn' => 33,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
          'data' => 
          array (
            'types' => 
            array (
              0 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'DOMNode',
                  'isIdentifier' => false,
                ),
              ),
              1 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'null',
                  'isIdentifier' => true,
                ),
              ),
            ),
          ),
        ),
        'attributes' => 
        array (
          0 => 
          array (
            'name' => 'JetBrains\\PhpStorm\\Internal\\TentativeType',
            'isRepeated' => false,
            'arguments' => 
            array (
            ),
          ),
        ),
        'docComment' => '/**
 * Retrieves a node specified by name
 * @link https://php.net/manual/en/domnamednodemap.getnameditem.php
 * @param string $qualifiedName <p>
 * The nodeName of the node to retrieve.
 * </p>
 * @return TNode|null A node (of any type) with the specified nodeName, or
 * null if no node is found.
 * @betterReflectionTentativeReturnType
 */',
        'startLine' => 31,
        'endLine' => 37,
        'startColumn' => 9,
        'endColumn' => 9,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'DOMNamedNodeMap',
        'implementingClassName' => 'DOMNamedNodeMap',
        'currentClassName' => 'DOMNamedNodeMap',
        'aliasName' => NULL,
      ),
      'item' => 
      array (
        'name' => 'item',
        'parameters' => 
        array (
          'index' => 
          array (
            'name' => 'index',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'int',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
              0 => 
              array (
                'name' => 'JetBrains\\PhpStorm\\Internal\\PhpStormStubsElementAvailable',
                'isRepeated' => false,
                'arguments' => 
                array (
                  'from' => 
                  array (
                    'code' => '\'7.1\'',
                    'attributes' => 
                    array (
                      'startLine' => 51,
                      'endLine' => 51,
                      'startTokenPos' => 105,
                      'startFilePos' => 2135,
                      'endTokenPos' => 105,
                      'endFilePos' => 2139,
                    ),
                  ),
                ),
              ),
              1 => 
              array (
                'name' => 'JetBrains\\PhpStorm\\Internal\\LanguageLevelTypeAware',
                'isRepeated' => false,
                'arguments' => 
                array (
                  0 => 
                  array (
                    'code' => '[\'8.0\' => \'int\']',
                    'attributes' => 
                    array (
                      'startLine' => 52,
                      'endLine' => 52,
                      'startTokenPos' => 112,
                      'startFilePos' => 2209,
                      'endTokenPos' => 118,
                      'endFilePos' => 2224,
                    ),
                  ),
                  'default' => 
                  array (
                    'code' => '\'\'',
                    'attributes' => 
                    array (
                      'startLine' => 52,
                      'endLine' => 52,
                      'startTokenPos' => 124,
                      'startFilePos' => 2236,
                      'endTokenPos' => 124,
                      'endFilePos' => 2237,
                    ),
                  ),
                ),
              ),
            ),
            'startLine' => 51,
            'endLine' => 53,
            'startColumn' => 13,
            'endColumn' => 22,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
          'data' => 
          array (
            'types' => 
            array (
              0 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'DOMNode',
                  'isIdentifier' => false,
                ),
              ),
              1 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'null',
                  'isIdentifier' => true,
                ),
              ),
            ),
          ),
        ),
        'attributes' => 
        array (
          0 => 
          array (
            'name' => 'JetBrains\\PhpStorm\\Internal\\TentativeType',
            'isRepeated' => false,
            'arguments' => 
            array (
            ),
          ),
        ),
        'docComment' => '/**
 * Retrieves a node specified by index
 * @link https://php.net/manual/en/domnamednodemap.item.php
 * @param int $index <p>
 * Index into this map.
 * </p>
 * @return DOMNode|null The node at the indexth position in the map, or null
 * if that is not a valid index (greater than or equal to the number of nodes
 * in this map).
 * @betterReflectionTentativeReturnType
 */',
        'startLine' => 49,
        'endLine' => 56,
        'startColumn' => 9,
        'endColumn' => 9,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'DOMNamedNodeMap',
        'implementingClassName' => 'DOMNamedNodeMap',
        'currentClassName' => 'DOMNamedNodeMap',
        'aliasName' => NULL,
      ),
      'getNamedItemNS' => 
      array (
        'name' => 'getNamedItemNS',
        'parameters' => 
        array (
          'namespace' => 
          array (
            'name' => 'namespace',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
              'data' => 
              array (
                'types' => 
                array (
                  0 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'string',
                      'isIdentifier' => true,
                    ),
                  ),
                  1 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'null',
                      'isIdentifier' => true,
                    ),
                  ),
                ),
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
              0 => 
              array (
                'name' => 'JetBrains\\PhpStorm\\Internal\\PhpStormStubsElementAvailable',
                'isRepeated' => false,
                'arguments' => 
                array (
                  'from' => 
                  array (
                    'code' => '\'8.0\'',
                    'attributes' => 
                    array (
                      'startLine' => 72,
                      'endLine' => 72,
                      'startTokenPos' => 161,
                      'startFilePos' => 3055,
                      'endTokenPos' => 161,
                      'endFilePos' => 3059,
                    ),
                  ),
                ),
              ),
            ),
            'startLine' => 72,
            'endLine' => 73,
            'startColumn' => 13,
            'endColumn' => 30,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'localName' => 
          array (
            'name' => 'localName',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
              0 => 
              array (
                'name' => 'JetBrains\\PhpStorm\\Internal\\PhpStormStubsElementAvailable',
                'isRepeated' => false,
                'arguments' => 
                array (
                  'from' => 
                  array (
                    'code' => '\'8.0\'',
                    'attributes' => 
                    array (
                      'startLine' => 74,
                      'endLine' => 74,
                      'startTokenPos' => 177,
                      'startFilePos' => 3174,
                      'endTokenPos' => 177,
                      'endFilePos' => 3178,
                    ),
                  ),
                ),
              ),
            ),
            'startLine' => 74,
            'endLine' => 75,
            'startColumn' => 13,
            'endColumn' => 29,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
          'data' => 
          array (
            'types' => 
            array (
              0 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'DOMNode',
                  'isIdentifier' => false,
                ),
              ),
              1 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'null',
                  'isIdentifier' => true,
                ),
              ),
            ),
          ),
        ),
        'attributes' => 
        array (
          0 => 
          array (
            'name' => 'JetBrains\\PhpStorm\\Internal\\TentativeType',
            'isRepeated' => false,
            'arguments' => 
            array (
            ),
          ),
        ),
        'docComment' => '/**
 * Retrieves a node specified by local name and namespace URI
 * @link https://php.net/manual/en/domnamednodemap.getnameditemns.php
 * @param string $namespace <p>
 * The namespace URI of the node to retrieve.
 * </p>
 * @param string $localName <p>
 * The local name of the node to retrieve.
 * </p>
 * @return TNode|null A node (of any type) with the specified local name and namespace URI, or
 * null if no node is found.
 * @betterReflectionTentativeReturnType
 */',
        'startLine' => 70,
        'endLine' => 78,
        'startColumn' => 9,
        'endColumn' => 9,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'DOMNamedNodeMap',
        'implementingClassName' => 'DOMNamedNodeMap',
        'currentClassName' => 'DOMNamedNodeMap',
        'aliasName' => NULL,
      ),
      'count' => 
      array (
        'name' => 'count',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'int',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
          0 => 
          array (
            'name' => 'JetBrains\\PhpStorm\\Internal\\TentativeType',
            'isRepeated' => false,
            'arguments' => 
            array (
            ),
          ),
        ),
        'docComment' => '/**
 * @return int<0,max>
 * @since 7.2
 * @betterReflectionTentativeReturnType
 */',
        'startLine' => 84,
        'endLine' => 87,
        'startColumn' => 9,
        'endColumn' => 9,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'DOMNamedNodeMap',
        'implementingClassName' => 'DOMNamedNodeMap',
        'currentClassName' => 'DOMNamedNodeMap',
        'aliasName' => NULL,
      ),
      'getIterator' => 
      array (
        'name' => 'getIterator',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Iterator',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @return Iterator<string, TNode>
 * @since 8.0
 */',
        'startLine' => 92,
        'endLine' => 94,
        'startColumn' => 9,
        'endColumn' => 9,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'DOMNamedNodeMap',
        'implementingClassName' => 'DOMNamedNodeMap',
        'currentClassName' => 'DOMNamedNodeMap',
        'aliasName' => NULL,
      ),
    ),
    'traitsData' => 
    array (
      'aliases' => 
      array (
      ),
      'modifiers' => 
      array (
      ),
      'precedences' => 
      array (
      ),
      'hashes' => 
      array (
      ),
    ),
  ),
));