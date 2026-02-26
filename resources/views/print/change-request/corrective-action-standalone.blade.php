@php
$manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);

$allDepts = \App\Models\Department::orderBy('short_name', 'asc')->get();
$activeDepts = collect([$changeRequest->department->short_name ?? '']);

if ($changeRequest->relationLoaded('relatedDepartments')) {
foreach ($changeRequest->relatedDepartments as $rd) {
if ($rd->department) $activeDepts->push($rd->department->short_name);
}
}
if ($changeRequest->relationLoaded('actionPlans')) {
foreach ($changeRequest->actionPlans as $ap) {
if ($ap->department) $activeDepts->push($ap->department->short_name);
}
}
if ($changeRequest->relationLoaded('followUpImplementations')) {
foreach ($changeRequest->followUpImplementations as $fup) {
if ($fup->employee && $fup->employee->department) {
$activeDepts->push($fup->employee->department->short_name);
}
}
}
$activeDepts = $activeDepts->filter()->map(fn($d) => strtoupper($d))->unique()->values()->all();

$itemsList = [];
foreach ($allDepts as $dept) {
$itemsList[] = [
'label' => $dept->short_name,
'checked' => in_array(strtoupper($dept->short_name), $activeDepts),
];
}
$itemsList[] = ['label' => 'Tim Halal', 'checked' => false];
$itemsList[] = ['label' => 'Pihak ketiga .....................', 'checked' => false];

$totalItems = count($itemsList);
$colsPerRow = ceil($totalItems / 3);
@endphp
<!DOCTYPE html>
<html>
<style>
    @page {
        size: A4 portrait;
        /*
             * This must be >= the rendered height of the DISTRIBUSI box
             * so DomPDF stops normal-flow content before DISTRIBUSI begins.
             * position:fixed bottom:0 sits at the bottom of the content area.
             */
        margin-bottom: 3.5cm !important;
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

    /*
         * position:fixed in DomPDF = repeat on every page.
         * background:white covers any content that could bleed underneath.
         */
    .distribusi-fixed {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: #fff;
    }
</style>

<head>
    <meta charset="utf-8">
    <title>{{ $changeRequest->title }}</title>
    <link rel="stylesheet" href="{{ public_path('assets/css/lib/bootstrap.min.css') }}">
</head>

<body style="margin-top: 3.2mm;">
    {{-- Table first, then the fixed footer — source order matters for DomPDF --}}
    @include('print.change-request.partials.corrective-action')

    <div class="distribusi-fixed">
        <div style="border: 1px solid #000; padding: 8px; font-size: 10pt;">
            <div style="font-weight: bold; margin-bottom: 6px;">DISTRIBUSI :</div>
            <table style="width: 100%; border: none; border-collapse: collapse;">
                @for ($rowIndex = 0; $rowIndex < 3; $rowIndex++)
                    <tr>
                    @for ($colIndex = 0; $colIndex < $colsPerRow; $colIndex++)
                        @php $currentIndex=($rowIndex * $colsPerRow) + $colIndex; @endphp
                        <td style="border: none; padding: 3px 10px 3px 0; vertical-align: middle;">
                        @if (isset($itemsList[$currentIndex]))
                        @php
                        $item = $itemsList[$currentIndex];
                        $boxContent = $item['checked'] ? 'X' : '&nbsp;';
                        @endphp
                        <table style="border: none; border-collapse: collapse; margin: 0; padding: 0; width: auto;">
                            <tr>
                                <td style="border: none; padding: 0 6px 0 0; vertical-align: middle;">
                                    <div style="width: 14px; height: 14px; border: 1px solid #000; text-align: center; line-height: 12px; font-weight: bold; font-size: 10pt; background: #fff;">{!! $boxContent !!}</div>
                                </td>
                                <td style="border: none; padding: 0; vertical-align: middle; white-space: nowrap; font-size: 10pt;">{{ $item['label'] }}</td>
                            </tr>
                        </table>
                        @endif
                        </td>
                        @endfor
                        </tr>
                        @endfor
            </table>
        </div>
    </div>
</body>

</html>