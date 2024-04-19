@extends('admin.layout')
@section('section')
<div class="py-4">
    <form action="{{route('admin.subjects.store')}}" method="post" class="text-capitalize">
        @csrf
        <input type="hidden" name="semester" id="" value="{{request('semester')}}}">
        <div class="row my-2">
            <label for="" class="col-md-3">{{__('text.word_title')}}</label>
            <div class="col-md-9 col-lg-9">
                <input type="text" name="name" id="" required class="form-control">
            </div>
        </div>
        <div class="row my-2">
            <label for="" class="col-md-3">{{__('text.course_code')}}</label>
            <div class="col-md-9 col-lg-9">
                <input type="text" name="code" id="" required class="form-control">
            </div>
        </div>
        <div class="row my-2">
            <label for="" class="col-md-3">{{__('text.word_coefficient')}}</label>
            <div class="col-md-9 col-lg-9">
                <input type="number" name="coef" id="" min="1" required class="form-control">
            </div>
        </div>
        <div class="row my-2">
            <label for="" class="col-md-3">{{__('text.word_class')}}</label>
            <div class="col-md-9 col-lg-9">
                <select name="level" id="" required class="form-control">
                    <option value="">{{__('text.select_class')}}</option>
                    @foreach(\App\Models\Level::all() as $level)
                    <option value="{{$level->id}}">{{$level->level}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row my-2">
            <label for="" class="col-md-3">{{__('text.word_status')}}</label>
            <div class="col-md-9 col-lg-9">
                <select name="status" id="" required class="form-control text-uppercase">
                    <option value="">{{__('text.select_status')}}</option>
                    <option value="C">C</option>
                    <option value="R">R</option>
                    <option value="G">G</option>
                </select>
            </div>
        </div>
        <div class="d-flex justify-content-end my-2">
            <input type="submit" value="{{__('text.word_save')}}" class="btn btn-primary btn-sm">
        </div>
    </form>
    <hr>
    <div>
        <table class="table">
            <thead>
                <th>###</th>
                <th>{{__('text.word_title')}}</th>
                <th>{{__('text.course_code')}}</th>
                <th>{{__('text.word_coefficient')}}</th>
                <th>{{__('text.word_class')}}</th>
                <th>{{__('text.word_status')}}</th>
                <th></th>
            </thead>
            <tbody>
                @php($k = 1)
                @foreach(\App\Models\Subjects::where('semester_id', request('semester'))->orderBy('updated_at', 'DESC')->get() as $subj)
                <tr>
                    <td>{{$k++}}</td>
                    <td>{{$subj->name}}</td>
                    <td>{{$subj->code}}</td>
                    <td>{{$subj->coef}}</td>
                    <td>{{\App\Models\Level::find($subj->level_id)->level}}</td>
                    <td>{{$subj->status}}</td>
                    <td>
                        <a href="{{route('admin.subjects.edit', $subj->id)}}" class="btn btn-primary btn-sm">{{__('text.word_edit')}}</a> | 
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection