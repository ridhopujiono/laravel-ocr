<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hasil OCR</title>
</head>
<body>
    <h2>Hasil Ekstraksi Teks</h2>
    <p><strong>Nama File:</strong> {{ $filename }}</p>

    <textarea rows="20" cols="100">{{ $text }}</textarea>
    <br><br>
    <a href="{{ route('ocr.form') }}">← Kembali</a>
</body>
</html>
