{{-- @php
    $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);
@endphp --}}
<!DOCTYPE html>
<html>
    <style>
        @page {
            size: A4 portrait;
            margin-bottom: 2.5cm !important;
            font-size: 1px !important;

        }

        @page landscape {
            size: A4 landscape;
            margin: 0.5cm;
            font-size: 1px !important;
        }

        @media print {
            body {
                margin: 0;

            }

            /* jangan pakai height / min-height halaman */
            .page-break {
                page-break-before: always;
                break-before: page;
            }

            /* BIAR KONTEN BOLEH LANJUT */
            table {
                page-break-inside: auto;
            }

            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }

            .landscape-page {
                page: landscape;
            }
        }

        /* TABLE */
        .print-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
            line-height: 1.3;
        }

        .print-table td,
        .print-table th {
            border: 1px solid #000;
            padding: 6px;
            vertical-align: top;
        }

        .no-border,
        .no-border td,
        .no-border th {
            border: none !important;
            padding: 6px !important;
        }
    </style>

    <head>
        <meta charset="utf-8">
        <title>{{ $changeRequest->title }}</title>
        <link rel="stylesheet" href="{{ public_path('assets/css/lib/bootstrap.min.css') }}">
        <link rel="icon" type="image/png" href="{{ public_path('assets/images/favicon.png') }}" sizes="16x16">
    </head>

    <body style="font-size : 10pt;">

        {{-- ================= HALAMAN 1 ================= --}}
        <div class="print-page">
            <div style="text-align: center; margin-bottom: 0.5cm; line-height: 1;">
                @include('print.change-request.partials.change-initiation-content')
            </div>
        </div>
    </body>

</html>
