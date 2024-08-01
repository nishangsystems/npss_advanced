@extends('admin.layout')

@section('section')

<div class="col-sm-12">
    
    <form method="post" target="new">
        @csrf
        <div class="d-flex justify-content-end">
            <label class="d-flex rounded border bg-light">
                <span class="fa fa-filter text-dark px-3 py-3 input-sm">filter</span>
                <select name="filter" class="d-inline-block border-0 input-sm py-0 form-control" style="max-width: 14rem;">
                    <option>all</option>
                    @foreach (\App\Models\Income::where('cash', 0)->get() as $inc)
                        <option value="{{ $inc->id }}">{{ $inc->name }}</option>
                    @endforeach
                </select>
                <input type="submit" value="Download CSV" class="btn btn-sm btn-dark py-0">
            </label>
        </div>
    </form>
    <div class="content-panel">
        <div class="adv-table table-responsive">
            <table cellpadding="0" cellspacing="0" border="0" class="table" id="hidden-table-info">
                <thead>
                    <tr class="text-capitalize">
                        <th>#</th>
                        <th>{{__('text.word_name')}}</th>
                        <th>{{__('text.income_type')}}</th>
                        <th>{{__('text.word_amount')}} ({{__('text.currency_cfa')}})</th>
                        <th>{{__('text.word_campus')}}</th>
                        <th>{{__('text.word_class')}}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pay_incomes as $k=>$income)
                    <tr>
                        <td>{{$k+1}}</td>
                        <td>{{$income->student_name}}</td>
                        <td>{{$income->income_name}}</td>
                        <td>{{number_format($income->paid_amount == null ? $income->amount : $income->paid_amount)}}</td>
                        <td>{{\App\Models\Campus::find($income->campus_id)->name}}</td>
                        <td>{{\App\Models\ProgramLevel::find($income->class_id)->program()->first()->name .' : LEVEL '.\App\Models\ProgramLevel::find($income->class_id)->level()->first()->level}}</td>
                        <td>
                            <a href="{{route('admin.income.print_reciept', [$income->id, $income->pay_income_id])}}" class="btn btn-sm btn-primary">{{__('text.word_print')}}</a>
                            <a href="{{route('admin.income.delete', [$income->id, $income->pay_income_id])}}" class="btn btn-sm btn-danger">{{__('text.word_delete')}}</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    // $('.section').on('change', function() {

    //     let value = $(this).val();
    //     url = "{{route('admin.getSections', ':id')}}";
    //     search_url = url.replace(':id', value);
    //     $.ajax({
    //         type: 'GET',
    //         url: search_url,
    //         success: function(response) {
    //             let size = response.data.length;
    //             let data = response.data;
    //             let html = "";
    //             if (size > 0) {
    //                 html += '<div><select class="form-control"  name="' + data[0].id + '" >';
    //                 html += '<option selected class="text-capitalize"> {{__("text.select_circle")}}</option>'
    //                 for (i = 0; i < size; i++) {
    //                     html += '<option value=" ' + data[i].id + '">' + data[i].name + '</option>';
    //                 }
    //                 html += '</select></div>';
    //             } else {
    //                 html += '<div><select class="form-control"  >';
    //                 html += '<option selected> {{__("text.no_data_available")}}</option>'
    //                 html += '</select></div>';
    //             }
    //             $('.circle').html(html);
    //         },
    //         error: function(e) {
    //             console.log(e)
    //         }
    //     })
    // })
    // $('#circle').on('change', function() {

    //     let value = $(this).val();
    //     url = "{{route('admin.getClasses',':id')}}";
    //     search_url = url.replace(':id', value);
    //     $.ajax({
    //         type: 'GET',
    //         url: search_url,
    //         success: function(response) {
    //             let size = response.data.length;
    //             let data = response.data;
    //             let html = "";
    //             if (size > 0) {
    //                 html += '<div><select class="form-control"  name="' + data[0].id + '" >';
    //                 html += '<option selected class="text-capitalize"> {{__("text.select_class")}}</option>'
    //                 for (i = 0; i < size; i++) {
    //                     html += '<option value=" ' + data[i].id + '">' + data[i].name + '</option>';
    //                 }
    //                 html += '</select></div>';
    //             } else {
    //                 html += '<div><select class="form-control"  >';
    //                 html += '<option selected> {{__("text.no_data_available")}}</option>'
    //                 html += '</select></div>';
    //             }
    //             $('.class').html(html);
    //         },
    //         error: function(e) {
    //             console.log(e)
    //         }
    //     })
    // })
</script>
@endsection