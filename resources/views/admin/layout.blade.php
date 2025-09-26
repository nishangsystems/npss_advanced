<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta charset="utf-8" />
    <title>{!! $title ?? '' !!} | {{__('text.app_name')}}</title>

    <meta name="description" content="overview &amp; stats" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="{{asset('css/app.css')}}" />
    <link rel="stylesheet" href="{{asset('assets/css/bootstrap.min.css')}}" />
    <link rel="stylesheet" href="{{asset('assets/font-awesome/4.5.0/css/font-awesome.min.css')}}" />
    <link rel="stylesheet" href="{{asset('assets/css/fonts.googleapis.com.css')}}" />
    <link rel="stylesheet" href="{{asset('assets/css/ace.min.css')}}" class="ace-main-stylesheet" id="main-ace-style" />
    <link rel="stylesheet" href="{{asset('assets/css/ace-part2.min.css')}}" class="ace-main-stylesheet" />
    <link rel="stylesheet" href="{{asset('assets/css/ace-skins.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('assets/css/ace-rtl.min.css')}}"/>
    <script src="{{asset('assets/js/ace-extra.min.js')}}"></script>
    <link rel="stylesheet" href="{{asset('assets/css/custom.css')}}" class="ace-main-stylesheet" id="main-ace-style"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('libs')}}/datatables.net-bs4/css/dataTables.bootstrap4.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('libs')}}/datatables.net-bs4/css/responsive.dataTables.min.css">


    <link rel="stylesheet" href="{{asset('richtexteditor/rte_theme_default.css')}}" />
    <script type="text/javascript" src="{{asset('/richtexteditor/rte.js')}}"></script>
    <script type="text/javascript" src="{{asset('/richtexteditor/plugins/all_plugins.js')}}"></script>
    
    
    <!-- <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-show-password/1.0.3/bootstrap-show-password.min.js"></script> -->

    <STYLE>
        body {
            font-family: Arial, Helvetica, sans-serif;
        }
        .input-group {
            position: relative;
            display: flex;
            flex-wrap: nowrap;
            align-items: stretch;
            width: 100%;
        }
        .dt-button{
            background-image: none!important;
            border: 1px solid #FFF;
            border-radius: 0;
            padding: 5px 20px;
            border-radius: 5px;
            box-shadow: none!important;
            -webkit-transition: background-color .15s,border-color .15s,opacity .15s;
            -o-transition: background-color .15s,border-color .15s,opacity .15s;
            transition: background-color .15s,border-color .15s,opacity .15s;
            vertical-align: middle;
            margin: 0;
            position: relative;
        }
        table{padding: 0px !important}
        table th, table td{
            padding: 10px;
        }
        .table td{
            border-bottom: 1px  solid  #f1f1f1 !important;
        }
        .nav li {
            display: block;
            width: 100% !important;
        }
        .dropdown-toggle:after {
            display: none;
        }

        .compressed-table{
            padding: 0 !important;
            font-size: 9px !important;
            font-weight: 600 !important;
            color: black;
            border: 1px solid #ccc !important;
        }
        .compressed-table td, .compressed-table th{
            padding: 1px !important;
            border: 1px solid #ccc !important;
        }
        .compressed-table tr, .compressed-table thead, .compressed-table tbody{
            padding: 0 !important;
        }
        
    </STYLE>
@php
    $bg1 = \App\Http\Controllers\HomeController::getColor('background_color_1');
    $bg2 = \App\Http\Controllers\HomeController::getColor('background_color_2');
    $bg3 = \App\Http\Controllers\HomeController::getColor('background_color_3');
    $current_year = \App\Helpers\Helpers::instance()->getCurrentAccademicYear();
@endphp
</head>
<body class="no-skin">
<div class="pre-loader">
    <div class="sk-fading-circle">
        <div class="sk-circle1 sk-circle"></div>
        <div class="sk-circle2 sk-circle"></div>
        <div class="sk-circle3 sk-circle"></div>
        <div class="sk-circle4 sk-circle"></div>
        <div class="sk-circle5 sk-circle"></div>
        <div class="sk-circle6 sk-circle"></div>
        <div class="sk-circle7 sk-circle"></div>
        <div class="sk-circle8 sk-circle"></div>
        <div class="sk-circle9 sk-circle"></div>
        <div class="sk-circle10 sk-circle"></div>
        <div class="sk-circle11 sk-circle"></div>
        <div class="sk-circle12 sk-circle"></div>
    </div>
</div>

