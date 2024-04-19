@extends('admin.layout')

@section('section')
    <div class="w-100 py-3">
        <form action="{{Request::url()}}" method="get">
            <div class="">
                <div class="py-2 form-group row">
                    <label for="" class="text-secondary h6 fw-bold col-sm-3 text-capitalize">{{__('text.select_academic_year')}}</label>
                    <div class="col-sm-9">
                        <select name="year" id="" class="form-control">
                            <option value="" selected>{{__('text.academic_year')}}</option>
                            @forelse(\App\Models\Batch::all() as $batch)
                                <option value="{{$batch->id}}" {{request('year') == $batch->id ? 'selected' : ''}}>{{$batch->name}}</option>
                            @empty
                                <option value="" selected>{{__('text.academic_year_not_set')}} </option>
                            @endforelse
                        </select>
                    </div>
                </div>
                <div class="py-2 form-group row">
                    <label for="" class="text-secondary h6 fw-bold col-sm-3 text-capitalize">{{__('text.filter_statistics_by')}}</label>
                    <div class="col-sm-9">
                        <select name="filter_key" id="" class="form-control text-uppercase">
                            <option value="">{{__('text.filter_by')}}</option>
                            <option value="class" {{request('filter_key') == 'class' ? 'selected' : ''}}>{{__('text.word_class')}}</option>
                            {{-- <option value="program" {{request('filter_key') == 'program' ? 'selected' : ''}}>{{__('text.word_program')}}</option>
                            <option value="level" {{request('filter_key') == 'level' ? 'selected' : ''}}>{{__('text.word_level')}}</option> --}}
                        </select>
                    </div>
                </div>
                
                @if (auth()->user()->campus_id == null)
                    <div class="py-2 form-group row">
                        <label for="" class="text-secondary h6 fw-bold col-sm-3 text-capitalize">{{__('text.select_campus')}}</label>
                        <div class="col-sm-9">
                            <select name="campus" id="" class="form-control">
                                <option value="" selected>{{__('text.word_all')}}</option>
                                @foreach(\App\Models\Campus::orderBy('name')->get() as $campus)
                                    <option value="{{$campus->id}}" {{request('campus') == $campus->id ? 'selected' : ''}}>{{$campus->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                @endif

                <div class="d-flex flex justify-content-end">
                    <input type="submit" name="" id="" class=" btn btn-primary btn-sm" value="get statistics">
                </div>
            </div>
        </form>
        <div class="mt-5 pt-2">
            <div class="py-2 text-uppercase fw-bolder text-dark h4">
                <span>{{$title}}</span>
            </div>
            @if(request()->has('filter_key'))
            <table class="table table-stripped">
                <thead class="bg-secondary text-black text-capitalize">
                    @php($count = 1)
                    <th>##</th>
                    <th>{{__('text.word_unit')}}</th>
                    <th>{{__('text.no_of_students')}}</th>
                    <th>{{__('text.number_completed')}}</th>
                    <th>{{__('text.number_uncompleted')}}</th>
                    <th>{{__('text.amount_expected')}}</th>
                    <th>{{__('text.amount_recieved')}}</th>
                    <th>{{__('text.percentage_students_completed')}}</th>
                    <th>{{__('text.percentage_students_uncompleted')}}</th>
                    <th>{{__('text.percentage_amount_recieved')}}</th>
                </thead>
                <tbody>
                    @forelse($data ?? [] as $value)

                        <tr class="border-bottom border-dark" style="background-color: rgba(242, 242, 250, 0.4);">
                            <td class="border-left border-right">{{$count++}}</td>
                            <td class="border-left border-right">{{$value['unit']}}</td>
                            <td class="border-left border-right">{{$value['students']}}</td>
                            <td class="border-left border-right">{{$value['complete']}}</td>
                            <td class="border-left border-right">{{$value['incomplete']}}</td>
                            <td class="border-left border-right">{{number_format($value['expected'])}}</td>
                            <td class="border-left border-right">{{number_format($value['recieved'])}}</td>
                            <td class="border-left border-right">{{number_format($value['per_completed'], 2)}}</td>
                            <td class="border-left border-right">{{number_format($value['per_uncompleted'], 2)}}</td>
                            <td class="border-left border-right">{{number_format($value['per_recieved'], 2)}}</td>
                        </tr>
                        
                    @empty
                        <tr class="border-bottom border-dark text-center">
                            {{__('text.phrase_6')}}
                        </tr>
                    @endforelse
                    @if(isset($data))
                    <tr class="border-bottom border-top border-primary" style="background-color: rgb(225, 245, 255);">
                        <td class="border-left border-right" colspan="2">{{__('text.word_total')}}</td>
                        <td class="border-left border-right">{{$data->sum('students')}}</td>
                        <td class="border-left border-right">{{$data->sum('complete')}}</td>
                        <td class="border-left border-right">{{$data->sum('incomplete')}}</td>
                        <td class="border-left border-right">{{number_format($data->sum('expected'), 2)}}</td>
                        <td class="border-left border-right">{{number_format($data->sum('recieved'), 2)}}</td>
                        <td class="border-left border-right">{{number_format($data->sum('per_completed')/$data->count(), 2)}}</td>
                        <td class="border-left border-right">{{number_format($data->sum('per_uncompleted')/$data->count(), 2)}}</td>
                        <td class="border-left border-right">{{number_format($data->sum('per_recieved')/$data->count(), 2)}}</td>
                    </tr>
                    @endif
                </tbody>
            </table>
            @endif
        </div>
    </div>
@endsection

@section('script')
@endsection
