<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\CampusProgram;
use App\Models\PaymentItem;
use App\Models\Payments;
use App\Models\SchoolUnits;
use App\Models\Students;
use App\Models\StudentScholarship;
use Illuminate\Http\Request;
use Session;
use Redirect;

use Auth;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{

    private $batch_id;

    public function __construct()
    {
        $this->batch_id = Batch::find(\App\Helpers\Helpers::instance()->getCurrentAccademicYear())->id;
    }
    public function index(Request $request, $student_id)
    {
        $student = Students::find($student_id);
        $data['title'] = __('text.fee_collections_for', ['item'=>$student->name]);
        $data['student'] = $student;
        return view('admin.fee.payments.index')->with($data);
    }

    public function create(Request $request, $student_id)
    {
        // return 1000000;
        $student = Students::find($student_id);
        $years = $student->classes()->join('batches', 'batches.id', '=', 'student_classes.year_id')->select(['batches.*'])->distinct()->get();
        $data['student'] = $student;
        $data['scholarship'] = Helpers::instance()->getStudentScholarshipAmount($student_id);
        $data['total_fee'] = (int)$student->total();
        // $data['total_fee'] = CampusProgram::where('campus_id', $student->campus_id)->where('program_level_id', $student->program_id)->first()->payment_items()->first()->amount;
        $data['balance'] =  $student->bal($student_id);
        $data['title'] = __('text.collect_fee_for', ['item'=>$student->name]);
        $data['years'] = $years;

        // if ($data['balance'] == 0) {
        //     return redirect(route('admin.fee.collect'))->with('success', 'Student has already completed fee');
        // }
        
        if ($data['total_fee'] <= 0) {

            return redirect(route('admin.fee.collect'))->with('error', __('text.fee_not_set'));
        }
        return view('admin.fee.payments.create')->with($data);
    }



    public function edit(Request $request, $student_id, $id)
    {
        $student = Students::find($student_id);
        $data['student'] = $student;
        $data['payment'] = Payments::find($id);
        $data['title'] = __('text.collect_fee_for', ['item'=>$student->name]);
        return view('admin.fee.payments.edit')->with($data);
    }

    public function store(Request $request, $student_id)
    {

        try {
            //code...
            DB::beginTransaction();
            // return $request->all();
            $student = Students::find($student_id);
            $total_fee = $student->total($student_id);
            $balance =  $student->bal($student_id);
            $debt = 0;
            $_data = [];
            
            $__amount = $request->amount;
            // $debt = $student->debt(Helpers::instance()->getCurrentAccademicYear());
            // return $balance;
            
            $this->validate($request, [
                'item' =>  'required',
                'amount' => 'required',
                'date' => 'required|date',
            ]);
            if($request->year != null){
                $class = $student->_class($request->year);
                // dd($class);
                if($class != null){
                    $cpid = $class->campus_programs->where('campus_id', $student->campus_id)->first();
                    if($cpid != null){
                        $payment_id = $request->year == Helpers::instance()->getCurrentAccademicYear() ? $request->item : PaymentItem::where(['campus_program_id'=>$cpid->id, 'year_id'=>$request->year])->first()->id??null;
                        $total_balance = $student->bal($student->id, $request->year);
                        // dd($total_balance);
                        if($total_balance > 0){
                            $amount = 0; $debt = 0;
                            if($__amount >= $total_balance){
                                $__amount -= $total_balance;
                                $amount = $total_balance;
                            }else{
                                $amount = $__amount;
                                $__amount = 0;
                            }
                            if($request->year == Helpers::instance()->getCurrentAccademicYear()){
                                $debt = $__amount > 0 ? -$__amount : 0;
                            }else{$debt = 0;}

                            $data = [
                                "payment_id" => $payment_id,
                                "student_id" => $student->id,
                                "unit_id" => $class->id,
                                "batch_id" => $request->year,
                                "amount" => $amount,
                                // "date" => $request->date,
                                'reference_number' => $request->reference_number.time().'_'.random_int(1000000, 99999999),
                                'user_id' => auth()->user()->id,
                                'payment_year_id'=>$request->year,
                                'debt' => $debt,
                                'created_at'=>date(DATE_ATOM, time()),
                                'updated_at'=>date(DATE_ATOM, time())
                            ];
                            if ($data['reference_number'] == null || (Payments::where(['reference_number' => $data['reference_number']])->count() == 0)) {
                                $_data[] = $data;
                            }else{return back()->with('error', __('text.reference_already_exist'));}
                        };
                    }
                }
                // dd($_data);
            }else{
                foreach (Batch::orderBy('name')->pluck('id')->toArray() as $key => $year_id) {
                    # code...
                    if($year_id > Helpers::instance()->getCurrentAccademicYear()) break;
                    $class = $student->_class($year_id);
                    if($class != null){
                        $cpid = $class->campus_programs->where('campus_id', $student->campus_id)->first();
                        if($cpid != null){
                            $payment_id = $year_id == Helpers::instance()->getCurrentAccademicYear() ? $request->item : PaymentItem::where(['campus_program_id'=>$cpid->id, 'year_id'=>$year_id])->first()->id??null;
                            $total_balance = $student->total_balance($student->id, $year_id);
                            if($total_balance > 0){
                                $amount = 0; $debt = 0;
                                if($__amount >= $total_balance){
                                    $__amount -= $total_balance;
                                    $amount = $total_balance;
                                }else{
                                    $amount = $__amount;
                                    $__amount = 0;
                                }
                                if($year_id == Helpers::instance()->getCurrentAccademicYear()){
                                    $debt = $__amount > 0 ? -$__amount : 0;
                                }else{$debt = 0;}
    
                                $data = [
                                    "payment_id" => $payment_id,
                                    "student_id" => $student->id,
                                    "unit_id" => $class->id,
                                    "batch_id" => $year_id,
                                    "amount" => $amount,
                                    // "date" => $request->date,
                                    'reference_number' => $request->reference_number.time().'_'.random_int(1000000, 99999999),
                                    'user_id' => auth()->user()->id,
                                    'payment_year_id'=>Helpers::instance()->getCurrentAccademicYear(),
                                    'debt' => $debt,
                                    'created_at'=>date(DATE_ATOM, time()),
                                    'updated_at'=>date(DATE_ATOM, time())
                                ];
                                if ($data['reference_number'] == null || (Payments::where(['reference_number' => $data['reference_number']])->count() == 0)) {
                                    $_data[] = $data;
                                }else{return back()->with('error', __('text.reference_already_exist'));}
                            };
                        }
                    }
                }
            }
            // dd($_data);
            Payments::insert($_data);
            DB::commit();
            return back()->with('success', __('text.word_done'));

        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }

    }


    public function update(Request $request, $student_id, $id)
    {
        $student = Students::find($student_id);
        $this->validate($request, [
            'item' =>  'required',
            'amount' => 'required',
        ]);
        $total_fee = $student->total($student_id);
        $paid =  $student->paid();
        if ($request->amount > $total_fee) {
            return back()->with('error', __('text.deposited_amount_exceeds_total_fee_amount'));
        }
        $p =  Payments::find($id);
        $new_balance = $paid - $p->amount;
        if(($new_balance + $request->amount) > $total_fee){
            return back()->with('error', __('text.deposited_amount_exceeds_total_fee_amount'));
        }
        $p->update([
            "payment_id" => $request->item,
            "amount" => $request->amount,
            "unit_id" => $student->class(Helpers::instance()->getYear())->id,
        ]);

        return redirect()->to(route('admin.fee.student.payments.index', $student_id))->with('success', __('text.word_done'));
    }

    public function destroy(Request $request, $student_id, $id)
    {
        try {
            //code...
            $p =  Payments::find($id);
            $p->delete();
            return back()->with('success', __('text.word_done'));
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('error', $th->getMessage());
        }
    }

    public function print(Request $request, $student_id, $item_id)
    {
        # code...
    }
}
