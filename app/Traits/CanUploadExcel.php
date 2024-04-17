<?php

declare(strict_types=1);

namespace App\Traits;

use App\Http\Controllers\Helpers\CraydelHelperFunctions;
use App\Http\Controllers\Helpers\CraydelInternalResponseHelper;
use App\Http\Controllers\Helpers\CreateExcelTemplateFileHelper;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

trait CanUploadExcel
{
    use CanLog;

    /**
     * @var array $column_control
     */
    protected array $column_control = [
        'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'
    ];

    /**
     * @var array $allowedMimeTypes
     */
    protected array $allowedExcelMimeTypes = [
        'application/xml', 'application/vnd.ms-excel', 'application/msexcel', 'application/x-msexcel', 'application/x-ms-excel',
        'application/x-excel', 'application/x-dos_ms_excel', 'application/xls', 'application/x-xls',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/octet-stream'
    ];

    /**
     * @var array $allowedFileExtensions
     */
    protected array $allowedFileExtensions = [
        'xls', 'xlsx', 'csv'
    ];

    /**
     * Verify the uploaded file
     *
     * @param Request $request
     * @param string $field_name
     * @return bool
     * @throws Exception
     */
    public function verifyUploadedExcelFile(Request $request, string $field_name): bool
    {
        if (!$request->file($field_name)) {
            $attribute = CraydelHelperFunctions::capitalizeTheFirstLetterOfTheFirstWord(str_replace("_", " ", $field_name));
            throw new Exception(__("Missing {$attribute} in the request."));
        }

        $file = $request->file($field_name);
        $file_mime_type = $file->getClientMimeType();
        $file_mime_type = CraydelHelperFunctions::toCleanString(strtolower($file_mime_type));

        if (!in_array($file_mime_type, $this->allowedExcelMimeTypes)) {
            throw new Exception(__("Invalid file type uploaded. You uploaded " . $file_mime_type));
        }

        if (!in_array($file->getClientOriginalExtension(), $this->allowedFileExtensions)) {
            throw new Exception(__("Invalid file extension. You uploaded " . $file->getClientOriginalExtension()));
        }

        $courses_file_size = $file->getSize();
        $file_size_in_mbs = CraydelHelperFunctions::convertBytesToMBs($courses_file_size);

        $maximum_allowed = config('app.security.maximum_uploaded_file_size', 20);

        if ($file_size_in_mbs > $maximum_allowed) {
            throw new Exception(__("The uploaded file is too big. You uploaded : " . $file_size_in_mbs . ", we allow " . $maximum_allowed));
        }

        return true;
    }

    /**
     * Upload the file
     *
     * @param Request $request
     * @param string $field_name
     * @return string
     */
    public function uploadExcelFile(Request $request, string $field_name): string
    {
        $file = $request->file($field_name);
        $staged_files_path = storage_path() . DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR;
        $course_file_name = CraydelHelperFunctions::makeRandomString(20, null, true) . '.' . $file->getClientOriginalExtension();

        $file_mime_type = $file->getClientMimeType();
        $file_mime_type = CraydelHelperFunctions::toCleanString(strtolower($file_mime_type));

        if ($file_mime_type === 'application/octet-stream') {
            Storage::disk('temp_folder')->put($course_file_name, File::get($file->getrealpath()));
        } else {
            $file->move($staged_files_path, $course_file_name);
        }

        return $staged_files_path . $course_file_name;
    }

