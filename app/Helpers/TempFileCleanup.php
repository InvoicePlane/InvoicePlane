<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

function deleteViewCache()
{
    foreach (File::files(storage_path('framework/views')) as $file) {
        try {
            unlink($file);
        } catch (Exception $e) {
            Log::info('Could not delete ' . $file);
        }
    }
}

function deleteTempFiles()
{
    foreach (File::files(storage_path()) as $file) {
        if (in_array(File::extension($file), ['pdf', 'csv'])) {
            try {
                unlink($file);
            } catch (Exception $e) {
                Log::info('Could not delete ' . $file);
            }
        }
    }
}
