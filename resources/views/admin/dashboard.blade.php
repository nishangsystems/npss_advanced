@extends('admin.layout')
@section('section')
<div>
    <div>
        {{-- <div class="space-6"></div> --}}

        <div class="w-100 py-3 text-capitalize" style="font-size: larger; font-weight: bold; color: gray !important;">{{ __('text.general_information') }}</div>
        <div class="text-capitalize">
            {{-- GENERAL STATISTICS --}}
            <div class="infobox border border-dark mx-2 my-1 rounded infobox-green">
                <div class="infobox-icon">
                    <i class="ace-icon fa fa-users"></i>
                </div>

                <div class="infobox-data">
                    <span class="infobox-data-number">{{ number_format(count($active_students)) }}</span>
                    <div class="infobox-content">{{ __('text.active_students') }}</div>
                </div>

                <div class="stat stat-success">{{count($students) == 0 ? 0 : number_format(100*count($active_students)/count($students), 2) }}%</div>
            </div>

            <div class="infobox border border-dark mx-2 my-1 rounded infobox-black">
                <div class="infobox-icon">
                    <i class="ace-icon fa fa-users"></i>
                </div>

                <div class="infobox-data">
                    <span class="infobox-data-number">{{ number_format(count($inactive_students)) }}</span>
                    <div class="infobox-content">{{ __('text.inactive_students') }}</div>
                </div>

                <div class="badge badge-dark">
                    {{count($students) == 0 ? 0 : number_format(100*count($inactive_students)/count($students), 2) }}%
                    <i class="ace-icon fa fa-arrow-up"></i>
                </div>
            </div>

            <div class="infobox border border-dark mx-2 my-1 rounded infobox-black">
                <div class="infobox-icon">
                    <i class="ace-icon fa fa-users"></i>
                </div>

                <div class="infobox-data">
                    <span class="infobox-data-number">{{ number_format(count($active_students->where('gender', 'male'))) }}</span>
                    <div class="infobox-content">{{ __('text.active_male_students') }}</div>
                </div>

                <div class="badge badge-dark">
                    {{count($active_students) == 0 ? 0 : number_format(100*count($active_students->where('gender', 'male'))/count($active_students), 2) }}%
                    <i class="ace-icon fa fa-arrow-up"></i>
                </div>
            </div>

            <div class="infobox border border-dark mx-2 my-1 rounded infobox-black">
                <div class="infobox-icon">
                    <i class="ace-icon fa fa-users"></i>
                </div>

                <div class="infobox-data">
                    <span class="infobox-data-number">{{ number_format(count($active_students->where('gender', 'female'))) }}</span>
                    <div class="infobox-content">{{ __('text.active_female_students') }}</div>
                </div>

                <div class="badge badge-dark">
                    {{ count($active_students) == 0 ? 0 : number_format(100*count($active_students->where('gender', 'female'))/count($active_students), 2) }}%
                    <i class="ace-icon fa fa-arrow-up"></i>
                </div>
            </div>

            <div class="infobox border border-dark mx-2 my-1 rounded infobox-pink">
                <div class="infobox-icon">
                    <i class="ace-icon fa fa-user"></i>
                </div>

                <div class="infobox-data">
                    <span class="infobox-data-number">{{ $n_teachers }}</span>
                    <div class="infobox-content">{{ __('text.word_teachers') }}</div>
                </div>
            </div>

            <div class="infobox border border-dark mx-2 my-1 rounded infobox-red">
                <div class="infobox-icon">
                    <i class="ace-icon fa fa-flask"></i>
                </div>

                <div class="infobox-data">
                    <span class="infobox-data-number">{{ $_programs->count() }}</span>
                    <div class="infobox-content">{{ __('text.word_programs') }}</div>
                </div>
            </div>

            <div class="infobox border border-dark mx-2 my-1 rounded infobox-purple">
                <div class="infobox-icon">
                    <a class="ace-icon fa fa-bell"></a>
                </div>

                <div class="infobox-data">
                    <span class="infobox-data-number">{{ $sms_total }}</span>
                    <div class="infobox-content">{{ __('text.total_sms_sent') }}</div>
                </div>

            </div>
            @if(!(isset($is_head_of_school) and $is_head_of_school == 1))
                @foreach ($other_incomes as $income)
                    <div class="infobox border border-dark mx-2 my-1 rounded infobox-purple">
                        <div class="infobox-icon">
                            <a class="ace-icon fa fa-money"></a>
                        </div>

                        <div class="infobox-data">
                            <span class="infobox-data-number">{{ number_format($income->amount) }}</span>
                            <div class="infobox-content">{{ $income->name }}</div>
                        </div>

                    </div>
                @endforeach
            @endif
        </div>


        @if(!(isset($is_head_of_school) and $is_head_of_school == 1))
            {{-- FINANCIAL STATISTICS --}}
            <div class="w-100 py-3 text-capitalize" style="font-size: larger; font-weight: bold; color: gray !important;">{{ __('text.fee_information') }}</div>
            <div class=" text-capitalize">
                <div class="infobox border border-dark mx-2 my-1 rounded infobox-blue2">
                    <div class="infobox-icon">
                        <a class="ace-icon fa fa-money fa-spin"></a>
                    </div>
                    <div class="infobox-data">
                        <span class="infobox-text">{{ number_format($expected_fee??0) }}</span>

                        <div class="infobox-content">
                            {{ __('text.total_fee_expected') }}
                        </div>
                    </div>
                </div>

                <div class="infobox border border-dark mx-2 my-1 rounded infobox-green">

                    <div class="infobox-icon">
                        <i class="ace-icon fa fa-money fa-spin"></i>
                    </div>

                    <div class="infobox-data">
                        <span class="infobox-text">{{ number_format($paid_fee??0) }}</span>

                        <div class="infobox-content">
                            {{ __('text.total_fee_paid') }}
                        </div>
                    </div>
                    <div class="stat stat-success mt-3">{{ $expected_fee == 0 ? 0 : number_format(100*$paid_fee/$expected_fee, 2) }}%</div>
                </div>
                
                <div class="infobox border border-dark mx-2 my-1 text-danger rounded infobox-red">
                    <div class="infobox-icon">
                        <i class="ace-icon fa fa-money fa-spin"></i>
                    </div>
                    
                    <div class="infobox-data">
                        <span class="infobox-text">{{ number_format($owed_fee) }}</span>
                        
                        <div class="infobox-content">
                            {{ __('text.total_fee_owed') }}
                        </div>
                    </div>
                    <div class="stat stat-danger mt-3" style="color: red !important;">{{ $expected_fee == 0 ? 0 : number_format(100*$owed_fee/$expected_fee, 2) }}%</div>
                </div>

                <div class="infobox border border-dark mx-2 my-1 rounded infobox-blue2">
                    <div class="infobox-icon">
                        <a class="ace-icon fa fa-money fa-spin"></a>
                    </div>
                    <div class="infobox-data">
                        <span class="infobox-text">{{ number_format($recovered_debt??0) }}</span>
                        
                        <div class="infobox-content">
                            {{ __('text.debts_recovered') }}
                        </div>
                    </div>
                </div>
                
                <div class="infobox border border-dark mx-2 my-1 rounded infobox-blue2">
                    <div class="infobox-icon">
                        <a class="ace-icon fa fa-money fa-spin"></a>
                    </div>
                    <div class="infobox-data">
                        <span class="infobox-text">{{ number_format($expected_reg_fee??0) }}</span>

                        <div class="infobox-content">
                            {{ __('text.registration_fee_expected') }}
                        </div>
                    </div>
                </div>

                <div class="infobox border border-dark mx-2 my-1 rounded infobox-green">

                    <div class="infobox-icon">
                        <i class="ace-icon fa fa-money fa-spin"></i>
                    </div>

                    <div class="infobox-data">
                        <span class="infobox-text">{{ number_format($recieved_reg_fee??0) }}</span>

                        <div class="infobox-content">
                            {{ __('text.registration_fee_paid') }}
                        </div>
                    </div>
                    <div class="stat stat-success mt-3">{{ $expected_reg_fee == 0 ? 0 : number_format(100*$recieved_reg_fee/$expected_reg_fee, 2) }}%</div>
                </div>
            </div>
        @endif

        {{-- PROGRAM STATISTICS--}}
        <div class="w-100 py-3 text-capitalize" style="font-size: larger; font-weight: bold; color: gray !important;">{{ __('text.program_information') }}</div>
        <div class="">
            <table class="table-stripped table-light">
                <thead class="border-top border-bottom border-dark bg-dark text-white text-capitalize" style="font-weight: semibold;">
                    <tr class="border-top border-bottom">
                        <th class="border-left border-right border-secondary">{{ __('text.sn') }}</th>
                        <th colspan="4" class="border-left border-right border-secondary"></th>
                        <th colspan="{{ count($levels) }}" class="border-left border-right border-secondary text-capitalize">{{ __('text.word_class') }}</th>
                    </tr>
                    <tr class="border-top border-bottom" style="letter-spacing: 2px !important;">
                        <th class="border-left border-right border-secondary"> <span style="">{{ __('text.sn') }}</span></th>
                        <th class="border-left border-right border-secondary"> <span style="">{{ __('text.word_program') }}</span></th>
                        <th class="border-left border-right border-secondary"> <span style="writing-mode: vertical-rl !important; text-orientation: mixed important;">{{ __('text.no_of_students') }}</span></th>
                        <th class="border-left border-right border-secondary"> <span style="writing-mode: vertical-rl !important; text-orientation: mixed important;">{{ __('text.word_males') }}</span></th>
                        <th class="border-left border-right border-secondary"> <span style="writing-mode: vertical-rl !important; text-orientation: mixed important;">{{ __('text.word_females') }}</span></th>
                        @foreach ($levels as $level)
                            <th class="border-left border-right border-secondary"> <span style="writing-mode: vertical-rl !important; text-orientation: mixed important;">{{ $level->level }}</span></th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @php
                        $k = 1;
                    @endphp
                    @foreach ($programs as $program)
                        <tr class="border-bottom border-secondary">
                            <td class="border-left border-right border-secondary">{{ $k++ }}</td>
                            <td class="border-left border-right border-secondary">{{ $program->first()->program_name??'' }}</td>
                            <td class="border-left border-right border-secondary">{{ $program->count() }}</td>
                            <td class="border-left border-right border-secondary">{{ $program->where('gender', 'male')->count() }}</td>
                            <td class="border-left border-right border-secondary">{{ $program->where('gender', 'female')->count() }}</td>
                            @foreach($program->levels as $key => $level)
                                <td class="border-left border-right border-secondary">{{ $level }}</td>  
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
@section('script')

@endsection
