@extends('admin.layout')
@section('section')
@php
    $year = request('year_id') == null ? \App\Helpers\Helpers::instance()->getCurrentAccademicYear() : request('year_id');
@endphp
<div class="py-4">
    <div class="col-lg-12">
        <div class="input-group-merge d-flex rounded border border-dark my-3">
            <select class="form-control col-sm-10" name="year" id="year_filter_field">
                <option></option>
                @foreach (\App\Models\Batch::all() as $batch)
                    <option value="{{$batch->id}}" {{$batch->id == $year ? 'selected' : ''}}>{{$batch->name}}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-sm btn-dark text-capitalize col-sm-2 text-center" onclick="event.preventDefault(); window.location = '{{url('admin/class_list')}}/'+$('#year_filter_field').val()">{{__('text.word_get')}}</button>
        </div>
    </div>
    <table class="table">
        <!-- list program level students per campus -->
        @if(request()->has('campus_id'))
            <thead class="text-capitalize">
                <th>###</th>
                <th>{{__('text.word_name')}}</th>
                <th>{{__('text.word_matricule')}}</th>
                <th>{{__('text.academic_year')}}</th>
            </thead>
            <tbody>
                @php($k = 1)
                @foreach(\App\Models\StudentClass::where('student_classes.class_id', request('id'))->where('student_classes.year_id', $year)
                ->join('students', ['students.id'=>'student_classes.student_id'])->where('students.campus_id', request('campus_id'))->where('students.active', 1)->get(['students.*', 'student_classes.year_id as year']) as $stud)
                    <tr>
                        <td>{{$k++}}</td>
                        <td>{{$stud->name}}</td>
                        <td>{{$stud->matric}}</td>
                        <td>{{\App\Models\Batch::find($stud->year)->name ?? '----'}}</td>
                    </tr>
                @endforeach
            </tbody>
        @else
            
        <!-- list program levels (classes) -->
            @if(!request()->has('id'))
                <thead class="text-capitalize">
                    <th>###</th>
                    <th>{{__('text.word_class')}}</th>
                    <th></th>
                </thead>
                <tbody>
                    @php($k = 1)
                    @foreach(\App\Http\Controllers\Controller::sorted_program_levels() as $pl)
                        @if((\Auth::user()->campus_id != null) && in_array($pl['id'], \App\Models\Campus::find(\Auth::user()->campus_id)->campus_programs()->pluck('program_level_id')->toArray()))
                        <tr>
                            <td>{{ $k++ }}</td>
                            <td>{{ $pl['name'] }}</td>
                            <td>
                                <a href="{{Request::url().'?id='.$pl['id']}}" class="btn btn-sm btn-primary">{{__('text.word_students')}}</a>

                            </td>
                        </tr>
                        @endif
                        @if(\Auth::user()->campus_id == null)
                        <tr>
                            <td>{{ $k++ }}</td>
                            <td>{{ $pl['name'] }}</td>
                            <td>
                                <a href="{{Request::url().'?id='.$pl['id']}}" class="btn btn-sm btn-primary">{{__('text.word_students')}}</a>
                                <a href="{{Request::url().'?action=campuses&id='.$pl['id']}}" class="btn btn-sm btn-success">{{__('text.word_campuses')}}</a>

                            </td>
                        </tr>
                        @endif
                    @endforeach
                </tbody>
            @endif
            <!-- general class list for a program level -->
            @if(request()->has('id') && !request()->has('action'))
                <thead class="text-capitalize">
                    <th>###</th>
                    <th>{{__('text.word_name')}}</th>
                    <th>{{__('text.word_matricule')}}</th>
                    <th>{{__('text.academic_year')}}</th>
                </thead>
                <tbody>
                    @php($k = 1)
                    @foreach(\App\Models\StudentClass::where('class_id', request('id'))->where('year_id', $year)->join('students', ['students.id'=>'student_classes.student_id'])->where('students.active', 1)->distinct()->get(['students.*', 'student_classes.year_id as year']) as $stud)
                        @if((\Auth::user()->campus_id == null) || ($stud->campus_id == \Auth::user()->campus_id))
                        <tr>
                            <td>{{$k++}}</td>
                            <td>{{$stud->name}}</td>
                            <td>{{$stud->matric}}</td>
                            <td>{{\App\Models\Batch::find($stud->year)->name ?? '----'}}</td>
                        </tr>
                        @endif
                    @endforeach
                </tbody>
            @endif

            <!-- List campuses for a given program level -->
            @if(request()->has('id') && request('action') =='campuses')
                <thead class="text-capitalize">
                    <th>###</th>
                    <th>{{__('text.word_campus')}}</th>
                    <th></th>
                </thead>
                <tbody>
                    @php($k = 1)
                    @foreach(\App\Models\ProgramLevel::find(request('id'))->campuses()->get() as $campus)
                        <tr>
                            <td>{{$k++}}</td>
                            <td>{{$campus->name}}</td>
                            <td>
                                <a href="{{Request::url().'?action=campus_students&id='.request('id').'&campus_id='.$campus->id}}" class="btn btn-success btn-sm">{{__('text.word_students')}}</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            @endif
        @endif
    </table>
</div>
@endsection