<div id="navbar" class="navbar navbar-default  ace-save-state" style="background-color: {{$bg1}};">
    <div class="navbar-container w-100 ace-save-state" id="navbar-container">
        <button type="button" class="navbar-toggle menu-toggler pull-left display" id="menu-toggler"
                data-target="#sidebar">
            <span class="sr-only">Toggle sidebar</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>

        <div class="navbar-header pull-left">
            <a class="navbar-brand">
                <small style="color: white;">
                    <i class="fa fa-leaf"></i>
                    {{config('app.name')}}
                    <sub>V 1.1</sub>
                </small>
            </a>
        </div>

        <div class="navbar-buttons navbar-header pull-right" role="navigation">
            <ul class="nav ace-nav d-flex flex-nowrap" style="">

                <li class="light-blue">
                    <span>
                        <span class="nav-user-photo fa fa-globe"></span>
                        <span>
                            <small class="text-capitalize text-white">
                                {{\Auth::user()->campus_id ? \App\Models\Campus::find(\Auth::user()->campus_id)->name : 'campus'}}
                            </small>
						</span>
                    </span>
                </li>
                <li class="grenn dropdown-modal">
                    <a data-toggle="dropdown" class="dropdown-toggle text-white font-weight-bold text-capitalize" href="#" id="navbarDropdownMenuLink" style="background-color: {{$bg2}};">
                        {{ Config::get('languages')[Session::has('appLocale') ? Session::get('appLocale') : App::getLocale()] }}
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                    @foreach (Config::get('languages') as $lang => $language)
                        @if ($lang != Session::get('appLocale'))
                                <a class="dropdown-item" href="{{ route('lang.switch', $lang) }}"> {{$language}}</a>
                        @endif
                    @endforeach
                    </div>
                </li>
                <li class="grenn dropdown-modal">
                    <a data-toggle="dropdown" class="dropdown-toggle text-white font-weight-bold text-capitalize" id="bg_primary_1"  style="background-color: {{$bg2}};">
                      {{\App\Models\Batch::find(\App\Helpers\Helpers::instance()->getCurrentAccademicYear())->name}}
                        <i class="ace-icon fa fa-caret-down"></i>
                    </a>


                    <ul class="dropdown-menu">
                       @foreach(\App\Models\Batch::all() as $batch)
                            <li>
                                <a href="{{ route('mode',$batch->id) }}">{{$batch->name}}</a>
                            </li>
                       @endforeach
                    </ul>
                </li>
                <li class="light-blue dropdown-modal">
                    <a data-toggle="dropdown" href="#" class="dropdown-toggle" id="bg_primary_2"  style="background-color: {{$bg2}};">
                        {{-- <img class="nav-user-photo" src="{{asset('assets/images/avatars/user.jpg')}}"
                             alt="Jason's Photo"/> --}}
                        <span>
						<small class="text-capitalize">{{__('text.word_welcome')}}</small>
                         {{\Auth::user()->name}}
						</span>

                        <i class="ace-icon fa fa-caret-down"></i>
                    </a>

                    <ul class="user-menu dropdown-menu-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close">

                        <li>
                            @if(\Auth::guard('student')->user() == null)
                                @if(\Auth::user()->isHod || \Auth::user()->isTeacher)
                                    <a href="{{route('user.home')}}" class="text-capitalize"><i
                                            class="ace-icon fa fa-user"></i>__('text.word_profile')</a>
                                @elseif(\Auth::user()->isAdmin)
                                    <a href="{{route('admin.home')}}" class="text-capitalize"><i
                                            class="ace-icon fa fa-user"></i>__('text.word_profile')</a>
                                @endif
                            @else
                                <a href="{{route('student.home')}}" class="text-capitalize"><i
                                        class="ace-icon fa fa-user"></i>__('text.word_profile')</a>
                            @endif
                        </li>
                        <li>
                            <a href="{{ route('logout') }}" onclick="event.preventDefault();
												document.getElementById('logout-form').submit();" class="text-capitalize">
                                <i class="ace-icon fa fa-power-off"></i>
                                {{__('text.word_logout')}}
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div><!-- /.navbar-container -->
</div>
<div class="main-container ace-save-state" id="main-container">

    <div id="sidebar" class="sidebar                  responsive                    ace-save-state">
        <script type="text/javascript">
            try {
                ace.settings.loadState('sidebar')
            } catch (e) {
            }
        </script>

        <div class="sidebar-shortcuts" id="sidebar-shortcuts" style="background-color: {{$bg3}};">
            <div>
                <h5>{{\App\Models\Batch::find(\App\Helpers\Helpers::instance()->getCurrentAccademicYear())->name}}</h5>
            </div>
        </div><!-- /.sidebar-shortcuts -->
        <ul class="nav nav-list">
            <li>
                <a
                    href="{{route('admin.home')}}">
                    <i style="color: {{$bg1}}" class="menu-icon fa fa-dashboard"></i>
                    <span class="menu-text text-capitalize">{{__('text.word_dashboard')}}</span>
                </a>
                <b class="arrow"></b>
            </li>
            
           
            @if (\Auth::user()->hasPermissionTo('manage_subject'))
            <li>
                <a href="" class="dropdown-toggle text-capitalize">
                    <i  style="color: {{$bg1}}"class="menu-icon  fa fa-book"></i>
                    <span class="menu-text">
							{{__('text.course_zone')}}
					</span>
                    <b class="arrow fa fa-angle-down"></b>
                </a>

                <ul class="submenu">
                    <li>
                        <a href="{{route('admin.subjects.index')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.all_courses')}}
                        </a>
                        <b class="arrow"></b>
                    </li>
                    <li>
                        <a href="{{route('admin.subjects.create')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.add_course')}}
                        </a>
                        <b class="arrow"></b>
                    </li>

                </ul>
            </li>
            @endif
            
            @if (\Auth::user()->hasPermissionTo('basic_settings'))
            <li>
                <a href="#" class="dropdown-toggle text-capitalize">
                    <i  style="color: {{$bg1}}"class="menu-icon  fa fa-cog"></i>
                    <span class="menu-text"> {{__('text.base_settings')}}</span>

                    <b class="arrow fa fa-angle-down"></b>
                </a>

                <b class="arrow"></b>

                <ul class="submenu">
                    
                    <li>
                        <a href="{{route('admin.set_letter_head')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.set_letter_head')}}
                        </a>

                        <b class="arrow"></b>
                    </li>
                    {{-- <li>
                        <a href="{{route('admin.set_background_image')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.set_background_image')}}
                        </a>

                        <b class="arrow"></b>
                    </li> --}}
                    <li>
                        <a href="{{route('admin.set_watermark')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.set_watermark')}}
                        </a>

                        <b class="arrow"></b>
                    </li>
                    
                    <li>
                        <a href="{{route('admin.setayear')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.set_school_year')}}
                        </a>

                        <b class="arrow"></b>
                    </li>
                    <li>
                        <a href="{{route('admin.setsemester')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.set_semester')}}
                        </a>

                        <b class="arrow"></b>
                    </li>
                    <li>
                        <a href="{{route('admin.setcontacts')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.school_contacts')}}
                        </a>

                        <b class="arrow"></b>
                    </li>
                    <li>
                        <a href="{{route('admin.periods')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            @lang('text.word_periods')
                        </a>

                        <b class="arrow"></b>
                    </li>
                </ul>
            </li>
            @endif

            @if (\Auth::user()->hasPermissionTo('manage_setting'))
            <li>
                <a href="#" class="dropdown-toggle text-capitalize">
                    <i  style="color: {{$bg1}}"class="menu-icon  fa fa-cog"></i>
                    <span class="menu-text"> {{__('text.settings_zone')}}</span>

                    <b class="arrow fa fa-angle-down"></b>
                </a>

                <b class="arrow"></b>

                <ul class="submenu">

                    @if(auth()->user()->campus_id == null)
                        <li>
                            <a href="{{route('admin.sections')}}" class="text-capitalize">
                                <i class="menu-icon fa fa-caret-right"></i>
                                {{__('text.manage_sections')}}
                            </a>

                            <b class="arrow"></b>
                        </li>
                        
                        <li>
                            <a href="{{route('admin.programs.index')}}" class="text-capitalize">
                                <i class="menu-icon fa fa-caret-right"></i>
                                {{__('text.manage_programs')}}
                            </a>

                            <b class="arrow"></b>
                        </li>
                    @endif

                    <li>
                        <a href="{{route('admin.campuses.index')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                           {{__('text.manage_campuses')}}
                        </a>
                        <b class="arrow"></b>
                    </li>
                </ul>
            </li>
            @endif


            @if (\Auth::user()->hasPermissionTo('manage_student'))
                <li>
                    <a href="#" class="dropdown-toggle">
                        <i  style="color: {{$bg1}}"class="menu-icon  fa fa-graduation-cap"></i>
                        <span class="menu-text text-capitalize"> {{__('text.student_management')}}</span>

                        <b class="arrow fa fa-angle-down"></b>
                    </a>

                    <b class="arrow"></b>

                    <ul class="submenu">
                        <li>
                            <a href="{{route('admin.student.create')}}" class="text-capitalize">
                                <i class="menu-icon fa fa-caret-right"></i>
                                {{__('text.enroll_student')}}
                            </a>
                            <b class="arrow"></b>
                        </li>
                        <li>
                            <a href="{{route('admin.student.index')}}" class="text-capitalize">
                                <i class="menu-icon fa fa-caret-right"></i>
                                {{__('text.enrolled_students')}}
                            </a>
                            <b class="arrow"></b>
                        </li>
                        <li>
                            <a href="{{route('admin.students.inactive')}}" class="text-capitalize">
                                <i class="menu-icon fa fa-caret-right"></i>
                                {{__('text.inactive_students')}}
                            </a>
                            <b class="arrow"></b>
                        </li>
                        
                        <li>
                            <a href="{{route('admin.class.list')}}?action=class_list"  class="text-capitalize">
                                <i class="menu-icon fa fa-caret-right"></i>
                                {{__('text.class_list')}}
                            </a>
                            <b class="arrow"></b>
                        </li>
                        
                        <li>
                            <a href="{{route('admin.student.bulk.index')}}?action=class_list"  class="text-capitalize">
                                <i class="menu-icon fa fa-caret-right"></i>
                                {{__('text.student_listing')}}
                            </a>
                            <b class="arrow"></b>
                        </li>

                        <li>
                            <a href="{{route('admin.students.import')}}"  class="text-capitalize">
                                <i class="menu-icon fa fa-caret-right"></i>
                                {{trans_choice('text.import_student', 2)}}
                            </a>
                            <b class="arrow"></b>
                        </li>
                        @if (\Auth::user()->hasPermissionTo('promote_students'))
                            <li>
                                <a href="{{route('admin.students.init_promotion')}}"  class="text-capitalize">
                                    <strong  style="color: {{$bg1}}"class=" menu-icon ">&leftrightharpoons;</strong>
                                    {{__('text.promote_students')}}
                                </a>
                                <b class="arrow"></b>
                            </li>
                            <li>
                                <a href="{{route('admin.students.custom_promotion')}}"  class="text-capitalize">
                                    <strong  style="color: {{$bg1}}"class=" menu-icon ">&leftrightharpoons;</strong>
                                    {{__('text.custom_promotion')}}
                                </a>
                                <b class="arrow"></b>
                            </li>
                            <li>
                                <a href="{{route('admin.students.repeat_students')}}"  class="text-capitalize">
                                    <strong  style="color: {{$bg1}}"class=" menu-icon ">&leftrightharpoons;</strong>
                                    {{__('text.repeat_students')}}
                                </a>
                                <b class="arrow"></b>
                            </li>
                        @endif
                        <li>
                            <a href="{{route('admin.students.promotions')}}"  class="text-capitalize">
                                <strong  style="color: {{$bg1}}"class=" menu-icon ">&leftrightharpoons;</strong>
                                {{__('text.promotion_history')}}
                            </a>
                            <b class="arrow"></b>
                        </li>
                        {{-- @if (\Auth::user()->hasPermissionTo('demote_students-option_already_removed'))
                            <li>
                                <a href="{{route('admin.students.init_demotion')}}?type=promotion" class="text-capitalize">
                                    <strong style="color: {{$bg1}}" class="menu-icon">&Rrightarrow;</strong>
                                    {{__('text.demote_students')}}
                                </a>
                                <b class="arrow"></b>
                            </li>
                            <li>
                                <a href="{{route('admin.students.init_demotion')}}?type=demotion" class="text-capitalize">
                                    <strong style="color: {{$bg1}}" class="menu-icon">&Rrightarrow;</strong>
                                    {{__('text.word_demotions')}}
                                </a>
                                <b class="arrow"></b>
                            </li>
                        @endif --}}
                        {{-- @if (\Auth::user()->hasPermissionTo('approve_promotion'))
                            <li>
                                <a href="{{route('admin.students.trigger_approval')}}" class="text-capitalize">
                                    <strong style="color: {{$bg1}}" class="menu-icon">&Rrightarrow;</strong>
                                    {{__('text.validate_promotion')}}
                                </a>
                                <b class="arrow"></b>
                            </li>
                        @endif --}}
                        {{-- @if (\Auth::user()->hasPermissionTo('bypass_result'))
                            <li>
                                <a href="{{route('admin.result.bypass')}}" class="text-capitalize">
                                    <strong style="color: {{$bg1}}" class="menu-icon">&Rrightarrow;</strong>
                                    {{__('text.bypass_result')}}
                                </a>
                                <b class="arrow"></b>
                            </li>
                        @endif --}}
                        <li>
                            <a href="{{route('admin.student.section')}}" class="text-capitalize">
                                <strong style="color: {{$bg1}}" class="menu-icon">&Rrightarrow;</strong>
                                {{__('text.change_section')}}
                            </a>
                            <b class="arrow"></b>
                        </li>
                        {{-- <li>
                            <a href="{{route('admin.bio_data.download')}}" class="text-capitalize">
                                <strong style="color: {{$bg1}}" class="menu-icon">&Rrightarrow;</strong>
                                @lang('text.student_data')
                            </a>
                            <b class="arrow"></b>
                        </li> --}}
                    </ul>
                </li>
            @endif


            @if (\Auth::user()->hasPermissionTo('manage_fee'))
            <li>
                <a href="#" class="dropdown-toggle text-capitalize">
                    <i  style="color: {{$bg1}}"class="menu-icon  fa fa-eur"></i>
                    <span class="menu-text">
						{{__('text.fee_managenent')}}
						</span>
                    <b class="arrow fa fa-angle-down"></b>
                </a>


                <ul class="submenu">
                    
                    <li>
                        <a href="{{route('admin.extra-fee')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.collect_etxra_fee')}}
                        </a>
                        <b class="arrow"></b>
                    </li>
                    
                    <li>
                        <a href="{{route('admin.fee.collect')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.collect_fee')}}
                        </a>
                        <b class="arrow"></b>
                    </li>
                    
                    <li>
                        <a href="{{route('admin.print_fee')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.print_reciept')}}
                        </a>
                        <b class="arrow"></b>
                    </li>


                    <li>
                        <a href="{{route('admin.fee.daily_report')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.daily_reports')}}
                        </a>
                        <b class="arrow"></b>
                    </li>

                    <li>
                        <a href="{{route('admin.fee.payment_details')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.detailed_payment_report')}}
                        </a>
                        <b class="arrow"></b>
                    </li>

                     <li>
                        <a href="{{route('admin.import_fee')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.import_fees')}}
                        </a>
                        <b class="arrow"></b>
                    </li>
                    <li>
                        <a href="{{route('admin.fee')}}?type=completed" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.completed_fees')}}
                        </a>
                        <b class="arrow"></b>
                    </li>

                    <li>
                        <a href="{{route('admin.fee')}}?type=uncompleted" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.uncompleted_fees')}}
                        </a>
                        <b class="arrow"></b>
                    </li>


                    <li>
                        <a href="{{route('admin.fee.drive')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.fee_drive')}}
                        </a>
                        <b class="arrow"></b>
                    </li>
                    
                    <li>
                        <a href="{{route('admin.fee.situation')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.fee_situation')}}
                        </a>
                        <b class="arrow"></b>
                    </li>
                    
                    <li>
                        <a href="{{route('admin.clearance.fee')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.fees_clearance')}}
                        </a>
                        <b class="arrow"></b>
                    </li>



                </ul>
            </li>
            @endif

            @if (\Auth::user()->hasPermissionTo('manage_fee'))
            <li>
                <a href="#" class="dropdown-toggle text-capitalize">
                    <i  style="color: {{$bg1}}"class="menu-icon  fa fa-dollar"></i>
                    <span class="menu-text">
						{{__('text.fee_settings')}}
						</span>
                    <b class="arrow fa fa-angle-down"></b>
                </a>


                <ul class="submenu">
                    
                    <li>
                        <a href="{{route('admin.fee_settings')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.word_fee')}}
                        </a>
                        <b class="arrow"></b>
                    </li>

                </ul>
            </li>
            @endif

            {{-- @if (\Auth::user()->hasPermissionTo('manage_result'))
            <li>
                <a href="#" class="dropdown-toggle text-capitalize">
                    <i  style="color: {{$bg1}}"class="menu-icon  fa fa-folder-open"></i>
                    <span class="menu-text">
						{{__('text.word_results')}}
						</span>
                    <b class="arrow fa fa-angle-down"></b>
                </a>


                <ul class="submenu">
                    <li>
                        <a href="{{route('admin.result.ca.index')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.CA')}}
                        </a>
                        <b class="arrow"></b>
                    </li>


                    <li>
                        <a href="{{route('admin.result.exam.index')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.word_exams')}}
                        </a>
                        <b class="arrow"></b>
                    </li>


                    <li>
                        <a href="{{route('admin.result.imports')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.word_imports')}}
                        </a>
                        <b class="arrow"></b>
                    </li>


                    <li>
                        <a href="{{route('admin.result.individual_results')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.individual_results')}}
                        </a>
                        <b class="arrow"></b>
                    </li>


                    <li>
                        <a href="{{route('admin.result.class_results')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.class_results')}}
                        </a>
                        <b class="arrow"></b>
                    </li>
                </ul>
            </li>
            @endif --}}


            @if (\Auth::user()->hasPermissionTo('manage_scholarship'))
            <li>
                <a href="#" class="dropdown-toggle">
                <i style="color: {{$bg1}}" class="menu-icon fa fa-graduation-cap"></i>
                    <span class="menu-text text-capitalize">
						{{trans_choice('text.scholarship', 2)}}
						</span>
                    <b class="arrow fa fa-angle-down"></b>
                </a>


                <ul class="submenu">
                    <li>
                        <a href="{{route('admin.scholarship.index')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{trans_choice('text.scholarship', 2)}}
                        </a>
                        <b class="arrow"></b>
                    </li>


                    <li>
                        <a href="{{route('admin.scholarship.eligible')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.award_scholarship')}}
                        </a>
                        <b class="arrow"></b>
                    </li>

                     <li>
                        <a href="{{route('admin.scholarship.awarded_students')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                           {{trans_choice('text.our_scholar', 2)}}
                        </a>
                        <b class="arrow"></b>
                    </li>
                </ul>
            </li>
            @endif

            @if (\Auth::user()->hasPermissionTo('manage_incomes'))
            <li>
                <a href="#" class="dropdown-toggle text-capitalize">
                <i style="color: {{$bg1}}" class="menu-icon fa fa-money"></i>
                    <span class="menu-text">
						{{__('text.other_incomes')}}
						</span>
                    <b class="arrow fa fa-angle-down"></b>
                </a>


                <ul class="submenu">
                    <li>
                        <a href="{{route('admin.income.index')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.income_list')}}
                        </a>
                        <b class="arrow"></b>
                    </li>
                     <li>
                        <a href="{{route('admin.income.pay_income.create')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.collect_income')}}
                        </a>
                        <b class="arrow"></b>
                    </li>
                     <li>
                        <a href="{{route('admin.income.pay_income.create_cash')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.collect_cash')}}
                        </a>
                        <b class="arrow"></b>
                    </li>
                     <li>
                        <a href="{{route('admin.pay_income.index')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.paid_incomes')}}
                        </a>
                        <b class="arrow"></b>
                    </li>
                </ul>
            </li>
            @endif

            @if (\Auth::user()->hasPermissionTo('manage_stock'))
            <li>
                <a href="#" class="dropdown-toggle text-capitalize">
                    <i  style="color: {{$bg1}}"class="menu-icon  fa fa-folder-open"></i>
                    <span class="menu-text">
						{{__('text.stock_management')}}
						</span>
                    <b class="arrow fa fa-angle-down"></b>
                </a>


                <ul class="submenu">
                    @if(auth()->user()->campus_id == null)
                    <li>
                        <a href="{{route('admin.stock.create')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.create_item')}}
                        </a>
                        <b class="arrow"></b>
                    </li>
                    
                    <li>
                        <a href="{{route('admin.stock.index')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.word_items')}}
                        </a>
                        <b class="arrow"></b>
                    </li>

                    @endif



                    @if(!auth()->user()->campus_id == null)
                    <li>
                        <a href="{{route('admin.stock.campus.index', auth()->user()->campus_id)}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.word_items')}}
                        </a>
                        <b class="arrow"></b>
                    </li>
                    <li>
                        <a href="{{route('admin.stock.campus.givable.report', auth()->user()->campus_id)}}?type=givable" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.givable_report')}}
                        </a>
                        <b class="arrow"></b>
                    </li>
                    <li>
                        <a href="{{route('admin.stock.campus.receivable.report', auth()->user()->campus_id)}}?type=receivable" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.receivable_report')}}
                        </a>
                        <b class="arrow"></b>
                    </li>
                    @endif
                </ul>
            </li>
            @endif


            {{-- @if (\Auth::user()->hasPermissionTo('manage_notifications'))
            <li>
                <a href="#" class="dropdown-toggle text-capitalize">
                <i style="color: {{$bg1}}" class="menu-icon fa fa-bell"></i>
                    <span class="menu-text">
						{{__('text.manage_notifications')}}
						</span>
                    <b class="arrow fa fa-angle-down"></b>
                </a>
                <ul class="submenu">
                    <li>
                        <a href="{{route('notifications.index', ['S', 0, auth()->user()->campus_id ?? 0])}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.word_notifications')}}
                        </a>
                        <b class="arrow"></b>
                    </li>
                    <li>
                        <a href="{{route('admin.student.bulk.index')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.word_create')}}
                        </a>
                        <b class="arrow"></b>
                    </li>
                    <li>
                        <a href="{{route('admin.student.bulk.index')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.send_SMS')}}
                        </a>
                        <b class="arrow"></b>
                    </li>
                </ul>
            </li>
            @endif --}}
            
            {{-- @if(auth()->user()->campus_id != null)
                @if (\Auth::user()->hasPermissionTo('manage_notifications'))
                <li>
                    <a href="#" class="dropdown-toggle text-capitalize">
                    <i style="color: {{$bg1}}" class="menu-icon fa fa-comments"></i>
                        <span class="menu-text">
                            {{__('text.manage_messages')}}
                            </span>
                        <b class="arrow fa fa-angle-down"></b>
                    </a>
                    <ul class="submenu">
                        <li>
                            <a href="{{route('messages.create')}}" class="text-capitalize">
                                <i class="menu-icon fa fa-caret-right"></i>
                                {{__('text.word_create')}}
                            </a>
                            <b class="arrow"></b>
                        </li>
                        <li>
                            <a href="{{route('messages.sent')}}" class="text-capitalize">
                                <i class="menu-icon fa fa-caret-right"></i>
                                {{__('text.word_sent')}}
                            </a>
                            <b class="arrow"></b>
                        </li>
                    </ul>
                </li>
                @endif

                @if (\Auth::user()->hasPermissionTo('manage_faqs'))
                <li>
                    <a href="#" class="dropdown-toggle text-capitalize">
                    <i style="color: {{$bg1}}" class="menu-icon fa fa-question"></i>
                        <span class="menu-text">
                            {{__('text.manage_faqs')}}
                            </span>
                        <b class="arrow fa fa-angle-down"></b>
                    </a>
                    <ul class="submenu">
                        <li>
                            <a href="{{route('faqs.index')}}" class="text-capitalize">
                                <i class="menu-icon fa fa-caret-right"></i>
                                {{__('text.word_faqs')}}
                            </a>
                            <b class="arrow"></b>
                        </li>
                        <li>
                            <a href="{{route('faqs.create')}}" class="text-capitalize">
                                <i class="menu-icon fa fa-caret-right"></i>
                                {{__('text.word_create')}}
                            </a>
                            <b class="arrow"></b>
                        </li>
                    </ul>
                </li>
                @endif
            @endif --}}


            @if (\Auth::user()->hasPermissionTo('manage_expenses'))
            <li>
                <a href="#" class="dropdown-toggle text-capitalize">
                <i style="color: {{$bg1}}" class="menu-icon fa fa-spinner"></i>
                    <span class="menu-text">
						{{__('text.school_expenses')}}
						</span>
                    <b class="arrow fa fa-angle-down"></b>
                </a>


                <ul class="submenu">
                    <li>
                        <a href="{{route('admin.expense.index')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.expense_list')}}
                        </a>
                        <b class="arrow"></b>
                    </li>
                </ul>
            </li>
            @endif

            {{-- @if (\Auth::user()->hasPermissionTo('manage_school_debts'))
            <li>
                <a href="#" class="dropdown-toggle text-capitalize">
                <i class="menu-icon fa fa-money"></i>
                    <span class="menu-text">
						{{__('text.school_debts')}}
						</span>
                    <b class="arrow fa fa-angle-down"></b>
                </a>
                <ul class="submenu">
                    <li>
                        <a href="{{route('admin.debts.schoolDebts')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.manage_school_debts')}}
                        </a>
                        <b class="arrow"></b>
                    </li>
                </ul>
            </li>
            @endif --}}

            
            @if (\Auth::user()->hasPermissionTo('manage_statistics'))
            <li>
                <a href="#" class="dropdown-toggle text-capitalize">
                    <i  style="color: {{$bg1}}"class="menu-icon  fa fa-signal"></i>
                    <span class="menu-text"> {{__('text.statistics_zone')}}</span>

                    <b class="arrow fa fa-angle-down"></b>
                </a>

                <b class="arrow"></b>

                <ul class="submenu">
                    @if (\Auth::user()->hasPermissionTo('manage_student_statistics'))
                    <li>
                        <a href="{{route('admin.stats.students')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.student_statistics')}}
                        </a>
                        <b class="arrow"></b>
                    </li>
                    @endif
                    @if (\Auth::user()->hasPermissionTo('__________manage_result_statistics'))
                    <li>
                        <a href="{{route('admin.stats.results')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.results_statistics')}}
                        </a>

                        <b class="arrow"></b>
                    </li>
                    @endif
                    @if (\Auth::user()->hasPermissionTo('manage_finance_statistics'))
                    <li>
                        <a href="{{route('admin.stats.fees')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.fee_statistics')}}
                        </a>

                        <b class="arrow"></b>
                    </li>
                    
                    <li>
                        <a href="{{route('admin.stats.regfees')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.registration_fee_statistics')}}
                        </a>

                        <b class="arrow"></b>
                    </li>

                    <li>
                        <a href="{{route('admin.stats.income')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.income_statistics')}}
                        </a>
                        <b class="arrow"></b>
                    </li>

                    <li>
                        <a href="{{route('admin.stats.expenditure')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                           {{__('text.expenditure_statistics')}}
                        </a>
                        <b class="arrow"></b>
                    </li>

                    <li>
                        <a href="{{route('admin.stats.ie_report')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                           {{__('text.IE_report')}}
                        </a>
                        <b class="arrow"></b>
                    </li>
                    @endif
                </ul>
            </li>
            @endif

            {{-- @if (\Auth::user()->hasPermissionTo('manage_importation'))
            <li>
                <a href="#" class="dropdown-toggle">
                    <i  style="color: {{$bg1}}"class="menu-icon  fa fa-rocket"></i>
                    <span class="menu-text text-capitalize">
						{{__('text.importation_center')}}
						</span>
                    <b class="arrow fa fa-angle-down"></b>
                </a>


                <ul class="submenu">
                    <li>
                        <a href="{{route('admin.imports.import_ca')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.import_ca')}}
                        </a>
                        <b class="arrow"></b>
                    </li>
                    
                    <li>
                        <a href="{{route('admin.imports.import_exam')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.import_exams')}}
                        </a>
                        <b class="arrow"></b>
                    </li>
                    
                    <li>
                        <a href="{{route('admin.imports.clear_ca')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.clear_results')}}
                        </a>
                        <b class="arrow"></b>
                    </li>


                    <li>
                        <a href="{{route('admin.import_fee')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.import_fees')}}
                        </a>
                        <b class="arrow"></b>
                    </li>
                    <li>
                        <a href="{{route('admin.imports.clear_fee')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.clear_fees')}}
                        </a>
                        <b class="arrow"></b>
                    </li>
                </ul>
            </li>
            @endif --}}

            {{-- @if (\Auth::user()->hasPermissionTo('manage_result'))
            <li>
                <a href="#" class="dropdown-toggle text-capitalize">
                    <i  style="color: {{$bg1}}"class="menu-icon fa fa-question-circle"></i>
                    <span class="menu-text">
						{{__('text.import_marks')}}
						</span>
                    <b class="arrow fa fa-angle-down"></b>
                </a>


                <ul class="submenu">
                    <li>
                        <a href="{{route('admin.result.ca.index')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.CA')}}
                        </a>
                        <b class="arrow"></b>
                    </li>


                    <li>
                        <a href="{{route('admin.result.exam.index')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.word_exams')}}
                        </a>
                        <b class="arrow"></b>
                    </li>


                    <li>
                        <a href="{{route('admin.result.imports')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.word_imports')}}
                        </a>
                        <b class="arrow"></b>
                    </li>

                </ul>
            </li>
            @endif --}}


            {{-- @if (\Auth::user()->hasPermissionTo('manage_transcripts_and_results'))
            <li>
                <a href="#" class="dropdown-toggle text-capitalize">
                    <i  style="color: {{$bg1}}"class="menu-icon  fa fa-circle"></i>
                    <span class="menu-text">
						{{__('text.results_slash_transcripts')}}
						</span>
                    <b class="arrow fa fa-angle-down"></b>
                </a>


                <ul class="submenu">
                    <li>
                        <a class="text-capitalize" onclick="$('#fre_dis_post_form').submit()">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.frequency_distribution')}}
                        </a>
                        <b class="arrow"></b>
                        <form action="{{route('admin.res_and_trans.fre_dis')}}" method="post" class="hidden" id="fre_dis_post_form">@csrf</form>
                    </li>
                    <li>
                        <a class="text-capitalize" onclick="$('#spr_sheet_post_form').submit()">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.spread_sheet')}}
                        </a>
                        <b class="arrow"></b>
                        <form action="{{route('admin.res_and_trans.spr_sheet')}}" method="post" class="hidden" id="spr_sheet_post_form">@csrf</form>
                    </li>
                    <li>
                        <a class="text-capitalize" onclick="$('#passfail_post_form').submit()">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.passfail_report')}}
                        </a>
                        <b class="arrow"></b>
                        <form action="{{route('admin.res_and_trans.passfail_report')}}" method="post" class="hidden" id="passfail_post_form">@csrf</form>
                    </li>
                    <li>
                        <a class="text-capitalize" onclick="$('#sem_res_post_form').submit()">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.semester_results_report')}}
                        </a>
                        <b class="arrow"></b>
                        <form action="{{route('admin.res_and_trans.sem_res_report')}}" method="post" class="hidden" id="sem_res_post_form">@csrf</form>
                    </li>
                    <li>
                        <a class="text-capitalize" onclick="$('#ca_only_post_form').submit()">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.CA_only')}}
                        </a>
                        <b class="arrow"></b>
                        <form action="{{route('admin.res_and_trans.ca_only')}}" method="post" class="hidden" id="ca_only_post_form">@csrf</form>
                    </li>
                    <li>
                        <a class="text-capitalize" onclick="$('#grd_post_form').submit()">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.grades_sheet')}}
                        </a>
                        <b class="arrow"></b>
                        <form action="{{route('admin.res_and_trans.grd_sheet')}}" method="post" class="hidden" id="grd_post_form">@csrf</form>
                    </li>

                    <li>
                        <a href="{{route('admin.result.individual_results')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.individual_results')}}
                        </a>
                        <b class="arrow"></b>
                    </li>

                    <li>
                        <a href="{{route('admin.transcript.index')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.print_transcript')}}
                        </a>
                        <b class="arrow"></b>
                    </li>


                    <li>
                        <a href="{{route('admin.result.class_results')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.class_results')}}
                        </a>
                        <b class="arrow"></b>
                    </li>
                </ul>
            </li>
            @endif --}}


            
            {{-- @if (\Auth::user()->hasPermissionTo('manage_transcripts'))
                <li>
                    <a href="#" class="dropdown-toggle text-capitalize">
                        <i  style="color: {{$bg1}}"class="menu-icon  fa fa-flag"></i>
                        <span class="menu-text">
                            {{trans_choice('text.word_transcript', 2)}}
                            </span>
                        <b class="arrow fa fa-angle-down"></b>
                    </a>

                    <ul class="submenu">
                        @if (\Auth::user()->hasPermissionTo('configure_transcripts'))
                            <li>
                                <a class="text-capitalize" href="{{route('admin.res_and_trans.transcripts.config')}}">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    {{__('text.word_configure')}}
                                </a>
                                <b class="arrow"></b>
                            </li>
                        @endif
                        <li>
                            <a class="text-capitalize" href="{{route('admin.res_and_trans.transcripts.completed')}}">
                                <i class="menu-icon fa fa-caret-right"></i>
                                {{__('text.word_completed')}}
                            </a>
                            <b class="arrow"></b>
                        </li>
                        <li>
                            <a class="text-capitalize" href="{{route('admin.res_and_trans.transcripts.pending')}}">
                                <i class="menu-icon fa fa-caret-right"></i>
                                {{__('text.word_pending')}}
                            </a>
                            <b class="arrow"></b>
                        </li>
                        <li>
                            <a class="text-capitalize" href="{{route('admin.res_and_trans.transcripts.undone')}}">
                                <i class="menu-icon fa fa-caret-right"></i>
                                {{__('text.word_undone')}}
                            </a>
                            <b class="arrow"></b>
                        </li>
                    </ul>
                </li>
            @endif
             --}}

            
            @if (\Auth::user()->hasPermissionTo('manage_user'))
            <li>
                <a href="#" class="dropdown-toggle text-capitalize">
                    <i  style="color: {{$bg1}}"class="menu-icon  fa fa-users"></i>
                    <span class="menu-text">
						{{__('text.user_accounts')}}
						</span>
                    <b class="arrow fa fa-angle-down"></b>
                </a>

                <ul class="submenu">
                    @if(auth()->user()->campus_id == null)
                    <li>
                        <a href="{{route('admin.users.index')}}?type=admin" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{trans_choice('text.add_admin', 2)}}
                        </a>
                        <b class="arrow"></b>
                    </li>
                    @endif

                    <li>
                        <a href="{{route('admin.users.index')}}?type=teacher" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{trans_choice('text.word_teacher', 2)}}
                        </a>
                        <b class="arrow"></b>
                    </li>


                    @if(auth()->user()->campus_id == null)
                    <li>
                        <a href="{{route('admin.roles.index')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                           {{trans_choice('text.role', 2)}}
                        </a>
                        <b class="arrow"></b>
                    </li>
                    @if(auth()->user()->hasPermissionTo('reset_user_password'))
                        <li>
                            <a href="{{route('admin.users.reset_password')}}" class="text-capitalize">
                                <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.reset_password')}}
                            </a>
                            <b class="arrow"></b>
                        </li>
                    @endif
                    @endif
                </ul>
            </li>
            @endif
        
            

            
            {{-- @if (\Auth::user()->hasPermissionTo('manage_attendance'))
            <!-- Attendance management -->
            <li>
                <a href="#" class="dropdown-toggle text-capitalize">
                    <i  style="color: {{$bg1}}"class="menu-icon  fa fa-users"></i>
                    <span class="menu-text">
						{{__('text.manage_attendance')}}
						</span>
                    <b class="arrow fa fa-angle-down"></b>
                </a>

                <ul class="submenu">
                    @if(auth()->user()->campus_id != null)
                    <li>
                        <a href="{{route('admin.attendance.teacher.init')}}?type=admin" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.teacher_attendance')}}
                        </a>
                        <b class="arrow"></b>
                    </li>
                    <li>
                        <a href="{{route('admin.attendance.report', ['type'=>'general', 'campus_id'=>auth()->user()->campus_id])}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.attendance_report')}}
                        </a>
                        <b class="arrow"></b>
                    </li>
                    @endif
                </ul>
            </li>
            <!-- End attaendance management -->
            @endif --}}

            
            {{-- @if (\Auth::user()->headOfSchoolFor()->count() > 0)
            <!-- Attendance management -->
            <li>
                <a href="#" class="dropdown-toggle text-capitalize">
                    <i  style="color: {{$bg1}}"class="menu-icon  fa fa-users"></i>
                    <span class="menu-text">
						{{__('text.head_of_school')}}
						</span>
                    <b class="arrow fa fa-angle-down"></b>
                </a>

                <ul class="submenu">
                    <li>
                        <a href="{{route('admin.headOfSchools.index')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.word_schools')}}
                        </a>
                        <b class="arrow"></b>
                    </li>
                </ul>
            </li>
            <!-- End attaendance management -->
            @endif --}}

            <li>
                <a href="{{route('admin.reset_password')}}" class="text-capitalize">
                    <i  style="color: {{$bg1}};" class="fa fa-refresh menu-icon   "></i>
                    {{__('text.reset_password')}}
                </a>
                <b class="arrow"></b>
            </li>

            <li>
                <a onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                   href="{{route('logout')}}" class="text-capitalize">
                    <i style="color: {{$bg1}}" class="menu-icon fa fa-lock"></i>
                    <span class="menu-text">	{{__('text.word_logout')}}</span>
                </a>
                <b class="arrow"></b>
            </li>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                {{ csrf_field() }}
            </form>


        </ul><!-- /.nav-list -->


        <div class="sidebar-toggle sidebar-collapse" id="sidebar-collapse">
            <i id="sidebar-toggle-icon" class="ace-icon f ace-save-state"></i>
        </div>
    </div>
    <div class="main-content">
        <div class="main-content-inner">

            <marquee behavior="" direction="" style="font-weight: 700; font-size: large; color: #c83c3cff;">Your plan expires by September 25th 2025. Renew your plan to continue using the system</marquee>

            <div class="breadcrumbs ace-save-state" id="breadcrumbs">
                <ul class="breadcrumb text-capitalize">
                    <li>
                        <i class="ace-icon fa fa-home home-icon"></i>
                        <a href="#">{{__('text.word_home')}}</a>
                    </li>
                    <li class="active">{{__('text.word_dashboard')}}</li>
                    <li class="active"> {{__('text.fullname')}} : <b style="color: #e30000">{{\Auth::user()->name}}</b></li>

                </ul><!-- /.breadcrumb -->
            </div>

            <div style="max-height: 65vh; overflow:auto">
                @if(Session::has('success'))
                    <div class="alert alert-success fade in">
                        <strong>Success!</strong> {{Session::get('success')}}
                    </div>
                @endif

                @if(Session::has('error'))
                    <div class="alert alert-danger fade in">
                        <strong>Error!</strong> {{Session::get('error')}}
                    </div>
                @endif

                @if(Session::has('message'))
                    <div class="alert alert-primary fade in">
                        <strong>Message!</strong> {!! Session::get('message') !!}
                    </div>
                @endif
            </div>


            <div class="mb-4 mx-3">
                <h4 id="title" class="font-weight-bold text-capitalize">{!! $title ?? '' !!}</h4>
            </div>
            @if ((auth()->user()->password_reset != 1) && (now()->diffInDays(\Illuminate\Support\Carbon::createFromTimestamp(auth()->user()->created_at)) >= 14) && (url()->current() != route('admin.reset_password')))
                <div class="py-5 h3 text-center text-danger mt-5 text-capitalize">{{__('text.password_reset_request')}}</div>
                <div class="py-3 d-flex justify-content-center mt-2">
                    <a class="btn btn-lg col-sm-4 rounded btn-primary text-center" href="{{route('admin.reset_password')}}">{{__('text.word_proceed')}}</a>
                </div>
            @else
                <div class="container-fluid">
                    @yield('section')
                    {{$slot ?? ''}}
                </div>
            @endif
        </div>
    </div>
