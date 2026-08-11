<?php declare(strict_types = 1);

// phpinternal-PHPStan\BetterReflection\Reflection\ReflectionClass-threaded
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v1-6.70.0.3-dev-master@709e512-8.4.24',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\InternalLocatedSource',
      'data' => 
      array (
        'name' => 'Threaded',
        'filename' => 'phar:///var/www/projects/exprmt/vendor/phpstan/phpstan/phpstan.phar/vendor/ondrejmirtes/better-reflection/src/SourceLocator/SourceStubber/../../../../../jetbrains/phpstorm-stubs/pthreads/pthreads.stub',
        'extensionName' => 'pthreads',
        'aliasName' => NULL,
      ),
    ),
    'namespace' => NULL,
    'name' => 'Threaded',
    'shortName' => 'Threaded',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Threaded objects form the basis of pthreads ability to execute user code
 * in parallel; they expose synchronization methods and various useful
 * interfaces.<br/>
 * Threaded objects, most importantly, provide implicit safety for the programmer;
 * all operations on the object scope are safe.
 *
 * @link https://secure.php.net/manual/en/class.threaded.php
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 13,
    'endLine' => 213,
    'startColumn' => 5,
    'endColumn' => 5,
    'parentClassName' => NULL,
    'implementsClassNames' => 
    array (
      0 => 'Collectable',
      1 => 'Traversable',
      2 => 'Countable',
      3 => 'ArrayAccess',
    ),
    'traitClassNames' => 
    array (
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
      'worker' => 
      array (
        'declaringClassName' => 'Threaded',
        'implementingClassName' => 'Threaded',
        'name' => 'worker',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * Worker object in which this Threaded is being executed
 * @var Worker
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 19,
        'endLine' => 19,
        'startColumn' => 9,
        'endColumn' => 26,
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
      'addRef' => 
      array (
        'name' => 'addRef',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * (PECL pthreads &gt;= 3.0.0)<br/>
 * Increments the internal number of references to a Threaded object
 * @return void
 */',
        'startLine' => 25,
        'endLine' => 27,
        'startColumn' => 9,
        'endColumn' => 9,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Threaded',
        'implementingClassName' => 'Threaded',
        'currentClassName' => 'Threaded',
        'aliasName' => NULL,
      ),
      'chunk' => 
      array (
        'name' => 'chunk',
        'parameters' => 
        array (
          'size' => 
          array (
            'name' => 'size',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 37,
            'endLine' => 37,
            'startColumn' => 31,
            'endColumn' => 35,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'preserve' => 
          array (
            'name' => 'preserve',
            'default' => 
            array (
              'code' => '\\false',
              'attributes' => 
              array (
                'startLine' => 37,
                'endLine' => 37,
                'startTokenPos' => 63,
                'startFilePos' => 1420,
                'endTokenPos' => 63,
                'endFilePos' => 1424,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 37,
            'endLine' => 37,
            'startColumn' => 38,
            'endColumn' => 54,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * (PECL pthreads &gt;= 2.0.0)<br/>
 * Fetches a chunk of the objects property table of the given size,
 * optionally preserving keys
 * @link https://secure.php.net/manual/en/threaded.chunk.php
 * @param int $size <p>The number of items to fetch</p>
 * @param bool $preserve [optional] <p>Preserve the keys of members, by default false</p>
 * @return array <p>An array of items from the objects property table</p>
 */',
        'startLine' => 37,
        'endLine' => 39,
        'startColumn' => 9,
        'endColumn' => 9,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Threaded',
        'implementingClassName' => 'Threaded',
        'currentClassName' => 'Threaded',
        'aliasName' => NULL,
      ),
      'count' => 
      array (
        'name' => 'count',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * (PECL pthreads &gt;= 2.0.0)<br/>
 * Returns the number of properties for this object
 * @link https://secure.php.net/manual/en/threaded.count.php
 * @return int <p>The number of properties for this object</p>
 */',
        'startLine' => 46,
        'endLine' => 48,
        'startColumn' => 9,
        'endColumn' => 9,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Threaded',
        'implementingClassName' => 'Threaded',
        'currentClassName' => 'Threaded',
        'aliasName' => NULL,
      ),
      'delRef' => 
      array (
        'name' => 'delRef',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * (PECL pthreads &gt;= 3.0.0)<br/>
 * Decrements the internal number of references to a Threaded object
 * @return void
 */',
        'startLine' => 54,
        'endLine' => 56,
        'startColumn' => 9,
        'endColumn' => 9,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Threaded',
        'implementingClassName' => 'Threaded',
        'currentClassName' => 'Threaded',
        'aliasName' => NULL,
      ),
      'extend' => 
      array (
        'name' => 'extend',
        'parameters' => 
        array (
          'class' => 
          array (
            'name' => 'class',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 64,
            'endLine' => 64,
            'startColumn' => 39,
            'endColumn' => 44,
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
 * (PECL pthreads &gt;= 2.0.8)<br/>
 * Makes thread safe standard class at runtime
 * @link https://secure.php.net/manual/en/threaded.extend.php
 * @param string $class <p>The class to extend</p>
 * @return bool <p>A boolean indication of success</p>
 */',
        'startLine' => 64,
        'endLine' => 66,
        'startColumn' => 9,
        'endColumn' => 9,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => NULL,
        'declaringClassName' => 'Threaded',
        'implementingClassName' => 'Threaded',
        'currentClassName' => 'Threaded',
        'aliasName' => NULL,
      ),
      'getRefCount' => 
      array (
        'name' => 'getRefCount',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * (PECL pthreads &gt;= 3.0.0)<br/>
 * Retrieves the internal number of references to a Threaded object
 * @return int <p>The number of references to the Threaded object</p>
 */',
        'startLine' => 72,
        'endLine' => 74,
        'startColumn' => 9,
        'endColumn' => 9,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Threaded',
        'implementingClassName' => 'Threaded',
        'currentClassName' => 'Threaded',
        'aliasName' => NULL,
      ),
      'isRunning' => 
      array (
        'name' => 'isRunning',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * (PECL pthreads &gt;= 2.0.0)<br/>
 * Tell if the referenced object is executing
 * @link https://secure.php.net/manual/en/thread.isrunning.php
 * @return bool <p>A boolean indication of state</p>
 */',
        'startLine' => 81,
        'endLine' => 83,
        'startColumn' => 9,
        'endColumn' => 9,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Threaded',
        'implementingClassName' => 'Threaded',
        'currentClassName' => 'Threaded',
        'aliasName' => NULL,
      ),
      'isGarbage' => 
      array (
        'name' => 'isGarbage',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'bool',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * (PECL pthreads &gt;= 3.1.0)<br/>
 * @inheritdoc
 * @see Collectable::isGarbage()
 */',
        'startLine' => 89,
        'endLine' => 91,
        'startColumn' => 9,
        'endColumn' => 9,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Threaded',
        'implementingClassName' => 'Threaded',
        'currentClassName' => 'Threaded',
        'aliasName' => NULL,
      ),
      'isTerminated' => 
      array (
        'name' => 'isTerminated',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * (PECL pthreads &gt;= 2.0.0)<br/>
 * Tell if the referenced object was terminated during execution; suffered
 * fatal errors, or threw uncaught exceptions
 * @link https://secure.php.net/manual/en/threaded.isterminated.php
 * @return bool <p>A boolean indication of state</p>
 */',
        'startLine' => 99,
        'endLine' => 101,
        'startColumn' => 9,
        'endColumn' => 9,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Threaded',
        'implementingClassName' => 'Threaded',
        'currentClassName' => 'Threaded',
        'aliasName' => NULL,
      ),
      'merge' => 
      array (
        'name' => 'merge',
        'parameters' => 
        array (
          'from' => 
          array (
            'name' => 'from',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 110,
            'endLine' => 110,
            'startColumn' => 31,
            'endColumn' => 35,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'overwrite' => 
          array (
            'name' => 'overwrite',
            'default' => 
            array (
              'code' => '\\true',
              'attributes' => 
              array (
                'startLine' => 110,
                'endLine' => 110,
                'startTokenPos' => 189,
                'startFilePos' => 3979,
                'endTokenPos' => 189,
                'endFilePos' => 3982,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 110,
            'endLine' => 110,
            'startColumn' => 38,
            'endColumn' => 54,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * (PECL pthreads &gt;= 2.0.0)<br/>
 * Merges data into the current object
 * @link https://secure.php.net/manual/en/threaded.merge.php
 * @var mixed <p>The data to merge</p>
 * @var bool [optional] <p>Overwrite existing keys, by default true</p>
 * @return bool <p>A boolean indication of success</p>
 */',
        'startLine' => 110,
        'endLine' => 112,
        'startColumn' => 9,
        'endColumn' => 9,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Threaded',
        'implementingClassName' => 'Threaded',
        'currentClassName' => 'Threaded',
        'aliasName' => NULL,
      ),
      'notify' => 
      array (
        'name' => 'notify',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * (PECL pthreads &gt;= 2.0.0)<br/>
 * Send notification to the referenced object
 * @link https://secure.php.net/manual/en/threaded.notify.php
 * @return bool <p>A boolean indication of success</p>
 */',
        'startLine' => 119,
        'endLine' => 121,
        'startColumn' => 9,
        'endColumn' => 9,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Threaded',
        'implementingClassName' => 'Threaded',
        'currentClassName' => 'Threaded',
        'aliasName' => NULL,
      ),
      'notifyOne' => 
      array (
        'name' => 'notifyOne',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * (PECL pthreads &gt;= 3.0.0)<br/>
 * Send notification to the referenced object. This unblocks at least one
 * of the blocked threads (as opposed to unblocking all of them, as seen with
 * Threaded::notify()).
 * @link https://secure.php.net/manual/en/threaded.notifyone.php
 * @return bool <p>A boolean indication of success</p>
 */',
        'startLine' => 130,
        'endLine' => 132,
        'startColumn' => 9,
        'endColumn' => 9,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Threaded',
        'implementingClassName' => 'Threaded',
        'currentClassName' => 'Threaded',
        'aliasName' => NULL,
      ),
      'pop' => 
      array (
        'name' => 'pop',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * (PECL pthreads &gt;= 2.0.0)<br/>
 * Pops an item from the objects property table
 * @link https://secure.php.net/manual/en/threaded.pop.php
 * @return mixed <p>The last item from the objects property table</p>
 */',
        'startLine' => 139,
        'endLine' => 141,
        'startColumn' => 9,
        'endColumn' => 9,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Threaded',
        'implementingClassName' => 'Threaded',
        'currentClassName' => 'Threaded',
        'aliasName' => NULL,
      ),
      'run' => 
      array (
        'name' => 'run',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * (PECL pthreads &gt;= 2.0.0)<br/>
 * The programmer should always implement the run method for objects
 * that are intended for execution.
 * @link https://secure.php.net/manual/en/threaded.run.php
 * @return void
 */',
        'startLine' => 149,
        'endLine' => 151,
        'startColumn' => 9,
        'endColumn' => 9,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Threaded',
        'implementingClassName' => 'Threaded',
        'currentClassName' => 'Threaded',
        'aliasName' => NULL,
      ),
      'shift' => 
      array (
        'name' => 'shift',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * (PECL pthreads &gt;= 2.0.0)<br/>
 * Shifts an item from the objects property table
 * @link https://secure.php.net/manual/en/threaded.shift.php
 * @return mixed <p>The first item from the objects property table</p>
 */',
        'startLine' => 158,
        'endLine' => 160,
        'startColumn' => 9,
        'endColumn' => 9,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Threaded',
        'implementingClassName' => 'Threaded',
        'currentClassName' => 'Threaded',
        'aliasName' => NULL,
      ),
      'synchronized' => 
      array (
        'name' => 'synchronized',
        'parameters' => 
        array (
          'block' => 
          array (
            'name' => 'block',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Closure',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 171,
            'endLine' => 171,
            'startColumn' => 38,
            'endColumn' => 52,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          '_' => 
          array (
            'name' => '_',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => true,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 171,
            'endLine' => 171,
            'startColumn' => 55,
            'endColumn' => 59,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * (PECL pthreads &gt;= 2.0.0)<br/>
 * Executes the block while retaining the referenced objects
 * synchronization lock for the calling context
 * @link https://secure.php.net/manual/en/threaded.synchronized.php
 * @param Closure $block <p>The block of code to execute</p>
 * @param mixed ...$_ [optional] <p>Variable length list of arguments
 * to use as function arguments to the block</p>
 * @return mixed <p>The return value from the block</p>
 */',
        'startLine' => 171,
        'endLine' => 173,
        'startColumn' => 9,
        'endColumn' => 9,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => true,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Threaded',
        'implementingClassName' => 'Threaded',
        'currentClassName' => 'Threaded',
        'aliasName' => NULL,
      ),
      'wait' => 
      array (
        'name' => 'wait',
        'parameters' => 
        array (
          'timeout' => 
          array (
            'name' => 'timeout',
            'default' => 
            array (
              'code' => '0',
              'attributes' => 
              array (
                'startLine' => 182,
                'endLine' => 182,
                'startTokenPos' => 301,
                'startFilePos' => 6799,
                'endTokenPos' => 301,
                'endFilePos' => 6799,
              ),
            ),
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
            ),
            'startLine' => 182,
            'endLine' => 182,
            'startColumn' => 30,
            'endColumn' => 45,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * (PECL pthreads &gt;= 2.0.0)<br/>
 * Will cause the calling context to wait for notification from the
 * referenced object
 * @link https://secure.php.net/manual/en/threaded.wait.php
 * @param int $timeout [optional] <p>An optional timeout in microseconds</p>
 * @return bool <p>A boolean indication of success</p>
 */',
        'startLine' => 182,
        'endLine' => 184,
        'startColumn' => 9,
        'endColumn' => 9,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Threaded',
        'implementingClassName' => 'Threaded',
        'currentClassName' => 'Threaded',
        'aliasName' => NULL,
      ),
      'offsetExists' => 
      array (
        'name' => 'offsetExists',
        'parameters' => 
        array (
          'offset' => 
          array (
            'name' => 'offset',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 189,
            'endLine' => 189,
            'startColumn' => 38,
            'endColumn' => 44,
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
 * @inheritdoc
 * @see ArrayAccess::offsetExists()
 */',
        'startLine' => 189,
        'endLine' => 191,
        'startColumn' => 9,
        'endColumn' => 9,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Threaded',
        'implementingClassName' => 'Threaded',
        'currentClassName' => 'Threaded',
        'aliasName' => NULL,
      ),
      'offsetGet' => 
      array (
        'name' => 'offsetGet',
        'parameters' => 
        array (
          'offset' => 
          array (
            'name' => 'offset',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 196,
            'endLine' => 196,
            'startColumn' => 35,
            'endColumn' => 41,
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
 * @inheritdoc
 * @see ArrayAccess::offsetGet()
 */',
        'startLine' => 196,
        'endLine' => 198,
        'startColumn' => 9,
        'endColumn' => 9,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Threaded',
        'implementingClassName' => 'Threaded',
        'currentClassName' => 'Threaded',
        'aliasName' => NULL,
      ),
      'offsetSet' => 
      array (
        'name' => 'offsetSet',
        'parameters' => 
        array (
          'offset' => 
          array (
            'name' => 'offset',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 203,
            'endLine' => 203,
            'startColumn' => 35,
            'endColumn' => 41,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'value' => 
          array (
            'name' => 'value',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 203,
            'endLine' => 203,
            'startColumn' => 44,
            'endColumn' => 49,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @inheritdoc
 * @see ArrayAccess::offsetSet()
 */',
        'startLine' => 203,
        'endLine' => 205,
        'startColumn' => 9,
        'endColumn' => 9,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Threaded',
        'implementingClassName' => 'Threaded',
        'currentClassName' => 'Threaded',
        'aliasName' => NULL,
      ),
      'offsetUnset' => 
      array (
        'name' => 'offsetUnset',
        'parameters' => 
        array (
          'offset' => 
          array (
            'name' => 'offset',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 210,
            'endLine' => 210,
            'startColumn' => 37,
            'endColumn' => 43,
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
 * @inheritdoc
 * @see ArrayAccess::offsetUnset()
 */',
        'startLine' => 210,
        'endLine' => 212,
        'startColumn' => 9,
        'endColumn' => 9,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Threaded',
        'implementingClassName' => 'Threaded',
        'currentClassName' => 'Threaded',
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