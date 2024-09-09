<?php


namespace App\Http\Controllers\Admin;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Resources\StudentFee;
use App\Models\Attendance;
use App\Models\Background;
use App\Models\Batch;
use App\Models\CampusSemesterConfig;
use App\Models\Config;
use App\Models\CourseLog;
use App\Models\File;
use App\Models\Income;
use App\Models\Level;
use App\Models\PaymentItem;
use App\Models\Payments;
use App\Models\Period;
use App\Models\PlatformCharge;
use App\Models\ProgramLevel;
use App\Models\Resit;
use App\Models\SchoolContact;
use App\Models\SchoolUnits;
use App\Models\Semester;
use App\Models\StudentClass;
use App\Models\Students;
use App\Models\StudentSubject;
use App\Models\Subjects;
use App\Models\User;
use App\Models\Wage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config as FacadesConfig;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use MongoDB\Driver\Session;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Hash;

// use Khill\Lavacharts\Laravel\LavachartsFacade as Lava;

use function PHPUnit\Framework\returnSelf;

class HomeController  extends Controller
{
    public function index()
    {

        $year = request()->has('year') ? request('year') : Helpers::instance()->getCurrentAccademicYear();
        $campus_id = auth()->user()->campus_id;
        $expected_fees = PaymentItem::where('payment_items.year_id', $year)->where('payment_items.name', 'TUTION')
            ->join('campus_programs', 'campus_programs.id', '=', 'payment_items.campus_program_id')
            ->where(function($query)use($campus_id){
                $campus_id != null ? $query->where('campus_programs.campus_id', $campus_id) : null;
            })->join('program_levels', 'program_levels.id', '=', 'campus_programs.program_level_id')
            ->join('student_classes', 'student_classes.class_id', '=', 'program_levels.id')->where('student_classes.year_id', $year)
            ->join('students', 'students.id', '=', 'student_classes.student_id')->where(function($query)use($campus_id){
                $campus_id != null ? $query->where('students.campus_id', $campus_id) : null;
            })->distinct()->get(['students.id as student_id', 'campus_programs.campus_id', 'payment_items.campus_program_id', 'students.matric', 'payment_items.amount']);

        $payments = Payments::where('batch_id', $year)->whereIn('student_id', $expected_fees->pluck('student_id')->toArray());

        $other_incomes = Income::where('year_id', $this->current_accademic_year)->join('pay_incomes', 'pay_incomes.income_id', '=', 'incomes.id')->select('incomes.id', 'incomes.name', DB::raw('sum(pay_incomes.amount) as amount'))->groupBy('id')->get();

        $levels = Level::all();
        $data['other_incomes'] = $other_incomes;
        $data['expected_fee'] = $expected_fees->sum('amount');
        $data['paid_fee'] = $payments->sum('amount') - $payments->sum('debt');
        $data['owed_fee'] = $data['expected_fee'] - $data['paid_fee'];
        $data['levels'] = $levels;

        $user = \Auth()->user();
        $campus = $user->campus_id;
        $data['campus'] = $campus;
        $data['user'] = $user;
        $data['year'] = $year;
        
        $auth_user = auth()->user();
        // $schools = SchoolUnits::where('id', '<=', '2')->get();
        
        
        $programs = SchoolUnits::where('school_units.unit_id', 4);
        $program_students = SchoolUnits::where('school_units.unit_id', 4)->join('program_levels', 'program_levels.program_id', '=', 'school_units.id')
            ->join('student_classes', 'student_classes.class_id', '=', 'program_levels.id')->where('student_classes.year_id', $year)
            ->join('students', 'students.id', '=', 'student_classes.student_id')->where('students.active', 1)
            ->where(function($query)use($campus_id){
                $campus_id != null ? $query->where('students.campus_id', $campus_id) : null;
            })->distinct()->get(['school_units.id', 'school_units.name as program_name', 'school_units.id as program', 'students.id as student_id', 'students.gender', 'program_levels.level_id'])->groupBy('program')->each(function($rec)use($levels){
                // return $rec;
                $rec->levels = $levels->map(function($level)use($rec){
                    return $rec->where('level_id', $level->id)->count();
                });
            });

        $students = StudentClass::where('year_id', $year)->join('students', 'students.id', '=', 'student_classes.student_id')->where('students.active', 1)
            ->where(function($query)use($campus){
                $campus != null ? $query->where('campus_id', $campus) : null;
            })->distinct()->get(['students.*']);

            
        $data['n_programs'] = SchoolUnits::where('unit_id', 4)->count();
        $data['sms_total'] = Config::where('year_id', $year)->first()->sms_sent;
        $data['n_teachers'] = User::where('type', 'teacher')->count();

        // dd($students);
        $data['active_students'] = $students->where('active', 1);
        $data['inactive_students'] = $students->where('active', 0);
        $data['total_fee_expected'] = 1;
        $data['total_fee_paid'] = 1;
        $data['total_fee_owed'] = 1;

        $data['students'] = $students;
        $data['programs'] = $program_students;
        $data['recovered_debt'] = Payments::where('batch_id', '!=', $year)->where('payment_year_id', $year)->sum('amount');
        $data['_programs'] = $programs;


        // Fee summary template
        $fee_expected = Students::where('students.active' ,1)
            ->join('student_classes', ['student_classes.student_id'=>'students.id'])
            ->where(['student_classes.year_id'=>$year])
            ->join('program_levels', ['program_levels.id'=>'student_classes.class_id'])
            ->join('campus_programs', function($query){
                $query->on(['campus_programs.program_level_id'=>'program_levels.id'])
                    ->on(['campus_programs.campus_id'=>'students.campus_id']);
            })->join('payment_items', function($query){
                $query->on(['payment_items.campus_program_id'=>'campus_programs.id'])
                    ->on(['payment_items.year_id'=>'student_classes.year_id']);
            })->distinct()->select(['students.id', DB::raw("SUM(payment_items.amount) as amount")])->groupBy('students.id')->get()->sum('amount');

        $fee_paid = Students::where('students.active' ,1)
            ->join('student_classes', ['student_classes.student_id'=>'students.id'])
            ->where(['student_classes.year_id'=>$year])
            ->join('program_levels', ['program_levels.id'=>'student_classes.class_id'])
            ->join('campus_programs', function($query){
                $query->on(['campus_programs.program_level_id'=>'program_levels.id'])
                    ->on(['campus_programs.campus_id'=>'students.campus_id']);
            })->join('payment_items', function($query){
                $query->on(['payment_items.campus_program_id'=>'campus_programs.id'])
                    ->on(['payment_items.year_id'=>'student_classes.year_id']);
            })->join('payments', function($query){
                $query->on(['payments.payment_id'=>'payment_items.id'])
                    ->on(['payments.student_id'=>'students.id']);
            })
            ->select(['students.id', DB::raw("SUM(payments.amount) as amount")])
            ->groupBy('students.id')->get()->sum('amount');

        $extra_fee = Students::where('students.active' ,1)
            ->join('student_classes', ['student_classes.student_id'=>'students.id'])
            ->where(['student_classes.year_id'=>$year])
            ->join('extra_fees', ['extra_fees.student_id'=>'students.id'])
            ->where('extra_fees.year_id', $year)->sum('extra_fees.amount');

        $scholarship = Students::where('students.active' ,1)
            ->join('student_classes', ['student_classes.student_id'=>'students.id'])
            ->where(['student_classes.year_id'=>$year])
            ->join('student_scholarships', ['student_scholarships.student_id'=>'students.id'])
            ->where('student_scholarships.batch_id', $year)->sum('student_scholarships.amount');

        $expense = \App\Models\Expenses::where('year_id', $year)->sum('amount_spend');


        $fs['expected'] = $fee_expected + $extra_fee;
        $fs['paid'] = $fee_paid;
        $fs['scholarship'] = $scholarship;
        $fs['expenses'] = $expense;
        $fs['cash'] = $fee_paid - $scholarship - $expense;
        $fs['owed'] = $fee_expected - $fee_paid - $scholarship;
        
        $data['fee_summary'] = $fs;
        // dd($fs);
        
        return view('admin.dashboard', $data);
    }

