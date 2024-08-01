<?php

namespace App\Http\Controllers;

use App\Models\Campus;
use App\Models\CampusDegree;
use App\Models\Certificate;
use App\Models\CertificateProgram;
use App\Models\Degree;
use App\Models\DegreeCertificate;
use App\Models\Level;
use App\Models\ProgramLevel;
use App\Models\SchoolUnits;
use App\Models\StudentClass;
use App\Models\Students;
use Illuminate\Foundation\Console\RouteListCommand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{
    //

    public function degrees()
    {
        # code...
        return response()->json(['data'=>Degree::all()]);
    }

    public function certificates()
    {
        # code...
        return response()->json(['data'=>Certificate::all()]);
    }

    public function campuses()
    {
        # code...
        return response()->json(['data'=>Campus::all()]);
    }

    public function campus_degrees(Request $request, $campus_id)
    {
        # code...
        return response()->json(['data'=>Campus::find($campus_id)->degrees]);
    }

    public function update_campus_degrees(Request $request, $campus_id)
    {
        # code...
        $validity = Validator::make($request->all(), ['degrees'=>'array']);
        if($validity->fails()){
            return response()->json(['data'=>$validity->errors()->first()])->setStatusCode(400, 'Invalid data provided.');
        }
        if($request->degrees != null){
            CampusDegree::where('campus_id', $campus_id)->each(function($row){
                $row->delete();
            });
            $campus_degs = array_map(function($degree_id)use($campus_id){
                return ['campus_id'=>$campus_id, 'degree_id'=>$degree_id];
            }, $request->degrees);
            CampusDegree::insert($campus_degs);
            return response()->json(['data'=>'1']);
        }else{
            CampusDegree::where('campus_id', $campus_id)->each(function($row){
                $row->delete();
            });
            return response()->json(['data'=>'1']);
        }
    }

    public function campus_program_levels(Request $request, $campus_id, $program_id)
    {
        # code...
        return response()->json(['data'=>Campus::find($campus_id)->programs()->where('program_id', $program_id)->join('levels', ['levels.id'=>'program_levels.level_id'])->select(['levels.*'])->distinct()->get()]) ;
    }

    public function campus_degree_certificate_programs(Request $request, $campus_id, $degree_id, $certificate_id)
    {
        # code...
        // return response()->json(['data'=> Campus::find($campus_id)->programs()->join('school_units', ['school_units.id'=>'program_levels.program_id'])->where('school_units.degree_id', $degree_id)->join('school_units as departments', ['departments.id'=>'school_units.parent_id'])->join('certificate_programs', ['certificate_programs.program_id'=>'school_units.id'])->where('certificate_programs.certificate_id', $certificate_id)->distinct()->get(['school_units.id', 'school_units.name', 'school_units.unit_id', 'departments.name as parent'])]);
        $certificate = Certificate::find($certificate_id);
        $certificate_programs = $certificate->programs()->pluck('school_units.id')->toArray();
        $cert_degree_programs = SchoolUnits::where('degree_id', $degree_id)->whereIn('id', $certificate_programs)->pluck('id')->toArray();
        $campus = Campus::find($campus_id);
        $campus_deg_cert_programs = $campus->programs()->join('school_units', 'school_units.id', '=', 'program_levels.program_id')->whereIn('school_units.id', $cert_degree_programs)->join('school_units as departments', ['departments.id'=>'school_units.parent_id'])->select(['school_units.*', 'departments.name as parent'])->get()->unique('id')->all();
        return response()->json(['data'=> $campus_deg_cert_programs]);
    }

    public function get_certificate_programs(Request $request, $certificate_id)
    {
        # code...
        return response()->json(['data'=>Certificate::find($certificate_id)->programs()->join('school_units as departments', ['departments.id'=>'school_units.parent_id'])->orderBy('school_units.name')->distinct()->get(['school_units.*', 'departments.name as parent'])]);
    }
    public function save_certificate_programs(Request $request, $certificate_id)
    {
        # code...
        $validity = Validator::make($request->all(), ['program_ids'=>'required|array', 'certificate_id'=>'required']);

        if($validity->fails()){
            return response($validity->errors()->first(), 400);
        }

        // delete and re-save certificate programs
        CertificateProgram::where('certificate_id', $certificate_id)->each(function($row){$row->delete();});

        $program_ids = $request->program_ids;   //array of program ids coming from request
        foreach($program_ids as $p_id){
            $instance = new CertificateProgram(['program_id'=>$p_id, 'certificate_id'=>$certificate_id]);
            $instance->save();
        }

        // CertificateProgram::insert($instances);
        return response('success');
    }

    public function programs($program_id = null){

        return response()->json([
            'data'=>$program_id == null ? 
                SchoolUnits::where('school_units.unit_id', 4)->join('school_units as departments', ['departments.id'=>'school_units.parent_id'])->orderBy('school_units.name')->distinct()->get(['school_units.*', 'departments.name as parent']) :
                SchoolUnits::where('school_units.id', $program_id)->join('school_units as departments', ['departments.id'=>'school_units.parent_id'])->orderBy('school_units.name')->distinct()->select(['school_units.*', 'departments.name as parent'])->first()
        ]);
    }

    public function campus_programs($campus_id)
    {
        # code...
        return response()->json(['data'=>Campus::find($campus_id)->programs()->join('school_units', ['school_units.id'=>'program_levels.program_id'])->join('school_units as departments', ['departments.id'=>'school_units.parent_id'])->groupBy('school_units.id')->orderBy('school_units.name')->distinct()->get(['school_units.*', 'departments.name as parent'])]);
    }

    public function campus_programs_by_school($campus_id)
    {
        # code...
        return response()->json([
            'data'=>Campus::find($campus_id)->programs()
                ->join('school_units', ['school_units.id'=>'program_levels.program_id'])
                ->join('school_units as departments', ['departments.id'=>'school_units.parent_id'])
                ->join('school_units as schools', ['schools.id'=>'departments.parent_id'])
                ->groupBy('school_units.id')->orderBy('school_units.name')->distinct()->get(['school_units.*', 'departments.name as department', 'schools.name as school'])
        ]);
    }

    public function levels()
    {
        # code...
        return response()->json(['data'=>Level::all()]);
    }

    public function store_student(Request $request)
    {
        # code...
        // return ['data'=>156745453];
        try {
            //code...
            $student = json_decode($request->student);
    
            if($student == null){
                return response(json_encode(['data'=>'No student data specified']), 400);
            }
            if(Students::where('matric', $student->matric)->count() > 0){
                return response(json_encode(['data'=>'Assigned matricule number already used']), 400);
            }
            if(Students::where('email', $student->email)->where('admission_batch_id', $student->year_id)->where('active', 1)->count() > 0){
                return response(json_encode(['data'=>'Applicant email already used']), 400);
            }
            if($student != null){
                // return ['data'=>$student->matric];
                // save student to Database
                $record = [
                    'name'=>$student->name??null, 'email'=>$student->email??null, 'phone'=>$student->phone??null,
                    'address'=>$student->residence??null, 'gender'=>$student->gender??null,
                    'matric'=>$student->matric??null, 'dob'=>$student->dob??null, 'pob'=>$student->pob??null,
                    'campus_id'=>$student->campus_id??null, 'admission_batch_id'=>$student->year_id??null,
                    'password'=>Hash::make('12345678'), 'parent_name'=>$student->fee_payer_name??null,
                    'program_id'=>$student->program_first_choice??null, 'region'=>$student->region??null,
                    'parent_phone_number'=>$student->fee_payer_tel??null, 'division'=>$student->division??null, 
                    'imported'=>0, 'active'=>1,
                ];
                // return ['data'=>$record];
                $student_instance = new Students($record);
                $student_instance->save();
    
                // create student_class
                $level = Level::where('level', $student->level)->first();
                $program_level = ProgramLevel::where('program_id', $student->program_first_choice)->where('level_id', $level->id)->first();
                if($program_level == null){
                    $program_level = new ProgramLevel(['program_id'=>$student->program_first_choice, 'level_id'=> $level->id]);
                    $program_level->save();
                }
                $student_class = ['student_id'=>$student_instance->id, 'class_id'=>$program_level->id, 'year_id'=>$student->year_id, 'current'=>1];
                StudentClass::updateOrInsert(['student_id'=>$student_instance->id, 'class_id'=>$program_level->id, 'year_id'=>$student->year_id], ['current'=>1]);
    
                return response()->json(['data'=>['student'=>$student_instance->toArray(), 'student_class'=>$student_class, 'class'=>$program_level->toArray(), 'status'=>1]]);
            }
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['data'=>"L:: {$th->getLine()}, M:: {$th->getMessage()}"], 400);
        }
    }

    public function update_student(Request $request)
    {
        # code...
        $update = json_decode($request->query('student'))??collect([]);
        $matric = json_decode($request->query('matric'));
        // return $update;

        $student = Students::where('matric', $matric)->first();
        if($update == null){
            return response(json_encode(['data'=>'No update data specified']), 400);
        }
        if(Students::where('matric', $update->matric)->count() > 0){
            return response(json_encode(['data'=>'Assigned matricule number already used']), 400);
        }
        // return $student;
        
        if($student != null){
            // save student to Database
            $record = ['matric'=>$update->matric, 'program_id'=>$update->program];
            $student->update($record);

            // create student_class
            $level = Level::where('level', $update->level)->first();
            $program_level = ProgramLevel::where('program_id', $update->program)->where('level_id', $level->id)->first();
            if($program_level == null){
                $program_level = new ProgramLevel(['program_id'=>$update->program, 'level_id'=> $level->id]);
                $program_level->save();
            }
            $student_class = ['student_id'=>$student->id, 'class_id'=>$program_level->id, 'year_id'=>$student->year_id, 'current'=>1];
            StudentClass::updateOrInsert(['student_id'=>$student->id, 'year_id'=>$student->admission_batch_id], ['current'=>1, 'class_id'=>$program_level->id]);

            return response()->json(['data'=>['student'=>$student->toArray(), 'student_class'=>$student_class, 'class'=>$program_level->toArray(), 'status'=>1]]);
        }
    }

    public function max_matric(Request $request, $prefix, $year)
    {
        # code...
        // return ['data'=>25372];
        return response()->json(['data'=> Students::where('matric', 'LIKE', "%{$prefix}/{$year}/%")->orderBy('matric', 'DESC')->get()->pluck('matric')->first()]);
    }

    public function matricule_exists(Request $request)
    {
        $validity = Validator::make($request->all(), ['matric'=>'required']);
        if($validity->fails()){
            return response()->json(['data'=>$validity->errors()->first()]);
        }
        return response()->json(['data'=>(Students::where('matric', $request->matric)->count() > 0)]);
    }

    public function get_degree_certificates($degree_id)
    {
        # code...
        $degree = Degree::find($degree_id);
        if($degree != null){
            return response()->json(['status'=>'success', 'data'=>$degree->certificates->unique('id')]) ;
        }
        return response()->json(['status'=>'failed', 'data'=>[], 'message'=>"specified degree is missing"]);
    }

    public function set_degree_certificates(Request $request, $degree_id)
    {
        # code...
        $certs = $request->certificates; //an indexed array of certificate IDs
        if($certs != null && is_array($certs)){
            DegreeCertificate::where('degree_id', $degree_id)->each(function($row){
                $row->delete();
            });
            $degree_certs = array_map(function($cert)use($degree_id){
                return ['degree_id'=>$degree_id, 'certificate_id'=>$cert];
            }, $certs);
            DegreeCertificate::insert($degree_certs);
            return response()->json(['status'=>'success', 'data'=>$certs]);
        }
        return response()->json(['status'=>'failed', 'data'=>[], 'message'=>'null or wrongly formated request data']);
    }

    public function portal_fee_structure($year_id = null)
    {
        # code...
        try {
            //code...
            $year_id = $year_id ==  null ? \App\Helpers\Helpers::instance()->getCurrentAccademicYear() : $year_id;
            $fees = ProgramLevel::
                join('school_units',  'school_units.id', '=', 'program_levels.program_id')
                ->where('school_units.unit_id', 4)
                // ->join('program_banks', 'program_banks.school_units_id', '=', 'school_units.id')
                // ->join('banks', 'banks.id', '=', 'program_banks.bank_id')
                ->join('campus_programs', 'campus_programs.program_level_id', '=', 'program_levels.id')
                ->join('payment_items', 'payment_items.campus_program_id', '=', 'campus_programs.id')
                ->where('payment_items.year_id', $year_id)
                ->select(['program_levels.*', 'payment_items.amount', 'payment_items.name as payment_type', 'payment_items.first_instalment', 'payment_items.international_amount'])
                ->get();

            $banks = ProgramLevel::
                join('school_units',  'school_units.id', '=', 'program_levels.program_id')
                ->where('school_units.unit_id', 4)
                ->join('program_banks', 'program_banks.school_units_id', '=', 'school_units.id')
                ->join('banks', 'banks.id', '=', 'program_banks.bank_id')
                ->select(['program_levels.program_id', 'banks.name as bank_name', 'banks.account_number as bank_account_number', 'banks.account_name as bank_account_name'])
                ->distinct()->get();

            $structure = ProgramLevel::
                join('school_units',  'school_units.id', '=', 'program_levels.program_id')
                ->where('school_units.unit_id', 4)
                ->join('school_units as departments', 'school_units.parent_id', '=', 'departments.id')
                ->join('school_units as _schools', 'departments.parent_id', '=', '_schools.id')
                ->select(['program_levels.*', '_schools.name as school', 'departments.name as department', 'school_units.name as program', 'school_units.id as program_id'])
                ->get()->map(function($rec)use($fees, $banks){
                    $fee = $fees->where('id', $rec->id)->where('payment_type', 'TUTION')->first(); 
                    $backs = $banks->where('program_id', $rec->program_id)->first(); 
                    
                    $rec->class_name = $rec->name();
                    $rec->amount = $fee->amount??null;
                    $rec->first_instalment = $fee->first_instalment??null;
                    $rec->international_amount = $fee->international_amount??null;
                    
                    $rec->bank_name = $fee->bank_name??null;
                    $rec->bank_account_number = $fee->bank_account_number??null;
                    $rec->bank_account_name = $fee->bank_account_name??null;
                    
                    return $rec;
                })->unique();
                
    
            return response()->json(['data'=>$structure]) ;
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['data'=>[], 'message'=>$th->getMessage(), 'status'=>500]) ;
        }

    }

    public function class_portal_fee_structure($prog_id, $level, $year_id = null)
    {
        # code...
        try {
            //code...
            $year_id = $year_id ==  null ? \App\Helpers\Helpers::instance()->getCurrentAccademicYear() : $year_id;
            $fees = ProgramLevel::where(['program_id'=>$prog_id])
                ->join('levels', 'levels.id', '=', 'program_levels.level_id')
                ->where('levels.level', $level)
                ->join('school_units',  'school_units.id', '=', 'program_levels.program_id')
                ->where('school_units.unit_id', 4)
                ->join('campus_programs', 'campus_programs.program_level_id', '=', 'program_levels.id')
                ->join('payment_items', 'payment_items.campus_program_id', '=', 'campus_programs.id')
                ->where('payment_items.year_id', $year_id)
                ->select(['program_levels.*', 'payment_items.amount', 'payment_items.name as payment_type', 'payment_items.first_instalment', 'payment_items.international_amount'])
                ->get();

            $banks = ProgramLevel::where(['program_id'=>$prog_id])
                ->join('levels', 'levels.id', '=', 'program_levels.level_id')
                ->where('levels.level', $level)
                ->join('school_units',  'school_units.id', '=', 'program_levels.program_id')
                ->where('school_units.unit_id', 4)
                ->join('program_banks', 'program_banks.school_units_id', '=', 'school_units.id')
                ->join('banks', 'banks.id', '=', 'program_banks.bank_id')
                ->select(['program_levels.program_id', 'banks.name as bank_name', 'banks.account_number as bank_account_number', 'banks.account_name as bank_account_name'])
                ->distinct()->get();

            $structure = ProgramLevel::where(['program_id'=>$prog_id])
                ->join('levels', 'levels.id', '=', 'program_levels.level_id')
                ->where('levels.level', $level)
                ->join('school_units',  'school_units.id', '=', 'program_levels.program_id')
                ->where('school_units.unit_id', 4)
                ->join('school_units as departments', 'school_units.parent_id', '=', 'departments.id')
                ->join('school_units as _schools', 'departments.parent_id', '=', '_schools.id')
                ->select(['program_levels.*', '_schools.name as school', 'departments.name as department', 'school_units.name as program', 'school_units.id as program_id'])
                ->get()->map(function($rec)use($fees, $banks){
                    $fee = $fees->where('id', $rec->id)->where('payment_type', 'TUTION')->first(); 
                    $backs = $banks->where('program_id', $rec->program_id)->first(); 
                    
                    $rec->class_name = $rec->name();
                    $rec->amount = $fee->amount??null;
                    $rec->first_instalment = $fee->first_instalment??null;
                    $rec->international_amount = $fee->international_amount??null;
                    
                    $rec->bank_name = $fee->bank_name??null;
                    $rec->bank_account_number = $fee->bank_account_number??null;
                    $rec->bank_account_name = $fee->bank_account_name??null;
                    
                    return $rec;
                })->unique();
                
    
            return response()->json(['data'=>$structure]) ;
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['data'=>[], 'message'=>$th->getMessage(), 'status'=>500]) ;
        }

    }

    public function school_program_structure(){
        try {
            //code...
            $structure = ProgramLevel::join('school_units', 'school_units.id', '=', 'program_levels.program_id')
                ->join('school_units as departments', 'departments.id', '=', 'school_units.parent_id')
                ->join('school_units as _schools', '_schools.id', '=', 'departments.parent_id')
                ->select(['_schools.id as school_id', '_schools.name as school', 'departments.id as department_id', 'departments.name as department', 'school_units.name as program', 'program_levels.*'])
                ->groupBy(['school','department','program'])
                ->distinct()->get();
                // ->groupBy(['school','department','program']);
    
            // return $structure;
            return response()->json(['data'=>$structure]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['data'=>[], 'message'=>$th->getMessage(), 'status'=>500]) ;
        }
    }
}
