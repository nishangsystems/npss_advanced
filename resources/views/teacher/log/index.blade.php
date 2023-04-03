@extends('teacher.layout')
@section('section')
    <div class="row py-2">
        <div class="col-md-6 col-lg-6 px-2">
            <div class="shadow-lg px-2">
                <div class="py-4">
                    <form method="get" class="form py-4 px-3 bg-light">
                        <div class="input-group-merge d-flex border rounded">
                            <span class="input-group-text text-uppercase">{{__('text.select_campus')}}</span>
                            <select class="form-control" name="campus">
                                <option></option>
                                @foreach (\App\Models\Campus::all() as $campus)
                                    <option value="{{$campus->id}}">{{$campus->name}}</option>
                                @endforeach
                            </select>
                            <input class="btn btn-sm btn-primary" value="{{__('text.word_get')}}" type="submit">
                        </div>
                    </form>
                </div>
                @if (request()->has('campus') AND request('campus') != null)
                    <div class="py-4">
                        <table class="table table-light">
                            <thead class="text-primary">
                                <th class="border-left border-right border-light">{{__('text.sn')}}</th>
                                <th class="border-left border-right border-light">{{__('text.checked_in')}}</th>
                                <th class="border-left border-right border-light">{{__('text.checked_out')}}</th>
                                <th></th>
                            </thead>
                            <tbody>
                                @php($k = 1)
                                @foreach ($attendance as $record)
                                    <tr class="border-bottom border-light">
                                        <td class="border-left border-right border-light">{{$k++}}</td>
                                        <td class="border-left border-right border-light">{{$record->check_in}}</td>
                                        <td class="border-left border-right border-light">{{$record->check_out}}</td>
                                        <td class="border-left border-right border-light">
                                            <a class="btn btn-sm btn-primary" id="attendance_{{$record->id}}" data={{$record}} onclick="__loadContent('{{$record->id}}')">{{__('text.sign_course_log')}}</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
        <div class="col-md-6 col-lg-6 px-2">
            @if (isset($content))
                <div class="shadow-lg px-4 py-5 hidden" id="content">
                    <div class="py-2 border-bottom">
                        <label class="text-capitalize mb-2">{{__('text.word_attendance')}}</label>
                        <div class="d-flex justify-content-between text-capitalize form-control">
                            <span class="text-primary">{{__('text.word_from')}} : <span id="check_in"></span></span>
                            <span class="text-primary">{{__('text.word_to')}} : <span id="check_out"></span></span>
                            <input type="hidden" id="attendance_id_field">
                        </div>
                    </div>
                    <div class="py-2 border-bottom">
                        <label class="text-capitalize mb-2">{{__('text.word_content')}}</label>
                        <div class="text-capitalize">
                            <!-- display course content here for teacher to pick sub-topic -->

                            <div id="accordion" class="accordion-style1 panel-group">
                                @foreach ($content->where('level', 1) as $topic)
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h4 class="panel-title">
                                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapse_{{$topic->id}}">
                                                    <i class="ace-icon fa fa-angle-down bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
                                                    &nbsp;{!! $topic->title !!}
                                                </a>
                                            </h4>
                                        </div>
                                        
                                        <div class="panel-collapse collapse" id="collapse_{{$topic->id}}">
                                            <div class="">
                                                @foreach ($content->where('parent_id', $topic->id) as $sub_topic)
                                                    <div class="itemdiv dialogdiv">
                                                        <div class="user">
                                                            <img alt="Alexa's Avatar" src="{{asset('assets/images/avatars/user.jpg')}}" />
                                                        </div>

                                                        <div class="body">
                                                            <div class="time">
                                                                <i class="ace-icon fa fa-clock-o"></i>
                                                                <span class="green">4 sec</span>
                                                            </div>

                                                            <div class="name">
                                                                <a href="#">{{$sub_topic->teacher->name??null}}</a>
                                                                <span class="label label-info arrowed arrowed-in-right">NOT TAUGHT</span>
                                                            </div>
                                                            <div class="text fle flex-wrap">{!! $sub_topic->title !!}</div>

                                                            <div class="tools">
                                                                <a class="btn btn-xs btn-primary" onclick="window.location=`{{route('user.course.log.sign', ['subject_id'=>$sub_topic->subject_id, 'attendance_id'=>'__AT_ID__', 'campus_id'=>$sub_topic->campus_id, 'topic_id'=>$sub_topic->id])}}`.replace('__AT_ID__', $('#attendance_id_field').val())">
                                                                    {{__('text.word_sign')}}
                                                                    <i class="ml-2 text-white icon-only ace-icon fa fa-share"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
@section('script')
    <script>
        function __loadContent(params) {
            console.log(params);
            $('#attendance_id_field').val(params);
            // let attendance = $(params).attr(data);
            // console.log(attendance);
            $('#content').removeClass('hidden')
        }
    </script>
@endsection