    public function set_letter_head()
    {
        # code...
        $data['title'] = __('text.upload_letter_head');
        return view('admin.setting.set-letter-head', $data);
    }

    public function save_letter_head(Request $request)
    {

        # code...
        $check = Validator::make($request->all(), ['file'=>'required|file|mimes:png,jpg,jpeg,gif,tif']);
        if ($check->fails()) {
            # code...
            return back()->with('error', $check->errors()->first());
        }
        
        $file = $request->file('file');
        // return $file->getClientOriginalName();
        if(!($file == null)){
            $ext = $file->getClientOriginalExtension();
            $filename = 'lhead.png';
            $path = 'assets/images/avatars';
            if(!file_exists(url($path))){mkdir(url($path));}
            // $file->move(url($path), $filename);
            $file->move(public_path($path), $filename);
            if(File::where(['name'=>'letter-head'])->count() == 0){
                File::create(['name'=>'letter-head', 'path'=>$filename]);
            }else {
                File::where(['name'=>'letter-head'])->update(['path'=>$filename]);
            }
            return back()->with('success', __('text.word_done'));
        }
        return back()->with('error', __('text.error_reading_file'));
    }

    
    public function set_watermark()
    {
        # code...
        $data['title'] = __('text.set_watermark');
        return view('admin.setting.set_watermark', $data);
    }

