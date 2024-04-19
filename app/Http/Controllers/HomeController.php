<?php

namespace App\Http\Controllers;

use App\Helpers\Helpers;
use App\Http\Resources\Fee;
use App\Http\Resources\StudentResource3;
use App\Http\Resources\StudentRank;
use App\Http\Resources\CollectBoardingFeeResource;
use App\Models\Batch;
use App\Models\Rank;
use App\Models\SchoolUnits;
use App\Models\Sequence;
use App\Models\Students;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\SchoolUnitResource;
use App\Http\Resources\StudentFee;
use App\Http\Resources\StudentResource;
use App\Http\Resources\StudentResourceMain;
use App\Models\Campus;
use App\Models\Color;
use App\Models\ExtraFee;
use App\Models\PaymentItem;
use App\Models\Payments;
use App\Models\ProgramLevel;
use App\Models\StudentClass;
use App\Models\StudentScholarship;
use Illuminate\Support\Facades\Auth;
use Throwable;
use \PDF;

class HomeController extends Controller
{

    private $select = [
        'students.id as id',
        'students.name',
        'student_classes.year_id',
    ];
    private $select1 = [];
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return redirect()->to(route('login'));
    }

    public function children(Request $request,  $parent)
    {
        $id = trim($parent);
        $school_unit = SchoolUnits::where('parent_id',$id)->get();
        return response()->json([
            'data' => SchoolUnitResource::collection($school_unit),
            'total' => count($school_unit)
        ]);
    }

    public static function x_children($parent)
    {
        $id = trim($parent);
        $school_unit = SchoolUnits::where('parent_id',$id)->get();
        return $school_unit;
    }

    public function  subjects($parent)
    {
        $subjects = \App\Models\ClassSubject::where(['class_id' => $parent])->whereNull('class_subjects.deleted_at')
                    ->join('subjects', ['subjects.id' => 'class_subjects.subject_id'])
                    ->get(['subjects.id', 'subjects.name', 'subjects.code', 'class_subjects.class_id']);
        return response()->json([
            'array' => $subjects,
        ]);
    }

    public function student($name)
    {
        $students = Students::join('student_classes', ['students.id' => 'student_classes.student_id'])
            ->join('campuses', ['students.campus_id' => 'campuses.id'])
            ->where('student_classes.year_id', Helpers::instance()->getYear())
            ->join('program_levels', ['students.program_id' => 'program_levels.id'])
            ->join('school_units', ['program_levels.program_id' => 'school_units.id'])
            ->join('levels', ['program_levels.level_id' => 'levels.id'])
            ->where('students.name', 'LIKE', "%{$name}%")
            ->orWhere('students.matric', '=', $name)
            ->take(10)
            ->get(['students.*', 'campuses.name as campus']);

        return \response()->json(StudentFee::collection($students));
    }

    public function student_get()
    {
        $name = request('name');
        $students = Students::join('student_classes', ['student_classes.student_id' => 'students.id'])
            ->join('campuses', ['students.campus_id' => 'campuses.id'])
            ->where('student_classes.year_id', Helpers::instance()->getYear())
            ->join('program_levels', ['student_classes.class_id' => 'program_levels.id'])
            ->join('school_units', ['program_levels.program_id' => 'school_units.id'])
            ->join('levels', ['program_levels.level_id' => 'levels.id'])
            ->where(function($query)use($name){
                $query->where('students.name', 'LIKE', "%{$name}%")
                ->orWhere('students.matric', 'LIKE', "%{$name}%");
            })
            ->where(function($query){
                \auth()->user()->campus_id != null ? $query->where('students.campus_id', '=', \auth()->user()->campus_id) : null;
                        })
            ->take(10)->get(['students.*', 'campuses.name as campus']);

            // return $students;
        return \response()->json(StudentFee::collection($students));
    }

    public function searchStudents($name)
    {
        $name = str_replace('/', '\/', $name);
        try {
            //code...
            // $sql = "SELECT students.*, student_classes.student_id, student_classes.class_id, campuses.name as campus from students, student_classes, campuses where students.id = student_classes.student_id and students.campus_id = campuses.id and students.name like '%{$name}%' or students.matric like '%{$name}%'";

            // return DB::select($sql);
            $students  = DB::table('students')
                ->join('student_classes', ['students.id' => 'student_classes.student_id'])
                ->join('campuses', ['students.campus_id'=>'campuses.id'])
                ->where('students.name', 'LIKE', "%$name%")
                ->orWhere('students.matric', 'LIKE', "%$name%")->distinct()->take(10)
                ->get(['students.*', 'student_classes.student_id', 'student_classes.class_id', 'campuses.name as campus'])->toArray();
            return \response()->json(StudentResource3::collection($students));
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    public function searchStudents_get()
    {
        $name = request('key');
        // return $name;
        $name = str_replace('/', '\/', $name);
        try {
            //code...
            // $sql = "SELECT students.*, student_classes.student_id, student_classes.class_id, campuses.name as campus from students, student_classes, campuses where students.id = student_classes.student_id and students.campus_id = campuses.id and students.name like '%{$name}%' or students.matric like '%{$name}%'";

            // return DB::select($sql);
            $campus_id = auth()->user()->campus_id??null;
            $students  = DB::table('students')
                ->join('student_classes', ['students.id' => 'student_classes.student_id'])
                ->join('campuses', ['students.campus_id'=>'campuses.id'])
                ->where(function($query)use($name){
                    $query->where('students.name', 'LIKE', "%$name%")
                    ->orWhere('students.matric', 'LIKE', "%$name%");
                })
                ->where(function($query)use($campus_id){
                    $campus_id != null ? $query->where('students.campus_id', $campus_id) : null;
                })
                ->distinct()
                ->take(10)
                ->get(['students.*', 'student_classes.student_id', 'campuses.name as campus'])
                ->toArray();
            
            return \response()->json(StudentResource3::collection($students));
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    public function search_students()
    {
        $name = request('key');
        // return $name;
        $name = str_replace('/', '\/', $name);
        try {
            //code...
            // $sql = "SELECT students.*, student_classes.student_id, student_classes.class_id, campuses.name as campus from students, student_classes, campuses where students.id = student_classes.student_id and students.campus_id = campuses.id and students.name like '%{$name}%' or students.matric like '%{$name}%'";

            // return DB::select($sql);
            $students  = DB::table('students')
                ->join('student_classes', ['students.id' => 'student_classes.student_id'])
                ->join('campuses', ['students.campus_id'=>'campuses.id'])
                ->where(function($query)use($name){
                    $query->where('students.name', 'LIKE', "%$name%")
                    ->orWhere('students.matric', 'LIKE', "%$name%");
                })
                ->where(function($query){
                    \auth()->user()->campus_id != null ? $query->where('students.campus_id', '=', \auth()->user()->campus_id) : null;
                })
                ->distinct()->take(10)
                ->get(['students.*', 'student_classes.class_id', 'campuses.name as campus'])
                ->toArray();
            
            return \response()->json(StudentResourceMain::collection($students));
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    public function search_students_per_cammpus_class_per_year(Request $request){
        // expects year_id, campus_id, class_id, key
        try {
            //code...
            $class = ProgramLevel::find($request->class_id);
            if($class != null){
                $students = $class->_students($request->year_id)->where('students.campus_id', $request->campus_id)
                    ->where(function($query)use($request){
                        $query->where('name', 'LIKE', '%'.$request->key.'%')
                        ->orWhere('matric', 'LIKE', '%'.$request->key.'%')
                        ->orWhere('email', 'LIKE', '%'.$request->key.'%');
                    })
                    ->distinct()->get();
                    
                return response($students);
            }
            return response([], 400);
        } catch (\Throwable $th) {
            //throw $th;
            return response($th->getMessage(), 500);
        }
    }

    /* response payload: {
            name:string, gender:string,
            students.id:string, matric:string, 
            admission_batch_id:int, campus_id:int, 
            link:url={route('admin.fee.student.payments.index', [$student->id])}, 
            fee:number=(expected), total:number=(paid), owed:number, class:string=(class-name)
        }*/
    public static function _fee(Request $request)
    {
        $type = request('type', 'completed');
        $year = request('year', Helpers::instance()->getCurrentAccademicYear());
        $class = ProgramLevel::find(\request('class'));

        $campus = auth()->user()->campus;
        $title = $type . " fee  -  " . $class->name(). '  -  '.($campus ? $campus->name??'' : '');
        $title .= ' FOR ('.Batch::find($year)->name.') ONLY';
        $students = $class->_students($year)->where('students.active', 1)->where(function($qry)use($campus){
            $campus == null ? null : $qry->where('campus_id', $campus->id);
        })->get();
        // dd($students);

        $data = $students->map(function($student)use($campus, $year, $class){
            $extra_fee = ExtraFee::where('student_id', $student->id)->where('year_id', $year)->sum('amount');
            $fee = $class->campus_programs($student->campus_id)->first()->payment_items->where('name', 'TUTION')->where('year_id', $year)->first();
            $cash_paid = Payments::where('student_id', $student->id)->where('payment_year_id', $year)->sum(DB::raw('amount - debt'));
            $scholarship = StudentScholarship::where('student_id', $student->id)->where('batch_id', $year)->sum('amount');
            $student->link = route('admin.fee.student.payments.index', [$student->id]);
            $student->class = $class->name();
            $student->fee = ($fee != null ? $fee->amount : 0) + $extra_fee;
            $student->total = $cash_paid + $scholarship;
            $student->owed = ($fee != null ? $fee->amount : 0) + $extra_fee - $cash_paid - $scholarship;
            // dd($student);
            return $student;
        });

        // dd($data->where('id', 51)->first());
        if($type == 'completed'){
            $resp = $data->filter(function($row){
                return $row->owed <= 0 && $row->fee > 0;
            });
        }elseif(($amount = $request->amount) != null){
            $resp = $data->filter(function($row, $amount){
                return $row->total > $amount;
            });
        }else{
            $resp = $data->filter(function($row){
                return $row->owed > 0;
            });

        }
        


        $students = $resp->sortBy('name')->toArray();

        return ['title' => $title, 'students' => $students];
    }

    public function fee(Request  $request)
    {
        $type = request('type', 'completed');
        $year = request('year', Helpers::instance()->getCurrentAccademicYear());
        $class = ProgramLevel::find(\request('class'));

        $campus = auth()->user()->campus;
        $title = $type . " fee  -  " . $class->name(). '  -  '.($campus ? $campus->name??'' : '');
        $title .= ' ('.Batch::find($year)->name.')';
        $students = [];
        $fields = ['payment_items.id', 'payment_items.amount', 'payment_items.campus_program_id', 'payment_items.year_id', 'campus_programs.campus_id'];

        if($campus != null){
            $class_fees = $class->_campus_programs()->join('payment_items', 'campus_programs.id', '=', 'payment_items.campus_program_id')->where('payment_items.name', 'TUTION')->where('campus_programs.campus_id', $campus->id)->where('year_id', '<=', $year)->distinct();
                // ->join('students', 'students.campus_id', '=', 'campus_programs.campus_id');
        }else{
            $class_fees = $class->_campus_programs()->join('payment_items', 'campus_programs.id', '=', 'payment_items.campus_program_id')->where('payment_items.name', 'TUTION')->where('year_id', '<=', $year)->distinct();
        }

        $extra_fees = StudentClass::where(['class_id'=>$class->id, 'student_classes.year_id'=>$year])->join('extra_fees', 'extra_fees.student_id','=', 'student_classes.student_id')->where('extra_fees.year_id', $year)->get('extra_fees.*');
        $set_fees = $class_fees->distinct()->select($fields)->get();
        // $fee_payments = Payments::whereIn('payment_id', $class_fees->pluck('payment_items.id')->toArray())->where('batch_id', '<=', $year)->get(["id", "payment_id","student_id","batch_id","amount", 'debt']);
        if($campus == null){
            $fee_payments = Payments::whereIn('payments.payment_id', $class_fees->pluck('payment_items.id')->toArray())
                ->where('payments.payment_year_id', '<=', $year)
                ->join('students', 'students.id', '=', 'payments.student_id')
                ->select("payments.id", "payments.payment_id","payments.student_id","payments.batch_id", DB::raw('SUM(payments.amount - payments.debt) as amount_sum'),  'students.name','students.gender','students.matric', 'students.admission_batch_id', 'campus_id')->groupBy('payments.student_id')->get();
        }else{
            $fee_payments = Payments::whereIn('payment_id', $class_fees->pluck('payment_items.id')->toArray())
                ->where('payments.payment_year_id', '<=', $year)
                ->join('students', 'students.id', '=', 'payments.student_id')->where('students.campus_id', $campus->id)
                ->select("payments.id", "payments.payment_id","payments.student_id","payments.batch_id", DB::raw('SUM(payments.amount - payments.debt) as amount_sum'), 'students.name','students.gender','students.matric', 'students.admission_batch_id', 'campus_id')->groupBy('payments.student_id')->get();
        }

        if($campus != null){
            $students = $class->_students($year)->where('students.campus_id', '=', $campus->id)->distinct()->select(['name','gender','students.id','matric', 'admission_batch_id', 'campus_id'])->get();
        }else{
            $students = $class->_students($year)->distinct()->select(['name','gender','students.id','matric', 'admission_batch_id', 'campus_id'])->get();
        }

        $class_name = $class->name();
        $data = [];
        foreach ($students as $key => $student) {
            # code...
            $fee = $set_fees->where('campus_id', $student->campus_id)->where('year_id', '>=', $student->admission_batch_id)->sum('amount') + $extra_fees->where('year_id', '>=', $student->admission_batch_id)->where('student_id', $student->id)->sum('amount');
            $paid = $fee_payments->where('student_id', $student->id)->first()->amount_sum ?? 0;
            $owing = $fee - $paid;
    
            if($type == 'complete' && $owing <= 0){
                $student->link =  route('admin.fee.student.payments.index', [$student->id]);
                $student->fee = $fee;
                $student->total = $paid;
                $student->owed = $owing;
                $student->class = $class_name;
        
                $data[] = $student;
            }elseif(!$request->has('amount') && $owing > 0){
                $student->link =  route('admin.fee.student.payments.index', [$student->id]);
                $student->fee = $fee;
                $student->total = $paid;
                $student->owed = $owing;
                $student->class = $class_name;
        
                $data[] = $student;
            }elseif($request->has('amount') && ($request->amount >= ($set_fees->where('year_id', $year)->where('campus_id')->first()->sum_amount + ($extra_fees->where('student_id', $student->id)->where('year_id', $year)->first()->amount??0) - $owing))) {
                $student->link =  route('admin.fee.student.payments.index', [$student->id]);
                $student->fee = $fee;
                $student->total = $paid;
                $student->owed = $owing;
                $student->class = $class_name;
        
                $data[] = $student;
            }
        }
        $students = collect($data)->sortBy('name')->toArray();

        return response()->json(['title' => $title, 'students' => $students]);
    }

    public function rank(Request  $request)
    {
        $seq =  Sequence::find($request->sequence);
        $unit = SchoolUnits::find($request->class);
        $title = $seq->name . " ranking " . ($unit != null ? "for " . $unit->name : '');
        $students = $unit->students($request->year)->get();
        return response()->json(['students' => StudentRank::collection($students), 'title' => $title]);
    }

    public function load($unit, $type, $bal)
    {
        $students = [];
        foreach ($unit->_students(Helpers::instance()->getYear())->get() as $student) {
            if ($type == 'completed' && $student->bal($student->id) == 0) {
                array_push($students, $student);
            } elseif ($type == 'uncompleted' && $student->bal($student->id) > 0) {
                array_push($students, $student);
            }
        }
        // foreach ($unit->unit as $unit) {
        //     $students = array_merge($students, $this->load($unit, $type, $bal));
        // }

        return $students;
    }

    /**
     * get all school student boarders
     *
     * @param string $name
     */
    public function getStudentBoarders($name)
    {

        $type = 'boarding';
        $students = DB::table('student_classes')
            ->join('students', ['students.id' => 'student_classes.student_id'])
            ->join('school_units', ['school_units.id' => 'student_classes.class_id'])
            ->where('students.type', '=', $type)
            ->where('students.name', 'LIKE', "%{$name}%")
            ->orWhere('students.matric', 'LIKE', "%{$name}%")
            ->select('students.id', 'students.name', 'students.matric', 'school_units.name as class_name', 'school_units.id as class_id')->get();

        return response()->json(['data' => CollectBoardingFeeResource::collection($students)]);
    }

    public function rankPost(Request  $request)
    {
        $sequence = $request->sequence;
        $students = $request->students;
        foreach ($students as $k => $student) {
            $rank = Rank::where([
                'student_id' => $student,
                'sequence_id' => $sequence
            ])->first();
            if (!isset($rank)) {
                $rank = new Rank();
            }

            $rank->student_id = $student;
            $rank->sequence_id = $sequence;
            $rank->position = ($k + 1);
            $rank->year_id = Helpers::instance()->getYear();
            $rank->save();
        }
        return response()->json(['title' => "'success"]);
    }

    // return payload: [{'id':number, 'name'": string, 'matric': string, 'link':url, 'paid': number, 'owing': number, 'scholarship':number, 'class':string}];
    public static function fee_situation(Request $request)
    {
        # code...
        $year = request('year', Helpers::instance()->getCurrentAccademicYear());
        $class = ProgramLevel::find(\request('class'));

        // $st_classes = $class->student_classes()->join('students', 'students.id', '=', 'student_classes.student_id')->groupBy(['student_classes.year_id', 'students.campus_id'])->select(['students.campus_id', 'student_classes.id', 'student_classes.class_id', 'student_classes.id', 'student_classes.year_id'])->distinct()->get();
        // dd($st_classes);

        $data = [];
        $campus_id = auth()->user()->campus_id??null;
        foreach ($class->_students($year)->where(function($query)use($campus_id){
            $campus_id == null ? null : $query->where('campus_id', $campus_id);
        })->get() as $key => $student) {
            $items = [];
            foreach ($student->classes as $key => $_class) {
                if(($it = $_class->class->single_payment_item($student->campus_id, $_class->year_id)->where('name', 'TUTION')->get()->first()) != null){
                    $items[] = $it;
                };
            }
            # code...

            $fee_items = collect($items);

            $cum_fee_items = $fee_items->filter(function($row)use($year){
                return $row != null && $row->year_id <= $year;
            });
            $past_fee = $fee_items->filter(function($row)use($year){
                return $row != null && $row->year_id < $year;
            });

            $_payments = Payments::where('student_id', $student->id)->whereIn('payment_id', $fee_items->pluck('id')->toArray())->distinct()->get();
            $_current_payments = Payments::where('student_id', $student->id)->whereIn('payment_id', $cum_fee_items->pluck('id')->toArray())->where('payment_year_id', '<=', $year)->distinct()->get();
            $past_payments = Payments::where('student_id', $student->id)->whereIn('payment_id', $past_fee->pluck('id')->toArray())->where('payment_year_id', '<=', $year)->distinct()->get();
            $payments = $_payments->sum('amount') - $_payments->sum('debt');
            $current_payments = $_current_payments->sum('amount') - $_payments->sum('debt');

            $current_scholarship = StudentScholarship::where('student_id', $student->id)->where('batch_id', '<=', $year)->distinct()->sum('amount');
            $cum_scholarship = StudentScholarship::where('student_id', $student->id)->where('batch_id', '<=', $year)->distinct()->sum('amount');
            $past_scholarship = StudentScholarship::where('student_id', $student->id)->where('batch_id', '<', $year)->distinct()->sum('amount');

            $current_extra_fees = ExtraFee::where('student_id', $student->id)->where('year_id', '=', $year)->distinct()->sum('amount');
            $current_extra_fees = ExtraFee::where('student_id', $student->id)->where('year_id', '<=', $year)->distinct()->sum('amount');
            $past_extra_fees = ExtraFee::where('student_id', $student->id)->where('year_id', '<', $year)->distinct()->sum('amount');

            // dd($fee_items);
            $data[] = [
                'id'=>$student->id, 'name'=>$student->name, 'matric'=>$student->matric,
                'link'=>route('admin.fee.student.payments.index', $student->id),
                'current_fee'=>$cum_fee_items->where('year_id', $year)->first()->amount??0,
                'debt'=>($past_fee->sum('amount') + $past_extra_fees) - (($past_payments->sum('amount') - $past_payments->sum('debt') + $past_scholarship)),
                'paid'=>$current_payments, 'scholarship'=>$current_scholarship, 'total'=>$cum_fee_items->sum('amount')+$current_extra_fees,
                'current_paid'=>$_payments->where('payment_year_id', $year)->sum('amount') + $_payments->where('payment_year_id', $year)->sum('debt'),
                'extra_fee'=>$current_extra_fees, 
                'owing'=>$cum_fee_items->sum('amount') + $current_extra_fees - ($current_payments + $cum_scholarship),
                'class'=>$class->name()
            ];
        }

        return ['students'=>collect($data)->sortBy('name')];
        // dd($data);

    }

    
    public static function rgfee_situation(Request $request)
    {
        # code...
        $year = request('year', Helpers::instance()->getCurrentAccademicYear());
        $class = ProgramLevel::find(\request('class'));

        // $st_classes = $class->student_classes()->join('students', 'students.id', '=', 'student_classes.student_id')->groupBy(['student_classes.year_id', 'students.campus_id'])->select(['students.campus_id', 'student_classes.id', 'student_classes.class_id', 'student_classes.id', 'student_classes.year_id'])->distinct()->get();
        // dd($st_classes);

        $data = [];

        # code...
        foreach ($class->_students($year)->get() as $key => $student) {
            $fee_items = $class->single_payment_item($student->campus_id, $year)->where('name', 'REGISTRATION')->get();
            
            if ($fee_items->count() > 0) {
    
                $_payments = Payments::where('student_id', $student->id)->whereIn('payment_id', $fee_items->pluck('id')->toArray())->distinct()->get();
                $payment = $_payments->sum('amount');
    
                
                // dd($fee_items);
                $data[] = [
                    'id'=>$student->id, 'name'=>$student->name, 'matric'=>$student->matric,
                    'link'=>route('admin.fee.student.payments.index', $student->id),
                    'paid'=>$payment,  'total'=>$fee_items->sum('amount'),
                    'class'=>$class->name()
                ];
            }
    
            // dd($data);
        }
        return ['students'=>collect($data)->sortBy('name')];

    }

    

    public static function getColor($label)
    {
        # code...
        $color = Color::where(['name'=>$label])->first();
        return $color == null ? null : $color->value;
    }

    public function class_target($class_id)
    {
        $class = ProgramLevel::find($class_id);
        if($class->target != null){
            $classes = ProgramLevel::where('program_levels.id', $class->target)
                ->get()
                ->map(function($row){
                    $row->name = $row->name();
                    return $row;
                });
            }else{
            $classes = ProgramLevel::all()
                ->map(function($row){
                    $row->name = $row->name();
                    return $row;
                });
        }
        return response()->json(['classes'=> $classes]);
    }
    
}
