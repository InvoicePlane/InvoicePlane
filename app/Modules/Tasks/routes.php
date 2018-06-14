<?php

Route::get('tasks/run', ['uses' => 'IP\Modules\Tasks\Controllers\TaskController@run', 'as' => 'tasks.run']);