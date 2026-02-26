@php
$manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);
@endphp
<!DOCTYPE html>
<html>
<style>
    @page {
        size: A4 portrait;
        margin-bottom: 2.5cm !important;
        font-size: 1px !important;
    }

    @media print {
        body {
            margin: 0;
        }

        table {
            page-break-inside: auto;
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
    }

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
</head>

<body style="margin-top: 3.2mm;">
    @include('print.change-request.partials.last-verification')
</body>

</html>