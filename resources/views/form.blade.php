<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>OCR Pro - {{ __('messages.app_description') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="">

    <!-- Navbar -->
    <header class="bg-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-blue-600">{{ __('messages.app_name') }}</h1>

            <nav class="space-x-6 text-gray-600 font-medium flex items-center">
                {{-- Button Login as google with google icon --}}
                @if (auth()->check())
                    <div class="flex items-center space-x-4">
                        <span class="text-gray-700 font-medium flex gap-2">
                            <img class="w-6 h-6 rounded-full" src="{{ auth()->user()->avatar }}">
                            {{ auth()->user()->name }}</span>
                        <span class="text-gray-700 font-light">|</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="py-2 rounded hover:text-red-700 text-red-600">
                                {{ __('messages.logout') }}
                            </button>
                        </form>
                    </div>
                @else
                    <a href="/auth/google"
                        class="flex px-4 py-2 border gap-2 border-slate-200 dark:border-slate-700 rounded-lg hover:border-slate-400 dark:hover:border-slate-500">
                        <img class="w-6 h-6" src="https://www.svgrepo.com/show/475656/google-color.svg" loading="lazy"
                            alt="google logo">
                        <span>{{ __('messages.login_with_google') }}</span>
                    </a>
                @endif

                <!-- Language switcher -->
                <a href="/lang/id" class="hover:text-blue-600">🇮🇩</a>
                <a href="/lang/en" class="hover:text-blue-600">🇬🇧</a>
            </nav>

        </div>
    </header>

    <!-- Hero Section -->
    <section class="text-center py-20 px-4 bg-gradient-to-b from-blue-50 to-white text-gray-800 font-sans">
        <p class="text-sm text-blue-600 font-semibold mb-2">⚡ Powered by Laravel & Tesseract</p>
        <h2 class="text-4xl md:text-5xl font-bold mb-4">{{ __('messages.hero_title') }} <span
                class="text-blue-600">{{ __('messages.hero_title_1') }}</span></h2>
        <p class="max-w-2xl mx-auto text-gray-600 text-lg">{{ __('messages.hero_description') }}</p>
        <div class="mt-8">
            <a href="#upload"
                class="px-6 py-3 bg-blue-600 text-white rounded-lg text-lg hover:bg-blue-700">{{ __('messages.try_now') }}</a>
        </div>
    </section>

    <!-- Divider -->


    <!-- Upload Section -->
    <section>
        <div id="upload" class="max-w-6xl mx-auto px-4 py-10 pb-20 bg-white rounded-xl">
            <h3 class="text-2xl font-bold mb-6 text-center">{{ __('messages.upload_title') }}</h3>
            <form id="ocr-form" class="flex flex-col md:flex-row justify-between items-stretch gap-6">
                @csrf
                <div class="flex-1 border-2 border-dashed border-blue-400 rounded-lg p-6 text-center">
                    <label for="image" class="cursor-pointer block">
                        <div class="flex flex-col items-center justify-center space-y-2">
                            <svg class="w-12 h-12 text-blue-500" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path d="M12 4v16m8-8H4" />
                            </svg>
                            <p class="text-lg font-semibold">{{ __('messages.upload_hint') }}</p>
                            {{-- <p class="text-gray-500 text-sm">or click to browse from your device</p> --}}
                            <span
                                class="mt-2 inline-block bg-blue-600 text-white px-4 py-2 rounded cursor-pointer hover:bg-blue-700">{{ __('messages.choose_file') }}</span>
                        </div>
                        <input type="file" id="image" name="image" accept="image/*" class="hidden" required>
                    </label>
                    <p class="text-sm text-gray-400 mt-2">Supports JPG, JPEG, PNG (max 2MB)</p>
                </div>

                <div class="flex-1 border bg-gray-50 p-6 rounded-lg relative">
                    <div class="flex justify-between mb-3">
                        <h4 class="font-semibold">{{ __('messages.result_title') }}</h4>
                        <button id="copy-btn" type="button"
                            class="hidden bg-transparent hover:bg-blue-500 text-gray-700 hover:text-white py-1 px-3 border border-blue-500 hover:border-transparent rounded">
                            {{ __('messages.copy') }}
                        </button>
                    </div>

                    <!-- Progress shown here -->
                    <div id="progress-container"
                        class="absolute inset-0 bg-white bg-opacity-80 flex items-center justify-center rounded-lg hidden">
                        <div class="text-center">
                            <div class="relative w-48 h-4 bg-gray-200 rounded-full overflow-hidden mb-2">
                                <div id="progress-bar"
                                    class="absolute top-0 left-0 h-full bg-blue-600 transition-all duration-300"
                                    style="width: 0%"></div>
                            </div>
                            <p id="progress-text" class="text-sm text-gray-600">{{ __('messages.processing') }} %</p>
                        </div>
                    </div>

                    <div id="result"
                        class="text-sm whitespace-pre-wrap text-gray-800 h-60 overflow-y-auto hidden bg-white p-4 border rounded">
                    </div>
                </div>

            </form>

        </div>
    </section>

    <script>
        window.isLoggedIn = {{ auth()->check() ? 'true' : 'false' }};
    </script>


    <script>
        const form = document.getElementById('ocr-form');
        const imageInput = document.getElementById('image');
        const resultDiv = document.getElementById('result');
        const progressContainer = document.getElementById('progress-container');
        const progressBar = document.getElementById('progress-bar');
        const progressText = document.getElementById('progress-text');
        const copyBtn = document.getElementById('copy-btn');

        imageInput.addEventListener('change', async function() {

            // ⛔ Jika belum login, redirect ke Google Auth
            if (!window.isLoggedIn) {
                window.location.href = "/auth/google";
                return;
            }
            // Reset UI
            const formData = new FormData();
            formData.append('image', imageInput.files[0]);

            progressContainer.classList.remove('hidden');
            resultDiv.classList.add('hidden');
            resultDiv.innerText = "";
            progressBar.style.width = '0%';
            progressText.innerText = 'Processing... 0%';
            copyBtn.classList.add('hidden');

            // Upload dan proses
            const response = await fetch("{{ route('ocr.process') }}", {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: formData
            });

            const {
                batch_id
            } = await response.json();

            // Mulai polling
            const interval = setInterval(async () => {
                const status = await fetch(`/batch-status/${batch_id}`);
                const json = await status.json();

                // Update progress real dari Laravel
                const progress = json.progress ?? 0;
                progressBar.style.width = `${progress}%`;
                progressText.innerText = `Processing... ${progress}%`;

                if (json.finished || json.failed) {
                    clearInterval(interval);

                    progressBar.style.width = `100%`;
                    progressText.innerText = json.failed ?
                        '❌ Gagal memproses gambar.' :
                        '✅ Selesai.';

                    setTimeout(() => {
                        progressContainer.classList.add('hidden');
                        resultDiv.classList.remove('hidden');
                        resultDiv.innerText = json.text || '[No text found]';

                        if (!json.failed) {
                            copyBtn.classList.remove('hidden');
                        }
                    }, 500);
                }
            }, 1000);
        });

        // Copy button logic
        copyBtn.addEventListener('click', () => {
            navigator.clipboard.writeText(resultDiv.innerText)
                .then(() => {
                    copyBtn.innerText = '{{ __('messages.copied') }}';
                    setTimeout(() => {
                        copyBtn.innerText = '{{ __('messages.copy') }}';
                    }, 1500);
                })
                .catch(() => {
                    alert('Gagal menyalin teks.');
                });
        });
    </script>


</body>

</html>
