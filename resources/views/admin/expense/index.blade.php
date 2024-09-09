@extends('admin.layout')

@section('section')

<div class="col-sm-12">
    <div class="col-sm-12 mb-5">
        @include("admin.expense.create")
    </div>
    <div class="content-panel">
        <div class="adv-table table-responsive">
            <table cellpadding="0" cellspacing="0" border="0" class="table" id="hidden-table-info">
                <thead>
                    <tr class="text-capitalize">
                        <th>#</th>
                        <th>{{__('text.word_name')}}</th>
                        <th>{{__('text.word_year')}}</th>
                        <th>{{__('text.amount_spent')}}({{__('text.currency_cfa')}})</th>
                        <th>{{__('text.word_date')}}</th>
                        <th></th>
                        <!-- <th></th> -->
                    </tr>
                </thead>
                <tbody>
                    @foreach($expenses as $k=>$expense)
                    <tr>
                        <td>{{$k+1}}</td>
                        <td>{!! $expense->name !!}</td>
                        <td>{!! optional($expense->year)->name??'' !!}</td>
                        <td>{{number_format($expense->amount_spend)}}</td>
                        <td>{{date('jS F Y', strtotime($expense->date))}}</td>
                        <td class="d-flex justify-content-end  align-items-center">

                            <a class="btn btn-sm btn-success m-3 text-capitalize" href="{{route('admin.expense.edit',[$expense->id])}}"><i class="fa fa-edit"> {{__('text.word_edit')}}</i></a> |
                            <a onclick="event.preventDefault();
                                            document.getElementById('delete').submit();" class=" btn btn-danger btn-sm m-3"><i class="fa fa-trash text-capitalize"> {{__('text.word_delete')}}</i></a>
                            <form id="delete" action="{{route('admin.expense.destroy',$expense->id)}}" method="POST" style="display: none;">
                                @method('DELETE')
                                {{ csrf_field() }}
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="d-flex justify-content-end">
            </div>
        </div>
    </div>
</div>
@endsection