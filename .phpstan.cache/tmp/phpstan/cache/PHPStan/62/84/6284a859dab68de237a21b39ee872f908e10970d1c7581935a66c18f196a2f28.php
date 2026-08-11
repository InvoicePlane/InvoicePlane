<?php declare(strict_types = 1);

// phpinternal-PHPStan\BetterReflection\Reflection\ReflectionClass-domnodelist
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v1-6.70.0.3-dev-master@709e512-8.4.24',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\InternalLocatedSource',
      'data' => 
      array (
        'name' => 'DOMNodeList',
        'filename' => 'phar:///var/www/projects/exprmt/vendor/phpstan/phpstan/phpstan.phar/vendor/ondrejmirtes/better-reflection/src/SourceLocator/SourceStubber/../../../../../jetbrains/phpstorm-stubs/dom/dom_c.stub',
        'extensionName' => 'dom',
        'aliasName' => NULL,
      ),
    ),
    'namespace' => NULL,
    'name' => 'DOMNodeList',
    'shortName' => 'DOMNodeList',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * The DOMNodeList class
 * @link https://php.net/manual/en/class.domnodelist.php
 *
 * @template-covariant TNode of DOMNode|DOMNameSpaceNode
 * @implements IteratorAggregate<int, TNode>
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 11,
    'endLine' => 61,
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
        'declaringClassName' => 'DOMNodeList',
        'implementingClassName' => 'DOMNodeList',
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
 * @var int
 * The number of nodes in the list. The range of valid child node indices is 0 to length - 1 inclusive.
 * @link https://php.net/manual/en/class.domnodelist.php#domnodelist.props.length
 */',
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
                'code' => '[\'8.1\' => \'int\']',
                'attributes' => 
                array (
                  'startLine' => 25,
                  'endLine' => 25,
                  'startTokenPos' => 43,
                  'startFilePos' => 903,
                  'endTokenPos' => 49,
                  'endFilePos' => 918,
                ),
              ),
              'default' => 
              array (
                'code' => '\'\'',
                'attributes' => 
                array (
                  'startLine' => 25,
                  'endLine' => 25,
                  'startTokenPos' => 55,
                  'startFilePos' => 930,
                  'endTokenPos' => 55,
                  'endFilePos' => 931,
                ),
              ),
            ),
          ),
          1 => 
          array (
            'name' => 'JetBrains\\PhpStorm\\Immutable',
            'isRepeated' => false,
            'arguments' => 
            array (
            ),
          ),
        ),
        'startLine' => 25,
        'endLine' => 27,
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
                'name' => 'JetBrains\\PhpStorm\\Internal\\LanguageLevelTypeAware',
                'isRepeated' => false,
                'arguments' => 
                array (
                  0 => 
                  array (
                    'code' => '[\'8.0\' => \'int\']',
                    'attributes' => 
                    array (
                      'startLine' => 40,
                      'endLine' => 40,
                      'startTokenPos' => 82,
                      'startFilePos' => 1556,
                      'endTokenPos' => 88,
                      'endFilePos' => 1571,
                    ),
                  ),
                  'default' => 
                  array (
                    'code' => '\'\'',
                    'attributes' => 
                    array (
                      'startLine' => 40,
                      'endLine' => 40,
                      'startTokenPos' => 94,
                      'startFilePos' => 1583,
                      'endTokenPos' => 94,
                      'endFilePos' => 1584,
                    ),
                  ),
                ),
              ),
            ),
            'startLine' => 40,
            'endLine' => 41,
            'startColumn' => 13,
            'endColumn' => 22,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Retrieves a node specified by index
 * @link https://php.net/manual/en/domnodelist.item.php
 * @param int $index <p>
 * Index of the node into the collection.
 * The range of valid child node indices is 0 to length - 1 inclusive.
 * </p>
 * @return TNode|null The node at the indexth position in the
 * DOMNodeList, or null if that is not a valid
 * index.
 */',
        'startLine' => 39,
        'endLine' => 44,
        'startColumn' => 9,
        'endColumn' => 9,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'DOMNodeList',
        'implementingClassName' => 'DOMNodeList',
        'currentClassName' => 'DOMNodeList',
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
 * @return int<0, max>
 * @since 7.2
 * @betterReflectionTentativeReturnType
 */',
        'startLine' => 50,
        'endLine' => 53,
        'startColumn' => 9,
        'endColumn' => 9,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'DOMNodeList',
        'implementingClassName' => 'DOMNodeList',
        'currentClassName' => 'DOMNodeList',
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
 * @return Iterator<int, TNode>
 * @since 8.0
 */',
        'startLine' => 58,
        'endLine' => 60,
        'startColumn' => 9,
        'endColumn' => 9,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'DOMNodeList',
        'implementingClassName' => 'DOMNodeList',
        'currentClassName' => 'DOMNodeList',
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