@extends('admin.layout')


@section('section')
    <div class="form-panel">
        <form class="cmxform form-horizontal style-form" method="post" action="{{route('admin.units.update', $id)}}">
            {{csrf_field()}}
            <input type="hidden" name="parent" value="{{$unit->parent_id}}">
            <input type="hidden" name="_method" value="put">
            <div class="form-group @error('type') has-error @enderror"">
                <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.unit_type')}} ({{__('text.word_required')}})</label>
                <div class="col-lg-10">
                    <select class="form-control text-capitalize" name="type">
                        <option selected disabled>{{__('text.select_unit_type')}}</option>
                        @foreach(\App\Models\Unit::get() as $type)
                            <option {{ (old('type', $unit->unit_id) == $type->id)?'selected':''  }} value="{{$type->id}}">{{$type->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group @error('name') has-error @enderror">
                <label for="name" class="control-label col-lg-2 text-capitalize" >{{__('text.word_name')}} ({{__('text.word_required')}})</label>
                <div class="col-lg-10">
                    <input class=" form-control" name="name" value="{{old('name',$unit->name)}}" type="text" required />
                </div>
            </div>

            <div class="form-group">
                <label  class="control-label col-lg-2 text-capitalize">{{__('text.word_prefix')}}</label>
                <div class="col-lg-10">
                    <input maxlength="5" class=" form-control" name="prefix" value="{{old('prefix', $unit->prefix)}}" type="text" />
                </div>
            </div>

            <div class="form-group ">
                <label  class="control-label col-lg-2 text-capitalize">{{__('text.word_suffix')}}</label>
                <div class="col-lg-10">
                    <input maxlength="5" class=" form-control" name="suffix" value="{{old('suffix', $unit->suffix)}}" type="text"/>
                </div>
            </div>

            @if ($parent_id)
                @if (\App\Models\SchoolUnits::find($parent_id)->unit_id == 3)
                    <div class="form-group ">
                        <label  class="control-label col-lg-2 text-capitalize">{{__('text.word_department')}}</label>
                        <div class="col-lg-10">
                            <select name="parent_id" class="form-control" required>
                                @foreach (\App\Models\SchoolUnits::where('unit_id', '=', $unit->parent->unit_id)->orderBy('name')->get() as $s_unit)
                                    <option value="{{$s_unit->id}}" {{$s_unit->id == $parent_id ? 'selected' : ''}}>{{$s_unit->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>                
                @endif
                @if (\App\Models\SchoolUnits::find($parent_id)->unit_id == 1)
                    <div class="form-group ">
                        <label  class="control-label col-lg-2 text-capitalize">{{__('text.word_school')}}</label>
                        <div class="col-lg-10">
                            <select name="parent_id" class="form-control" required>
                                @foreach (\App\Models\SchoolUnits::where('unit_id', '=', $unit->parent->unit_id)->orderBy('name')->get() as $_unit)
                                    <option value="{{$_unit->id}}" {{$_unit->id == $parent_id ? 'selected' : ''}}>{{$_unit->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>                
                @endif
            @endif


            <div class="form-group">
                <div class="col-lg-offset-2 col-lg-10 text-capitalize">
                    <button class="btn btn-xs btn-theme" type="submit">{{__('text.word_save')}}</button>
                    <a class="btn btn-xs btn-theme04" href="{{route('admin.units.index',[$parent_id])}}" type="button">{{__('text.word_cancel')}}</a>
                </div>
            </div>
        </form>
    </div>
@stop
