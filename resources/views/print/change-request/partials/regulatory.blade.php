@php
    $facilityAffected = $changeRequest->impactOfChangeAssesment?->facility_affected;
    $regulatory = $changeRequest->regulatory?->regulatory_change_type;
    $map = [
        'yes_after_bpom_notification' => 'Yes After BPOM Notification',
        'Yes After BPOM Notification' => 'Yes After BPOM Notification',

        'yes_bpom_notification_required' => 'Yes, BPOM Notification Required',
        'Yes, BPOM Notification Required' => 'Yes, BPOM Notification Required',

        'facility_no' => 'No',
    ];

    $normalized = $map[$facilityAffected] ?? null;
    $prodevApproval = $changeRequest->approvals
        ->where('stage', 'Review Prodev Manager')
        ->where('decision', 'Approved')
        ->sortByDesc('updated_at')
        ->first();
@endphp

{{-- ================= K. PERIZINAN PERUBAHAN FASILITAS ================= --}}
<div style="margin-top: 0.5cm">
    <table class="print-table" style="width:100%; border-collapse:collapse; margin-top:10px;">
        <tr>
            <th style="text-align:left;">
                K. {{ __('Facility Change Permit') }}
            </th>
        </tr>

        <tr>
            <td>
                <table class="no-border" style="width:100%;">
                    {{-- YA - SETELAH NOTIFIKASI --}}
                    <tr>
                        <td style="width:14px; vertical-align:top;">
                            <label style="margin-right: 1cm; white-space: normal; display: inline-block;">
                                <input type="checkbox"
                                    {{ $normalized === 'Yes After BPOM Notification' ? 'checked' : '' }}
                                    style="position: relative; top: 10px; margin-right: 4px;">
                                <span style="position: relative; top: 5px;">
                                    {{ __('Facility changes can be directly implemented after notification submission to BPOM.') }}
                                </span>
                            </label>
                        </td>
                    </tr>

                    {{-- YA - PERLU PERSETUJUAN --}}
                    <tr>
                        <td style="width:14px; vertical-align:top;">
                            <label style="margin-right: 1cm; white-space: normal; display: inline-block;">
                                <input type="checkbox"
                                    {{ $normalized === 'Yes, BPOM Notification Required' ? 'checked' : '' }}
                                    style="position: relative; top: 10px; margin-right: 4px;">
                                <span style="position: relative; top: 5px;">
                                    {{ __('Facility changes cannot be directly implemented because they require:') }}
                                </span>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div style="margin-left:20px; margin-top:4px;">
                                <label style="margin-right: 1cm; white-space: normal; display: inline-block;">
                                    <input type="checkbox" style="position: relative; top: 10px; margin-right: 4px;">
                                    <span style="position: relative; top: 5px;">
                                        {{ __('BPOM Notification Approval') }}
                                    </span>
                                </label>
                                <br>
                                <label style="margin-right: 1cm; white-space: normal; display: inline-block;">
                                    <input type="checkbox" style="position: relative; top: 10px; margin-right: 4px;">
                                    <span style="position: relative; top: 5px;">
                                        {{ __('BPOM Inspection') }}
                                    </span>
                                </label>
                            </div>
                        </td>
                    </tr>
                    {{-- TIDAK --}}
                    <tr>
                        <td style="width:14px; vertical-align:top;">
                            <label style="margin-right: 1cm; white-space: normal; display: inline-block;">
                                <input type="checkbox" style="position: relative; top: 10px; margin-right: 4px;">
                                <span style="position: relative; top: 5px;">
                                    {{ __('Changes in Facilities Do Not Need to be Reported to BPOM') }}
                                </span>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td style="width:14px; vertical-align:top;">
                            <label style="margin-right: 1cm; white-space: normal; display: inline-block;">
                                <input type="checkbox" {{ $normalized === 'No' ? 'checked' : '' }}
                                    style="position: relative; top: 10px; margin-right: 4px;">
                                <span style="position: relative; top: 5px;">
                                    {{ __('Changes Not Related to Facilities.') }}
                                </span>
                            </label>
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
    </table>
</div>

