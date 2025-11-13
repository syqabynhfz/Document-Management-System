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

    /* * PERBAIKAN 1:
     * Kita atur margin halaman di sini. 
     * Mari kita beri 2.5cm di atas dan 2.5cm di bawah.
    */
    @page {
        margin-top: 2.5cm;
        margin-bottom: 2.5cm;
        margin-left: 1.5cm;
        margin-right: 1.5cm;
    }

    /* * PERBAIKAN 2:
     * Posisikan header di dalam area margin atas (-2.5cm).
    */
    header {
        position: fixed;
        top: -2cm;
        /* Mulai 2.5cm di atas halaman (masuk ke margin) */
        left: 0cm;
        right: 0cm;
        height: 2cm;
        /* Sesuaikan tinggi header Anda */
        width: 100%;
    }

    /* * PERBAIKAN 3:
     * Posisikan footer di dalam area margin bawah (-2.5cm).
    */
    footer {
        position: fixed;
        bottom: 0cm;
        /* Mulai 2.5cm di bawah halaman (masuk ke margin) */
        left: 0cm;
        right: 0cm;
        height: 1.5cm;
        /* Sesuaikan tinggi footer Anda */
        width: 100%;
    }

    /* * PERBAIKAN 4: 
     * Hapus margin-top dari main. Biarkan @page yang mengaturnya.
    */
    main {
        margin-top: 0;
    }

    /* CSS Lainnya (Sudah Benar) */
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