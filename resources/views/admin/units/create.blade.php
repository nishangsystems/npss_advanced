@extends('admin.layout')

@section('section')
    <!-- FORM VALIDATION -->
    <div class="form-panel">
        <form class="cmxform form-horizontal style-form" method="post" action="{{route('admin.units.store')}}">
            {{csrf_field()}}
            <input type="hidden" name="parent_id" value="{{$parent_id}}">
            <div class="form-group @error('type') has-error @enderror">
                <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.unit_type')}} ({{__('text.word_required')}})</label>
                <div class="col-lg-10">
                    <select class="form-control text-capitalize" name="type">
                        <option selected disabled>{{__('text.select_unit_type')}}</option>
                        @foreach(\App\Models\Unit::get() as $type)
                            <option {{old('type', request('type')) == $type->id ?'selected':''}} value="{{$type->id}}">{{$type->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group @error('name') has-error @enderror">
                <input type="hidden" name="parent_id" value="{{$parent_id}}">
                <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.word_name')}} ({{__('text.word_required')}})</label>
                <div class="col-lg-10">
                    <input class=" form-control" name="name" value="{{old('name')}}" type="text" required />
                </div>
            </div>

            <div class="form-group ">
                <label  class="control-label col-lg-2 text-capitalize">{{__('text.word_prefix')}}</label>
                <div class="col-lg-10">
                    <input maxlength="3" class=" form-control" name="prefix" value="{{old('prefix')}}" type="text" />
                </div>
            </div>

            <div class="form-group">
                <label  class="control-label col-lg-2 text-capitalize">{{__('text.word_suffix')}}</label>
                <div class="col-lg-10">
                    <input maxlength="3" class=" form-control" name="suffix" value="{{old('suffix')}}" type="text"/>
                </div>
            </div>

            <div class="form-group">
                <div class="col-lg-offset-2 col-lg-10 text-capitalize">
                    <button class="btn btn-xs btn-primary" type="submit">{{__('text.word_save')}}</button>
                    <a class="btn btn-xs btn-danger" href="{{route('admin.units.index',[$parent_id,0])}}" type="button">{{__('text.word_cancel')}}</a>
                </div>
            </div>
        </form>
    </div>
    <!-- /row -->
@stop