<div style="page-break-before: always;"></div>
<div style="margin-top: 0.5cm">
    <table class="print-table" style="width:100%; border-collapse:collapse; margin-top:10px;">
        <tbody>
            <tr>
                <th style="text-align:left;" colspan="3">
                    L. {{ __('Registration Reporting Category') }}
                </th>
            </tr>
            <tr>
                <td colspan="3">
                    <table class="no-border" style="width:100%; ">
                        {{-- TANPA PERSETUJUAN BPOM --}}
                        <tr>
                            <td style="width:14px; vertical-align:top; padding:0px; !important;">
                                <label style="margin-right: 1cm; white-space: normal; display: inline-block;">
                                    <input type="checkbox"
                                        {{ $regulatory === 'regulatory_change_type_1' || $regulatory === 'regulatory_change_type_2' ? 'checked' : '' }}
                                        style="position: relative; top: 10px; margin-right: 4px;">
                                    <span style="position: relative; top: 5px;">
                                        {{ __('Changes can be implemented immediately without waiting for permission from the POM Agency because:') }}
                                    </span>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div style="margin-left:30px;">
                                    <label style="margin-right:1cm; white-space: normal; display: inline-block;">
                                        <input type="checkbox"
                                            {{ $regulatory === 'regulatory_change_type_1' ? 'checked' : '' }}
                                            style="position: relative; top: 10px; margin-right: 4px;">
                                        <span style="position: relative; top: 5px;">
                                            {{ __('Notifications will be sent along with changes to the relevant documents (Notifications) by the Registration Department.') }}
                                        </span>
                                    </label>
                                    <br>
                                    <label style="margin-right: 1cm; white-space: normal; display: inline-block;">
                                        <input type="checkbox"
                                            {{ $regulatory === 'regulatory_change_type_2' ? 'checked' : '' }}
                                            style="position: relative; top: 10px; margin-right: 4px;">
                                        <span style="position: relative; top: 5px;">
                                            {{ __('No change notification required') }}
                                        </span>
                                    </label>
                                </div>
                            </td>
                        </tr>
                        {{-- TIDAK DAPAT LANGSUNG DILAKSANAKAN --}}
                        <tr>
                            <td style="width:14px; vertical-align:top;">
                                <label style="margin-right: 1cm; white-space: normal; display: inline-block;">
                                    <input type="checkbox"
                                        {{ $regulatory === 'regulatory_change_type_3' ? 'checked' : '' }}
                                        style="position: relative; top: 10px; margin-right: 4px;">
                                    <span style="position: relative; top: 5px;">
                                        {{ __('The changes cannot be implemented immediately because they require prior approval from the Indonesian Food and Drug Administration (BPOM).') }}
                                    </span>
                                </label>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr>
                <td width="50%" style="text-align: center">
                    {{ __('Sign And Date') }}
                    <small>{{ __('Approved By') }}</small><br>
                    <small>{{ $prodevApproval?->approver?->employee_code ?? '-' }}</small><br>
                    <small>{{ $prodevApproval->approved_at ?? '-' }}</small><br>
                    <small>{{ __('Technical Dossier & Packaging Development Manager') }}</small>
                </td>
                <td colspan="2">{{ __('Note') }} :</td>
            </tr>
            {{-- Persetujuan Pihak Ketiga --}}
            <tr>
                <th style="text-align:left;" colspan="3">
                    M. {{ __('Third Party Consent') }}
                </th>
            </tr>
            <tr>
                <td style="padding:4px 0;" colspan="3">
                    <table style="width:100%; border:none; border-collapse:collapse;">
                        <tr>
                            <td style="width:50%; border:none; vertical-align:top;">
                                {!! __('Changes require approval from the toll manufacturing party.') !!}
                            </td>
                            <td style="width:15%; border:none; vertical-align:top;">
                                <label style="margin-right: 1cm; white-space: normal; display: inline-block;">
                                    <input type="checkbox" style="position: relative; top: 10px; margin-right: 4px;">
                                    <span style="position: relative; top: 5px;">
                                        {{ __('Yes') }}
                                    </span>
                                </label>
                            </td>
                            <td style="width:15%; border:none; vertical-align:top;">
                                <label style="margin-right: 1cm; white-space: normal; display: inline-block;">
                                    <input type="checkbox" style="position: relative; top: 10px; margin-right: 4px;">
                                    <span style="position: relative; top: 5px;">
                                        {{ __('No') }}
                                    </span>
                                </label>
                            </td>
                        </tr>

                        <tr>
                            <td style="width:50%; border:none; vertical-align:top;">
                                {!! __('Changes require approval from the toll manufacturing party.') !!}
                            </td>
                            <td style="width:15%; border:none; vertical-align:top;">
                                <label style="margin-right: 1cm; white-space: normal; display: inline-block;">
                                    <input type="checkbox" style="position: relative; top: 10px; margin-right: 4px;">
                                    <span style="position: relative; top: 5px;">
                                        {{ __('Yes') }}
                                    </span>
                                </label>
                            </td>
                            <td style="width:15%; border:none; vertical-align:top;">
                                <label style="margin-right: 1cm; white-space: normal; display: inline-block;">
                                    <input type="checkbox" style="position: relative; top: 10px; margin-right: 4px;">
                                    <span style="position: relative; top: 5px;">
                                        {{ __('No') }}
                                    </span>
                                </label>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr border="1">
                <td width="50%"
                    style="text-align:center; border-top:1px solid #000; border-bottom:1px solid #000; padding-top:10px; padding-bottom:30px;">
                    {{ __('Sign And Date') }}<br>
                    <small>{{ __('Approved By') }}</small><br><br><br>
                    <small>{{ __('Third Party') }} ....................................</small>
                </td>
                <td colspan="2" style="border-top:1px solid #000; border-bottom:1px solid #000; padding:10px;">
                    {{ __('Note') }} :
                </td>
            </tr>
            {{-- <tr>
                <td width="40%">
                    {{ __("Changes require approval from the exporting country's regulatory agency.") }}
                </td>
                <td style="width:15%; vertical-align:top;">
                    <label style="margin-right: 1cm; white-space: normal; display: inline-block;">
                        <input type="checkbox" style="position: relative; top: 10px; margin-right: 4px;">
                        <span style="position: relative; top: 5px;">
                            {{ __('Yes') }}
                        </span>
                    </label>
                </td>
                <td style="width:15%; vertical-align:top;">
                    <label style="margin-right: 1cm; white-space: normal; display: inline-block;">
                        <input type="checkbox" style="position: relative; top: 10px; margin-right: 4px;">
                        <span style="position: relative; top: 5px;">
                            {{ __('No') }}
                        </span>
                    </label>
                </td>
            </tr> --}}

            {{-- KAJIAN TERKAIT HALAL --}}
            <tr>
                <th style="text-align:left;" colspan="3">
                    N. {{ __('Approval Related to Halal') }}
                </th>
            </tr>
            <tr>
                <td colspan="3">
                    <table class="no-border" style="width:100%;">
                        {{-- YA --}}
                        <tr>
                            <td style="width:14px; vertical-align:top;">
                                <span>{{ __('Are the proposed materials/activities related to Halal products?') }}</span>
                                <br>
                                <label style="margin-right: 1cm; white-space: normal; display: inline-block;">
                                    <input type="checkbox"
                                        {{ $changeRequest->impactOfChangeAssesment->halal_status === 'Yes BPJPH Required' || $changeRequest->impactOfChangeAssesment->halal_status === 'Yes No BPJH' ? 'checked' : '' }}
                                        style="position: relative; top: 10px; margin-right: 4px;">
                                    <span style="position: relative; top: 5px;">
                                        {{ __('Ya') }} :
                                    </span>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div style="margin-left:30px;">
                                    <label style="margin-right:1cm; white-space: normal; display: inline-block;">
                                        <input type="checkbox"
                                            {{ $changeRequest->impactOfChangeAssesment->halal_status === 'Yes BPJPH Required' ? 'checked' : '' }}
                                            style="position: relative; top: 10px; margin-right: 4px;">
                                        <span style="position: relative; top: 5px;">
                                            {{ __('Changes require prior approval from BPJPH.') }}
                                        </span>
                                    </label>
                                    <br>
                                    <label style="margin-right: 1cm; white-space: normal; display: inline-block;">
                                        <input type="checkbox"
                                            {{ $changeRequest->impactOfChangeAssesment->halal_status === 'Yes No BPJH' ? 'checked' : '' }}
                                            style="position: relative; top: 10px; margin-right: 4px;">
                                        <span style="position: relative; top: 5px;">
                                            {{ __('Changes do not require prior approval from BPJPH.') }}
                                        </span>
                                    </label>
                                </div>
                            </td>
                        </tr>
                        {{-- TIDAK --}}
                        <tr>
                            <td style="width:14px; vertical-align:top;">
                                <label style="margin-right: 1cm; white-space: normal; display: inline-block;">
                                    <input type="checkbox"
                                        {{ $changeRequest->impactOfChangeAssesment->halal_status === 'No' ? 'checked' : '' }}
                                        style="position: relative; top: 10px; margin-right: 4px;">
                                    <span style="position: relative; top: 5px;">
                                        {{ __('No') }}
                                    </span>
                                </label>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
</div>
