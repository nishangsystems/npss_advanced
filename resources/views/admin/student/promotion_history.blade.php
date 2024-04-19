@extends('admin.layout')
@section('section')
    <div class="py-3">
        @if (!isset($promotions))
            <table class="table table-stripped">
                <thead class="text-capitalize">
                    <th class="border-left border-right">#</th>
                    <th class="border-left border-right">{{ __('text.word_section') }}</th>
                    <th class="border-left border-right"></th>
                </thead>
                <tbody>
                    @php
                        $k = 1;
                    @endphp
                    @foreach (\App\Models\SchoolUnits::where('unit_id', 4)->orderBy('name')->get() as $prog)
                        <tr>
                            <td>{{ $k++ }}</td>
                            <td>{{ $prog->name }}</td>
                            <td><a href="{{ route('admin.students.promotions', $prog->id) }}" class="btn btn-primary btn-xs">{{ __('text.promotion_history') }}</a></td>
                        </tr>
                    @endforeach
                <tbody>
            </table>
        @else
            <table class="table table-stripped">
                <thead class="text-capitalize">
                    <th class="border-right border-left">#</th>
                    <th class="border-right border-left">{{ __('text.base_class') }}</th>
                    <th class="border-right border-left">{{ __('text.target_class') }}</th>
                    <th class="border-right border-left">{{ __('text.base_year') }}</th>
                    <th class="border-right border-left">{{ __('text.target_year') }}</th>
                    <th class="border-right border-left">{{ __('text.number_promoted') }}</th>
                    <th class="border-right border-left">{{ __('text.word_date') }}</th>
                </thead>
                <tbody>
                    @php
                        $x = 1;
                    @endphp
                    @foreach ($promotions as $prom)
                        <tr>
                            <td class="border-left border-right">{{ $x++ }}</td>
                            <td class="border-left border-right">{{ $prom->class->name() }}</td>
                            <td class="border-left border-right">{{ $prom->nextClass->name() }}</td>
                            <td class="border-left border-right">{{ $prom->year->name }}</td>
                            <td class="border-left border-right">{{ $prom->nextYear->name }}</td>
                            @if (auth()->user()->campus_id == null)
                                <td class="border-left border-right">{{ $prom->students->count() }}</td>
                            @else
                                <td class="border-left border-right">{{ $prom->students()->join('students', 'students.id', '=', 'student_promotions.student_id')->where('students.campus_id', auth()->user()->campus_id)->distinct()->count() }}</td>
                            @endif
                            <td class="border-left border-right">{{ \Illuminate\Support\Carbon::parse($prom->created_at)->format('d/m/Y - H:i') }}</td>
                        </tr>
                    @endforeach
                <tbody>
            </table>
            <div class=" px-3 py-2">
                <a href="{{ route('admin.students.promotions') }}" class="btn btn-xs btn-secondary text-capitalize px-3">{{ __('text.word_back') }}</a>
            </div>
        @endif
    </div>
@endsection