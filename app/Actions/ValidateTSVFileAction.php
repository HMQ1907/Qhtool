<?php

namespace App\Actions;

use Illuminate\Http\UploadedFile;

class ValidateTSVFileAction
{
    public function handle(UploadedFile $file, array $headers)
    {
        $errorMessages = [];
        $ext = $file->getClientOriginalExtension();
        $fileSize = config('upload.tsv.max_size');
        $fileRow = config('upload.tsv.row_limit');

        $errorMessages = $this->validateFormat($errorMessages, $file);
        $errorMessages = $this->validateExtension($errorMessages, $ext, 'tsv');
        $errorMessages = $this->validateSize($errorMessages, $file, $fileSize);
        $errorMessages = $this->validateRowLimit($errorMessages, $file, $fileRow);

        if (($handle = fopen($file, 'r')) !== false) {
            $row = fgetcsv($handle, 0, "\t");
            $errorMessages = $this->validateHeader($errorMessages, $row, $headers);

            $row = fgetcsv($handle, 0, "\t");
            $errorMessages = $this->validateContent($errorMessages, $row);
        }

        return $errorMessages;
    }

    protected function validateHeader($errorMessages, $fileHeader, $header)
    {
        if ($fileHeader != $header) {
            $errorMessages[] = 'The file header is invalid.';
        }

        return $errorMessages;
    }

    protected function validateContent($errorMessages, $fileContent)
    {
        if (!$fileContent) {
            $errorMessages[] = 'No data.';
        }

        return $errorMessages;
    }

    protected function validateFormat($errorMessages, $file)
    {
        $enc = mb_check_encoding(file_get_contents($file), 'UTF-8');

        if (!$enc) {
            $errorMessages[] = 'File format is not UTF-8.';
        }

        return $errorMessages;
    }

    protected function validateExtension($errorMessages, $extension, $format)
    {
        if ($extension != $format) {
            $errorMessages[] = 'Extension of file upload incorrect.';
        }

        return $errorMessages;
    }

    protected function validateSize($errorMessages, $file, $size)
    {
        if (filesize($file) > $size) {
            $errorMessages[] = sprintf('Size of the input file should be within %s.', $this->formatBytes($size));
        }

        return $errorMessages;
    }

    protected function validateRowLimit($errorMessages, $file, $rowLimit)
    {
        if (count(file($file)) > ($rowLimit + 1)) {
            $errorMessages[] = sprintf('Please input data less than %d rows.', number_format($rowLimit));
        }

        return $errorMessages;
    }

    protected function formatBytes($bytes, $precision = 2)
    {
        $unit = ['B', 'KB', 'MB', 'GB'];
        $exp = floor(log($bytes, 1024)) | 0;

        return round($bytes / (pow(1024, $exp)), $precision) . ' ' . $unit[$exp];
    }
}
