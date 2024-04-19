@extends('admin.layout')
@section('section')
@php
    $year = request('year_id') == null ? \App\Helpers\Helpers::instance()->getCurrentAccademicYear() : request('year_id');
@endphp
<div class="py-4">
    <div class="col-lg-12">
        <div class="input-group-merge d-flex rounded border border-dark my-3">
            <select class="form-control col-sm-10 text-uppercase" name="filter" id="group_filter_field">
                <option></option>
                <option value="SCHOOL" {{ ($filter == 'SCHOOL') ? 'selected' : ''}}>{{__('text.word_school')}}</option>
                <option value="DEPARTMENT" {{ ($filter == 'DEPARTMENT') ? 'selected' : ''}}>{{__('text.word_department')}}</option>
                <option value="SECTION" {{ ($filter == 'SECTION') ? 'selected' : ''}}>{{__('text.word_section')}}</option>
                <option value="CLASS" {{ ($filter == 'CLASS') ? 'selected' : ''}}>{{__('text.word_class')}}</option>
            </select>
            <button type="submit" class="btn btn-sm btn-dark text-capitalize col-sm-2 text-center" onclick="event.preventDefault(); window.location = `{{route('admin.student.bulk.index', ['filter'=>'__FILTER__'])}}`.replace('__FILTER__', $('#group_filter_field').val())">{{__('text.word_get')}}</button>
        </div>
        @if ($filter != null)
            <table class="adv-table table">
                <thead class="text-capitalize">
                    <th>###</th>
                    <th>{{__('text.word_unit')}}</th>
                    <th></th>
                </thead>
                <tbody>
                    @php($k = 1)
                    @foreach (collect($items ?? [])->sortBy('name')->toArray() as $row)
                        <tr>
                            <td>{{$k++}}</td>
                            <td>{{$row['name']}}</td>
                            <td>
                                <a class="btn btn-sm btn-primary" href="{{route('admin.student.bulk.list', ['filter'=>$filter, 'item_id'=>$row['id']])}}">{{__('text.word_students')}}</a>
                                <a class="btn btn-sm btn-success" href="{{route('admin.messages.bulk', ['filter'=>$filter, 'item_id'=>$row['id'], 'recipients'=>'students'])}}">{{__('text.notify_students')}}</a>
                                <a class="btn btn-sm btn-info" href="{{route('admin.messages.bulk', ['filter'=>$filter, 'item_id'=>$row['id'], 'recipients'=>'parents'])}}">{{__('text.notify_parents')}}</a>
                                <a class="btn btn-sm btn-dark" href="{{route('notifications.create', ['layer'=>$filter, 'layer_id'=>$row['id'], 'campus_id'=>auth()->user()->campus_id??0])}}">{{__('text.create_notification')}}</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        
        @endif
    </div>
    <table class="table">
        
</div>
@endsection