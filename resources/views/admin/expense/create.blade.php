 <div class="form-panel">
     <form class="form-horizontal" role="form" method="POST" action="{{route('admin.expense.store')}}">

         @csrf
         <div class="row">
            <div class=" @error('name') has-error @enderror col-md-4">
                <input  class=" form-control" name="name" required value="{{old('name')}}">
                @error('name')
                <span class="invalid-feedback">{{ $message }}</span>
                @enderror
                <label for="cname" class="control-label  text-capitalize">{{__('text.word_name')}} <span style="color:red">*</span></label>
             </div>
            <div class=" @error('year_id') has-error @enderror col-md-2">
                 <select class=" form-control" name="year_id" value="" required>
                    <option value="">@lang('text.academic_year')</option>
                    @foreach (\App\Models\Batch::all() as $year)
                        <option value="{{$year->id}}" {{old('year_id', $cayear) == $year->id ? 'selected' : ''}}>{{$year->name??''}}</option>
                    @endforeach
                 </select>
                 @error('year_id')
                 <span class="invalid-feedback">{{ $message }}</span>
                 @enderror
                 <label for="cname" class="control-label text-capitalize">{{__('text.academic_year')}} <span style="color:red">*</span></label>
             </div>
            <div class=" @error('amount_spend') has-error @enderror col-md-3">
                 <input class=" form-control" name="amount_spend" value="{{old('amount_spend')}}" type="number" required />
                 @error('amount_spend')
                 <span class="invalid-feedback">{{ $message }}</span>
                 @enderror
                 <label for="cname" class="control-label text-capitalize">{{__('text.amount_spent')}} <span style="color:red">*</span></label>
             </div>

            <div class=" @error('date') has-error @enderror col-md-2">
                <input class=" form-control" name="date" value="{{old('date', now()->format('Y-m-d'))}}" type="date" required />
                @error('date')
                <span class="invalid-feedback">{{ $message }}</span>
                @enderror
                <label for="cname" class="control-label text-capitalize">{{__('text.expense_date')}} <span style="color:red">*</span></label>
             </div>
             <div class="col-md-1">
                 <div class="d-flex justify-content-end">
                     <button id="save" class="btn btn-xs btn-primary ml-3 text-capitalize" type="submit">{{__('text.word_save')}}</button>
                 </div>
             </div>
         </div>
     </form>
 </div>