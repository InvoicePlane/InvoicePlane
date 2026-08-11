<?php declare(strict_types = 1);

// odsl-/var/www/projects/exprmt/application/modules/upload/models/Mdl_uploads.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Mdl_Uploads
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.4.24-f850fa769c044c674b85e4d81aaf17cfc090a5c8899284a3c9973480e5cb8b87',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Mdl_Uploads',
        'filename' => '/var/www/projects/exprmt/application/modules/upload/models/Mdl_uploads.php',
      ),
    ),
    'namespace' => NULL,
    'name' => 'Mdl_Uploads',
    'shortName' => 'Mdl_Uploads',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
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
    'startLine' => 16,
    'endLine' => 208,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Response_Model',
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
        'declaringClassName' => 'Mdl_Uploads',
        'implementingClassName' => 'Mdl_Uploads',
        'name' => 'table',
        'modifiers' => 1,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'ip_uploads\'',
          'attributes' => 
          array (
            'startLine' => 19,
            'endLine' => 19,
            'startTokenPos' => 46,
            'startFilePos' => 409,
            'endTokenPos' => 46,
            'endFilePos' => 420,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 19,
        'endLine' => 19,
        'startColumn' => 5,
        'endColumn' => 33,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'primary_key' => 
      array (
        'declaringClassName' => 'Mdl_Uploads',
        'implementingClassName' => 'Mdl_Uploads',
        'name' => 'primary_key',
        'modifiers' => 1,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'ip_uploads.upload_id\'',
          'attributes' => 
          array (
            'startLine' => 21,
            'endLine' => 21,
            'startTokenPos' => 55,
            'startFilePos' => 450,
            'endTokenPos' => 55,
            'endFilePos' => 471,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 21,
        'endLine' => 21,
        'startColumn' => 5,
        'endColumn' => 49,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'date_modified_field' => 
      array (
        'declaringClassName' => 'Mdl_Uploads',
        'implementingClassName' => 'Mdl_Uploads',
        'name' => 'date_modified_field',
        'modifiers' => 1,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'uploaded_date\'',
          'attributes' => 
          array (
            'startLine' => 23,
            'endLine' => 23,
            'startTokenPos' => 64,
            'startFilePos' => 509,
            'endTokenPos' => 64,
            'endFilePos' => 523,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 23,
        'endLine' => 23,
        'startColumn' => 5,
        'endColumn' => 50,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'content_types' => 
      array (
        'declaringClassName' => 'Mdl_Uploads',
        'implementingClassName' => 'Mdl_Uploads',
        'name' => 'content_types',
        'modifiers' => 1,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[
    \'avif\' => \'image/avif\',
    \'gif\' => \'image/gif\',
    \'png\' => \'image/png\',
    \'jpg\' => \'image/jpeg\',
    \'jpeg\' => \'image/jpeg\',
    \'svg\' => \'image/svg+xml\',
    \'webp\' => \'image/webp\',
    \'txt\' => \'text/plain\',
    \'xml\' => \'text/xml\',
    \'pdf\' => \'application/pdf\',
    // file-audio
    \'mp3\' => \'audio/mpeg\',
    \'oga\' => \'audio/ogg\',
    \'ogg\' => \'audio/ogg\',
    \'wav\' => \'audio/x-wav\',
    \'weba\' => \'audio/webm\',
    // file-document
    \'doc\' => \'application/msword\',
    \'docx\' => \'application/vnd.openxmlformats-officedocument.wordprocessingml.document\',
    \'odt\' => \'application/vnd.oasis.opendocument.text\',
    // file-spreadsheet
    \'xls\' => \'application/vnd.ms-excel\',
    \'xlsx\' => \'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet\',
    \'ods\' => \'application/vnd.oasis.opendocument.spreadsheet\',
    // file-presentation
    \'ppt\' => \'application/vnd.ms-powerpoint\',
    \'pptx\' => \'application/vnd.openxmlformats-officedocument.presentationml.presentation\',
    \'odp\' => \'application/vnd.oasis.opendocument.presentation\',
]',
          'attributes' => 
          array (
            'startLine' => 25,
            'endLine' => 54,
            'startTokenPos' => 73,
            'startFilePos' => 555,
            'endTokenPos' => 251,
            'endFilePos' => 1758,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 25,
        'endLine' => 54,
        'startColumn' => 5,
        'endColumn' => 6,
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
      '__construct' => 
      array (
        'name' => '__construct',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Constructor - load file security helper for validation.
 */',
        'startLine' => 59,
        'endLine' => 63,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Uploads',
        'implementingClassName' => 'Mdl_Uploads',
        'currentClassName' => 'Mdl_Uploads',
        'aliasName' => NULL,
      ),
      'default_order_by' => 
      array (
        'name' => 'default_order_by',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 65,
        'endLine' => 68,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Uploads',
        'implementingClassName' => 'Mdl_Uploads',
        'currentClassName' => 'Mdl_Uploads',
        'aliasName' => NULL,
      ),
      'create' => 
      array (
        'name' => 'create',
        'parameters' => 
        array (
          'db_array' => 
          array (
            'name' => 'db_array',
            'default' => 
            array (
              'code' => '\\null',
              'attributes' => 
              array (
                'startLine' => 73,
                'endLine' => 73,
                'startTokenPos' => 319,
                'startFilePos' => 2153,
                'endTokenPos' => 319,
                'endFilePos' => 2156,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 73,
            'endLine' => 73,
            'startColumn' => 28,
            'endColumn' => 43,
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
 * @return int|null
 */',
        'startLine' => 73,
        'endLine' => 76,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Uploads',
        'implementingClassName' => 'Mdl_Uploads',
        'currentClassName' => 'Mdl_Uploads',
        'aliasName' => NULL,
      ),
      'get_quote_uploads' => 
      array (
        'name' => 'get_quote_uploads',
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
            'startLine' => 83,
            'endLine' => 83,
            'startColumn' => 39,
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
 * @param $id
 *
 * @return array
 */',
        'startLine' => 83,
        'endLine' => 112,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Uploads',
        'implementingClassName' => 'Mdl_Uploads',
        'currentClassName' => 'Mdl_Uploads',
        'aliasName' => NULL,
      ),
      'get_invoice_uploads' => 
      array (
        'name' => 'get_invoice_uploads',
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
            'startLine' => 119,
            'endLine' => 119,
            'startColumn' => 41,
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
 * @param $id
 *
 * @return array
 */',
        'startLine' => 119,
        'endLine' => 148,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Uploads',
        'implementingClassName' => 'Mdl_Uploads',
        'currentClassName' => 'Mdl_Uploads',
        'aliasName' => NULL,
      ),
      'get_files' => 
      array (
        'name' => 'get_files',
        'parameters' => 
        array (
          'url_key' => 
          array (
            'name' => 'url_key',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 155,
            'endLine' => 155,
            'startColumn' => 31,
            'endColumn' => 38,
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
 * @param $url_key
 *
 * @return array
 */',
        'startLine' => 155,
        'endLine' => 186,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Uploads',
        'implementingClassName' => 'Mdl_Uploads',
        'currentClassName' => 'Mdl_Uploads',
        'aliasName' => NULL,
      ),
      'delete_file' => 
      array (
        'name' => 'delete_file',
        'parameters' => 
        array (
          'url_key' => 
          array (
            'name' => 'url_key',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 192,
            'endLine' => 192,
            'startColumn' => 33,
            'endColumn' => 40,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'filename' => 
          array (
            'name' => 'filename',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 192,
            'endLine' => 192,
            'startColumn' => 43,
            'endColumn' => 51,
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
 * @param $url_key
 * @param $filename
 */',
        'startLine' => 192,
        'endLine' => 195,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Uploads',
        'implementingClassName' => 'Mdl_Uploads',
        'currentClassName' => 'Mdl_Uploads',
        'aliasName' => NULL,
      ),
      'by_client' => 
      array (
        'name' => 'by_client',
        'parameters' => 
        array (
          'client_id' => 
          array (
            'name' => 'client_id',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 202,
            'endLine' => 202,
            'startColumn' => 31,
            'endColumn' => 40,
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
 * @param $client_id
 *
 * @return $this
 */',
        'startLine' => 202,
        'endLine' => 207,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => NULL,
        'declaringClassName' => 'Mdl_Uploads',
        'implementingClassName' => 'Mdl_Uploads',
        'currentClassName' => 'Mdl_Uploads',
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