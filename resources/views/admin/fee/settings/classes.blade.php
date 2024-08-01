@extends('admin.layout')
@section('section')
@php
    $year = request('year_id') ?? \App\Helpers\Helpers::instance()->getCurrentAccademicYear();
@endphp
<div class="py-4">
    <table class="table">
        <thead class="text-capitalize">
            <th>###</th>
            <th>{{__('text.word_programs')}}</th>
            <th>{{__('text.word_tution')}}</th>
            <th>{{__('text.first_instalment')}}</th>
            <th>{{__('text.second_instalment')}}</th>
            <th></th>
        </thead>
        <tbody>
            @php($k = 1)
            @foreach($classes as $program_level)
            <tr>
                <td>{{$k++}}</td>
                <td>{{ $program_level->name() }}</td>
                <td>{{ $program_level->amount ?? '----'}}</td>
                <td>{{ $program_level->first_instalment ?? '----'}}</td>
                <td>{{ $program_level->second_instalment ?? '----'}}</td>
                <td>
                    <a href="{{route('admin.campuses.set_fee', [$campus->id, $program_level->id])}}" class="btn btn-sm btn-primary">{{__('text.word_fees')}}</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection