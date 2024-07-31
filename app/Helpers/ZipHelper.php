<?php

namespace App\Helpers;

class ZipHelper
{
    public static function unzip(string $filename):string{
        // Raising this value may increase performance
        $buffer_size = 1048576; // read 4kb at a time
        $out_file_name = str_replace('.gz', '', $filename);

        // Open our files (in binary mode)
        $file = gzopen($filename, 'rb');
        $out_file = fopen($out_file_name, 'wb');

        // Keep repeating until the end of the input file
        while (!gzeof($file)) {
            // Read buffer-size bytes
            // Both fwrite and gzread and binary-safe
            fwrite($out_file, gzread($file, $buffer_size));
        }

        // Files are done, close files
        fclose($out_file);
        gzclose($file);

        return basename($out_file_name);
    }
}
