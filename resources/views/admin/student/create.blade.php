@extends('admin.layout')
@section('section')
<div class="mx-3">
    <div class="form-panel">
        <form class="form-horizontal" role="form" method="POST" action="{{route('admin.student.store')}}">

            <input name="type" value="{{request('type','teacher')}}" type="hidden" />
            <h5 class="mt-5 font-weight-bold text-capitalize">{{__('text.personal_information')}}</h5>
            @csrf
            <div class="form-group @error('name') has-error @enderror">
                <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.full_name')}} <span style="color:red">*</span></label>
                <div class="col-lg-10">
                    <input class=" form-control" name="name" type="text" required />
                    @error('name')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            {{-- <div class="form-group @error('matric') has-error @enderror">
                <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.word_matricule')}} <span style="color:red">*</span></label>
                <div class="col-lg-10">
                    <input class=" form-control" name="matric" value="{{old('matric')}}" type="text" required />
                    @error('matric')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div> --}}
            {{-- <div class="form-group @error('email') has-error @enderror">
                <label for="email" class="control-label col-lg-2 text-capitalize">{{__('text.word_email')}} </label>
                <div class="col-lg-10">
                    <input class=" form-control" name="email" value="{{old('email')}}" type="email"  />
                    @error('email')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div> --}}
            <div class="form-group @error('phone') has-error @enderror">
                <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.guidian_tel')}} </label>
                <div class="col-lg-10">
                    <input class=" form-control" name="phone" value="{{old('phone')}}" type="tel" />
                    @error('phone')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="form-group @error('address') has-error @enderror">
                <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.word_address')}} </label>
                <div class="col-lg-10">
                    <input class=" form-control" name="address" value="{{old('address')}}" type="text"  />
                    @error('address')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-group @error('dob') has-error @enderror">
                <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.date_of_birth')}} </label>
                <div class="col-lg-10">
                    <input class=" form-control" name="dob" value="{{old('dob')}}" type="date"  />
                    @error('dob')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="form-group @error('pob') has-error @enderror">
                <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.place_of_birth')}} </label>
                <div class="col-lg-10">
                    <input class=" form-control" name="pob" value="{{old('pob')}}" type="text"  />
                    @error('pob')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="form-group @error('gender') has-error @enderror">
                <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.word_gender')}} </label>
                <div class="col-lg-10">
                    <select class=" form-control" name="gender" required>
                        <option value="">{{__('text.word_gender')}}</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                    @error('gender')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            
            
            <h5 class="mt-5 mb-4 font-weight-bold text-capitalize">{{__('text.admission_class_information')}}</h5>
            
            <div class="form-group @error('year') has-error @enderror">
                <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.academic_year')}} <span style="color:red">*</span></label>
                <div class="col-lg-10">
                    <select class=" form-control" name="admission_batch_id" required>
                        <option value="">select academic year</option>
                        @forelse(\App\Models\Batch::all() as $batch)
                            <option {{ \App\Helpers\Helpers::instance()->getCurrentAccademicYear() == $batch->id ? 'selected' : ''}} value="{{$batch->id}}">{{$batch->name}}</option>
                        @empty
                            <option value="" selected>No data found</option>
                        @endforelse
                    </select>
                    @error('year')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-group @error('campus_id') has-error @enderror">
                <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.word_campus')}} </label>
                <div class="col-lg-10">
                    @if(\Auth::user()->campus_id != null)
                    <input type="hidden" name="campus_id" id="" value="{{\Auth::user()->campus_id}}">
                    @endif
                    <select name="campus_id" class="form-control" id="campus_id" oninput="loadPrograms(event.target)" {{ \Auth::user()->campus_id != null ? 'disabled' : ''}}>
                        <option value="">select campus</option>
                        @forelse(\App\Models\Campus::all() as $campus)
                            <option value="{{$campus->id}}" {{ \App\Models\Campus::first() == $campus ? 'selected' : ''}}>{{$campus->name}}</option>
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
                    <button id="save" class="btn btn-xs btn-primary mx-3" style="display: block" type="submit">{{__('text.word_save')}}</button>
                    <a class="btn btn-xs btn-danger" href="{{route('admin.users.index')}}" type="button">{{__('text.word_cancel')}}</a>
                </div>
            </div>

        </form>
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function(){
        loadPrograms(document.getElementById('campus_id'));
    });

    function loadPrograms(element){
        let val = element.value;
        url = "{{route('campus.programs', ['__V__'])}}";
        url =url.replace('__V__', val);
        $.ajax({
            method: 'get',
            url: url,
            success: function(data){
                console.log(data);
                let options = `<option value="">{{__('text.select_program')}}</option>`;
                for (const key in data) {
                    if (Object.hasOwnProperty.call(data, key)) {
                        const element = data[key];
                        // console.log(element);
                        options += `<option value="`+element.id+`">`+element.name+`</option>`;
                        
                    }
                }
                // data.forEach(element => {
                // });
                $('#program_id').html(options);
            },
            error: function(error){
                console.error(error);
            }
        });
    }
</script>
@endsection