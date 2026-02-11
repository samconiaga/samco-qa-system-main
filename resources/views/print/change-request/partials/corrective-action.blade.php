<div>
    <table class="print-table" style="width: 100%; border-collapse: collapse; margin-top: 0.2cm;">
        <thead>
            <tr>
                <th style="text-align: left;">
                    O. {{ __('Corrective Action') }}
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="border:1px solid #000; text-align:center;">
                    <table style="width: 100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th width="30%">{{ __('impact_of_change') }}</th>
                                <th>{{ __('PIC') }}</th>
                                <th>{{ __('Deadline') }}</th>
                                <th>{{ __('Realization') }}</th>
                                <th>{{ __('Status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($changeRequest->groupedActionPlans as $category => $plans)
                            @php $groupIndex = $loop->iteration; @endphp
                            @foreach ($plans as $ap)
                            <tr>
                                @if(isset($changeRequest->groupedActionPlans) && $changeRequest->groupedActionPlans->count() > 1)
                                <td style="text-align: center;">{{ $groupIndex }}.{{ $loop->iteration }}</td>
                                @else
                                <td style="text-align: center;">{{ $loop->iteration }}</td>
                                @endif
                                <td>{!! $ap->impact_of_change_description !!}</td>
                                <td>{{ $ap?->department?->short_name ?? $ap?->department?->name }}</td>
                                <td style="text-align: center;">
                                    {{ $ap->deadline }}
                                </td>
                                <td>{{ $ap->realization }}</td>
                                <td style="text-align: center;">
                                    {{ $ap->status }}
                                </td>
                            </tr>
                            @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>

</div>