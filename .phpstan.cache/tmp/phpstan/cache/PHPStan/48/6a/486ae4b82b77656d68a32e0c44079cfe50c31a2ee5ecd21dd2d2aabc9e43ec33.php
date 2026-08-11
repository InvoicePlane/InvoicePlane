<?php declare(strict_types = 1);

// odsl-/var/www/projects/exprmt/application/core/MY_Model.php-PHPStan\BetterReflection\Reflection\ReflectionClass-MY_Model
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.4.24-4a45e265f96f0a2eb428fa47c06bfbb980dc8403fef441b047f62c67cc244df2',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'MY_Model',
        'filename' => '/var/www/projects/exprmt/application/core/MY_Model.php',
      ),
    ),
    'namespace' => NULL,
    'name' => 'MY_Model',
    'shortName' => 'MY_Model',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * CodeIgniter CRUD Model 2
 * A base model providing CRUD, pagination and validation.
 *
 * Install this file as application/core/MY_Model.php
 *
 * @author        Jesse Terry
 * @copyright     Copyright (c) 2012-2013, Jesse Terry
 *
 * @see          http://developer13.com
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */',
    'attributes' => 
    array (
      0 => 
      array (
        'name' => 'AllowDynamicProperties',
        'isRepeated' => false,
        'arguments' => 
        array (
        ),
      ),
    ),
    'startLine' => 36,
    'endLine' => 512,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'CI_Model',
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
      'table' => 
      array (
        'declaringClassName' => 'MY_Model',
        'implementingClassName' => 'MY_Model',
        'name' => 'table',
        'modifiers' => 1,
        'type' => NULL,
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 39,
        'endLine' => 39,
        'startColumn' => 5,
        'endColumn' => 18,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'primary_key' => 
      array (
        'declaringClassName' => 'MY_Model',
        'implementingClassName' => 'MY_Model',
        'name' => 'primary_key',
        'modifiers' => 1,
        'type' => NULL,
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 41,
        'endLine' => 41,
        'startColumn' => 5,
        'endColumn' => 24,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'default_limit' => 
      array (
        'declaringClassName' => 'MY_Model',
        'implementingClassName' => 'MY_Model',
        'name' => 'default_limit',
        'modifiers' => 1,
        'type' => NULL,
        'default' => 
        array (
          'code' => '15',
          'attributes' => 
          array (
            'startLine' => 44,
            'endLine' => 44,
            'startTokenPos' => 58,
            'startFilePos' => 1597,
            'endTokenPos' => 58,
            'endFilePos' => 1598,
          ),
        ),
        'docComment' => '/** @var int */',
        'attributes' => 
        array (
        ),
        'startLine' => 44,
        'endLine' => 44,
        'startColumn' => 5,
        'endColumn' => 31,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'page_links' => 
      array (
        'declaringClassName' => 'MY_Model',
        'implementingClassName' => 'MY_Model',
        'name' => 'page_links',
        'modifiers' => 1,
        'type' => NULL,
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 46,
        'endLine' => 46,
        'startColumn' => 5,
        'endColumn' => 23,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'query' => 
      array (
        'declaringClassName' => 'MY_Model',
        'implementingClassName' => 'MY_Model',
        'name' => 'query',
        'modifiers' => 1,
        'type' => NULL,
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 48,
        'endLine' => 48,
        'startColumn' => 5,
        'endColumn' => 18,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'form_values' => 
      array (
        'declaringClassName' => 'MY_Model',
        'implementingClassName' => 'MY_Model',
        'name' => 'form_values',
        'modifiers' => 1,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 51,
            'endLine' => 51,
            'startTokenPos' => 79,
            'startFilePos' => 1695,
            'endTokenPos' => 80,
            'endFilePos' => 1696,
          ),
        ),
        'docComment' => '/** @var array */',
        'attributes' => 
        array (
        ),
        'startLine' => 51,
        'endLine' => 51,
        'startColumn' => 5,
        'endColumn' => 29,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'validation_errors' => 
      array (
        'declaringClassName' => 'MY_Model',
        'implementingClassName' => 'MY_Model',
        'name' => 'validation_errors',
        'modifiers' => 1,
        'type' => NULL,
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 53,
        'endLine' => 53,
        'startColumn' => 5,
        'endColumn' => 30,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'total_rows' => 
      array (
        'declaringClassName' => 'MY_Model',
        'implementingClassName' => 'MY_Model',
        'name' => 'total_rows',
        'modifiers' => 1,
        'type' => NULL,
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 55,
        'endLine' => 55,
        'startColumn' => 5,
        'endColumn' => 23,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'date_created_field' => 
      array (
        'declaringClassName' => 'MY_Model',
        'implementingClassName' => 'MY_Model',
        'name' => 'date_created_field',
        'modifiers' => 1,
        'type' => NULL,
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 57,
        'endLine' => 57,
        'startColumn' => 5,
        'endColumn' => 31,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'date_modified_field' => 
      array (
        'declaringClassName' => 'MY_Model',
        'implementingClassName' => 'MY_Model',
        'name' => 'date_modified_field',
        'modifiers' => 1,
        'type' => NULL,
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 59,
        'endLine' => 59,
        'startColumn' => 5,
        'endColumn' => 32,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'native_methods' => 
      array (
        'declaringClassName' => 'MY_Model',
        'implementingClassName' => 'MY_Model',
        'name' => 'native_methods',
        'modifiers' => 1,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'select\', \'select_max\', \'select_min\', \'select_avg\', \'select_sum\', \'join\', \'where\', \'or_where\', \'where_in\', \'or_where_in\', \'where_not_in\', \'or_where_not_in\', \'like\', \'or_like\', \'not_like\', \'or_not_like\', \'group_by\', \'distinct\', \'having\', \'or_having\', \'order_by\', \'limit\']',
          'attributes' => 
          array (
            'startLine' => 62,
            'endLine' => 85,
            'startTokenPos' => 111,
            'startFilePos' => 1875,
            'endTokenPos' => 179,
            'endFilePos' => 2328,
          ),
        ),
        'docComment' => '/** @var array */',
        'attributes' => 
        array (
        ),
        'startLine' => 62,
        'endLine' => 85,
        'startColumn' => 5,
        'endColumn' => 6,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'total_pages' => 
      array (
        'declaringClassName' => 'MY_Model',
        'implementingClassName' => 'MY_Model',
        'name' => 'total_pages',
        'modifiers' => 1,
        'type' => NULL,
        'default' => 
        array (
          'code' => '0',
          'attributes' => 
          array (
            'startLine' => 88,
            'endLine' => 88,
            'startTokenPos' => 190,
            'startFilePos' => 2378,
            'endTokenPos' => 190,
            'endFilePos' => 2378,
          ),
        ),
        'docComment' => '/** @var int */',
        'attributes' => 
        array (
        ),
        'startLine' => 88,
        'endLine' => 88,
        'startColumn' => 5,
        'endColumn' => 28,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'current_page' => 
      array (
        'declaringClassName' => 'MY_Model',
        'implementingClassName' => 'MY_Model',
        'name' => 'current_page',
        'modifiers' => 1,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/** @var int */',
        'attributes' => 
        array (
        ),
        'startLine' => 91,
        'endLine' => 91,
        'startColumn' => 5,
        'endColumn' => 25,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'next_page' => 
      array (
        'declaringClassName' => 'MY_Model',
        'implementingClassName' => 'MY_Model',
        'name' => 'next_page',
        'modifiers' => 1,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/** @var int */',
        'attributes' => 
        array (
        ),
        'startLine' => 94,
        'endLine' => 94,
        'startColumn' => 5,
        'endColumn' => 22,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'previous_page' => 
      array (
        'declaringClassName' => 'MY_Model',
        'implementingClassName' => 'MY_Model',
        'name' => 'previous_page',
        'modifiers' => 1,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/** @var int */',
        'attributes' => 
        array (
        ),
        'startLine' => 97,
        'endLine' => 97,
        'startColumn' => 5,
        'endColumn' => 26,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'offset' => 
      array (
        'declaringClassName' => 'MY_Model',
        'implementingClassName' => 'MY_Model',
        'name' => 'offset',
        'modifiers' => 1,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/** @var int */',
        'attributes' => 
        array (
        ),
        'startLine' => 100,
        'endLine' => 100,
        'startColumn' => 5,
        'endColumn' => 19,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'next_offset' => 
      array (
        'declaringClassName' => 'MY_Model',
        'implementingClassName' => 'MY_Model',
        'name' => 'next_offset',
        'modifiers' => 1,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/** @var int */',
        'attributes' => 
        array (
        ),
        'startLine' => 103,
        'endLine' => 103,
        'startColumn' => 5,
        'endColumn' => 24,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'previous_offset' => 
      array (
        'declaringClassName' => 'MY_Model',
        'implementingClassName' => 'MY_Model',
        'name' => 'previous_offset',
        'modifiers' => 1,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/** @var int */',
        'attributes' => 
        array (
        ),
        'startLine' => 106,
        'endLine' => 106,
        'startColumn' => 5,
        'endColumn' => 28,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'last_offset' => 
      array (
        'declaringClassName' => 'MY_Model',
        'implementingClassName' => 'MY_Model',
        'name' => 'last_offset',
        'modifiers' => 1,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/** @var int */',
        'attributes' => 
        array (
        ),
        'startLine' => 109,
        'endLine' => 109,
        'startColumn' => 5,
        'endColumn' => 24,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'id' => 
      array (
        'declaringClassName' => 'MY_Model',
        'implementingClassName' => 'MY_Model',
        'name' => 'id',
        'modifiers' => 1,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/** @var int */',
        'attributes' => 
        array (
        ),
        'startLine' => 112,
        'endLine' => 112,
        'startColumn' => 5,
        'endColumn' => 15,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'filter' => 
      array (
        'declaringClassName' => 'MY_Model',
        'implementingClassName' => 'MY_Model',
        'name' => 'filter',
        'modifiers' => 1,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 115,
            'endLine' => 115,
            'startTokenPos' => 257,
            'startFilePos' => 2784,
            'endTokenPos' => 258,
            'endFilePos' => 2785,
          ),
        ),
        'docComment' => '/** @var array */',
        'attributes' => 
        array (
        ),
        'startLine' => 115,
        'endLine' => 115,
        'startColumn' => 5,
        'endColumn' => 24,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'default_validation_rules' => 
      array (
        'declaringClassName' => 'MY_Model',
        'implementingClassName' => 'MY_Model',
        'name' => 'default_validation_rules',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'validation_rules\'',
          'attributes' => 
          array (
            'startLine' => 118,
            'endLine' => 118,
            'startTokenPos' => 269,
            'startFilePos' => 2854,
            'endTokenPos' => 269,
            'endFilePos' => 2871,
          ),
        ),
        'docComment' => '/** @var string */',
        'attributes' => 
        array (
        ),
        'startLine' => 118,
        'endLine' => 118,
        'startColumn' => 5,
        'endColumn' => 61,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'validation_rules' => 
      array (
        'declaringClassName' => 'MY_Model',
        'implementingClassName' => 'MY_Model',
        'name' => 'validation_rules',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/** @var array */',
        'attributes' => 
        array (
        ),
        'startLine' => 121,
        'endLine' => 121,
        'startColumn' => 5,
        'endColumn' => 32,
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
      '__call' => 
      array (
        'name' => '__call',
        'parameters' => 
        array (
          'name' => 
          array (
            'name' => 'name',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 129,
            'endLine' => 129,
            'startColumn' => 28,
            'endColumn' => 32,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'arguments' => 
          array (
            'name' => 'arguments',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 129,
            'endLine' => 129,
            'startColumn' => 35,
            'endColumn' => 44,
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
 * @param string $name
 * @param array  $arguments
 *
 * @return $this
 */',
        'startLine' => 129,
        'endLine' => 138,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'MY_Model',
        'implementingClassName' => 'MY_Model',
        'currentClassName' => 'MY_Model',
        'aliasName' => NULL,
      ),
      'get' => 
      array (
        'name' => 'get',
        'parameters' => 
        array (
          'include_defaults' => 
          array (
            'name' => 'include_defaults',
            'default' => 
            array (
              'code' => '\\true',
              'attributes' => 
              array (
                'startLine' => 149,
                'endLine' => 149,
                'startTokenPos' => 381,
                'startFilePos' => 3605,
                'endTokenPos' => 381,
                'endFilePos' => 3608,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 149,
            'endLine' => 149,
            'startColumn' => 25,
            'endColumn' => 48,
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
 * Sets CI query object and automatically creates active record query
 * based on methods in child model.
 * $this->model_name->get().
 *
 * @param bool $include_defaults
 *
 * @return $this
 */',
        'startLine' => 149,
        'endLine' => 170,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'MY_Model',
        'implementingClassName' => 'MY_Model',
        'currentClassName' => 'MY_Model',
        'aliasName' => NULL,
      ),
      'paginate' => 
      array (
        'name' => 'paginate',
        'parameters' => 
        array (
          'base_url' => 
          array (
            'name' => 'base_url',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 180,
            'endLine' => 180,
            'startColumn' => 30,
            'endColumn' => 38,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'offset' => 
          array (
            'name' => 'offset',
            'default' => 
            array (
              'code' => '0',
              'attributes' => 
              array (
                'startLine' => 180,
                'endLine' => 180,
                'startTokenPos' => 534,
                'startFilePos' => 4436,
                'endTokenPos' => 534,
                'endFilePos' => 4436,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 180,
            'endLine' => 180,
            'startColumn' => 41,
            'endColumn' => 51,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'uri_segment' => 
          array (
            'name' => 'uri_segment',
            'default' => 
            array (
              'code' => '3',
              'attributes' => 
              array (
                'startLine' => 180,
                'endLine' => 180,
                'startTokenPos' => 541,
                'startFilePos' => 4454,
                'endTokenPos' => 541,
                'endFilePos' => 4454,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 180,
            'endLine' => 180,
            'startColumn' => 54,
            'endColumn' => 69,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Call when paginating results
 * $this->model_name->paginate().
 *
 * @param     $base_url
 * @param int $offset
 * @param int $uri_segment
 */',
        'startLine' => 180,
        'endLine' => 229,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'MY_Model',
        'implementingClassName' => 'MY_Model',
        'currentClassName' => 'MY_Model',
        'aliasName' => NULL,
      ),
      'save' => 
      array (
        'name' => 'save',
        'parameters' => 
        array (
          'id' => 
          array (
            'name' => 'id',
            'default' => 
            array (
              'code' => '\\null',
              'attributes' => 
              array (
                'startLine' => 234,
                'endLine' => 234,
                'startTokenPos' => 999,
                'startFilePos' => 6463,
                'endTokenPos' => 999,
                'endFilePos' => 6466,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 234,
            'endLine' => 234,
            'startColumn' => 26,
            'endColumn' => 35,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
          'db_array' => 
          array (
            'name' => 'db_array',
            'default' => 
            array (
              'code' => '\\null',
              'attributes' => 
              array (
                'startLine' => 234,
                'endLine' => 234,
                'startTokenPos' => 1006,
                'startFilePos' => 6481,
                'endTokenPos' => 1006,
                'endFilePos' => 6484,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 234,
            'endLine' => 234,
            'startColumn' => 38,
            'endColumn' => 53,
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
 * Function to save an entry to the database.
 */',
        'startLine' => 234,
        'endLine' => 282,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'MY_Model',
        'implementingClassName' => 'MY_Model',
        'currentClassName' => 'MY_Model',
        'aliasName' => NULL,
      ),
      'db_array' => 
      array (
        'name' => 'db_array',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Returns an array based on $_POST input matching the ruleset used to
 * validate the form submission.
 *
 * @return array
 */',
        'startLine' => 290,
        'endLine' => 303,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'MY_Model',
        'implementingClassName' => 'MY_Model',
        'currentClassName' => 'MY_Model',
        'aliasName' => NULL,
      ),
      'delete' => 
      array (
        'name' => 'delete',
        'parameters' => 
        array (
          'id' => 
          array (
            'name' => 'id',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 311,
            'endLine' => 311,
            'startColumn' => 28,
            'endColumn' => 30,
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
 * Deletes a record based on primary key value
 * $this->model_name->delete(5);.
 *
 * @param $id
 */',
        'startLine' => 311,
        'endLine' => 315,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'MY_Model',
        'implementingClassName' => 'MY_Model',
        'currentClassName' => 'MY_Model',
        'aliasName' => NULL,
      ),
      'result' => 
      array (
        'name' => 'result',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Returns the CI query result object
 * $this->model_name->get()->result();.
 *
 * @return mixed
 */',
        'startLine' => 323,
        'endLine' => 326,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'MY_Model',
        'implementingClassName' => 'MY_Model',
        'currentClassName' => 'MY_Model',
        'aliasName' => NULL,
      ),
      'row' => 
      array (
        'name' => 'row',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Returns the CI query row object
 * $this->model_name->get()->row();.
 *
 * @return mixed
 */',
        'startLine' => 334,
        'endLine' => 337,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'MY_Model',
        'implementingClassName' => 'MY_Model',
        'currentClassName' => 'MY_Model',
        'aliasName' => NULL,
      ),
      'result_array' => 
      array (
        'name' => 'result_array',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Returns CI query result array
 * $this->model_name->get()->result_array();.
 *
 * @return mixed
 */',
        'startLine' => 345,
        'endLine' => 348,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'MY_Model',
        'implementingClassName' => 'MY_Model',
        'currentClassName' => 'MY_Model',
        'aliasName' => NULL,
      ),
      'row_array' => 
      array (
        'name' => 'row_array',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Returns CI query row array
 * $this->model_name->get()->row_array();.
 *
 * @return mixed
 */',
        'startLine' => 356,
        'endLine' => 359,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'MY_Model',
        'implementingClassName' => 'MY_Model',
        'currentClassName' => 'MY_Model',
        'aliasName' => NULL,
      ),
      'num_rows' => 
      array (
        'name' => 'num_rows',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Returns CI query num_rows()
 * $this->model_name->get()->num_rows();.
 *
 * @return mixed
 */',
        'startLine' => 367,
        'endLine' => 370,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'MY_Model',
        'implementingClassName' => 'MY_Model',
        'currentClassName' => 'MY_Model',
        'aliasName' => NULL,
      ),
      'prep_form' => 
      array (
        'name' => 'prep_form',
        'parameters' => 
        array (
          'id' => 
          array (
            'name' => 'id',
            'default' => 
            array (
              'code' => '\\null',
              'attributes' => 
              array (
                'startLine' => 379,
                'endLine' => 379,
                'startTokenPos' => 1609,
                'startFilePos' => 10029,
                'endTokenPos' => 1609,
                'endFilePos' => 10032,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 379,
            'endLine' => 379,
            'startColumn' => 31,
            'endColumn' => 40,
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
 * Used to retrieve record by ID and populate $this->form_values.
 *
 * @param int $id
 *
 * @return bool|null
 */',
        'startLine' => 379,
        'endLine' => 398,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'MY_Model',
        'implementingClassName' => 'MY_Model',
        'currentClassName' => 'MY_Model',
        'aliasName' => NULL,
      ),
      'get_by_id' => 
      array (
        'name' => 'get_by_id',
        'parameters' => 
        array (
          'id' => 
          array (
            'name' => 'id',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 407,
            'endLine' => 407,
            'startColumn' => 31,
            'endColumn' => 33,
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
 * Retrieves a single record based on primary key value.
 *
 * @param $id
 *
 * @return mixed
 */',
        'startLine' => 407,
        'endLine' => 410,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'MY_Model',
        'implementingClassName' => 'MY_Model',
        'currentClassName' => 'MY_Model',
        'aliasName' => NULL,
      ),
      'run_validation' => 
      array (
        'name' => 'run_validation',
        'parameters' => 
        array (
          'validation_rules' => 
          array (
            'name' => 'validation_rules',
            'default' => 
            array (
              'code' => '\\null',
              'attributes' => 
              array (
                'startLine' => 421,
                'endLine' => 421,
                'startTokenPos' => 1763,
                'startFilePos' => 11041,
                'endTokenPos' => 1763,
                'endFilePos' => 11044,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 421,
            'endLine' => 421,
            'startColumn' => 36,
            'endColumn' => 59,
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
 * Performs validation on submitted form. By default, looks for method in
 * child model called validation_rules, but can be forced to run validation
 * on any method in child model which returns array of validation rules.
 *
 * @param null|string $validation_rules
 *
 * @return mixed
 */',
        'startLine' => 421,
        'endLine' => 444,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'MY_Model',
        'implementingClassName' => 'MY_Model',
        'currentClassName' => 'MY_Model',
        'aliasName' => NULL,
      ),
      'form_value' => 
      array (
        'name' => 'form_value',
        'parameters' => 
        array (
          'key' => 
          array (
            'name' => 'key',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 454,
            'endLine' => 454,
            'startColumn' => 32,
            'endColumn' => 35,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'escape' => 
          array (
            'name' => 'escape',
            'default' => 
            array (
              'code' => '\\false',
              'attributes' => 
              array (
                'startLine' => 454,
                'endLine' => 454,
                'startTokenPos' => 1923,
                'startFilePos' => 11911,
                'endTokenPos' => 1923,
                'endFilePos' => 11915,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 454,
            'endLine' => 454,
            'startColumn' => 38,
            'endColumn' => 52,
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
 * Returns the assigned form value to a form input element.
 *
 * @param string $key
 * @param bool   $escape
 *
 * @return mixed|string
 */',
        'startLine' => 454,
        'endLine' => 459,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'MY_Model',
        'implementingClassName' => 'MY_Model',
        'currentClassName' => 'MY_Model',
        'aliasName' => NULL,
      ),
      'set_form_value' => 
      array (
        'name' => 'set_form_value',
        'parameters' => 
        array (
          'key' => 
          array (
            'name' => 'key',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 465,
            'endLine' => 465,
            'startColumn' => 36,
            'endColumn' => 39,
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
            'startLine' => 465,
            'endLine' => 465,
            'startColumn' => 42,
            'endColumn' => 47,
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
 * @param string $key
 * @param        $value
 */',
        'startLine' => 465,
        'endLine' => 468,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'MY_Model',
        'implementingClassName' => 'MY_Model',
        'currentClassName' => 'MY_Model',
        'aliasName' => NULL,
      ),
      'set_id' => 
      array (
        'name' => 'set_id',
        'parameters' => 
        array (
          'id' => 
          array (
            'name' => 'id',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 473,
            'endLine' => 473,
            'startColumn' => 28,
            'endColumn' => 30,
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
 * @param $id
 */',
        'startLine' => 473,
        'endLine' => 476,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'MY_Model',
        'implementingClassName' => 'MY_Model',
        'currentClassName' => 'MY_Model',
        'aliasName' => NULL,
      ),
      'set_defaults' => 
      array (
        'name' => 'set_defaults',
        'parameters' => 
        array (
          'exclude' => 
          array (
            'name' => 'exclude',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 483,
                'endLine' => 483,
                'startTokenPos' => 2028,
                'startFilePos' => 12479,
                'endTokenPos' => 2029,
                'endFilePos' => 12480,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 483,
            'endLine' => 483,
            'startColumn' => 35,
            'endColumn' => 47,
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
 * Query builder which listens to methods in child model.
 *
 * @param array $exclude
 */',
        'startLine' => 483,
        'endLine' => 498,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => NULL,
        'declaringClassName' => 'MY_Model',
        'implementingClassName' => 'MY_Model',
        'currentClassName' => 'MY_Model',
        'aliasName' => NULL,
      ),
      'run_filters' => 
      array (
        'name' => 'run_filters',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 500,
        'endLine' => 511,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => NULL,
        'declaringClassName' => 'MY_Model',
        'implementingClassName' => 'MY_Model',
        'currentClassName' => 'MY_Model',
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