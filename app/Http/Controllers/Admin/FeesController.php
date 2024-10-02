<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Controllers\HomeController;
use App\Models\Batch;
use App\Models\ExtraFee;
use App\Models\PaymentItem;
use App\Models\Payments;
use App\Models\ProgramLevel;
use App\Models\SchoolUnits;
use App\Models\StudentClass;
use App\Models\Students;
use App\Models\StudentScholarship;
use App\Models\TeachersSubject;
use Carbon\Carbon;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FeesController extends Controller
{

    public function classes(Request  $request)
    {

        $title = __('text.word_classes');
        $classes = SchoolUnits::where('parent_id', $request->get('parent_id', '0'))->get();

        return view('admin.fee.classes', compact('classes', 'title'));
    }

    public function student(Request  $request, $class_id)
    {
        $class = SchoolUnits::find($class_id);
        $title = $class->name . " ".__('text.word_students');
        $students = $class->students(session()->get('mode', Helpers::instance()->getCurrentAccademicYear()))->paginate(20);
        return view('admin.fee.students', compact('students', 'title'));
    }

    public function collect(Request  $request)
    {
        $title = __('text.collect_fee');
        return view('admin.fee.collect', compact('title'));
    }


    public function printFee(Request  $request)
    {
        $title = __('text.print_fee');
        return view('admin.fee.print', compact('title'));
    }

    public function printStudentFee(Request  $request, $student_id)
    {
        $student = Students::find($student_id);
        $year = \App\Helpers\Helpers::instance()->getYear();
        $numbers = [1, 2];
        return view('admin.fee.print_reciept', compact('student', 'year', 'numbers'));
    }

    public function daily_report(Request  $request)
    {
        $title = __('text.fee_daily_report_for')." " . ($request->date ? $request->date : Carbon::now()->format('d/m/Y'));
        $fees = Payments::whereDate('payments.created_at', $request->date ?? date('Y-m-d', time()))
        ->join('students', 'students.id', '=', 'payments.student_id')
        ->where(function($q) {
            # code...
            auth()->user()->campus_id == null ? null : $q->where('students.campus_id', '=', auth()->user()->campus_id);
        })
        ->get(['payments.*', 'students.id as student_id', 'students.matric', 'students.name', 'students.campus_id', 'students.program_id']);
        // return $fees;
        return view('admin.fee.daily_report', compact('fees', 'title'));
    }

    public function payment_details(){
        $year = $this->current_accademic_year;
        $data['title'] = "Detailed Payments Report";
        // $data['payments'] = Payments::where('payment_year_id', $year)
        //     ->orderBy('created_at', 'DESC')->get()->groupBy('student_id');
        // $data['total'] = Payments::where('payment_year_id', $year)->sum('amount');
        $data['payments'] = Payments::where('payment_year_id', $year)->join('students', ['students.id'=>'payments.student_id'])
            ->select('payments.*')->orderBy('payments.created_at', 'DESC')->get()->groupBy('student_id');
        $data['total'] = Payments::where('payment_year_id', $year)->join('students', ['students.id'=>'payments.student_id'])->select([DB::raw("SUM(payments.amount - payments.debt) as total")])->get()->sum('total');
            
        // dd($data);
        return view('admin.fee.payments.details', $data);
    }

    public function fee(Request  $request)
    {
        $type = request('type', 'completed');
        $data['title'] = $type . " ".__('text.word_fee');
        // dd($data);
        return view('admin.fee.fee', $data);
    }

    public function fee_situation(Request $request)
    {
        # code...

        $class = ProgramLevel::find($request->class);
        $data['title'] = __('text.fee_situation');
        return view('admin.fee.fee_situation', $data);
    }



    public function fee_situation_list(Request $request)
    {
        # code...
        $class = ProgramLevel::find($request->class);
        $data['title'] = __('text.fee_situation').' '.__('text.word_for').' '.$class->program->name.' : '.__('text.word_level').' '.$class->level->level.' - '.Batch::find($request->year)->name;
        $data['data'] = HomeController::fee_situation($request);
        // return $data;
        return view('admin.fee.fee_situation_list', $data);
    }


    public function fee_list(Request  $request)
    {
        $type = request('type', 'completed');
        $data['title'] = $type . " ".__('text.word_fee');
        $data['students'] = HomeController::_fee($request);

        // return $data;
        return view('admin.fee.fee_listing', $data);
    }

    public function drive(Request  $request)
    {
        $title = __('text.fee_drive');
        $students = [];
        return view('admin.fee.drive', compact('students', 'title'));
    }

    public function drive_listing(Request $request)
    {
        // return $request->all();
        # code...
        $data = HomeController::_fee($request);
        $data['title'] = $data['title'].' '.($request->has('amount') ? __('text.who_have_paid_atleast').' '.$request->amount : null);
        return view('admin.fee.drive_listing', $data);
    }

    public function delete(Request  $request, $id)
    {
        Payments::find($id)->delete();
        session()->flash('success', __('text.word_done'));
        return redirect()->back();
    }

    public function import()
    {
        # code...
        $data['title'] = __('text.import_fees');
        return view('admin.fee.import', $data);
    }

    public function import_save(Request $request)
    {
        # code...
        $validator = Validator::make($request->all(), [
            'file'=>'required|file',
            'import_reference'=>'required',
            'batch'=>'reuired',
        ]);
        // import_reference is a text string that identifoes a particular importation so that it can be cancelled if an error occurs.

        if ($validator->fails()) {
            # code...
            return back()->with('error', $validator->errors()->first());
        }
        try {
            //code...

            // cancel operation if provided import_reference already exist
            if(Payments::where('import_reference', $request->import_reference)->count() > 0){
                return back()->with('error', __('text.record_already_exist', ['item'=>__('text.word_reference').' '.$request->import_reference]));
            }

            $file = $request->file('file');
            if($file->getClientOriginalExtension() == 'csv'){
                // save file
                $filename = 'fee-'.date('Y_m_d@H_i_s',time()).'_'.random_int(100, 999).'.'.$file->getClientOriginalExtension();
                $file->move(public_path('files'), $filename);
                $file_path = public_path('files/'.$filename);
                
                $pointer = fopen($file_path, 'r');
                $file_data = [];
                while (($data_row = fgetcsv($pointer, 1000, ',')) !== false) {
                    $file_data[] = $data_row;
                }
                // return $file_data;
                
                $matric_probs = '';
                $ref_probs = '';
                $campus_access = '';
                $campus_access_prefix= __('text.x_phrase_2');
                $fee_settings_probs = '';
                if (count($file_data) > 0){
                    DB::beginTransaction();
                    $payments = [];
                    foreach ($file_data as $value) {
                        # code...
                        $student = \App\Models\Students::where('matric', '=', $value[0])->first() ?? null;
                        if (auth()->user()->campus_id != null && $student != null && $student->campus_id != auth()->user()->campus_id) {
                            # code...
                            $campus_access .= $student->matric .', ';
                            continue;
                        }
                        if ($student != null) {
                            # code...

                            // GET TUTION FEE FOR PROGRAM IN CAMPUS
                            $p_i = \App\Models\CampusProgram::where('campus_id', '=', $student->campus_id)
                            ->where('program_level_id', '=', $student->program_id)
                            ->join('payment_items', 'campus_program_id', '=', 'campus_programs.id')
                            ->where('name', '=', 'TUTION')->whereNotNull('amount');
                            // check if fee is set
                            // CHECK FEE SETTINGS FOR THE GIVEN PROGRAM IN THE GIVEN CAMPUS
                            if ($p_i->count() == 0) {
                                # code...
                                $prog = \App\Models\ProgramLevel::find($student->program_id);
                                $cmps = \App\Models\Campus::find($student->campus_id)->name;
                                $str = " Tution not set for Program : ".$prog->program()->first()->name." ".__('text.word_level')." ".$prog->level()->first()->level.' '.__('text.word_in').' '.$cmps.' '.__('text.word_campus');
                                $fee_settings_probs .= str_contains($fee_settings_probs, $str) ? '' : $str;
                                continue;
                            }
                            $payments[] = [
                                'payment_id' => $p_i->pluck('payment_items.id')[0],
                                'student_id' => $student->id,
                                'batch_id' => $request->batch_id,
                                'unit_id' => \App\Models\StudentClass::where('student_id', '=', $student->id)->pluck('class_id')[0],
                                'amount' => $value[1],
                                'reference_number' => $value['2'] ?? ''
                            ];
                        }else{
                            $matric_probs .= ' '.__('text.word_matricule').' '.$value[0].' '.__('text.not_found').',';
                        }
                    }
                    foreach ($payments as $value) {
                        if ($value['reference_number'] != '' && \App\Models\Payments::where('reference_number', '=', $value['reference_number'])->count() == 0) {
                            # code...
                            \App\Models\Payments::create($value);
                        }
                        else{
                            $ref_probs .= __('text.reference_error_with', ['item'=>\App\Models\Students::find($value['student_id'])->matric]);
                        }
                    }
                    DB::commit();
                    if(strlen($campus_access) > 0){throw new Error($campus_access_prefix.$campus_access);}
                    if(strlen($fee_settings_probs) > 0) {throw new Error($fee_settings_probs);}
                    if(strlen($matric_probs) > 0) {throw new Error($matric_probs);}
                    if(strlen($ref_probs) > 0){throw new Error($ref_probs);}
                    return back()->with('success', __('text.word_done'));
                    // return $request->all();
                }
                else {
                    return back()->with('error', __('text.x_phrase_3'));
                }
            }
            else{
                return back()->with('error', __('text.file_type_constraint', ['type'=>'.csv']));
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            // throw $th;
            return back()->with('error', $th->getMessage());
        }
    }

    public function import_undo(Request $request)
    {
        # code...
        $records = Payments::where('import_reference', $request->import_reference);
        if($records->count() > 0){
            foreach ($records->get() as $key => $record) {
                # code...
                $record->delete();
            }
        }
        return back()->with('success', __('text.word_done'));
    }

    public function fee_history($student_id)
    {
        # code...

        $student = Students::find($student_id);
        $data['student'] = $student;
        $data['classes'] = ProgramLevel::join('student_classes', 'student_classes.class_id', '=', 'program_levels.id')->where('student_id', $student_id)->select(['student_classes.year_id', 'student_classes.id as student_class_id', 'program_levels.*'])->distinct()->get();
        // $data['fee'] = PaymentItem::join('campus_programs', 'campus_programs.id', '=', 'payment_items.campus_program_id')->join('program_levels', 'program_levels.id', '=', 'campus_programs.program_level_id')->join('student_classes', 'student_classes.class_id', '=', 'program_levels.id')->where('student_classes.year_id', 'payment_items.year_id')->where('student_classes.student_id', $student_id)->select(['payment_items.*', 'student_classes.id as student_class_id'])->distinct()->get();
        $data['fee'] = $data['classes']->map(function($rec)use($student){
            return ['year_id'=>$rec->year_id, 'fee'=>$student->total($rec->year_id)];
        });
        $data['extra_fee'] = ExtraFee::where('student_id', $student_id)->get();
        $data['scholarship'] = StudentScholarship::where('student_id', $student_id)->get();
        $data['payments'] = Payments::where('student_id', $student_id)->get();
        $data['title'] = "Detailed Payment History for ".$student->name??'';
        // dd($data);

        // $_data = $data['fee'] = $data['classes']->map(function($rec)use($data){
        //     return [
        //         'class'=>$rec,
        //         'fee'=>$data['fee']->where('year_id', $rec->year_id)->first()->fee??'',
        //         'extra_fee'=>$data['extra_fee']->where('year_id', $rec->year_id)->sum('amount'),
        //         'scholarship'=>$data['scholarship']->where('batch_id', $rec->year_id)->sum('amount'),
        //         'payments'=>$data['payments']->where('payment_year_id', $rec->year_id)->sum('amount + debt')
        //     ];
        // });
        // dd($_data);
        return view('admin.fee.payments.history', $data);

    }

    public function fee_settings(Request $request)
    {
        # code...
        $year = \App\Helpers\Helpers::instance()->getCurrentAccademicYear();
        $data['title'] = "Fee Settings";
        $data['campuses'] = \App\Models\Campus::all();
        if($request->campus_id == null){
            return view('admin.fee.settings.campuses', $data);
        }
        $data['campus'] = \App\Models\Campus::find($request->campus_id);
        if($data['campus'] != null){
            $fee_items = $data['campus']->campus_programs()
                ->join('payment_items', 'payment_items.campus_program_id', '=', 'campus_programs.id')
                ->where('payment_items.year_id', $year)->select(['payment_items.*', 'campus_programs.program_level_id'])->get();
            
            $classes = $data['campus']->programs->unique()->map(function($rec)use($fee_items){
                $fee = $fee_items->where('program_level_id', $rec->id)->where('name', 'TUTION')->first();
                if($fee != null){
                    $rec->amount = $fee->amount??null;
                    $rec->first_instalment = $fee->first_instalment??null;
                    $rec->second_instalment = $fee->second_instalment??null;
                }
                return $rec;
            });
            $data['classes'] = $classes;
            // dd($classes);

        }
        return view('admin.fee.settings.classes', $data);
    }

    public function fee_banks($campus_id, $program_id = null)
    {
        # code...
        $campus = \App\Models\Campus::find($campus_id);
        $data['title'] = "Program Banks";
        $data['campus'] = $campus;
        $data['program'] = $program_id == null ? null : SchoolUnits::find($program_id);
        $data['program_bank'] = $program_id == null ? null : $data['program']->banks($campus_id)->first();
        $data['programs'] = SchoolUnits::whereIn('id', $campus->programs->pluck('id')->all())->where('unit_id', 4)->distinct()->get(['*']);
        $data['banks'] = \App\Models\Bank::all();
        // dd($data['programs']);
        return view('admin.fee.settings.program_banks', $data);
    }

    public function save_fee_banks(Request $request, $campus_id, $program_id = null)
    {
        # code...
        try {
            //code...
            $validity = Validator::make($request->all(), ['bank_id'=>'required', 'school_unit_id'=>'required']);
            if($validity->fails()){
                session()->flash('error', $validity->errors()->first());
                return back()->withInput();
            }
            $data = ['bank_id'=>$request->bank_id, 'school_units_id'=>$request->school_unit_id, 'campus_id'=>$campus_id];
            \App\Models\ProgramBank::updateOrInsert(['school_units_id'=>$request->school_unit_id, 'campus_id'=>$campus_id], $data);
            return back()->with('success', 'Done');
        } catch (\Throwable $th) {
            //throw $th;
            session()->flash('error', "F::{$th->getFile()}, L::{$th->getLine()}, M::{$th->getMessage()}");
            return back()->withInput();
        }
    }

    public function banks($id = null)
    {
        # code...
        $data['title'] = "All Banks";
        $data['banks'] = \App\Models\Bank::all();
        if($id !== null){
            $data['bank'] = \App\Models\Bank::find($id);
        }
        return view('admin.fee.settings.banks', $data);
    }

    public function save_bank(Request $request, $id = null)
    {
        # code...

        try {
            //code...
            $validity = Validator::make($request->all(), ['name'=>'required', 'account_name'=>'required', 'account_number'=>'required']);
            if($validity->fails()){
                session()->flash('error', $validity->errors()->first());
                return back()->withInput();
            }
    
            $instance = $id != null ?
                \App\Models\Bank::find($id) :
                new \App\Models\Bank();
    
            $instance->name = $request->name;
            $instance->account_name = $request->account_name;
            $instance->account_number = $request->account_number;
            $instance->save();
            return back()->with('success', 'Done');
        } catch (\Throwable $th) {
            //throw $th;
            session()->flash('error', "F::{$th->getFile()}, L::{$th->getLine()}, M::{$th->getMessage()}");
            return back()->withInput();
        }
    }
}
