@php
    use Carbon\Carbon;

    $isChecked = fn($cond) => $cond ? 'checked' : '';

@endphp
<div style="margin-top: 0.5cm">

    <table class="print-table" style="width: 100%; border-collapse: collapse;">
        <tbody>
            <tr>
                <th colspan="2" style="text-align:left;">
                    H. {{ __('Impacted Product') }}
                </th>
            </tr>

            <tr>
                <td colspan="2" style="padding: 10px;">
                    <div style="margin-bottom: 0.3cm">
                        {{ __('Mention') }} :
                        <ol style="margin: 4px 0 0 26px; padding: 0;">
                            @foreach ($changeRequest->affectedProducts as $product)
                                <li>{{ $product->name }}</li>
                            @endforeach
                        </ol>
                    </div>
                    {!! __('Does it impact <i>Toll Manufacturing</i> products?') !!} <br>
                    <label style="margin-right: 1cm; white-space: nowrap; display: inline-block;">
                        <input type="checkbox" {{ $changeRequest?->regulatory->third_party_involved ? 'checked' : '' }}
                            disabled style="position: relative; top: 10px; margin-right: 4px;">
                        <span style="position: relative; top: 5px;">
                            {{ __('Yes') }}
                        </span>
                    </label>
                    <label style="margin-right: 1cm; white-space: nowrap; display: inline-block;">
                        <input type="checkbox" {{ !$changeRequest?->regulatory->third_party_involved ? 'checked' : '' }}
                            disabled style="position: relative; top: 10px; margin-right: 4px;">
                        <span style="position: relative; top: 5px;">
                            {{ __('No') }}
                        </span>
                    </label>
                    @if ($changeRequest?->regulatory->third_party_involved)
                        <br><br>
                        {{ __('Mention') }} : {{ $changeRequest?->regulatory?->third_party_name }}
                    @endif
                </td>
            </tr>
        </tbody>
    </table>

    <table class="print-table" style="width: 100%; border-collapse: collapse; margin-top: 0.2cm;">
        <tbody>
            {{-- ================= H. KAJIAN DEPARTEMEN TERKAIT ================= --}}
            <tr>
                <td colspan="4">
                    <b>I. {{ __('Review of Sections Related to Changes') }}</b>
                </td>
            </tr>

            <tr>
                <th style="text-align: center;">No.</th>
                <td style="text-align: center;">
                    <b>{{ __('Assessment By') }}</b>
                    <br> <small>({{ __('Name & Department') }})</small>
                </td>
                <th style="text-align: center;">{{ __('Sign And Date') }}</th>
                <th style="text-align: center;">{{ __('Comment') }}</th>
            </tr>

            @forelse ($changeRequest->followUpImplementations  as $index => $fup)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ $fup->employee->employee_code ?? '-' }}</td>
                    <td>{{ $fup->created_at }}</td>
                    <td>
                        ({{ __($fup->evaluation_status) }})
                        {{ $fup->comments }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center;">-</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</div>
