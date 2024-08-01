@extends('admin.layout')
@section('section')
@php
    $header = \App\Helpers\Helpers::instance()->getHeader();
@endphp
<div class="card height-auto">
    <div class="card-body">
        <div class="heading-layout1">
            <div class="item-title">
                <h3 class="text-capitalize">{{$reciept->student_name}} {{__('text.fee_payment')}}</h3>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table data-table text-nowrap">
                <thead>
                    <tr class="text-capitalize">
                        <th>{{__('text.word_collector')}}</th>
                        <th>{{__('text.word_date')}}</th>
                        <th>{{__('text.word_amount')}}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php /*@foreach($student->payments()->where(['batch_id'=>$year])->get() as $payment)
                    @php
                    $fee = $payment;
                    $year = $year;
                    @endphp */
                    ?>
                    <tr>
                        <td>{{$reciept->student_name}}</td>
                        <td>{{date('Y-m-d', strtotime($reciept->created_at))}}</td>
                        <td>{{$reciept->amount}}</td>
                        <td>
                            <button onclick="printDiv('printHERE{{$reciept->id}}')" class="btn btn-primary"><i class="fas fa-print text-capitalize"></i>{{__('text.word_print')}}</button>
                            <div class="d-none">
                                <div id="printHERE{{$reciept->id}}" class="eachrec">
                                    
                                   <div class="mb-5">
                                   <div style="height:120px; width:95% ; ">
                                        <img width="100%" src="{{$header}}" />
                                    </div>
                                    <div style=" float:left; width:100%; margin-top:100px;TEXT-ALIGN:CENTER;  height:34px;font-size:24px; margin-bottom:10px; text-transform: uppercase;">
                                        {{__('text.cash_reciept')}} N<SUP>0</SUP> 00{{$reciept->id}}
                                    </div>
                                    <div style=" float:left; width:900px; margin-top:0px;TEXT-ALIGN:CENTER; font-family:arial; height:300px;font-size:13px; ">
                                        <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform:capitalize"> {{__('text.word_name')}} :</div>
                                        <div style=" float:left; width:700px;border-bottom:1px solid #000;font-weight:normal; height:25px;font-size:17px;">
                                            <div style=" text-align:center; margin-top:3px;">
                                                {{$reciept->student_name}}
                                            </div>
                                            <div style=" float:left; width:200px;  height:25px;margin-top:15px;">

                                            </div>
                                        </div>
                                        <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform:capitalize"> {{__('text.word_purpose')}} :</div>
                                        <div style=" float:left; width:700px;border-bottom:1px solid #000;font-weight:normal; height:25px;font-size:17px;">
                                            <div style=" float:left; margin-top:3px;">
                                                {{$reciept->income_name}}
                                            </div>
                                            <div style=" float:left; width:200px;  height:25px;margin-top:15px;"></div>
                                        </div>

                                        <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform: capitalize"> {{__('text.academic_year')}}:</div>
                                        <div style=" float:left; width:700px;border-bottom:1px solid #000;font-weight:normal; height:25px;font-size:17px;">
                                            <div style=" float:left; width:300px;margin-top:3px;">
                                                {{\App\Models\Batch::find(\App\Helpers\Helpers::instance()->getCurrentAccademicYear())->name}}
                                            </div>
                                            <div style=" float:left; width:200px;  height:25px;margin-top:7px;"></div>
                                        </div>
                                        <div style=" float:left; width:200px;  height:25px;margin-top:15px;"></div>
                                        <div style=" float:left; width:900px;margin-top:3px;TEXT-ALIGN:CENTER; font-family:arial; height:300px; font-size:13px; ">
                                            <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform:capitalize"> {{__('text.amount_in_figures')}}</div>
                                            <div style=" float:left; width:700px; height:25px;font-size:17px;">
                                                <div style=" float:left; width:400px;border:1px solid #000;margin-top:3px;">
                                                    {{__('text.currency_cfa')}} {{number_format($reciept->amount)}}
                                                </div>
                                                <div style=" float:left; width:100px;margin-top:5px; text-transform:uppercase">
                                                    {{__('text.word_date')}}
                                                </div>
                                                <div style=" float:left; border-bottom:1px solid #000;">
                                                    {{date('Y-m-d', strtotime($reciept->created_at))}}
                                                </div>
                                            </div>
                                            <div style=" float:left; width:200px;  height:25px;margin-top:7px;"></div>
                                            <div style=" float:left; width:900px;margin-top:3px;TEXT-ALIGN:CENTER; font-family:arial; height:30px; BORDER-BOTTOM:none; font-size:13px; ">
                                                <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform:capitalize"> <i>{{__('text.amount_in_words')}}</i></div>
                                                <div style=" float:left; width:700px; height:25px; border-bottom:none; font-size:16px; font-family:Chaparral Pro Light; border-bottom:1PX dashed#000"><i>{{\App\Helpers\Helpers::instance()->numToWord($reciept->amount)}}</i></div>
                                            </div>
                                            <div style=" float:left; width:200px;  height:25px;margin-top:7px;"></div>
                                            <div style=" float:left; width:900px;margin-top:3px;TEXT-ALIGN:CENTER; font-family:arial; height:30px; BORDER-BOTTOM:none; font-size:13px; ">
                                                <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform:capitalize"> <i>{{__('text.balance_due')}}</i></div>
                                                <div style=" float:left; width:700px; height:25px; border-bottom:none; font-size:16px; font-family:Chaparral Pro Light; border-bottom:1PX dashed#000"><i>{{ number_format($reciept->total-$reciept->amount) }}</i></div>
                                            </div>
                                            
                                            <div style=" clear:both; height:30px"></div>

                                            <div style="float:left; margin:30px 30px; height:30px; text-transform:capitalize">
                                                ___________________<br /><br />{{__('text.burser_signature')}}
                                            </div>

                                            <div style="float:right; margin:10px 10px; height:30px; text-transform:capitalize">
                                                ___________________<br /><br />{{__('text.student_signature')}}
                                            </div>
                                            
                                        </div>
                                        
                                    </div> 
                                   </div>
                                   <div>
                                      
                                        <div style="height:120px; width:95% ; margin-top:550px;">
                                            <img width="100%" src="{{$header}}" />
                                        </div>
                                        <div style=" float:left; width:100%; margin-top:100px;TEXT-ALIGN:CENTER;  height:34px;font-size:24px; margin-bottom:10px; text-transform:capitalize">
                                        {{__('text.cash_reciept')}} N<SUP>0</SUP> 00{{$reciept->id}}
                                    </div>
                                    <div style=" float:left; width:900px; margin-top:0px;TEXT-ALIGN:CENTER; font-family:arial; height:300px;font-size:13px; ">
                                        <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform:capitalize"> {{__('text.word_name')}} :</div>
                                        <div style=" float:left; width:700px;border-bottom:1px solid #000;font-weight:normal; height:25px;font-size:17px;">
                                            <div style=" text-align:center; margin-top:3px;">
                                                {{$reciept->student_name}}
                                            </div>
                                            <div style=" float:left; width:200px;  height:25px;margin-top:15px;">

                                            </div>
                                        </div>
                                        <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform:capitalize"> {{__('text.word_purpose')}} :</div>
                                        <div style=" float:left; width:700px;border-bottom:1px solid #000;font-weight:normal; height:25px;font-size:17px;">
                                            <div style=" float:left; margin-top:3px;">
                                                {{$reciept->income_name }}
                                            </div>
                                            <div style=" float:left; width:200px;  height:25px;margin-top:15px;"></div>
                                        </div>

                                        <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform:capitalize"> {{__('text.academic_year')}}:</div>
                                        <div style=" float:left; width:700px;border-bottom:1px solid #000;font-weight:normal; height:25px;font-size:17px;">
                                            <div style=" float:left; width:300px;margin-top:3px;">
                                                {{\App\Models\Batch::find(\App\Helpers\Helpers::instance()->getCurrentAccademicYear())->name}}
                                            </div>
                                            <div style=" float:left; width:200px;  height:25px;margin-top:7px;"></div>
                                        </div>
                                        <div style=" float:left; width:200px;  height:25px;margin-top:15px;"></div>
                                        <div style=" float:left; width:900px;margin-top:3px;TEXT-ALIGN:CENTER; font-family:arial; height:300px; font-size:13px; ">
                                            <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform:capitalize"> {{__('text.amount_in_figures')}}</div>
                                            <div style=" float:left; width:700px; height:25px;font-size:17px;">
                                                <div style=" float:left; width:400px;border:1px solid #000;margin-top:3px;">
                                                    {{__('text.currency_cfa')}} {{number_format($reciept->amount)}}
                                                </div>
                                                <div style=" float:left; width:100px;margin-top:5px; text-transform:uppercase">
                                                    {{__('text.word_date')}}
                                                </div>
                                                <div style=" float:left; border-bottom:1px solid #000; margin-top:53px;">
                                                    {{date('Y/m/d', strtotime($reciept->created_at))}}
                                                </div>
                                            </div>
                                            <div style=" float:left; width:200px;  height:25px;margin-top:7px;"></div>
                                            <div style=" float:left; width:900px;margin-top:3px;TEXT-ALIGN:CENTER; font-family:arial; height:30px; BORDER-BOTTOM:none; font-size:13px; ">
                                                <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform:capitalize"> <i>{{__('text.amount_in_words')}}</i></div>
                                                <div style=" float:left; width:700px; height:25px; border-bottom:none; font-size:16px; font-family:Chaparral Pro Light; border-bottom:1PX dashed#000"><i>{{\App\Helpers\Helpers::instance()->numToWord($reciept->amount)}}</i></div>
                                            </div>
                                            <div style=" float:left; width:200px;  height:25px;margin-top:7px;"></div>
                                            <div style=" float:left; width:900px;margin-top:3px;TEXT-ALIGN:CENTER; font-family:arial; height:30px; BORDER-BOTTOM:none; font-size:13px; ">
                                                <div style=" float:left; width:200px; height:25px;font-size:17px; text-transform:capitalize"> <i>{{__('text.balance_due')}}</i></div>
                                                <div style=" float:left; width:700px; height:25px; border-bottom:none; font-size:16px; font-family:Chaparral Pro Light; border-bottom:1PX dashed#000"><i>{{ number_format($reciept->total-$reciept->amount) }}</i></div>
                                            </div>
                                            
                                            <div style=" clear:both; height:30px"></div>

                                            <div style="float:left; margin:30px 30px; height:30px; text-transform:capitalize;">
                                                ___________________<br /><br />{{__('text.burser_signature')}}
                                            </div>

                                            <div style="float:right; margin:10px 10px; height:30px; text-transform:capitalize">
                                                ___________________<br /><br />{{__('text.student_signature')}}
                                            </div>
                                        </div>
                                    </div>
                                   </div>
                                    
                                </div>
                            </div>
                            <!-- ------------------------------- -->


                            
                        </td>
                    </tr>
                    <?php //@endforeach
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
@section('script')
<script>
    function printDiv(divName) {
        var printContents = document.getElementById(divName).innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }
</script>
@endsection