    public function save_watermark(Request $request)
    {
        # code...
        # code...
        $check = Validator::make($request->all(), ['file'=>'required|file|mimes:jpeg']);
        if ($check->fails()) {
            # code...
            return back()->with('error', $check->errors()->first());
        }
        
        $file = $request->file('file');
        // return $file->getClientOriginalName();
        if(!($file == null)){
            $ext = $file->getClientOriginalExtension();
            $filename = 'logo.jpeg';
            $path = base_path('/assets/images');
            // $file->n('/bg_image', $filename);
            // \Storage::put($path, $file);
            $request->file('file')->move($path, $filename);
            return back()->with('success', __('text.word_done'));
        }
        return back()->with('error', __('text.error_reading_file'));
    }


    public function set_background_image()
    {
        # code...
        $data['title'] = 'Set Background Image';
        return view('admin.setting.bg_image', $data);
    }
    public function save_background_image(Request $request)
    {
        # code...
        # code...
        $check = Validator::make($request->all(), ['file'=>'required|file|mimes:png,jpg,jpeg,gif,tif']);
        if ($check->fails()) {
            # code...
            return back()->with('error', $check->errors()->first());
        }
        
        $file = $request->file('file');
        // return $file->getClientOriginalName();
        if(!($file == null)){
            $ext = $file->getClientOriginalExtension();
            $filename = '_'.random_int(100000, 999999).'_'.time().'.'.$ext;
            $path = $filename;
            $file->storeAs('/files', $filename);
            if(File::where(['name'=>'background-image', 'campus_id'=>auth()->user()->campus_id??0])->count() == 0){
                File::create(['name'=>'background-image', 'campus_id'=>auth()->user()->campus_id??0, 'path'=>$path]);
            }else {
                File::where(['name'=>'background-image', 'campus_id'=>auth()->user()->campus_id??0])->update(['path'=>$path]);
            }
            return back()->with('success', 'Done');
        }
        return back()->with('error', 'Error reading file');
    }

    public function setayear()
    {
        $data['title'] = __('text.set_current_accademic_year');
        return view('admin.setting.setbatch')->with($data);
    }

    public function setsem()
    {
        return view('admin.setting.setsem');
    }

    public function courses_date_line(Request $request)
    {
        $data['title'] = __('text.set_course_registration_dateline').($request->has('semester') ? ' '.__('text.word_for').' '.Semester::find($request->semester)->name : '');
        if(request()->has('background')){
            $data['current_semester'] = Semester::where(['background_id'=>$request->background, 'status'=>1])->first()->id ?? null;
        }
        return view('admin.setting.set_course_date', $data);
    }

