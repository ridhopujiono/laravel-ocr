<div wire:poll.1s="pollProgress" class="flex flex-col md:flex-row justify-between items-stretch gap-6">
    <div class="flex-1 border-2 border-dashed border-blue-400 rounded-lg p-6 text-center">
        <label for="image" class="cursor-pointer block">
            <div class="flex flex-col items-center justify-center space-y-2">
                <svg class="w-12 h-12 text-blue-500" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path d="M12 4v16m8-8H4" />
                </svg>
                <p class="text-lg font-semibold">{{ __('messages.upload_hint') }}</p>
                <span
                    class="mt-2 inline-block bg-blue-600 text-white px-4 py-2 rounded cursor-pointer hover:bg-blue-700">
                    {{ __('messages.choose_file') }}
                </span>
            </div>
            <input type="file" wire:model="image" id="image" class="hidden" />
        </label>
        @error('image')
            <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
        @enderror
        <p class="text-sm text-gray-400 mt-2">Supports JPG, JPEG, PNG (max 2MB)</p>
    </div>

    <div class="flex-1 border bg-gray-50 p-6 rounded-lg relative">
        <div class="flex justify-between mb-3">
            <h4 class="font-semibold">{{ __('messages.result_title') }}</h4>
            @if ($text)
                <button onclick="navigator.clipboard.writeText(`{{ $text }}`)"
                    class="bg-transparent hover:bg-blue-500 text-gray-700 hover:text-white py-1 px-3 border border-blue-500 hover:border-transparent rounded">
                    {{ __('messages.copy') }}
                </button>
            @endif
        </div>

        @if ($loading)
            <div class="absolute inset-0 bg-white bg-opacity-80 flex items-center justify-center rounded-lg">
                <div class="text-center">
                    <div class="relative w-48 h-4 bg-gray-200 rounded-full overflow-hidden mb-2">
                        <div class="absolute top-0 left-0 h-full bg-blue-600 transition-all duration-300"
                            style="width: {{ $progress }}%"></div>
                    </div>
                    <p class="text-sm text-gray-600">Processing... {{ $progress }}%</p>
                </div>
            </div>
        @endif

        @if ($text)
            <div class="text-sm text-gray-800 h-60 overflow-y-auto bg-white p-4 border rounded">
                {{ $text }}
            </div>
        @endif
    </div>
</div>
