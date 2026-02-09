@php
    $managerSign = $changeRequest->approvals
        ->where('stage', 'Approve Manager')
        ->where('decision', 'Approved')
        ->sortByDesc('updated_at')
        ->first();

    $selectedScopes = collect($changeRequest->scopeOfChange ?? [])
        ->pluck('name')
        ->toArray();
    $allScopes = ['Quality', 'Severity', 'Environment'];

    $masterTypes = [
        'Continual improvement / Inovasi',
        'Transfer Teknologi',
        'Peralatan / Mesin',
        'Supplier',
        'Alih Daya / Perubahan Kepemilikan Produk',
        'Aspek Pembuatan Produk',
        'Penyesuaian Regulasi / Monografi',
        'Perubahan Proses Bisnis',
        'Relokasi Fasilitas / Pabrik',
        'Dokumen',
    ];

    $selectedTypes = collect($changeRequest->typeOfChange ?? [])
        ->pluck('type_name')
        ->toArray();

    $extraTypes = array_diff($selectedTypes, $masterTypes);

    $allTypes = array_merge($masterTypes, $extraTypes);
@endphp

<div style="margin-top: 0.5cm">
    {{-- === Header Nomor Usulan === --}}
    <table class="print-table" style="width: 100%">
        <tbody>
            <tr>
                <td colspan="2">
                    <b>{{ __('Change Request Number') }}</b> :
                    {{ $changeRequest->request_number ?? '-' }}
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <b> A. {{ __('Change Title') }}</b> :
                    {{ $changeRequest->title ?? '-' }}
                </td>
            </tr>

            <tr>
                <td style="width: 50%">
                    <table class="no-border">
                        <tbody>
                            <tr>
                                <td style="width: 40%">{{ __('Date') }}</td>
                                <td style="width: 5%">:</td>
                                <td style="width: 30%">{{ $changeRequest->requested_date ?? null }}</td>
                            </tr>
                            <tr>
                                <td>{{ __('Applicant Name') }}</td>
                                <td>:</td>
                                <td>{{ $changeRequest->initiator_name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td>{{ __('Division') }}</td>
                                <td>:</td>
                                <td>{{ $changeRequest->department->name ?? '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
                <td style="width: 50%; text-align: center; vertical-align: top;">
                    {{ __('Sign And Date') }}
                    <br><br>

                    <table style="width:100%; text-align:center; border:none;">
                        <tr style="border:none">
                            <td style="border:none">
                                <small>{{ $changeRequest->created_at ?? '-' }}</small><br>
                                <small>
                                    {{-- ({{ $changeRequest->initiator_name }}) --}}
                                    (Inisiator)
                                </small>
                            </td>
                            <td style="border:none">
                                <small>{{ $managerSign->approved_at ?? '-' }}</small><br>
                                <small>
                                    {{-- @if ($managerSign?->approver?->name)
                                        ({{ $managerSign->approver->employee_code }})
                                    @else
                                        -
                                    @endif --}}
                                    (Dept Head Inisiator)
                                </small>
                            </td>
                        </tr>
                    </table>
                </td>


            </tr>

            {{-- === B. Perubahan Terkait === --}}
            <tr>
                <td colspan="2">
                    <span style="font-weight: bold">
                        B. {{ __('Related Changes') }}* :
                    </span>

                    @foreach ($allScopes as $scope)
                        <label style="margin-right: 1cm; white-space: nowrap; display: inline-block;">
                            <input type="checkbox" {{ in_array($scope, $selectedScopes) ? 'checked' : '' }} disabled
                                style="position: relative; top: 10px; margin-right: 4px;">
                            <span style="position: relative; top: 5px;">
                                {{ $scope }}
                            </span>
                        </label>
                    @endforeach
                </td>

            </tr>
            {{-- === C. Jenis Perubahan === --}}
            <tr>
                <td colspan="2">
                    <div style="font-weight: bold; margin-bottom: 4px;">
                        C. {{ __('Type Of Change') }}* :
                    </div>
                    @php
                        $half = ceil(count($allTypes) / 2);
                        $leftColumn = array_slice($allTypes, 0, $half);
                        $rightColumn = array_slice($allTypes, $half);

                        $maxRows = max(count($leftColumn), count($rightColumn));
                    @endphp
                    <table style="width:100%; border-collapse: collapse; font-size:12px;">
                        @for ($row = 0; $row < $maxRows; $row++)
                            <tr style="height:14px;">
                                {{-- KOLOM KIRI --}}
                                @php $type = $leftColumn[$row] ?? null; @endphp

                                <td style="width:14px; border:1px solid #000; text-align:center;">
                                    @if ($type && in_array($type, $selectedTypes))
                                        X
                                    @endif
                                </td>
                                <td style="padding:4px 6px; border:none;">
                                    @if ($type)
                                        {{ in_array($type, $extraTypes) ? 'Lain-lain: ' . $type : $type }}
                                    @endif
                                </td>

                                {{-- KOLOM KANAN --}}
                                @php $type = $rightColumn[$row] ?? null; @endphp

                                <td style="width:14px; border:1px solid #000; text-align:center;">
                                    @if ($type && in_array($type, $selectedTypes))
                                        X
                                    @endif
                                </td>
                                <td style="padding:4px 6px; border:none;">
                                    @if ($type)
                                        {{ in_array($type, $extraTypes) ? 'Lain-lain: ' . $type : $type }}
                                    @endif
                                </td>
                            </tr>
                        @endfor
                    </table>

                </td>
            </tr>


            {{-- === D. Status Saat Ini === --}}
            <tr>
                <td colspan="2">
                    <div style="font-weight: bold">
                        D. {{ __('Current Status') }}* :
                    </div>
                    <div style="padding: 3px 0 0 0.5cm; font-weight: normal;">
                        {{ strip_tags($changeRequest->current_status ?? '-') }} <br><br>
                        <b>{{ __('Current Status File') }} :</b>
                        <ol>
                            @foreach ($changeRequest->currentStatusFiles as $currentFile)
                                <li>
                                    {{ Str::beforeLast($currentFile->original_name ?? '-', '.') }}
                                </li>
                            @endforeach
                        </ol>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div style="font-weight: bold">
                        E. {{ __('Proposed Change') }}* :
                    </div>
                    <div style="padding: 3px 0 0 0.5cm; font-weight: normal;">
                        {{ strip_tags($changeRequest->proposed_change ?? '-') }} <br><br>
                        <b>{{ __('Proposed Change File') }} :</b>
                        <ol>
                            @foreach ($changeRequest->proposedChangeFiles as $proposedFile)
                                <li>
                                    {{ Str::beforeLast($proposedFile->original_name ?? '-', '.') }}
                                </li>
                            @endforeach
                        </ol>
                    </div>
                </td>
            </tr>


            {{-- === F. Alasan Perubahan === --}}
            <tr>
                <td colspan="2">
                    <div style="font-weight: bold">
                        F. {{ __('Change Reason') }}* :
                    </div>
                    <div style="padding: 3px 0 0 0.5cm; font-weight: normal;">
                        {{ strip_tags($changeRequest->reason ?? '-') }}
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <table width="100%" cellpadding="4" cellspacing="0" class="print-table">
                        <tr>
                            <td colspan="2" style="font-weight: bold;">
                                {{ __('Supporting Data Attachments') }} :
                            </td>
                        </tr>
                        <tr>
                            <th style="width: 8%; text-align: center;">No.</th>
                            <th style="text-align: center;">{{ __('Document Name') }}</th>
                        </tr>

                        @forelse ($changeRequest->supportingAttachments as $index => $file)
                            <tr>
                                <td style="text-align: center;">
                                    {{ $index + 1 }}
                                </td>
                                <td>
                                    {{ Str::beforeLast($file->original_name ?? '-', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td style="text-align: center;">-</td>
                                <td>-</td>
                            </tr>
                        @endforelse
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
</div>