    public function save_courses_date_line(Request $request)
    {
        # code...
        $val = Validator::make($request->all(), ['semester'=>'required', 'date'=>'required|Date']);
        if ($val->fails()) {
            # code...
            return back()->with('error', $val->errors()->first());
        }

        try {
            //code...
            \App\Models\CampusSemesterConfig::updateOrInsert(['semester_id'=>$request->semester, 'campus_id'=>auth()->user()->campus_id ?? NULL], ['courses_date_line'=>$request->date]);

            return back()->with('success', __('text.word_done'));
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('error', $th->getMessage());
        }

    }

    public function course_date_line(Request $request, $campus, $semester)
    {
        # code...
        $conf = Helpers::instance()->campusSemesterConfig($semester, $campus);
            if ($conf->count() == 0) {
                # code...
                return ['semester'=>Semester::find($semester)->name, 'date_line'=>__('text.DATELINE_NOT_SET')];
            }
            // return __DIR__;
            return ['semester'=>Semester::find($semester)->name, 'date_line'=>date('l d-m-Y', strtotime($config->first()->courses_date_line)), 'date'=>$config->first()->courses_date_line];
    }

    public function program_settings(Request $request)
    {
        # code...
        $data['title'] = __('text.program_settings');
        return view('admin.setting.program_settings', $data);
    }

    public function post_program_settings(Request $request)
    {
        # code...
        $program = SchoolUnits::find($request->program);
        // return $program;
        if ($program != null) {
            # code...
            $program->max_credit=$request->max_credit;
            $program->ca_total=$request->ca_total;
            $program->exam_total=$request->exam_total;
            $program->resit_cost=$request->resit_cost;
            $program->save();
            return back()->with('success', __('text.word_done'));
        }
        return back()->with('error', __('text.page_not_found'));
    }


    public function setsemester(Request $request)
    {
        # code...
        $data['title'] = __('text.set_current_semester');
        $data['semesters'] = Semester::join('backgrounds', ['backgrounds.id'=>'semesters.background_id'])
                    ->distinct()->select(['semesters.*', 'backgrounds.background_name'])->orderBy('background_name', 'DESC')->orderBy('name', 'ASC')->get();
        // return $data;
        return view('admin.setting.setsemester', $data);
    }

    public function postsemesterminfee(Request $request, $semester_id)
    {
        $validity = Validator::make($request->all(), ['semester_min_fee'=>'required']);
        if($validity->fails()){
            return back()->with('error', $validity->errors()->first());
        }
        $semester = Semester::find($semester_id);
        if($semester != null){
            $semester->semester_min_fee = $request->semester_min_fee;
            $semester->save();
            return back()->with('success', __('text.word_Done'));
        }
        return back();
    }

    public function postsemester(Request $request, $id)
    {
        # code...
        try {
            // dd($request->all());
            //code...
            $semesters = Semester::where(['background_id'=>$request->background])->get();
            foreach ($semesters as $key => $sem) {
                # code...
                $sem->status = 0;
                $sem->save();
            }
            $semester = Semester::find($id);
            if($request->semester_min_fee != $semester->semester_min_fee){
                $semester->semester_min_fee = $request->semester_min_fee;
                $semester->user_id = auth()->id();
            }
            $semester->status = 1;
            $semester->save();
            return back()->with('success', __('text.word_done'));
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('error', __('text.operation_failed').' '.$th->getMessage());
        }
    }

    public function createsem(Request $request)
    {
        $id = $request->input('sem');
        $get_sem = \App\Models\Sequence::find($id);
        return redirect()->back();
    }

    public function deletebatch($id)
    {
        if (DB::table('batches')->count() == 1) {
            return redirect()->back()->with('error', __('text.can_not_delete_last_batch'));
        }
        DB::table('batches')->where('id', '=', $id)->delete();
        return redirect()->back()->with('success', __('text.word_done'));
    }



