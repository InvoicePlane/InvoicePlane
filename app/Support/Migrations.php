<?php

namespace IP\Support;

use Illuminate\Support\Facades\File;

class Migrations
{
    protected $exception;

    public function runMigrations($path)
    {
        $migrationRepository = app('migration.repository');

        if (!$migrationRepository->repositoryExists()) {
            $migrationRepository->createRepository();
        }

        $this->migrator = app('migrator');

        try {
            $this->migrator->run($path);

            return true;
        } catch (\Exception $e) {
            $this->exception = $e;

            return false;
        }

    }

    public function getPendingMigrations($path)
    {
        $migrationFiles = $this->getMigrationFiles($path);
        $ranMigrations = $this->getRanMigrations();

        $pendingMigrations = array_diff($migrationFiles, $ranMigrations);

        return $pendingMigrations;
    }

    public function getMigrationFiles($path)
    {
        $files = File::glob($path . '/*_*.php');

        if ($files === false) return [];

        $files = array_map(function ($file) {
            return str_replace('.php', '', basename($file));
        }, $files);

        sort($files);

        return $files;
    }

    public function getRanMigrations()
    {
        return \DB::table('migrations')->pluck('migration')->toArray();
    }

    public function getException()
    {
        return $this->exception;
    }
}