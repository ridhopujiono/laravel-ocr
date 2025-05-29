<?php

namespace App\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use thiagoalessio\TesseractOCR\TesseractOCR as TesseractOCRTesseractOCR;

class RunOcrJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function handle(): void
    {
        try {
            $ocr = new TesseractOCRTesseractOCR(storage_path('app/private/' . $this->path));
            $ocr->lang('eng', 'ind');
            $text = $ocr->run();

            // Bersihkan hasil OCR
            $cleanedText = preg_replace([
                '/\|+/',           // hapus semua tanda |
                '/\n{2,}/',        // ubah >1 newline jadi 1 newline
                '/[ ]{2,}/',       // hilangkan spasi berlebihan
            ], ["", "\n", " "], $text);

            $cleanedText = trim($cleanedText); // hapus spasi awal/akhir

            Cache::put('ocr-result-' . $this->batchId, $cleanedText, now()->addMinutes(10));
        } catch (\Throwable $e) {
            // Jika error, log biar tidak silent
            Log::error("OCR failed for {$this->path}: " . $e->getMessage());

            // Opsional: throw agar batch dianggap gagal
            throw $e;
        }
    }
}
