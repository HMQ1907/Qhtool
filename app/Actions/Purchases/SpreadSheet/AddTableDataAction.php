<?php

namespace App\Actions\Purchases\SpreadSheet;

use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AddTableDataAction
{
    public function handle(Worksheet $sheet, array $headers, $data, int &$row, bool $isDisplayTotalAmount = false, array $alphabet = []): void
    {
        $lastKey = array_key_last($data);
        $startRow = $row;
        $endRow = $row;

        foreach ($data as $key => $record) {
            if ($key == $lastKey) {
                $endRow = $row;
            }

            foreach ($headers as $index => $header) {
                $sheet->getStyle("{$alphabet[$index]}{$row}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

                if ($key === $lastKey) {
                    $sheet->getStyle("{$alphabet[$index]}{$row}")->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_DOTTED,
                                'color' => ['argb' => 'FF000000'],
                            ],
                            'bottom' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['argb' => 'FF000000'],
                            ],
                        ],
                    ]);
                } else {
                    $sheet->getStyle("{$alphabet[$index]}{$row}")->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_DOTTED,
                                'color' => ['argb' => 'FF000000'],
                            ],
                        ],
                    ]);
                }

                if (in_array($header['mapping'], ['unit_price', 'quantity', 'amount'])) {
                    $sheet->getStyle("{$alphabet[$index]}{$row}")->getNumberFormat()->setFormatCode('#,##0');
                }

                if ($header['mapping'] === 'unit_price') {
                    $sheet->getStyle("{$alphabet[$index]}{$row}")->getFill()->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setARGB('6fa8dc');
                }

                if (in_array($header['mapping'], ['no', 'quantity'])) {
                    $sheet->getStyle("{$alphabet[$index]}{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }

                if ($header['mapping'] === 'no') {
                    $sheet->setCellValue("{$alphabet[$index]}{$row}", $key + 1);
                } elseif ($header['mapping'] == 'image') {
                    $currentHeight = $sheet->getRowDimension($row)->getRowHeight();

                    if ($currentHeight < 60) {
                        $sheet->getRowDimension($row)->setRowHeight(60);
                    }

                    $value = $record[$header['mapping']];
                    $this->addImage(
                        $sheet,
                        $value,
                        "{$alphabet[$index]}{$row}",
                        56,
                        (int)($header['width'] ?? 10),
                    );
                } else {
                    $sheet->setCellValue("{$alphabet[$index]}{$row}", $record[$header['mapping']] ?? '');

                    if ($header['mapping'] == 'order_number' || $header['mapping'] == 'scm_code') {
                        $sheet->getStyle("{$alphabet[$index]}{$row}")->getAlignment()->setWrapText(true);
                    }
                }
            }

            $row++;
        }

        $index = array_search('AMOUNT', array_column($headers, 'title'));

        $totalWidth = 0;
        $column = '';
        $hasAmountColumn = ($index !== false);
        $hasDataRows = !empty($data);
        $amountEndIndex = $hasAmountColumn ? $index - 1 : (count($headers) - 1);

        for ($i = $amountEndIndex; $i >= 0; $i--) {
            if (isset($headers[$i])) {
                $totalWidth += $headers[$i]['width'];

                if ($totalWidth >= 13) {
                    $column = $alphabet[$i];
                    break;
                }
            }
        }

        if ($isDisplayTotalAmount) {
            if (!empty($column)) {
                $sheet->mergeCells(("{$column}{$row}:{$alphabet[$amountEndIndex]}{$row}"));
                $sheet->getStyle("{$column}{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("{$column}{$row}")->getFont()->setBold(true);
                $sheet->setCellValue("{$column}{$row}", "Total Amount");
            }

            if ($hasAmountColumn) {
                $colAmount = $alphabet[$index];
                $sheet->getStyle("{$colAmount}{$row}")->applyFromArray([
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => Border::BORDER_DOUBLE,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ]);
                $sheet->getStyle("{$colAmount}{$row}")->getNumberFormat()->setFormatCode('#,##0');

                if ($hasDataRows) {
                    $sheet->setCellValue("{$colAmount}{$row}", "=SUM({$colAmount}{$startRow}:{$colAmount}{$endRow})");
                } else {
                    $sheet->setCellValue("{$colAmount}{$row}", "");
                }
            }
        }

        $row++;
    }

    protected function addImage(Worksheet $sheet, string $url, string $cell, int $targetHeight = 56, int $columnWidthChars = 10): void
    {
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 300);
        
        $context = stream_context_create([
            'http' => [
                'ignore_errors' => true,
                'timeout'       => 30,
            ],
        ]);

        $parts = parse_url($url);
        $path = implode('/', array_map('rawurlencode', explode('/', $parts['path'])));
        $url = $parts['scheme'] . '://' . $parts['host'] . $path;
        $imageData = file_get_contents($url, false, $context);

        if (!$imageData || !($img = imagecreatefromstring($imageData))) {
            return;
        }

        $origW = imagesx($img) ?: 1;
        $origH = imagesy($img) ?: 1;

        $colPx = (int)($columnWidthChars * 7.5);
        $rowPx = preg_match('/^[A-Z]+(\d+)$/', $cell, $m)
            ? (int)(($sheet->getRowDimension((int)$m[1])->getRowHeight() ?: 60) * 96 / 72)
            : 80;

        $padding = 5;
        $scale = min(
            ($colPx - $padding * 2) / $origW,
            ($rowPx - $padding * 2) / $origH,
            $targetHeight / $origH
        );

        $scaledW = max(1, (int)($origW * $scale));
        $scaledH = max(1, (int)($origH * $scale));

        $bytes = is_string($imageData) ? strlen($imageData) : 0;
        $pixelCount = $origW * $origH;
        $shouldResample = ($bytes > 1500000) || ($pixelCount > 900000);

        if ($shouldResample) {
            $resampled = imagecreatetruecolor($scaledW, $scaledH);
            imagealphablending($resampled, false);
            imagesavealpha($resampled, true);
            $transparent = imagecolorallocatealpha($resampled, 255, 255, 255, 127);
            imagefilledrectangle($resampled, 0, 0, $scaledW, $scaledH, $transparent);
            imagecopyresampled($resampled, $img, 0, 0, 0, 0, $scaledW, $scaledH, $origW, $origH);
            imagedestroy($img);
            $img = $resampled;
        }

        $drawing = new MemoryDrawing();
        $drawing->setImageResource($img)
            ->setRenderingFunction(MemoryDrawing::RENDERING_PNG)
            ->setMimeType(MemoryDrawing::MIMETYPE_DEFAULT)
            ->setHeight($scaledH)
            ->setCoordinates($cell)
            ->setOffsetX((int)(($colPx - $scaledW) / 2) - 3)
            ->setOffsetY((int)(($rowPx - $scaledH) / 2))
            ->setWorksheet($sheet);
    }
}
