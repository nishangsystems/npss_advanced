@extends('admin.layout')
@section('section')
    <div class="mx-3">
        <div class="form-panel row">
            <div class="col-md-6 col-lg-8 border-right py-3">
                <form class="form-horizontal" enctype="multipart/form-data" role="form" method="POST" action="{{route('admin.students.import')}}">
                    @csrf
                    <div class="form-group @error('section') has-error @enderror">
                        <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.word_batch')}}</label>
                        <div class="col-lg-10">
                            <div>
                                @if(!auth()->user()->campus_id == null)
                                    <input type="hidden" name="batch" value="{{\App\Helpers\Helpers::instance()->getCurrentAccademicYear()}}">
                                @endif
                                <select class="form-control" required name="batch" {{!(auth()->user()->campus_id == null) ? 'disabled' : ''}}>
                                    <option selected></option>
                                    @forelse(\App\Models\Batch::orderBy('name')->get() as $section)
                                        <option {{ $section->id == \App\Helpers\Helpers::instance()->getCurrentAccademicYear() ? 'selected' : '' }} value="{{$section->id}}">{{$section->name}}</option>
                                    @empty
                                        <option>{{__('text.no_batch_created')}}</option>
                                    @endforelse
                                </select>
                            </div>
                        </div>
                    </div>
    
    
                    <div class="form-group @error('file') has-error @enderror text-capitalize">
                        <label for="cname" class="control-label col-lg-2">{{__('text.csv_file')}} ({{__('text.word_required')}})</label>
                        <div class="col-lg-10">
                            <input class=" form-control" name="file" value="{{old('file')}}" type="file" required/>
                            @error('file')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
    
                    <h5 class="mt-5 mb-4 font-weight-bold text-capitalize">{{__('text.admission_class_information')}}</h5>
    
                    <div class="form-group @error('campus_id') has-error @enderror">
                        <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.word_campus')}} </label>
                        <div class="col-lg-10">
                            @if(\Auth::user()->campus_id != null)
                            <input type="hidden" name="campus_id" id="" value="{{\Auth::user()->campus_id}}">
                            @endif
                            <select name="campus_id" class="form-control" id="campus_id" onchange="loadPrograms(event.target, 'program_id')" {{ \Auth::user()->campus_id != null ? 'disabled' : ''}}>
                                <option value="">select campus</option>
                                @forelse(\App\Models\Campus::all() as $campus)
                                    <option value="{{$campus->id}}" {{ \Auth::user()->campus_id == $campus->id ? 'selected' : ''}}>{{$campus->name}}</option>
                                @empty
                                    <option value="" selected>No data found</option>
                                @endforelse
                            </select>
                            @error('year')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
    
                    <div class="form-group @error('program_id') has-error @enderror">
                        <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.word_class')}}</label>
                        <div class="col-lg-10">
                            <select class=" form-control" name="program_id" id="program_id" required>
                            </select>
                            @error('program_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
    
                    <div class="form-group">
                        <div class="d-flex justify-content-end col-lg-12">
                            <button id="save" class="btn btn-xs btn-primary mx-3" type="submit">{{__('text.word_save')}}</button>
                        </div>
                    </div>
    
                </form>
                <div class="py-3 bg-light card">
                    <div class="btn w-100 text-center btn-danger btn-sm text-uppercase" onclick="toggle_clear()">{{__('text.clear_students')}}</div>
                    <form action="{{route('admin.students.clear')}}" method="post" class="hidden rounded-md px-2 container-fluid py-2" id="clear_students">
                        @csrf
                        <div class="row py-3">
                            <label class="col-lg-2 text-capitalize">{{__('text.word_year')}}</label>
                            <div class="col-lg-10">
                                <select name="year" class="form-control" id="" required>
                                    <option value="">{{__('text.academic_year')}}</option>
                                    @foreach(\App\Models\Batch::all()  as  $batch)
                                        <option value="{{$batch->id}}">{{$batch->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row py-3">
                            <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.word_campus')}} </label>
                            <div class="col-lg-10">
                                @if(\Auth::user()->campus_id != null)
                                <input type="hidden" name="campus" id="" value="{{\Auth::user()->campus_id}}">
                                @endif
                                <select name="campus" class="form-control" required onchange="loadPrograms(this, 'clear_program_id')" {{ \Auth::user()->campus_id != null ? 'disabled' : ''}}>
                                    <option>select campus</option>
                                    @foreach(\App\Models\Campus::all() as $campus)
                                        <option value="{{$campus->id}}" {{ \Auth::user()->campus_id == $campus->id ? 'selected' : ''}}>{{$campus->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row py-3">
                            <label class="col-lg-2 text-capitalize">{{__('text.word_class')}}</label>
                            <div class="col-lg-10">
                                <select name="class" class="form-control" id="clear_program_id" required>
                                    <option value="">{{__('text.select_class')}}</option>
                                    @foreach(\App\Http\Controllers\Controller::sorted_program_levels()  as  $class)
                                        <option value="{{$class['id']}}">{{$class['name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end py-3">
                            <a href="{{url()->previous()}}" class="btn btn-info btn-sm mr-1">{{__('text.word_cancel')}}</a>
                            <input type="submit" value="submit" class="btn btn-sm btn-danger">
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 py-3 px-2">
                <div class="text-center text-capitalize text-primary py-3">{{__('text.file_format_csv')}}</div>
                <table class="bg-light">
                    <thead class="text-capitalize bg-dark text-light fs-6">
                        <th>@lang('text.word_name') <span class="text-danger">*</span></th>
                        <th>@lang('text.word_matricule') <span class="text-danger">*</span></th>
                        <th>@lang('text.word_gender')</th>
                        <th>@lang('text.place_of_birth')</th>
                        <th>@lang('text.date_of_birth')</th>
                    </thead>
                    <tbody>
                        @for($i=0; $i < 4; $i++)
                        <tr class="border-bottom">
                            <td>---</td>
                            <td>---</td>
                            <td>---</td>
                            <td>---</td>
                            <td>---</td>
                        @endfor
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>

    function toggle_clear(){
        $('#clear_students').toggleClass('hidden');
    }

    $(document).ready(function(){
        loadPrograms(document.getElementById('campus_id'), 'program_id');
    });

    function loadPrograms(element, space_id){
        let val = element.value;
        url = "{{route('campus.programs', '__V__')}}".replace('__V__', val);
        console.log(url);
        $.ajax({
            method: 'get',
            url: url,
            success: function(data){
                console.log(data);
                let options = `<option>{{__('text.select_program')}}</option>`;
                for (const key in data) {
                    if (data.hasOwnProperty.call(data, key)) {
                        const element = data[key];
                        console.log(element);
                        options += `<option value="`+element.id+`">`+element.name+`</option>`;
                    }
                }
                $('#'+space_id).html(options);
            }
        })
    }
</script>
@endsection
