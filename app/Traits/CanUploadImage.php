<?php

declare(strict_types=1);

namespace App\Traits;

use App\Helpers\CraydelHelperFunctions;
use App\Helpers\CraydelInternalResponseHelper;
use Aws\S3\S3Client;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;

trait CanUploadImage
{
    private ?array $sizesToCreate;

    /**
     * @throws FilesystemException
     * @throws Exception
     */
    public static function deleteFromS3(string $filePath): void
    {
        if (CraydelHelperFunctions::isNull($filePath) || CraydelHelperFunctions::isURL($filePath) === false) {
            throw new Exception('Missing or invalid file path while deleting the image from s3.');
        }

        $file_name = CraydelHelperFunctions::getFileNameFromURL($filePath);

        if (empty($file_name)) {
            throw new Exception('Invalid CDN image path. Can not retrieve the file name.');
        }

        $client = new S3Client([
            'credentials' => [
                'key' => config('services.files_storage.storage_key'),
                'secret' => config('services.files_storage.storage_secret'),
            ],
            'region' => config('services.files_storage.storage_server_region'),
            'version' => 'latest',
            'visibility' => 'public',
        ]);

        $adapter = new AwsS3V3Adapter($client, config('services.files_storage.storage_bucket_name'));
        $filesystem = new Filesystem($adapter);

        if ($filesystem->has($file_name)) {
            $filesystem->delete($file_name);
        }
    }

    /**
     * Upload image
     *
     *
     * @throws FilesystemException
     * @throws Exception
     */
    public function toS3(string $filePath, string $fileName): CraydelInternalResponseHelper
    {
        if (file_exists($filePath) === false) {
            throw new Exception('Could not locate the file to move to the CDN');
        }

        $client = new S3Client([
            'credentials' => [
                'key' => config('services.files_storage.storage_key'),
                'secret' => config('services.files_storage.storage_secret'),
            ],
            'region' => config('services.files_storage.storage_server_region'),
            'version' => 'latest',
            'visibility' => 'public',
        ]);

        $adapter = new AwsS3V3Adapter($client, config('services.files_storage.storage_bucket_name'));
        $filesystem = new Filesystem($adapter);

        if ($filesystem->has($fileName)) {
            throw new Exception('File already exists in CDN');
        }

        $filesystem->write($fileName, file_get_contents($filePath));

        @unlink($filePath);

        return new CraydelInternalResponseHelper(
            true,
            'File uploaded',
            (object) [
                'filename' => $fileName,
                'file_url' => sprintf(
                    config('services.files_storage.storage_server_file_cdn_path'),
                    $fileName
                ),
            ]
        );
    }

    /**
     * Upload image
     *
     *
     * @throws Exception|FilesystemException
     */
    private function toDO(string $filePath, string $fileName): CraydelInternalResponseHelper
    {
        if (file_exists($filePath) === false) {
            throw new Exception('Could not locate the file to move to the CDN');
        }

        $spaceAccessKey = config('services.files_storage.storage_key');
        $spaceSecretKey = config('services.files_storage.storage_secret');
        $spaceBucketName = config('services.files_storage.storage_bucket_name');
        $spaceRegion = config('services.files_storage.storage_server_region');
        $spaceURL = config('services.files_storage.storage_url');

        if (empty($spaceAccessKey)) {
            throw new Exception('Missing DO CDN spaces API key');
        }

        if (empty($spaceSecretKey)) {
            throw new Exception('Missing DO CDN spaces API Secret');
        }

        if (empty($spaceBucketName)) {
            throw new Exception('Missing DO CDN spaces bucket name');
        }

        if (empty($spaceRegion)) {
            throw new Exception('Missing DO CDN spaces region name');
        }

        $client = new S3Client([
            'endpoint' => $spaceURL,
            'credentials' => [
                'key' => $spaceAccessKey,
                'secret' => $spaceSecretKey,
            ],
            'region' => $spaceRegion,
            'version' => 'latest',
            'visibility' => 'public',
        ]);

        $adapter = new AwsS3V3Adapter($client, $spaceBucketName);
        $filesystem = new Filesystem($adapter);

        $filesystem->write($fileName, file_get_contents($filePath), [
            'visibility' => 'public',
        ]);

        return new CraydelInternalResponseHelper(
            true,
            'Uploaded',
            (object) [
                'status' => true,
                'filename' => $fileName,
                'file_url' => sprintf(
                    config('services.files_storage.storage_server_file_cdn_path'),
                    $fileName
                ),
            ]
        );
    }

