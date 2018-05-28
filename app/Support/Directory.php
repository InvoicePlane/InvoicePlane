<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Support;

class Directory
{
    /**
     * Provide a list of directory contents minus the top directory.
     *
     * @param  string $path
     * @return array
     */
    public static function listContents($path)
    {
        return array_diff(scandir($path), ['.', '..']);
    }

    /**
     * Provide an associative array of directory contents ['dir' => 'dir'].
     *
     * @param  string $path
     * @return array
     */
    public static function listAssocContents($path)
    {
        $files = self::listContents($path);

        return array_combine($files, $files);
    }

    /**
     * Provide a list of only directories.
     *
     * @param  string $path
     * @return array
     */
    public static function listDirectories($path)
    {
        $directories = self::listContents($path);

        foreach ($directories as $key => $directory)
        {
            if (!is_dir($path . '/' . $directory))
            {
                unset($directories[$key]);
            }
        }

        return $directories;
    }
}