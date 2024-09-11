@extends('admin.layout')

@section('section')
    <div class="col-sm-12">
        <div class="text-muted mb-3 d-flex justify-content-end">
            <a href="{{route('admin.fee.student.payments.create', $student->id)}}" class="btn btn-info btn-xs text-capitalize">{{__('text.collect_fee')}}</a>
        </div>       

        <div class="content-panel">
            @forelse($student->payments()->where(['batch_id'=>\App\Helpers\Helpers::instance()->getYear()])->get() as $item)
                <div class="card border bg-light py-3 px-5 d-flex justify-content-between my-4 align-items-end">
                    <div>
                        <div>{{($item->item)?$item->item->name:$item->created_at->format('d/m/Y')}}</div>
                        <h4 class="font-weight-bold">{{$item->amount}} {{__('text.currency_cfa')}}</h4>
                    </div>
                    <div class="d-inline-flex">
                        <a href="{{route('admin.fee.student.payments.edit', [ $student->id, $item->id])}}" class="btn m-2 btn-sm btn-primary text-white text-capitalize">{{__('text.word_edit')}}</a>

                        <a onclick="event.preventDefault();
                                            document.getElementById('delete').submit();" class=" btn btn-danger btn-sm m-2 text-capitalize">{{__('text.word_delete')}}</a>
                        <form id="delete" action="{{route('admin.fee.student.payments.destroy',[$student->id,$item->id])}}" method="POST" style="display: none;">
                            @method('DELETE')
                            {{ csrf_field() }}
                        </form>
                    </div>
                </div>
            @empty
                <div class="card border bg-light py-3 px-5 d-flex justify-content-between my-4 align-items-end">
                    <p>{{__('text.phrase_2', ['in_bold'=>__('text.collect_fee')])}}</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection
 