    /**
     * Delete uploaded image
     *
     *
     * @throws FilesystemException
     * @throws Exception
     */
    public function deleteFromDO(string $file_to_delete): void
    {
        $spaceAccessKey = config('services.files_storage.storage_key');
        $spaceSecretKey = config('services.files_storage.storage_secret');
        $spaceBucketName = config('services.files_storage.storage_bucket_name');
        $spaceRegion = config('services.files_storage.storage_server_region');
        $spaceURL = config('services.files_storage.storage_url');

        if (empty($spaceAccessKey)) {
            throw new Exception('Missing CDN spaces API key');
        }

        if (empty($spaceSecretKey)) {
            throw new Exception('Missing CDN spaces API Secret');
        }

        if (empty($spaceBucketName)) {
            throw new Exception('Missing CDN spaces bucket name');
        }

        if (empty($spaceRegion)) {
            throw new Exception('Missing CDN spaces region name');
        }

        $client = new S3Client([
            'endpoint' => $spaceURL,
            'credentials' => [
                'key' => $spaceAccessKey,
                'secret' => $spaceSecretKey,
            ],
            'region' => $spaceRegion,
            'version' => 'latest',
            'visibility' => 'public',
        ]);

        $adapter = new AwsS3V3Adapter($client, $spaceBucketName);
        $filesystem = new Filesystem($adapter);

        if ($filesystem->has($file_to_delete)) {
            $filesystem->delete($file_to_delete);
        }
    }

    /**
     * Upload file
     *
     * @throws Exception
     */
    private function uploadFileStagingFolder(Request $request, string $fileFieldName): CraydelInternalResponseHelper
    {
        $file = $request->file($fileFieldName);

        if (in_array(strtolower($file->getMimeType()), (new self())->allowedImageMimeTypes) === false) {
            throw new Exception('Invalid file extension. Allowed (' . implode(',', (new self())->allowedImageMimeTypes) . ')');
        }

        $fileName = Str::random(20) . '.' . $file->getClientOriginalExtension();
        $file->move(Storage::disk('temp_folder')->path(''), $fileName);
        $newFilePath = Storage::disk('temp_folder')->path($fileName);

        return new CraydelInternalResponseHelper(
            true,
            'File has been saved',
            (object) [
                'file_path' => $newFilePath,
                'file_size' => filesize($newFilePath),
                'file_extension' => $file->getClientOriginalExtension(),
                'original_file_name' => $file->getClientOriginalName(),
            ]
        );
    }

    /**
     * Stage base64Image
     *
     * @throws Exception
     */
    private function uploadBase64FileToStagingFolder(Request $request, string $fileFieldName): CraydelInternalResponseHelper
    {
        $base64Image = $request->input($fileFieldName);
        $fileInfo = explode(';base64,', $base64Image);
        $fileExtension = str_replace('data:image/', '', $fileInfo[0]);
        $file = str_replace(' ', '+', $fileInfo[1]);
        $fileName = 'temp-file-' . Str::random(10) . '.' . $fileExtension;
        Storage::disk('temp_folder')->put($fileName, base64_decode($file));

        if (Storage::disk('temp_folder')->exists($fileName) === false) {
            throw new Exception('Unable to save the uploaded file');
        }

        $filePath = Storage::disk('temp_folder')->path($fileName);

        return new CraydelInternalResponseHelper(
            true,
            'File has been saved',
            (object) [
                'file_path' => $filePath,
                'file_size' => filesize($filePath),
                'file_extension' => pathinfo($filePath, PATHINFO_EXTENSION),
                'original_file_name' => $fileName,
            ]
        );

    }

    /**
     * Make CDN file name
     */
    private function makeCDNFileName(string $file_path): string
    {
        return CraydelHelperFunctions::makeRandomString(20) . '.' . pathinfo($file_path, PATHINFO_EXTENSION);
    }

    /**
     * @throws Exception
     */
    public function validateBase64Image(string $data): CraydelInternalResponseHelper
    {
        $raw_image_data = $data;
        $data = explode(':', $data);

        if (strcmp(CraydelHelperFunctions::toCleanString($data[0]), 'data') !== 0) {
            throw new Exception('Invalid base64 image data');
        }

        $data = explode(';', $data[1]);
        $mimeType = $data[0] ?? null;

        if (CraydelHelperFunctions::isNull($mimeType)) {
            throw new Exception('Unable get the file type');
        }

        if (in_array(strtolower($mimeType), $this->allowedImageMimeTypes) === false) {
            throw new Exception('Invalid file type uploaded (' . $mimeType . '), allowed file types: ' . implode(', ', $this->allowedImageMimeTypes));
        }

        $image_data = $data[1] ?? null;
        $image_data = CraydelHelperFunctions::toCleanString($image_data);

        if (CraydelHelperFunctions::isNull($image_data)) {
            throw new Exception('Missing image data');
        }

        $image_contents = base64_decode($image_data);

        if ($image_contents === false) {
            throw new Exception('The image data is not a valid base64 string');
        }

        $image_info = explode(';base64,', $raw_image_data);
        $image_extension = str_replace('data:image/', '', $image_info[0]);
        $image = str_replace(' ', '+', $image_info[1]);
        $image_name = 'temp-image-' . Str::random(10) . '.' . $image_extension;
        Storage::disk('temp_folder')->put($image_name, base64_decode($image));

        if (Storage::disk('temp_folder')->exists($image_name) === false) {
            throw new Exception('Unable to save the uploaded file');
        }

        return new CraydelInternalResponseHelper(
            true,
            'Image has been validated',
            (object) [
                'file_path' => Storage::disk('temp_folder')->path($image_name),
            ]
        );
    }
}