    public function setAcademicYear($id)
    {
        // dd($id);
        $year = Config::all()->last();
        $data = [
            'year_id' => $id
        ];
        $year->update($data);

        return redirect()->back()->with('success', __('text.word_done'));
    }

    public function extraFee(Request $request)
    {
        # code...
        $data['title'] = __('text.add_additional_fee_for', ['item'=>$request->student_id == null ? '' : Students::find($request->student_id)->name ?? '']);
        return view('admin.fee.extra-fee', $data);
    }

    public function extraFeeSave(Request $request)
    {
        # code...
        $check = Validator::make($request->all(), ['amount'=>'required', 'year_id'=>'required']);
        if ($check->fails()) {
            # code...
            return back()->with('error', $check->errors()->first());
        }
        // return $request->all();
        \App\Models\ExtraFee::create(['student_id'=>$request->student_id, 'amount'=>$request->amount, 'year_id'=>$request->year_id]);
        return back()->with('success', __('text.word_done'));
    }

    public function extraDestroy(Request $request){
        $row = \App\Models\ExtraFee::where(['student_id'=>$request->student_id, 'id'=>$request->extra_fee_id])->first();
        if($row != null){
            $row -> delete();
        }
        return back()->with('success', __('text.word_done'));
    }
    
    public function custom_resit_create()
    {
        # code...
        $data['title'] = __('text.open_resit');
        return view('admin.setting.custom_resit.create', $data);
    }

    public function custom_resit_edit(Request $request, $id)
    {
        # code...
        $data['title'] = __('text.edit_resit');
        $data['resit'] = Resit::find($id);
        return view('admin.setting.custom_resit.edit', $data);
    }

    public function custom_resit_save(Request $request)
    {
        # code...
        $validator = Validator::make($request->all(), ['year_id'=>'required', 'background_id'=>"required", 'start_date'=>'required|date', 'end_date'=>'required|date']);
        if($validator->fails()){
            return back()->with('error', $validator->errors()->first());
        }

        // if(Resit::where(['year_id'=>$request->year_id, 'background_id'=>$request->background_id, 'campus_id'=>$request->campus_id])->whereBetween('start_date'))
        $resit = new Resit($request->all());
        $resit->save();
        return back()->with('success', __('text.word_done'));
    }

    public function custom_resit_update(Request $request, $id)
    {
        # code...
        $validator = Validator::make($request->all(), ['year_id'=>'required', 'background_id'=>"required", 'start_date'=>'required|date', 'end_date'=>'required|date']);
        if($validator->fails()){
            return back()->with('error', $validator->errors()->first());
        }

        $resit = Resit::find($id);
        if($resit != null){
            $resit->fill($request->all());
            $resit->save();
            return back()->with('success', __('text.word_done'));
        }

        return back()->with('error', __('text.operation_failed_record_not_found'));
    }

    public function custom_resit_delete(Request $request, $id)
    {

        $resit = Resit::find($id);
        if($resit != null){
            $resit->delete();
            return back()->with('success', 'Done');
        }

        return back()->with('error', __('text.operation_failed_record_not_found'));
    }

    public function resits_index()
    {
        # code...
        $data['title'] = __('text.word_resits');
        return view('admin.resit.index', $data);
    }

    public function resit_course_list(Request $request, $resit_id)
    {
        # code...
        $resit =  Resit::find($resit_id);
        if($resit == null){
            return back()->with('error', 'Resit is not found');
        }
        $data['title'] = __('text.course_list_for', ['item'=>$resit->name]);
        // return 'nonsense going on here';
        $data['courses'] = Subjects::join('student_courses', ['student_courses.course_id'=>'subjects.id'])
                    ->whereNotNull('student_courses.paid')
                    ->where(['student_courses.resit_id'=>$resit_id, 'student_courses.year_id'=>Helpers::instance()->getCurrentAccademicYear()])
                    ->join('students', ['students.id'=>'student_courses.student_id'])
                    ->where(['students.campus_id'=>auth()->user()->campus_id])
                    ->select(['subjects.*', 'resit_id', 'year_id'])->orderBy('subjects.name')->distinct()->get();
        // dd($data['courses']);
        $data['resit'] = $resit;
        if($request->has('print') && $request->print == 1){
            $pdf = Pdf::loadView('admin.resit.course_list_downloadable', $data);
            return $pdf->download($data['title'] . '.pdf');
        }
        return view('admin.resit.course_list', $data);
    }

