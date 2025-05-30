<?php
namespace App\Livewire;

use App\Jobs\RunOcrJob;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class OcrForm extends Component
{
    use WithFileUploads;

    public $image;
    public $text;
    public $progress = 0;
    public $batchId;
    public $loading = false;

    public function updatedImage()
    {
        $this->validate([
            'image' => 'image|max:2048|mimes:jpeg,jpg,png',
        ]);

        if (!auth()->check()) {
            return redirect('/auth/google');
        }

        $path = $this->image->store('uploads');

        $batch = Bus::batch([
            new RunOcrJob($path),
        ])->dispatch();

        $this->batchId = $batch->id;
        $this->loading = true;
    }

    public function pollProgress()
    {
        if (!$this->batchId) return;

        $batch = Bus::findBatch($this->batchId);
        $this->progress = $batch?->progress() ?? 0;

        if ($batch?->finished()) {
            $this->text = Cache::get("ocr-result-{$this->batchId}");
            $this->loading = false;
        }

        if ($batch?->hasFailures()) {
            $this->text = '[❌] Gagal memproses gambar.';
            $this->loading = false;
        }
    }

    public function render()
    {
        return view('livewire.ocr-form');
    }
}
