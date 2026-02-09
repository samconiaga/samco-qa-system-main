@php
    $masterItems = [
        'DPI-PPI PP',
        'DPI-PPI KP',
        'DPI-PPI KS',
        'Formula',
        'Spesifikasi',
        'Stabilitas',
        'Transfer Metode',
        'Daftar Pemasok',
        'Kualifikasi Vendor',
        'Final Artwork',
        'Uji BE',
        'Uji Disolusi Terbanding',

        'Registrasi Baru',
        'Registrasi Variasi',
        'URS',
        'KD',
        'KI',
        'KO',
        'KK',
        'Validasi Proses',
        'Validasi Metode Analisa',
        'Validasi Pembersihan',
        'Validasi Aseptis',
        'Validasi Sistem Komputer',

        'Training',
        'Struktur Organisasi',
        'Job desk',
        'Manual Mutu',
        'PM',
        'PK',
        'FO',
        'DA',
        'GA',
        'Jadwal Maintenance',
        'SMF',
        'Perizinan',

        'IBPR',
        'IADL',
        'RBT',
        'SPP disetujui',
    ];
    $dbItems = $changeRequest->actionPlans
        ->pluck('impactCategory.impact_of_change_category')
        ->filter()
        ->unique()
        ->values()
        ->toArray();

    $extraItems = array_diff($dbItems, $masterItems);
@endphp
<div>
    <table class="print-table" style="width: 100%; border-collapse: collapse; margin-top: 0.2cm;">
        <thead>
            <tr>
                <th colspan="6" style="text-align: left;">
                    J. {{ __('Analysis of affected documents') }}
                </th>
            </tr>
        </thead>
        <tbody>
            @php
                $colCount = 3;
                $perCol = ceil(count($masterItems) / $colCount);

                $columns = [];
                for ($i = 0; $i < $colCount; $i++) {
                    $columns[] = array_slice($masterItems, $i * $perCol, $perCol);
                }

                $maxRows = max(array_map('count', $columns));
            @endphp

            @for ($row = 0; $row < $maxRows; $row++)
                <tr>
                    @for ($col = 0; $col < $colCount; $col++)
                        @php $item = $columns[$col][$row] ?? null; @endphp

                        <td style="width:20px; border:1px solid #000; text-align:center;">
                            @if ($item && in_array($item, $dbItems))
                                X
                            @endif
                        </td>
                        <td style="padding:4px 6px;">
                            {{ $item }}
                        </td>
                    @endfor
                </tr>
            @endfor

            {{-- EXTRA ITEMS --}}
            @foreach ($extraItems as $extra)
                <tr>
                    <td style="width:20px; border:1px solid #000; text-align:center;">X</td>
                    <td colspan="5" style="padding:4px 6px;">
                        {{ $extra }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</div>