</div>

<div class="footer">
    <div class="footer-inner">
        <div class="footer-content" style="background:#fff">
            <span class="bigger-120">
               &copy; {{__('text.copyright')}}
            </span>
            &nbsp; &nbsp;
            
        </div>
    </div>
</div>

<a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse">
    <i class="ace-icon fa fa-angle-double-up icon-only bigger-110"></i>
</a>
</div>
<script src="{{asset('assets/js/jquery-2.1.4.min.js')}}"></script>
<script src="{{asset('assets/js/bootstrap.min.js')}}"></script>
<script src="{{ asset('assets/vendor/toastr/toastr.min.js') }}"></script>
<script src="{{asset('assets/js/ace.min.js')}}"></script>
<script src="{{ asset('libs')}}/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="{{ asset('libs')}}/datatables.net-bs4/js/dataTables.responsive.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.2/bootstrap3-typeahead.min.js" ></script>
<script>
    $(function () {

        $('.table , .adv-table table').DataTable({
            responsive: true,
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel',
                {
                    text: 'Download PDF',
                    extend: 'pdfHtml5',
                    message: '',
                    orientation: 'portrait',
                    exportOptions: {
                        columns: ':visible'
                    },
                    customize: function (doc) {
                        doc.pageMargins = [10,10,10,10];
                        doc.defaultStyle.fontSize = 7;
                        doc.styles.tableHeader.fontSize = 7;
                        doc.styles.title.fontSize = 9;
                        doc.content[0].text = doc.content[0].text.trim();

                        doc['footer']=(function(page, pages) {
                            return {
                                columns: [
                                    "{!! $title ?? '' !!}",
                                    {
                                        // This is the right column
                                        alignment: 'right',
                                        text: ['page ', { text: page.toString() },  ' of ', { text: pages.toString() }]
                                    }
                                ],
                                margin: [10, 0]
                            }
                        });
                        // Styling the table: create style object
                        var objLayout = {};
                        // Horizontal line thickness
                        objLayout['hLineWidth'] = function(i) { return .5; };
                        // Vertikal line thickness
                        objLayout['vLineWidth'] = function(i) { return .5; };
                        // Horizontal line color
                        objLayout['hLineColor'] = function(i) { return '#aaa'; };
                        // Vertical line color
                        objLayout['vLineColor'] = function(i) { return '#aaa'; };
                        // Left padding of the cell
                        objLayout['paddingLeft'] = function(i) { return 4; };
                        // Right padding of the cell
                        objLayout['paddingRight'] = function(i) { return 4; };
                        // Inject the object in the document
                        doc.content[1].layout = objLayout;
                    }
                }

            ],
            info:     false,
            searching: true,
            // order: [
            //     [1, 'asc']
            // ],
        });

    });

    function delete_alert(event, data) {
        event.preventDefault();
        let yes = confirm('You are about to delete an item:'+data+'. This operation can not be reversed. Delete item?');
        if(yes){
            window.location = event.target.href;
        }
    }
    $('#menu-toggler').on('click', function(){
        $('#sidebar').toggleClass('d-block');
    })

    alert("Your subscription plan has expired. Renew your plan to continue using the system");
    // window.location.href = "{{ request()->url() }}";
</script>

<script src="{{ asset('libs')}}/datatables.net/js/dataTables.buttons.min.js"></script>
<script src="{{ asset('libs')}}/datatables.net/js/jszip.min.js"></script>
<script src="{{ asset('libs')}}/datatables.net/js/pdfmake.min.js"></script>
<script src="{{ asset('libs')}}/datatables.net/js/vfs_fonts.js"></script>
<script src="{{ asset('libs')}}/datatables.net/js/buttons.html5.min.js"></script>

<script>
    (function($){
        'use strict';
        $(window).on('load', function () {
            if ($(".pre-loader").length > 0)
            {
                $(".pre-loader").fadeOut("slow");
            }
        });
    })(jQuery)
</script>
@yield('script')
</body>
</html>