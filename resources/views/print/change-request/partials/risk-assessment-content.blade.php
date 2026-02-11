@php
$isChecked = fn($cond) => $cond ? 'checked' : '';
@endphp

<div style="margin-top: 0.5cm">
    <table class="print-table" style="width: 100%">
        <tbody>

            <thead>
                {{-- === F. Sebelum Mitigasi === --}}
                <tr>
                    <th colspan="15" style="text-align: left !important;">
                        <b>G.{{ __('Risk Assessment of Medicines (quality, safety, and efficacy)') }}</b>
                    </th>
                </tr>
                <tr>
                    <th colspan="10">{{ __('Initial Risk Assessment') }}</th>
                    <th colspan="5">{{ __('Risk Assessment After Mitigation') }}</th>
                </tr>
                <tr>
                    <th>No.</th>
                    <th>{{ __('Change Subject') }}</th>
                    <th>{{ __('Risk Impact Due To Change') }}</th>
                    <th>S</th>
                    <th>{{ __('Cause Of Risk Due to Change') }}</th>
                    <th>P</th>
                    <th>{{ __('Available Control') }}</th>
                    <th>D</th>
                    <th>RPN</th>
                    <th>{{ __('Risk Category') }}</th>
                    <th>S</th>
                    <th>P</th>
                    <th>D</th>
                    <th>RPN</th>
                    <th>{{ __('Risk Category') }}</th>
                </tr>
            <tbody>
                <tr>
                    <td>1.</td>
                    <td>{{ $changeRequest->scopeOfChange->pluck('name')->join(', ') ?? '-' }}</td>
                    <td>{!! $changeRequest->impactRiskAssesment->impact_of_risk ?? '-' !!}</td>
                    <td>{!! $changeRequest->impactRiskAssesment->severity ?? '-' !!}</td>
                    <td>{!! $changeRequest->impactRiskAssesment->cause_of_risk ?? '-' !!}</td>
                    <td>{!! $changeRequest->impactRiskAssesment->probability ?? '-' !!}</td>
                    <td>{!! $changeRequest->impactRiskAssesment->control_implemented ?? '-' !!}</td>
                    <td>{!! $changeRequest->impactRiskAssesment->detectability ?? '-' !!}</td>
                    <td>{!! $changeRequest->impactRiskAssesment->rpn ?? '-' !!}</td>
                    <td>{!! $changeRequest->impactRiskAssesment->risk_category ?? '-' !!}</td>
                    <td>{!! $changeRequest->qaRiskAssesment->severity ?? '-' !!}</td>
                    <td>{!! $changeRequest->qaRiskAssesment->probability ?? '-' !!}</td>
                    <td>{!! $changeRequest->qaRiskAssesment->detectability ?? '-' !!}</td>
                    <td>{!! $changeRequest->qaRiskAssesment->rpn ?? '-' !!}</td>
                    <td>{!! $changeRequest->qaRiskAssesment->risk_category ?? '-' !!}</td>
                </tr>
            </tbody>
            </thead>
        </tbody>
    </table>
</div>