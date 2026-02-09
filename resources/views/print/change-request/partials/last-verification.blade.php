@php
    $isChecked = fn($cond) => $cond ? 'checked' : '';
    $qaSpvApproval = $changeRequest->approvals
        ->where('stage', 'Approve QA SPV')
        ->where('decision', 'Approved')
        ->sortByDesc('updated_at')
        ->first();

    $qaManagerApproval = $changeRequest->approvals
        ->where('stage', 'Approve QA Manager')
        ->where('decision', 'Approved')
        ->sortByDesc('updated_at')
        ->first();
    $plantManagerApproval = $changeRequest->approvals
        ->where('stage', 'Approve Plant Manager')
        ->where('decision', 'Approved')
        ->sortByDesc('updated_at')
        ->first();
@endphp

<div style="margin-top: 1cm">
    <table class="print-table" style="width: 100%; border-collapse: collapse; ">
        <tbody>
            {{-- DISPOSISI --}}
            <tr>
                <td colspan="3">
                    <b> P. {{ __('Disposition') }} :</b>
                    <label style="margin-right: 1cm; white-space: normal; display: inline-block;">
                        <input type="checkbox" {{ $plantManagerApproval ? 'checked' : '' }}
                            style="position: relative; top: 10px; margin-right: 4px;">
                        <span style="position: relative; top: 5px;">
                            {{ __('Approved') }}
                        </span>
                    </label>
                    <label style="margin-left: 1cm; white-space: normal; display: inline-block;">
                        <input type="checkbox" {{ !$plantManagerApproval ? 'checked' : '' }}
                            style="position: relative; top: 10px; margin-right: 4px;">
                        <span style="position: relative; top: 5px;">
                            {{ __('Not Approved') }}
                        </span>
                    </label>
                </td>
            </tr>
            <tr>
                <td width="30%" style="text-align:center; " colspan="2">
                    {{ __('Sign And Date') }}
                </td>
                <td>
                    {{ __('Note') }} :
                </td>
            </tr>
            <tr height="15px">
                <td style="padding-top:10px; padding-bottom:30px; width:35%;">Quality Assurance Sub Dept. Head</td>
                <td width="15%">{{ $qaSpvApproval->approved_at }}</td>
                <td></td>
            </tr>
            <tr height="15px">
                <td style="padding-top:10px; padding-bottom:30px;">Quality Assurance Dept. Head</td>
                <td>{{ $qaManagerApproval->approved_at }}</td>
                <td></td>
            </tr>
            <tr height="15px">
                <td style="padding-top:10px; padding-bottom:30px;">Plant Division Head</td>
                <td>{{ $plantManagerApproval->approved_at }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>
    <table class="print-table" style="width: 100%; border-collapse: collapse;">
        <tbody>
            <tr>
                <td style="text-align: justify;">
                    <div style="font-weight: bold;">
                        Q. {{ __('Verification of Change Enforcement') }}
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <b>Penilaian resiko setelah dilakukan mitigasi resiko: <br>
                        <small>
                            (Lakukan verifikasi penilaian terhadap poin G. Kajian Resiko terhadap Obat
                            (kualitas,keamanan,dan efektivitas))
                        </small>
                    </b>
                    {{-- Checkbox status --}}
                    <div style="margin-top: 10px; margin-bottom: 10px;">
                        <table class="no-border">
                            <tr>
                                <td style="width: 18px; vertical-align: middle;">
                                    <input type="checkbox" disabled
                                        {{ $isChecked($changeRequest->conclusion == 'Can be implemented') }}>
                                </td>
                                <td style="vertical-align: middle;">
                                    {{ __('Can Be Implemented') }}
                                </td>

                                <td style="width: 20px;"></td>

                                <td style="width: 18px; vertical-align: middle;">
                                    <input type="checkbox" disabled
                                        {{ $isChecked($changeRequest->conclusion == 'Can not be implemented') }}>
                                </td>
                                <td style="vertical-align: middle;">
                                    {{ __('Can Not Be Implemented') }}
                                </td>
                            </tr>
                        </table>
                    </div>
                    {{-- Catatan --}}
                    <div style="margin-top: 10px; margin-bottom: 10px;">
                        <table style="width:100%; border-collapse: collapse;">
                            <tr>
                                <td
                                    style="height:120px; width:100%; vertical-align: top; padding: 8px; border:1px solid #000;">
                                    {{ __('Note') }}
                                </td>
                            </tr>
                        </table>
                    </div>
                    {{-- Signature --}}
                    <table style="width: 100%; border: none;" border="0">
                        <tr>
                            {{-- ================= QA SPV ================= --}}
                            <td style="width: 50%; vertical-align: top;">
                                <div>{{ __('Assessment By') }},</div>


                                <div style="margin-bottom: 20px;">{{ __('Sign And Date') }}</div>
                                {{-- Area TTD --}}
                                {{-- <div style="margin: 15px 0 35px 0; height: 50px;">
                                    @if (!empty($qaSpvApproval?->approver?->sign))
                                        <img src="{{ public_path('storage/' . $qaSpvApproval->approver->sign) }}"
                                            height="60">
                                    @endif
                                </div> --}}

                                <div style="font-size: 11px;">
                                    {{ $changeRequest?->closing?->qa_spv_sign }}
                                </div>
                                <div>Quality Assurance Sub Dept. Head</div>
                            </td>

                            {{-- ================= QA MANAGER ================= --}}
                            <td style="vertical-align: top;">
                                <div>{{ __('Approved By') }},</div>


                                <div style="margin-bottom: 20px;">{{ __('Sign And Date') }}</div>
                                {{-- Area TTD --}}
                                {{-- <div style="margin: 15px 0 35px 0; height: 50px;">
                                    @if (!empty($qaManagerApproval?->approver?->sign))
                                        <img src="{{ public_path('storage/' . $qaManagerApproval->approver->sign) }}"
                                            height="60">
                                    @endif
                                </div> --}}
                                <div style="font-size: 11px;">
                                    {{ $changeRequest?->closing?->qa_manager_sign }}
                                </div>
                                <div>Quality Assurance Dept. Head</div>

                            </td>
                        </tr>
                    </table>

                </td>
            </tr>

        </tbody>
    </table>
</div>
