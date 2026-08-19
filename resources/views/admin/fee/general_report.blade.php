@extends('admin.layout')
@section('section')
    <div class="container-fluid">
        <form method="post" class=" row py-2">
            @csrf
            <div class="col-md-9">
                <select name="year_id" required class="form-control rounded chosen-select" id="">
                    <option value=""></option>
                    @foreach ($batches as $batch)
                        <option value="{{ $batch->id }}" {{ request('year_id', $current_year_id) == $batch->id ? 'selected' : '' }}>{{ $batch->name }}</option>
                    @endforeach
                </select>
                <label for="" class="text-secondary text-capitalize">@lang('text.academic_year')</label>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-sm btn-primary rounded form-control text-capitalize">@lang('text.word_download')</button>
            </div>
        </div>
    </div>
@endsection