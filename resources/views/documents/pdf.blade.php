<!DOCTYPE html>
<html lang="id">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ $document->title }}</title>

    <style>
    body {
        font-family: 'Helvetica', 'Arial', sans-serif;
        font-size: 12px;
        line-height: 1.5;
        margin: 0;
        padding: 0;
        word-wrap: break-word;
        overflow-wrap: break-word;
    }

    /* *
     * 1. ATUR MARGIN KERTAS
     * Kita beri ruang 2.5cm di atas dan 2.5cm di bawah
     * untuk header dan footer.
    */
    @page {
        margin-top: 2.5cm;
        margin-bottom: 2.5cm;
        margin-left: 1.5cm;
        margin-right: 1.5cm;
    }

    /* *
     * 2. POSISIKAN HEADER
     * 'position: fixed' akan membuatnya muncul di setiap halaman.
     * 'top: -2.5cm' akan menempatkannya di dalam margin atas.
    */
    header {
        position: fixed;
        top: -2cm;
        /* Sesuaikan nilai ini agar pas dengan margin-top @page */
        left: 0cm;
        right: 0cm;
        height: 2.5cm;
        /* Tinggi area header */
        width: 100%;
    }

    /* *
     * 3. POSISIKAN FOOTER
     * 'position: fixed' akan membuatnya muncul di setiap halaman.
     * 'bottom: -2.5cm' akan menempatkannya di dalam margin bawah.
    */
    footer {
        position: fixed;
        bottom: -0.8cm;
        /* Sesuaikan nilai ini agar pas dengan margin-bottom @page */
        left: 0cm;
        right: 0cm;
        height: 2cm;
        /* Tinggi area footer */
        width: 100%;
    }

    /* *
     * 4. ATUR MAIN CONTENT
     * Kita beri margin 0 agar konten utama 
     * dimulai tepat di bawah margin @page.
    */
    main {
        margin-top: 0;
    }

    /* *
     * 5. PERBAIKAN PAGE-BREAK (PALING PENTING)
     * Ini mencegah dompdf memotong halaman secara otomatis
     * di tempat yang salah (seperti di antara tabel).
    */
    table,
    tr,
    td,
    th,
    img,
    p,
    div,
    h1,
    h2,
    h3,
    h4,
    li {
        page-break-inside: auto !important;
    }

    /* *
     * 6. STYLE PDF LAINNYA
    */
    .page-break {
        page-break-after: always;
    }

    img {
        max-width: 100% !important;
        height: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
        margin-bottom: 10px;
    }

    table th,
    table td {
        border: 1px solid #999;
        padding: 6px;
        text-align: left;
        vertical-align: top;
    }

    table th {
        background-color: #f2f2f2;
    }
    </style>
</head>

<body>

    @if(!empty($header))
    <header>
        {!! $header !!}
    </header>
    @endif

    @if(!empty($footer))
    <footer>
        {!! $footer !!}
    </footer>
    @endif

    <main>
        @foreach ($pages as $page)

        <div class="content">
            {!! $page->content !!}
        </div>

        @if (!$loop->last)
        <div class="page-break"></div>
        @endif

        @endforeach
    </main>

</body>

</html>