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
                                <th width="30%">{{ __('Action') }}</th>
                                <th>{{ __('PIC') }}</th>
                                <th>{{ __('Deadline') }}</th>
                                <th>{{ __('Initials And Date') }}</th>
                                <th>{{ __('Status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($changeRequest?->actionPlans as $ap)
                                <tr>
                                    <td style="text-align: center;">{{ $loop->iteration }}</td>
                                    <td>{{ $ap->realization }}</td>
                                    <td>{{ $ap?->pic?->name }}</td>
                                    <td style="text-align: center;">
                                        {{ $ap->deadline }}
                                    </td>
                                    <td>
                                        @if ($ap->status == 'Close')
                                            {{ $ap->updated_at }}
                                        @endif
                                    </td>
                                    <td style="text-align: center;">
                                        {{ $ap->status }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>

</div>
