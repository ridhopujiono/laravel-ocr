<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>OCR Pro - {{ __('messages.app_description') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>

    @livewireStyles
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
        <livewire:ocr-form />
    </section>

    {{-- <script>
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
    </script> --}}


    @livewireScripts

</body>

</html>
