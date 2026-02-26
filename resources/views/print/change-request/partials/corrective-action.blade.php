<div>
    <!-- CORRECTIVE ACTION LIST -->
    <table class="print-table" style="width: 100%; border-collapse: collapse; margin-top: 0.2cm;">
        <thead>
            <tr style="page-break-inside: avoid;">
                <th colspan="6" style="text-align: left; padding: 5px; border: 1px solid #000; border-bottom: none;">
                    O. {{ __('Corrective Action') }} / Tindakan Perbaikan
                </th>
            </tr>
            <tr style="page-break-inside: avoid;">
                <th style="border: 1px solid #000; font-weight: bold; padding: 5px; white-space: nowrap; width: 6%;">No</th>
                <th style="border: 1px solid #000; font-weight: bold; padding: 5px; width: 30%;">{{ __('impact_of_change') }}</th>
                <th style="border: 1px solid #000; font-weight: bold; padding: 5px; white-space: nowrap; width: 5%;">{{ __('PIC') }}</th>
                <th style="border: 1px solid #000; font-weight: bold; padding: 5px; white-space: nowrap;">{{ __('Deadline') }}</th>
                <th style="border: 1px solid #000; font-weight: bold; padding: 5px; width: 30%;">{{ __('Realization') }}</th>
                <th style="border: 1px solid #000; font-weight: bold; padding: 5px; white-space: nowrap;">{{ __('Status') }}</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($changeRequest->groupedActionPlans) && count($changeRequest->groupedActionPlans) > 0)
            @foreach ($changeRequest->groupedActionPlans as $category => $plans)
            @php $groupIndex = $loop->iteration; @endphp
            @foreach ($plans as $ap)
            <tr style="page-break-inside: avoid;">
                <td style="border: 1px solid #000; text-align: center; padding: 5px; white-space: nowrap;">{{ $groupIndex }}.{{ $ap->tree_number }}</td>
                <td style="border: 1px solid #000; text-align: left; padding: 5px; word-wrap: break-word; overflow-wrap: break-word; word-break: break-word;">{!! $ap->impact_of_change_description !!}</td>
                <td style="border: 1px solid #000; padding: 5px; white-space: nowrap;">{{ $ap?->department?->short_name ?? $ap?->department?->name }}</td>
                <td style="border: 1px solid #000; text-align: center; padding: 5px; white-space: nowrap;">{{ $ap->deadline }}</td>
                <td style="border: 1px solid #000; text-align: left; padding: 5px; word-wrap: break-word; overflow-wrap: break-word; word-break: break-word;">{{ $ap->realization }}</td>
                <td style="border: 1px solid #000; text-align: center; padding: 5px; white-space: nowrap;">{{ ucfirst(str_replace('_', ' ', $ap->status)) }}</td>
            </tr>
            @endforeach
            @endforeach
            @else
            <tr style="page-break-inside: avoid;">
                <td colspan="6" style="border: 1px solid #000; text-align: center; padding: 5px;">-</td>
            </tr>
            @endif
        </tbody>
    </table>
</div>