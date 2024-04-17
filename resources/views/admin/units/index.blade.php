@extends('admin.layout')

@section('section')
    <!-- page start-->

    <div class="col-sm-12">
        <p class="text-muted  text-capitalize">
           @if(!request('action'))
                @if(\App\Models\SchoolUnits::find($parent_id)->unit->count() > 0)
                    <a href="{{route('admin.units.create', [$parent_id])}}?type={{\App\Models\SchoolUnits::find($parent_id)->unit->first()->type->id}}" class="btn btn-info btn-xs">{{__('text.word_add')}} {{\App\Models\SchoolUnits::find($parent_id)->unit->first()->type->name}} </a>
                @elseif(\App\Models\SchoolUnits::find($parent_id)->subjects->count() > 0)
                    <a href="{{route('admin.subjects.index', [$parent_id])}}" class="btn btn-info btn-xs">{{__('text.add_subjects')}}</a>
                @else
                    <a href="{{route('admin.units.create', [$parent_id])}}" class="btn btn-info btn-xs">{{__('text.add_unit')}}</a> |
                    <a href="{{route('admin.subjects.index', [$parent_id])}}" class="btn btn-info btn-xs">{{__('text.add_subjects')}}</a>
                @endif
           @endif
        </p>

        <div class="content-panel">
            <div class="table-responsive">
                <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered" id="hidden-table-info">
                    <thead>
                    <tr class=" text-capitalize">
                        <th>{{__('text.word_name')}}</th>
                        <th>{{__('text.word_type')}}</th>
                        <th>{{__('text.word_matricule')}}</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>

                    @foreach($units as $unit)
                        <tr>
                            <td>{{ $unit->name }}</td>
                            <td>{{$unit->type->name}}</td>
                            <td>{{$unit->prefix}}xx{{$unit->suffix}}xxx</td>
                            <td class="d-flex justify-content-end align-items-center text-capitalize">
                                
                                @if(request('action') == 'class_list')
                                    <a class="btn btn-xs btn-primary" href="{{route('admin.students.index', [$unit->id])}}?action={{request('action')}}">{{trans_choice('text.word_student', 2)}}</a> |
                                    <a  class="btn btn-xs btn-success" href="{{route('admin.units.index', [$unit->id])}}?action={{request('action')}}">{{__('text.sub_unit')}}</a>
                                @else
                                    @if($unit->students(Session::get('mode', \App\Helpers\Helpers::instance()->getCurrentAccademicYear()))->count()  > 0)
                                        <a class="btn btn-xs btn-primary" href="{{route('admin.students.index', [$unit->id])}}">{{trans_choice('text.word_student', 2)}}</a> |
                                    @endif
                                    
                                    <a  class="btn btn-xs btn-primary" href="{{route('admin.units.index', [$unit->id])}}">{{__('text.sub_unit')}}</a> |
                                    <a class="btn btn-xs btn-success" href="{{route('admin.units.edit',[$unit->id])}}"><i class="fa fa-edit"> {{__('text.word_edit')}}</i></a>
                                    @if($unit->unit->count() == 0)
                                        | <a onclick="event.preventDefault();
                                        document.getElementById('delete_{{ $unit->id }}').submit();" class=" btn btn-danger btn-xs m-2">{{__('text.word_delete')}}</a>
                                        <form id="delete_{{ $unit->id }}" action="{{route('admin.units.destroy',$unit->id)}}" method="POST" style="display: none;">
                                            @method('DELETE')
                                            {{ csrf_field() }}
                                        </form>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop
