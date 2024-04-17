<?php

declare(strict_types=1);

namespace App\Helpers;

use Exception;
use Illuminate\Support\Facades\File;

class DownloadFileFromURLHelper
{
    /**
     * Handle
     * @throws Exception
     */
    public function handle(string $file_url, string $file_name, string $destination_folder): ?string
    {
        if(!CraydelHelperFunctions::isURL($file_url)) {
            return null;
        }

        $destination = $destination_folder . DIRECTORY_SEPARATOR . $file_name;

        file_put_contents(
            $destination,
            file_get_contents($file_url)
        );

        if(File::isFile($destination)) {
            return $destination;
        }

        throw new Exception('Error while downloading the file.');
    }
}
