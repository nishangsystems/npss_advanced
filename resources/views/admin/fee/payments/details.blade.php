@extends('admin.layout')
@section('section')
    <div class="py-3">
        <table class="table">
            <thead class="text-capitalize">
                <tr><th class="text-center title text-primary" colspan="7">@lang('text.word_total') : {{number_format($total)}} @lang('text.currency_cfa')</th></tr>
                <tr>
                    <th>@lang('text.sn')</th>
                    <th>@lang('text.word_matricule')</th>
                    <th>@lang('text.word_name')</th>
                    {{-- <th>@lang('text.total_paid') (@lang('text.currency_cfa'))</th> --}}
                    <th>@lang('text.word_amount') (@lang('text.currency_cfa'))</th>
                    <th>@lang('text.pre-paid_fee') (@lang('text.currency_cfa'))</th>
                    <th>@lang('text.paid_on')</th>
                    <th>@lang('text.record_by')</th>
                </tr>
            </thead>
            <tbody>
                @php($k = 1)
                @foreach ($payments as $payment_group)
                    @foreach ($payment_group as $c => $payment)
                        <tr>
                            <td class="border-left border-right">{{$k++}}</td>
                            <td class="border-left border-right">{{optional($payment->student)->matric??''}}</td>
                            <td class="border-left border-right">{{optional($payment->student)->name??''}}</td>
                            {{-- @if($c == 0)
                                <td rowspan="{{$payment_group->count()}}"><b>{{number_format($payment_group->sum('amount'))}}</b></td> 
                            @endif --}}
                            <td class="border-left border-right">{{$payment->amount??''}}</td>
                            <td class="border-left border-right">{{-1*($payment->debt??0)}}</td>
                            <td class="border-left border-right">{{$payment->created_at == null ? '' : $payment->created_at->format('l dS m Y @ H:m')}}</td>
                            <td class="border-left border-right">{{optional($payment->user)->name??''}} | {{optional($payment->user)->type??''}}</td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
@endsection