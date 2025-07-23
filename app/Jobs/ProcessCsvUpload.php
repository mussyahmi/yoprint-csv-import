<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\FileUpload;
use App\Models\Product;
use Illuminate\Support\Facades\Log;

class ProcessCsvUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    public $upload;

    public function __construct(FileUpload $upload)
    {
        $this->upload = $upload;
    }

    public function handle()
    {
        $this->upload->update(['status' => 'processing']);

        try {
            $file = storage_path("app/uploads/{$this->upload->filename}");

            if (!file_exists($file)) {
                throw new \Exception("CSV file not found at $file");
            }

            $raw = file($file);
            $data = array_map('str_getcsv', $raw);

            if (empty($data) || count($data) < 2) {
                throw new \Exception("CSV file is empty or missing rows");
            }

            $headers = array_map([$this, 'cleanUtf8'], array_shift($data));

            foreach ($data as $index => $row) {
                // Skip empty lines
                if (count($row) === 0 || implode('', $row) === '') continue;

                $row = array_map([$this, 'cleanUtf8'], $row);

                // Handle mismatch row size
                if (count($headers) !== count($row)) {
                    Log::warning("Row column mismatch at index $index", [
                        'expected' => count($headers),
                        'actual' => count($row),
                        'row' => $row
                    ]);
                    continue;
                }

                $row = array_combine($headers, $row);

                if (!isset($row['UNIQUE_KEY'])) {
                    Log::warning("Missing UNIQUE_KEY at row index $index", ['row' => $row]);
                    continue;
                }

                Product::updateOrInsert(
                    ['unique_key' => $row['UNIQUE_KEY']],
                    [
                        'product_title' => $row['PRODUCT_TITLE'] ?? '',
                        'product_description' => $row['PRODUCT_DESCRIPTION'] ?? '',
                        'style' => $row['STYLE#'] ?? '',
                        'sanmar_mainframe_color' => $row['SANMAR_MAINFRAME_COLOR'] ?? '',
                        'size' => $row['SIZE'] ?? '',
                        'color_name' => $row['COLOR_NAME'] ?? '',
                        'piece_price' => $row['PIECE_PRICE'] ?? '',
                    ]
                );
            }

            $this->upload->update(['status' => 'completed']);
        } catch (\Exception $e) {
            Log::error('CSV Upload Failed: ' . $e->getMessage(), [
                'file' => $this->upload->filename,
                'trace' => $e->getTraceAsString(),
            ]);

            $this->upload->update(['status' => 'failed']);
        }
    }

    protected function cleanUtf8($value)
    {
        $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8'); // Normalize encoding
        $value = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $value); // Remove non-UTF characters
        return trim($value);
    }
}
