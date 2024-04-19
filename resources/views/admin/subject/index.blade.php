@extends('admin.layout')

@section('section')
<!-- page start-->

<div class="col-sm-12">
    <p class="text-muted">
        <a href="{{route('admin.subjects.create')}}" class="btn btn-info btn-xs">Add Subjects</a>
    </p>

    <div class="content-panel">
        <div class="adv-table table-responsive">
            <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered" id="hidden-table-info">
                <thead>
                    <tr class=" text-capitalize">
                        <th>#</th>
                        <th>{{__('text.course_code')}}</th>
                        <th>{{__('text.word_name')}}</th>
                        <th>{{__('text.word_coefficient')}}</th>
                        <th>{{__('text.word_semester')}}</th>
                        <th>{{__('text.word_level')}}</th>
                        <th>{{__('text.word_status')}}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>

                    @foreach($subjects as $k=>$subject)
                    <tr>
                        <td>{{ $k+1 }}</td>
                        <td>{{ $subject->code }}</td>
                        <td>{{ $subject->name }}</td>
                        <td>{{ $subject->coef }}</td>
                        <td>{{ \App\Models\Semester::find($subject->semester_id)->name }}</td>
                        <td>{{ \App\Models\Level::find($subject->level_id)->level }}</td>
                        <td>{{ $subject->status }}</td>
                        <td class="d-flex justify-content-end align-items-center text-capitalize">
                            <a class="btn btn-xs btn-success" href="{{route('admin.subjects.edit',[$subject->id])}}"><i class="fa fa-edit"> {{__('text.word_edit')}}</i></a> |
                            @if ((\App\Models\ClassSubject::where('subject_id', $subject->id)->count() == 0) && (\App\Models\Result::where('subject_id', $subject->id)->count() == 0))
                                <a onclick="event.preventDefault();
                                                document.getElementById('delete').submit();" class=" btn btn-danger btn-xs m-2">{{__('text.word_delete')}}</a>
                                <form id="delete" action="{{route('admin.subjects.destroy',$subject->id)}}" method="POST" style="display: none;">
                                    @method('DELETE')
                                    {{ csrf_field() }}
                                </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="d-flex justify-content-end">
                {{-- {{$subjects->links() ?? ''}} --}}
            </div>
        </div>
    </div>
</div>
@endsection