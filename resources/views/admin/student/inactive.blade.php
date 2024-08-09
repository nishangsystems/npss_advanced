@extends('admin.layout')

@section('section')
@php
    $year = request('year') ?? \App\Helpers\Helpers::instance()->getCurrentAccademicYear();
@endphp

<div class="col-sm-12">
    <div class="col-lg-12">
        <form method="get">
            <div class="input-group-merge d-flex rounded border border-dark my-3">
                <select class="form-control col-sm-10" name="year">
                    <option></option>
                    @foreach (\App\Models\Batch::all() as $batch)
                        <option value="{{$batch->id}}" {{$batch->id == $year ? 'selected' : ''}}>{{$batch->name}}</option>
                    @endforeach
                </select>
                <input type="submit" value="{{__('text.word_get')}}" class="btn btn-sm btn-dark text-capitalize col-sm-2 text-center">
            </div>
        </form>
    </div>
    <div class="">
        <div class=" ">
            <table cellpadding="0" cellspacing="0" border="0" class="table table-stripped" id="hidden-table-info">
                <thead>
                    <tr class="text-capitalize">
                        <th>#</th>
                        <th>{{__('text.word_name')}}</th>
                        <th>{{__('text.word_matricule')}}</th>
                        <th>{{__('text.word_campus')}}</th>
                        <th>{{__('text.word_class')}}</th>
                        <th></th>

                    </tr>
                </thead>
                <tbody id="table_body">
                    @php
                        $k = 1;
                    @endphp
                    @foreach ($students as $stud)
                        <tr>
                            <td>{{$k++}}</td>
                            <td>{{$stud->name}}</td>
                            <td>{{$stud->matric}}</td>
                            <td>{{$stud->campus->name}}</td>
                            <td>{{$stud->_class()->name()}}</td>
                            <td class="d-flex justify-content-end  align-items-start text-capitalize">
                                <a class="btn btn-sm btn-primary m-1" href="{{route('admin.student.show', $stud->id)}}"><i class="fa fa-info-circle"> {{__('text.word_view')}}</i></a> |
                                <a class="btn btn-sm btn-warning m-1" onclick="confirm('Your are about to change student status for {{$stud->name}}?') ? (window.location='{{ route('admin.student.change_status', $stud->id) }}') : null">
                                    <i class="fa fa-cog">
                                            {{__('text.word_activate')}}
                                    </i>
                                </a>
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