    /**
     * Upload the file
     * @param string $file_path
     * @param array $expected_headers
     * @param string $sheet_name
     * @param int $row_index
     * @return CraydelInternalResponseHelper
     */
    public function validateHeadersOnHeavySheets(string $file_path, array $expected_headers, string $sheet_name, int $row_index = 0): CraydelInternalResponseHelper
    {
        try {
            $expected_headers = array_map('strtolower', $expected_headers);
            return CreateExcelTemplateFileHelper::validateHeadersOnHeavySheets($file_path, $expected_headers, $sheet_name, $row_index);
        } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception|\PhpOffice\PhpSpreadsheet\Exception|Exception $e) {
            $this->logException($e);

            return new CraydelInternalResponseHelper(
                false,
                $e->getMessage()
            );
        }
    }

    /**
     * Upload the file
     *
     * @param string $file_path
     * @param array $expected_headers
     * @return CraydelInternalResponseHelper
     */
    public function confirmExcelFileHeaders(string $file_path, array $expected_headers): CraydelInternalResponseHelper
    {
        try {
            return CreateExcelTemplateFileHelper::validateFileHeaders($file_path, $expected_headers);
        } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception|\PhpOffice\PhpSpreadsheet\Exception|Exception $e) {
            $this->logException($e);

            return new CraydelInternalResponseHelper(
                false,
                $e->getMessage()
            );
        }
    }

    /**
     * @param string $file_path
     * @param array $expected_headers
     * @param string $sheet_name
     * @param int $row_index
     * @return CraydelInternalResponseHelper
     */
    protected function validateRequirementsHeaders(string $file_path, array $expected_headers, string $sheet_name, int $row_index = 0): CraydelInternalResponseHelper
    {
        try {
            $expected_headers = array_map('strtolower', $expected_headers);
            return CreateExcelTemplateFileHelper::validateRequirementsHeaders($file_path, $expected_headers, $sheet_name, $row_index);
        } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception|Exception $e) {
            $this->logException($e);

            return new CraydelInternalResponseHelper(
                false,
                $e->getMessage()
            );
        }
    }

    /**
     * Read the file data
     *
     * @param string $file_path
     * @param array $fileHeaders
     * @return array
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function readExcelFile(string $file_path, array $fileHeaders): array
    {
        $inputFileType = IOFactory::identify($file_path);
        $reader = IOFactory::createReader($inputFileType);
        $reader->setReadDataOnly(true);
        $reader->setReadEmptyCells(false);
        $reader->setLoadSheetsOnly('Data');
        $spreadsheet = $reader->load($file_path);
        $data = $spreadsheet->getActiveSheet()->toArray();
        unset($data[0]);

        @unlink($file_path);

        return collect($data)
            ->map(function ($line) use ($fileHeaders) {
                return CraydelHelperFunctions::changeMultipleArrayKeys(
                    $line,
                    $fileHeaders
                );
            })->toArray();
    }

    /**
     * Open Excel file
     * @param string $file_path
     * @return Spreadsheet
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function openExcelFile(string $file_path): Spreadsheet
    {
        $inputFileType = IOFactory::identify($file_path);
        $reader = IOFactory::createReader($inputFileType);
        return $reader->load($file_path);
    }

    /**
     * Write Excel file
     * @param Spreadsheet $spreadsheet
     * @param array $data
     * @param string $updated_file_path
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function writeIntoExcelFile(Spreadsheet $spreadsheet, array $data, string $updated_file_path): void
    {
        $sheet = $spreadsheet->getSheet(0);
        $current_data = $sheet->toArray();

        $current_data = collect($current_data)
            ->reject(function ($line) {
                return CraydelHelperFunctions::isNull($line[0]);
            });

        $start_row_index = $current_data->count() > 0 ? $current_data->count() + 1 : 1;
        $predicted_rows = $this->makeFileColumnsBasedOnDataColumns($data, $start_row_index);

        foreach ($data as $key => $datum) {
            if (is_array($datum)) {
                $row = $predicted_rows[$key];
                $datum = array_values($datum);

                foreach ($datum as $datum_key => $datum_val) {
                    $sheet->setCellValue($row[$datum_key], $datum_val);
                }
            }
        }

        for ($i = 1; $i <= count(array_keys($data[0])); $i++) {
            $cell_number = CraydelHelperFunctions::convertNumberToAlphabet($i);

            if (!empty($cell_number)) {
                $sheet
                    ->getColumnDimension($cell_number)
                    ->setAutoSize(true);
            }
        }

        for ($j = 1; $j <= count(array_keys($data[0])); $j++) {
            $sheet
                ->getStyle(CraydelHelperFunctions::convertNumberToAlphabet($j))
                ->getAlignment()
                ->setWrapText(true)
                ->setHorizontal(Alignment::HORIZONTAL_LEFT)
                ->setVertical(Alignment::VERTICAL_TOP);
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save($updated_file_path);
    }

    /**
     * Make file column indexes
     *
     * @param array $data
     * @param int $starting_row
     * @return array
     */
    public function makeFileColumnsBasedOnDataColumns(array $data, int $starting_row = 0): array
    {
        $column_count = count(array_keys($data[0]));
        $rows = [];
        $data_count = $starting_row > 0 ? ((count($data) + $starting_row) - 1) : (count($data) - 1);

        if ($column_count <= 26) {
            for ($i = $starting_row; $i <= $data_count; $i++) {
                $child_row = [];

                foreach ($this->column_control as $key => $value) {
                    $child_row[] = $value . "" . $i;
                }

                $rows[] = $child_row;
            }
        }

        return $rows;
    }
}
