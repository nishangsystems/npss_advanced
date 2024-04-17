@extends('admin.layout')
@section('section')
<div class="py-4">
    <table class="table">
        <thead class="text-capitalize">
            <th>S/N</th>
            <th>{{__('text.word_level')}}</th>
            <th>{{__('text.word_matricule')}}/@lang('text.word_prefix')</th>
            <th></th>
        </thead>
        <tbody>
            @php($k = 1)
            @foreach(\App\Models\Level::All() as $level)
            <tr style="background-color: {{in_array($level->id, $program_levels) ? '#f4fcfc' : '#fff'}}">
                <td>{{$k++}}</td>
                <td>{{$level->level}}</td>
                <td>
                    @if(!in_array($level->id, $program_levels))
                        <form method="POST"  action="{{ route('admin.students.matricule.prefix', ['program_id'=>$program->id, 'level_id'=>$level->id]) }}">
                            @csrf
                            <div class="d-flex">
                                <div>
                                    <input class="form-control input-md rounded" name="prefix" value="{{ \App\Models\ProgramLevel::where(['program_id'=>$program->id, 'level_id'=>$level->id])->first()->prefix??'' }}">
                                </div>
                                <button type="submit" class="btn btn-primary btn-xs rounded "><span class="fa fa-save"></span><button>
                            </div>
                        <form>
                    @endif
                </td>
                <td>
                    @if(!in_array($level->id, $program_levels))
                        {{-- <a href="{{route('admin.units.subjects', [\App\Models\ProgramLevel::where('program_id', request('id'))->where('level_id', $level->id)->first()->id])}}" class="btn btn-sm btn-primary">{{__('text.word_subjects')}}</a>|
                        @if (\App\Models\ClassSubject::where(['class_id'=>\App\Models\ProgramLevel::where('program_id', request('id'))->where('level_id', $level->id)->first()->id])->count() == 0)
                            <a href="" onclick="event.preventDefault(); url = `{{route('admin.units.drop_level', [\App\Models\ProgramLevel::where(['program_id'=>request('id'),'level_id'=>$level->id])->first()->id])}}`; window.location = confirm(`You are about to drop {{\App\Models\ProgramLevel::where(['program_id'=>request('id'),'level_id'=>$level->id])->first()->name()}}`) ? url : '#'" class="btn btn-sm btn-danger">{{__('text.word_drop')}}</a>|
                        @endif
                    @else --}}
                        <a href="{{route('admin.programs.levels.add', [request('id'), $level->id])}}" class="btn btn-sm btn-success"> <i class="fa fa-plus fa-lg  "></i> {{__('text.word_add')}}</a>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection