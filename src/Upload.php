<?php

namespace App;

use Psr\Http\Message\UploadedFileInterface;

class Upload {
    
    private static $allowedExtensions = ['mmdb'];
    private static $directory = __DIR__ . '/../data';
    private static $basename = 'database';

    /**
     * Moves the uploaded file to the upload directory and assigns it a unique name
     * to avoid overwriting an existing uploaded file.
     *
     * @param string $directory The directory to which the file is moved
     * @param UploadedFileInterface $uploadedFile The file uploaded file to move
     *
     * @return string The filename of moved file
     */
    public static function moveUploadedFile(UploadedFileInterface $uploadedFile)
    {
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);

        if (in_array($extension, self::$allowedExtensions)) {
            
            $filename = sprintf('%s.%0.8s', self::$basename, $extension);
            $path = self::$directory . DIRECTORY_SEPARATOR . $filename;

            self::renameExistingFile($path, $extension);

            $uploadedFile->moveTo($path);
            return $filename;
        }
        return false;
    }

    /**
     * Renames a file if it already exists in the upload directory.
     *
     * @param string $path The path to the uploaded file
     * @param string $extension The extension of the uploaded file
     */
    private static function renameExistingFile($path, $extension) {
        if (file_exists($path)) {
            $new_filename = sprintf('%s-%s.%0.8s', self::$basename, date('Y-m-d_H-i'), $extension);
            $new_path = self::$directory . DIRECTORY_SEPARATOR . $new_filename;
            rename($path, $new_path);
        }

        self::removeOldBackups($new_path);
    }

    /**
     * Removes old backups of the database file.
     *
     * @param string $new_path The path to the new database file
     */
    private static function removeOldBackups($new_path) {
        $files = glob(self::$directory . DIRECTORY_SEPARATOR . '*.mmdb');
        $files = array_diff($files, [$new_path]);
        foreach ($files as $file) {
            unlink($file);
        }
    }

}