    public function resit_course_list_download(Request $request)
    {
        # code...
        $subject = Subjects::find($request->subject_id);
        $data['title'] = __('text.resit_course_list_for', ['item'=>Resit::find($request->resit_id)->name]);
        $data['subjects'] = Subjects::find($request->subject_id)->student_subjects()->where(['resit_id' => $request->resit_id])->whereNotNull('paid')
                        ->join('students',  ['students.id'=>'student_courses.student_id'])
                        ->orderBy('students.name')->get(['student_courses.*']);
        if($request->print == 1){

            $pdf = Pdf::loadView('admin.resit._course_list_print', $data);
            return $pdf->download(__('text.resit_course_list_for', ['item'>"[ ".$subject->code .' ] '. $subject->name.' - '.Resit::find($request->resit_id)->year->name . '.pdf']));
        }
        // dd($data['subjects']);
        return view('admin.resit.course_list_print', $data);
    }

    public function set_charges()
    {
        # code...
        $data['title'] = __('text.set_charges');
        return view('admin.setting.charges', $data);
    }

    public function save_charges(Request $request)
    {
        # code...
        // return $request->all();
        $validity = Validator::make($request->all(), [
            'year_id'=>'required',
            'yearly_amount'=>'numeric',
            'transcript_amount'=>'numeric',
            'result_amount'=>'numeric',
            'parent_amount'=>'numeric'
        ]);
        if($validity->failed()){
            return back()->with('error', $validity->errors()->first());
        }
        PlatformCharge::updateOrInsert(['year_id'=>$request->year_id], ['yearly_amount'=>$request->yearly_amount, 'result_amount'=>$request->result_amount, 'transcript_amount'=>$request->transcript_amount, 'parent_amount'=>$request->parent_amount]);
        return back()->with('success', __('text.word_done'));
    }



    // MANAGE WAGES
    public function wages(Request $request)
    {
        # code...
        $campus_id = auth()->user()->campus_id;
        $data['title'] = "Wages";
        return view('admin.setting.wages.index', $data);
    }

    public function create_wages(Request $request)
    {
        # code...
        $data['title'] = "Add Teacher Hour Wages for ".User::find($request->teacher_id)->name;
        $data['rates'] = Wage::where('teacher_id', $request->teacher_id)->get();
        return view('admin.setting.wages.create', $data);
    }

    public function save_wages(Request $request)
    {
        # code...
        // return $request->all();
        $validate = Validator::make($request->all(), ['background_id'=>'required', 'rate'=>'required']);
        if($validate->failed()){
            return back()->with('error', $validate->errors()->first());
        }
        $data = ['price'=>$request->rate, 'teacher_id'=>$request->teacher_id, 'level_id'=>$request->level_id??null];
        if(Wage::where(['teacher_id'=>$request->teacher_id, 'level_id'=>$request->level_id??null])->count() > 0){
            return back()->with('error', __('text.record_already_exist', ['item'=>'']));
        }
        $instance = new Wage($data);
        $instance->save();
        return back()->with('success', __('text.word_done'));
    }

    public function drop_wages(Request $request)
    {
        # code...
        $wage = Wage::find($request->wage_id);
        if($wage !== null){
            $wage->delete();
            return back()->with('success', __('text.word_done'));
        }
    }

    public function school_contacts($id = null)
    {
        # code...
        $data['title'] = __('text.school_contacts');
        $data['contacts'] = SchoolContact::all();
        if($id != null){
            $data['_contact'] = SchoolContact::find($id);
        }
        return view('admin.setting.school_contacts', $data);
    }

