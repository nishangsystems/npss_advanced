@extends('admin.layout')

@section('section')

<div class="col-sm-12">
    <div class="form-panel mb-5 ml-2">
        <form class="form-horizontal" role="form" method="POST" action="{{route('admin.boarding_fee.index')}}">
            <div class="form-group ">
                <label for="cname" class="control-label col-sm-1">Dormitory Type<span style="color:red">*</span></label>
                <div class="col-sm-2">
                    <div class="form-group @error('boarding_type') has-error @enderror">
                        <select class="form-control" name="boarding_type" required>
                            <option value="">Select Section</option>
                            <option value="4">First Cycle</option>
                            <option value="20">Second Cycle Science</option>
                            <option value="21">Second Cycle Arts</option>
                            <option value="2">Second Cycle Commcercial</option>
                        </select>
                        @error('boarding_type')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <label for="cname" class="control-label col-sm-2">New Students <span style="color:red">*</span></label>
                <div class="col-sm-2">
                    <div class="form-group @error('amount_new_student') has-error @enderror">
                        <input class=" form-control" name="amount_new_student" value="{{old('amount_new_student')}}" type="number" required />
                        @error('amount_new_student')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <label for="cname" class="control-label col-sm-2 ">Old Students <span style="color:red">*</span></label>
                <div class="col-md-2">
                    <div class="form-group @error('amount_old_student') has-error @enderror">
                        <input class=" form-control" name="amount_old_student" value="{{old('amount_old_student')}}" type="number" required />
                        @error('amount_old_student')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-sm-1">
                    <div class="d-flex justify-content-end ">
                        <button id="save" class="btn btn-sm btn-primary mx-3" type="submit">Save</button>

                    </div>
                </div>
            </div>
            @csrf
        </form>
    </div>
    <div class="content-panel">
        <div class="adv-table table-responsive">
            <table cellpadding="0" cellspacing="0" border="0" class="table" id="hidden-table-info">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Dormitory Type</th>
                        <th>Amount New Students (CFA)</th>
                        <th>Amount Old Students (CFA)</th>
                        <th></th>

                    </tr>
                </thead>
                <tbody>
                    @foreach($boarding_fees as $k=>$boarding_fee)
                    <tr>
                        <td>{{$k+1}}</td>
                        <td>{{$boarding_fee->schoolUnit->name}}</td>
                        <td>{{number_format($boarding_fee->amount_new_student)}}</td>
                        <td>{{number_format($boarding_fee->amount_old_student)}}</td>
                        <td class="d-flex justify-content-end  align-items-center">
                            <a class="btn btn-sm btn-info m-3" href="{{route('admin.boarding_fee.installments',[$boarding_fee->id])}}"><i class="fa fa-eye"> Installments</i></a> |
                            <a class="btn btn-sm btn-success m-3" href="{{route('admin.boarding_fee.edit',[$boarding_fee->id])}}"><i class="fa fa-edit"> Edit</i></a> |
                            <a onclick="event.preventDefault();
                                            document.getElementById('delete').submit();" class=" btn btn-danger btn-sm m-3"><i class="fa fa-trash"> Delete</i></a>
                            <form id="delete" action="{{route('admin.boarding_fee.destroy',$boarding_fee->id)}}" method="POST" style="display: none;">
                                @method('DELETE')
                                {{ csrf_field() }}
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>
</div>
@endsection
