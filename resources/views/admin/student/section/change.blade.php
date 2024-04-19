@extends('admin.layout')
@section('section')
    <div class="my-2">
        <div class="row my-3">
            <span class="col-12 col-md-3 col-lg-2">Current Section:</span>
            <div class="col-12 col-md-9 col-lg-10">
                <div class="row ">
                    <div class="col-12 col-lg-4">
                        <span class="d-block mb-1"> <i class="text-info text-capitalize">@lang('text.word_department')</i> : {{ $department->name??'' }}<span>
                    </div><hr>
                    <div class="col-12 col-lg-4">
                        <span class="d-block mb-1"> <i class="text-info text-capitalize">@lang('text.word_section')</i> : {{ $program->name??'' }}<span>
                    </div><hr>
                    <div class="col-12 col-lg-4">
                        <span class="d-block mb-1"> <i class="text-info text-capitalize">@lang('text.word_class')</i> : {{ $level->level }}<span>
                    </div><hr>
                </div>
            </div>
        </div>
        <hr>
        <div class="my-3">
            <div class="">Update Section:</div>
            <div class="">
                <form method="post">
                    @csrf
                    {{-- <div class=" my-2 container">
                        <div class="row"> 
                            <i class="text-info col-md-3">Department</i> : 
                            <div class="col-md-9">
                                <select class="form-control" name="department" required onchange="loadPrograms(event)">
                                    <option></option>
                                    @foreach ($sections->where('unit_id', 3)->sortBy('name') as $dept)
                                        <option value="{{ $dept->id }}" {{ old('department', $dept->id) == $department->id ? 'selected' : '' }}>{{ $dept->name??'' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        <div>
                    </div> --}}
                    <div class="my-2 container">
                        <div class="row"> 
                            <i class="text-info col-md-3 text-capitalize">@lang('text.word_section')</i> : 
                            <div class="col-md-9" id="__programs">
                                <select class="form-control" name="program" required onchange="loadLevels(event)">
                                    <option></option>
                                    @foreach ($programs as $prog)
                                        <option value="{{ $prog->id }}" {{ old('program', $prog->id) == $program->id ? 'selected' : '' }}>{{ $prog->name??'' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        <div>
                    </div>
                    <div class="my-2 container">
                        <div class="row"> 
                            <i class="text-info col-md-3 text-capitalize">@lang('text.word_class')</i> : 
                            <div class="col-md-9">
                                <select class="form-control" name="level" required id="__levels">
                                    <option></option>
                                    @foreach ($levels as $lev)
                                        <option value="{{ $lev->id }}" {{ old('level', $lev->id) == $level->id ? 'selected' : '' }}>{{ $lev->level??'' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        <div>
                    </div>
                    <div class="d-flex justify-content-end py-2">
                        <button class="btn btn-primary btn-xs">{{ __('text.word_save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        let loadPrograms = function(event){
            let dept = event.target.value;
            let url = `{{ route('department.programs', '__DID__') }}`.replace('__DID__', dept);
            $.ajax({
                method: 'GET', url: url, success: function(response){
                    let programs = response.programs;
                    let select = `<select class="form-control" name="program" required onchange="loadLevels(event)"><option></option>`;
                    programs.forEach(element=>{
                        select += `<option value="${element.id}">${element.name}</option>`
                    })
                    select += `</select>`;
                    $('#__programs').html(select);
                }
            })
        }

        let loadLevels = function(event){
            let prog = event.target.value;
            let url = `{{ route('program.levels', '__PID__') }}`.replace('__PID__', prog);
            $.ajax({
                method: 'GET', url: url, success: function(response){
                    let select = `<select class="form-control" name="level" required><option></option>`;
                    response.levels.forEach(element=>{
                        select += `<option value="${element.id}">${element.level}</option>`;
                    })
                    select += `</select>`;
                    $('#__levels').html(select);
                }
            })
        }
    </script>
@endsection