    public function save_school_contact(Request $request, $id = null)
    {
        # code...
        $validity = Validator::make($request->all(), [
            'title'=>'required', 'contact'=>'required'
        ]);
        if($validity->fails()){
            return back()->with('error', $validity->errors()->first());
        }
        $data = ['name'=>$request->name??null, 'title'=>$request->title, 'contact'=>$request->contact];
        $instance = new SchoolContact($data);
        if($id != null){
            $instance = SchoolContact::find($id);
            $instance->fill($data);
        }
        $instance->save();
        return back()->with('success', __('text.word_Done'));
    }

    public function drop_school_contacts(Request $request, $id)
    {
        # code...
        $contact = SchoolContact::find($id);
        if($contact != null){
            $contact->delete();
        }
        return back()->with('success', __('text.word_Done'));
    }

    public function block_user($user_id)
    {
        # code...
        $user = User::find($user_id);
        if($user != null){
            $update = ['active'=>0, 'activity_changed_by'=>auth()->id(), 'activity_changed_at'=>now()->format(DATE_ATOM)];
            $user->update($update);
        }
        return back()->with('success', __('text.word_Done'));
    }

    public function activate_user($user_id)
    {
        # code...
        $user = User::find($user_id);
        if($user != null){
            $update = ['active'=>1, 'activity_changed_by'=>auth()->id(), 'activity_changed_at'=>now()->format(DATE_ATOM)];
            $user->update($update);
        }
        return back()->with('success', __('text.word_Done'));
    }

    public function course_periods()
    {
        # code...
        $data['title'] = "All Periods";
        $data['periods'] = Period::orderBy('id', 'DESC')->get();
        return view('admin.periods.index', $data);
    }

    public function save_course_periods(Request $request)
    {
        # code...
        $validity = Validator::make($request->all(), ['starts_at'=>'required', 'ends_at'=>'required']);
        if($validity->fails()){
            session()->flash('error', $validity->errors()->first());
            return back()->withInput();
        }

        $data = $request->only(['starts_at', 'ends_at']);
        if(Period::where($data)->count() > 0){
            session()->flash('error', 'A period with the same start and end time already exist');
            return back()->withInput();
        }
        (new Period($data))->save();
        return back()->with('success', 'Period successfully created');
    }

    public function edit_course_periods($period_id)
    {
        # code...
        $data['title'] = "All Periods";
        $data['period'] = Period::find($period_id);
        $data['periods'] = Period::orderBy('id', 'DESC')->get();
        return view('admin.periods.edit', $data);
    }

    public function update_course_period(Request $request, $period_id)
    {
        $validity = Validator::make($request->all(), ['starts_at'=>'required', 'ends_at'=>'required']);
        if($validity->fails()){
            session()->flash('error', $validity->errors()->first());
            return back()->withInput();
        }

        $period = Period::find($period_id);
        $data = $request->only(['starts_at', 'ends_at']);
        if(Period::where($data)->count() > 0 && Period::where($data)->where('id', $period_id)->count() == 0){
            session()->flash('error', 'Another period with the same start and end time already exist');
            return back()->withInput();
        }

        $period->fill($data);
        $period->save();
        return back()->with('success', 'Period successfully updated');
    }

    public function delete_course_period($period_id)
    {
        # code...
        if(Attendance::where('period_id', $period_id)->count() > 0){
            return back()->with('error', 'Period is already has a course log or attendance record');
        }
        $period = Period::find($period_id);
        $period->delete();
        return back()->with('success', 'Record successfully deleted');
    }

    public function abs_password_reset(Request $request){
        $data['title'] = "Reset user password";
        return view('admin.user.reset_password', $data);
    }

    public function abs_reset_password(Request $request){
        $validity = Validator::make($request->all(), ['user_id'=>'required']);
        if($validity->fails()){
            return back()->with('error', $validity->errors()->first());
        }

        $user = User::find($request->user_id);
        if($user != null){
            $user->update(['password'=>Hash::make('12345678')]);
            return back()->with('success', "User password reset to 12345678");
        }
        return back()->with('error', "User record not found");
    }

}
