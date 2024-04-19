@extends('admin.layout')

@section('section')
    <!-- FORM VALIDATION -->
    <div class="mx-3">
        <div class="form-panel">
            <form class="cmxform form-horizontal style-form" method="post" action="{{route('admin.courses.create_next')}}">
                {{csrf_field()}}
                {{-- <div class="form-group @error('name') has-error @enderror">
                    <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.word_background')}} ({{__('text.word_required')}})</label>
                    <div class="col-lg-10">
                        <select class=" form-control" name="background" required onchange="loadSemesters(event.target)">
                            <option value="">{{__('text.select_background')}}</option>
                            @foreach(\App\Models\SemesterType::all() as $bgs)
                                <option value="{{$bgs->id}}">{{$bgs->background_name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div> --}}

                <div class="form-group @error('coef') has-error @enderror" id="semesters-box">
                    <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.word_semester')}} ({{__('text.word_required')}})</label>
                    <div class="col-lg-10" id="semesters">
                        <select class=" form-control" name="semester" required >
                            <option value="">{{__('text.select_semester')}}</option>
                            @foreach(\App\Models\Semester::all() as $sem)
                                <option value="{{$sem->id}}">{{$sem->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-lg-offset-2 col-lg-10 text-capitalize">
                        <button class="btn btn-xs btn-primary" type="submit">{{__('text.word_save')}}</button>
                        <a class="btn btn-xs btn-danger" href="{{route('admin.subjects.index')}}" type="button">{{__('text.word_cancel')}}</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('script')
<script>
    function loadSemesters(element){
        let bg = element.value;
        let url = "{{route('semesters', '__BG')}}";
        url = url.replace('__BG', bg);
        $.ajax({
            method:'get',
            url: url,
            success: function(data){
                console.log(data);
                let semester_ = `<select name="semester" class="form-control" required>`;
                data.forEach(element => {
                    semester_ = semester_+`<option value="`+element.id+`">`+element.name+`</option>`;
                });
                semester_ = semester_+`</select>`;
                $('#semesters').html(semester_);
            }
        })
    }
</script>
@endsection
