<?php

Route::get('tasks/run', ['uses' => 'FI\Modules\Tasks\Controllers\TaskController@run', 'as' => 'tasks.run']);