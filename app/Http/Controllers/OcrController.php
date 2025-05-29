<?php

namespace App\Http\Controllers;

use App\Jobs\RunOcrJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use TesseractOCR;
use Illuminate\Support\Facades\Storage;
use thiagoalessio\TesseractOCR\TesseractOCR as TesseractOCRTesseractOCR;

class OcrController extends Controller
{
    public function index()
    {
        return view('form');
    }

    public function process(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $path = $request->file('image')->store('uploads');

        $batch = Bus::batch([
            new RunOcrJob($path),
        ])->dispatch();

        return response()->json(['batch_id' => $batch->id]);
    }

    public function batchStatus(string $batchId)
    {
        $batch = Bus::findBatch($batchId);
        $text = Cache::get("ocr-result-{$batchId}");

        return response()->json([
            'progress' => $batch?->progress(), // ini persentase asli
            'finished' => $batch?->finished(),
            'failed' => $batch?->hasFailures(),
            'text' => $text,
        ]);
    }
}
