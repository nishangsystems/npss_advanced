<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\CampusProgram;
use App\Models\PaymentItem;
use App\Models\Payments;
use App\Models\ProgramLevel;
use App\Models\Promotion;
use App\Models\SchoolUnits;
use App\Models\StudentClass;
use App\Models\StudentPromotions;
use App\Models\Students;
use App\Option;
use Error;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session as FacadesSession;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Stringable;
use phpDocumentor\Reflection\Types\Self_;
use Prophecy\Util\StringUtil;

use function PHPUnit\Framework\stringStartsWith;

class StudentController extends Controller
{

    private $years;
    private $year;
    private $select = [
        'students.id',
        'students.name',
        'students.matric',
        'students.email',
        'students.phone',

    ];

    public function __construct()
    {
        $this->year = Batch::find(Helpers::instance()->getCurrentAccademicYear())->name;
        $this->years = Batch::all();
        // $this->batch_id = Batch::find(Helpers::instance()->getCurrentAccademicYear())->id;
    }
    public function index(Request $request)
    {
        $curent_year = Helpers::instance()->getCurrentAccademicYear();
        $data['title'] = 'Manage Students';
        $data['school_units'] = DB::table('school_units')->where('parent_id', 0)->get()->toArray();
        $data['years'] = $this->years;
        // $data['students'] = DB::table('students')->whereYear('students.created_at', $curent_year)->get()->toArray();
        $data['students'] = Students::join('student_classes', 'student_classes.student_id', '=', 'students.id')->get(['students.*', 'student_classes.class_id']);

        return view('admin.student.index')->with($data);
    }
    public function getStudentsPerClass(Request $request)
    {
        // return $request->all();
        $type = $this->CheckoutSchoolType($request);
        $data['school_units'] = DB::table('school_units')->where('parent_id', 0)->get()->toArray();
        $data['years'] = $this->years;
        $class_name =  DB::table('school_units')->where('id', $request->class_id)->pluck('name')->first();
        
        $data['title'] = 'Manage Students Under '.$type.' in '.$class_name;
        $success = $request->type != null ? $request->type.' ' : '';
        $success .= $request->circle != null ? SchoolUnits::find($request->circle)->name .' ' : '';
        $success .= $request->class_id != null ? Self::baseClasses()[$request->class_id].' ' : '';
        $success .= $request->batch_id != null ? 'For '.Batch::find($request->batch_id)->name.' ': '';
        $success .= $request->class != null ? ' in ' . $class_name : '';
        $data['students'] = DB::table('student_classes')
            ->join('students', 'students.id', '=', 'student_classes.student_id')
            ->join('school_units', 'school_units.id', '=', 'student_classes.class_id')
            ->where('student_classes.year_id', $request->batch_id)
            ->where('school_units.id', $request->class_id)
            ->where('students.type', $request->type)
            ->select($this->select)->paginate(15);
        return view('admin.student.index')->with($data)->with('success', $success);
    }
    private function CheckoutSchoolType($request)
    {
        $type = null;
        if ($request->type == 'day') {
            $type = 'Day Section';
        } else if ($request->type == 'boarding') {
            $type = 'Boarding Section';
        }
        return $type;
    }
    
    public function getBaseClasses()
    {
        # code...
        // added by Germanus. Loads listing of all classes accross all sections in a given school
        
        $base_units = DB::table('school_units')->get();
    
        // return $base_units;
        $listing = [];
        $options = [];
        $separator = ' : ';
        foreach ($base_units as $key => $value) {
            # code...
            // set current parent as key and name as value, appending from the parent_array
            if (array_key_exists($value->parent_id, $listing) ) {
                $listing[$value->id] = $listing[$value->parent_id] . $separator . $value->name; 
            }else {$listing[$value->id] = $value->name;}
    
            // atatch parent units if there be any
            if ($base_units->where('id', '=', $value->parent_id)->count() > 0) {
                // return $base_units->where('id', '=', $value->parent_id)->pluck('name')[0];
                $listing[$value->id] = array_key_exists($value->parent_id, $listing) ? 
                $listing[$value->parent_id] . $separator . $value->name :
                $base_units->where('id', '=', $value->parent_id)->pluck('name')[0] . $separator . $value->name ;
            }
            // if children are obove, move over and prepend to children listing
            foreach ($base_units->where('parent_id', '=', $value->id) as $keyi => $valuei) {
                $value->id > $valuei->id ?
                $listing[$valuei->id] = $listing[$value->id] . $separator . $listing[$value->id]:
                null;
            }
            // if unit has no child, add to options
            if ($base_units->where('parent_id', '=', $value->id)->count() == 0) {
                $options[$value->id] = '';

                // remove second highest child before adding
                // $options[$value->id] = mb_split(':', $listing[$value->id]); old format with complete hierarchy
                $split = mb_split(':', $listing[$value->id]);
                foreach ($split as $i => $word) {
                    # code...
                    if($i != 1)
                    $options[$value->id] .= ' : '.$word;
                }
                $options[$value->id] = substr($options[$value->id], 2);

            }

        }

        return $options;
    }

    public static function baseClasses()
    {
             # code...
        // added by Germanus. Loads listing of all classes accross all sections in a given school
        
        $base_units = DB::table('school_units')->get();
    
        // return $base_units;
        $listing = [];
        $options = [];
        $separator = ' : ';
        foreach ($base_units as $key => $value) {
            # code...
            // set current parent as key and name as value, appending from the parent_array
            if (array_key_exists($value->parent_id, $listing) ) {
                $listing[$value->id] = $listing[$value->parent_id] . $separator . $value->name; 
            }else {$listing[$value->id] = $value->name;}
    
            // atatch parent units if there be any
            if ($base_units->where('id', '=', $value->parent_id)->count() > 0) {
                // return $base_units->where('id', '=', $value->parent_id)->pluck('name')[0];
                $listing[$value->id] = array_key_exists($value->parent_id, $listing) ? 
                $listing[$value->parent_id] . $separator . $value->name :
                $base_units->where('id', '=', $value->parent_id)->pluck('name')[0] . $separator . $value->name ;
            }
            // if children are obove, move over and prepend to children listing
            foreach ($base_units->where('parent_id', '=', $value->id) as $keyi => $valuei) {
                $value->id > $valuei->id ?
                $listing[$valuei->id] = $listing[$value->id] . $separator . $listing[$value->id]:
                null;
            }
            // if unit has no child, add to options
            if ($base_units->where('parent_id', '=', $value->id)->count() == 0) {
                $options[$value->id] = '';

                // remove second highest child before adding
                // $options[$value->id] = mb_split(':', $listing[$value->id]); old format with complete hierarchy
                $split = mb_split(':', $listing[$value->id]);
                foreach ($split as $i => $word) {
                    # code...
                    if($i != 1)
                    $options[$value->id] .= ' : '.$word;
                }
                $options[$value->id] = substr($options[$value->id], 2);

            }

        }

        return $options;
    }

    public function create(Request $request)
    {
        $data['options'] = $this->getBaseClasses();
        // end of germanus' work

        // Original stuff
        $data['title'] = "Admit New Student";
        return view('admin.student.create')->with($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request)
    {

        // return $request->all();
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            // 'matric' => 'required',
            'admission_batch_id' => 'required',
            'campus_id' => 'required',
            'program_id'=>'required',
        ]);
        try {
            if(Students::where('matric', $request->matric)->count() == 0){
                // return $request->all();
                DB::beginTransaction();
                // read user input
                $input = $request->all();
                $input['name'] = mb_convert_case($request->name, MB_CASE_UPPER);
                // $input['password'] = Hash::make('password');
                // create a student
                $input['matric'] = $this->getNextAvailableMatricule($request->section);
                $student = new \App\Models\Students($input);
                $student->save();
                // dd($student);
                // create student class (check to be sure student class doesn't already exist for current academic year before creating one)
                $classes = StudentClass::where(['student_id' => $student->id])
                    ->where(['class_id' => $request->program_id])
                    ->where(['year_id' => $request->admission_batch_id]);
                
                $classes->count() == 0 ?
                StudentClass::create([
                    'student_id' => $student->id,
                    'class_id' => $request->program_id,
                    'year_id' => $request->admission_batch_id
                ]):null;
    
                DB::commit();
                return redirect()->to(route('admin.students.index', $request->program_id))->with('success', "Student saved successfully !");
            }
            else {
                throw new \Exception('User with matricule '.$request->matric.' already exist');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', "F::{$e->getFile()}, L::{$e->getLine()}, M::{$e->getMessage()}");
            return back()->withInput();
        }
    }

    public function getNextAvailableMatricule($section)
    {
        $unit = \App\Models\SchoolUnits::find($section);
        $academic_year_name = \App\Models\Batch::find(Helpers::instance()->getCurrentAccademicYear())->name;
        $matric_template = $unit->prefix . substr($academic_year_name, 2, 2) . $unit->suffix;

        $last_matric = DB::table('students')
                        ->whereRaw('students.matric like "'.$matric_template.'%"')
                        ->orderBy('matric', 'desc')
                        ->first();

        if($last_matric){
            $next = ((int)substr($last_matric->matric, -3, 3)) + 1;
            while(strlen($next) < 3){
                $next = '0'.$next;
            }
            return substr($last_matric->matric, 0, -3).$next;
        }
        else {
            return $matric_template.'001';
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $data['title'] = "Student Profile";
        $data['user'] = \App\Models\Students::find($id);
        return view('admin.student.show')->with($data);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function edit(Request $request, $id)
    {
        $data['title'] = "Edit Student Profile";
        $data['student'] = \App\Models\Students::find($id);
        $data['classes'] = $this->getBaseClasses();
        return view('admin.student.edit')->with($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     */
    public function update(Request $request, $id)
    {

        $this->validate($request, [
            'name' => 'required',
            'phone' => 'nullable',
            'address' => 'nullable',
            'gender' => 'nullable',
            'program_id' => 'required',
            'matric' => 'required',
        ]);
        try {       

            DB::beginTransaction();
            $input = $request->all();
            $student = Students::find($id);
            $student->update($input);
            // $class = StudentClass::where('student_id', $student->id)->where('year_id', $request)->first();
            // $class->class_id = $request->program_id;
            // $class->save();
            DB::commit();
            return redirect()->back()->with('success', "Student saved successfully !");
        } catch (\Exception $e) {
            DB::rollBack();
            echo $e;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     */
    public function destroy($id)
    {
        $student = Students::find($id);
        if ($student->classes->count() > 1 
        || $student->result->count() > 0
         || $student->payment) {
            return redirect()->back()->with('error', "Student cant be deleted !");
        }
        $student->classes->first()->delete();
        $student->delete();
        return redirect()->back()->with('success', "Student deleted successfully !");
    }

    public  function import()
    {
        $data['title'] = "Import Student";
        return view('admin.student.import')->with($data);
    }

    public function clearStudents(Request $request)
    {
        $this->validate($request, [
            'class'=>'required',
            'year'=>'required'
        ]);
        try {
            DB::beginTransaction();
            //code...
            $ids = \App\Models\StudentClass::where(['student_classes.year_id' => $request->year])
                    ->where(['student_classes.class_id' => $request->class])
                    ->join('students', ['students.id' => 'student_classes.student_id'])
                    ->where(['students.imported'=>1])
                    ->where(function($q)use($request){
                        $request->has('campus') ? $q->where(['students.campus_id' => $request->campus]) : null;
                    })
                    ->get(['students.id as student', 'student_classes.id as class']);
            foreach ($ids as $key => $value) {
                # code...
                Students::find($value->student)->delete();
                StudentClass::find($value->class)->delete();
            };

            DB::commit();
            return redirect()->to(route('admin.students.index', $request->class))->with('success', "Student saved successfully !");
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public  function matric()
    {
        $data['title'] = "Generate Student Matricule Number";
        return view('admin.student.matricule')->with($data);
    }

    public  function matricPost(Request  $request)
    {
        // return $request->all();
        $this->validate($request, ['batch' => 'required','section' => 'required']);
        $sec = $request->section;
        $id = $sec[count($request->section) - 1];
        $students = Students::join('student_classes', ['students.admission_batch_id' => 'student_classes.id'])->where(['student_classes.year_id' => $request->batch, 'student_classes.class_id' => $id])->orderBy('name')->get();
        $section = SchoolUnits::find($id);
        $batch = Batch::find($request->batch);
        foreach ($students as $k => $student) {
            $student->matric = $section->prefix . substr(Batch::find($request->batch)->name, 2, 2) . $section->suffix . str_pad(($k + 1), 3, 0, STR_PAD_LEFT);
            $student->save();
        }
        return redirect()->to(route('admin.students.index', [$id]))->with('success', 'Matricule number generated successfully!');
    }

    public  function importPost(Request  $request)
    {
        // Validate request
        $validate = Validator::make($request->all(), [
            'batch' => 'required',
            'file' => 'required',
            'campus_id' => 'required',
            'program_id'=> 'required'
        ]);

        if ($validate->fails()) {
            # code...
            return back()->with('error', $validate->errors()->first());
        }

        // used to track duplicate records that won't be inserted/saved.
        $duplicates = '';

        $file = $request->file('file');
        // File Details

        $extension = $file->getClientOriginalExtension();
        $filename = "Names." . $extension;
        // Valid File Extensions;
        $valid_extension = array("csv");
        if (in_array(strtolower($extension), $valid_extension)) {
            // File upload location
            $location = public_path() . '/files/';
            // Upload file
            $file->move($location, $filename);
            $filepath = public_path('/files/' . $filename);
            
            $file = fopen($filepath, "r");

            $importData_arr = array();
            $i = 0;

            while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
                $num = count($filedata);
                for ($c = 0; $c < $num; $c++) {
                    $importData_arr[$i][] = $filedata[$c];
                }
                $i++;
            }
            fclose($file);
            // dd($importData_arr);

            DB::beginTransaction();
            try {
                foreach ($importData_arr as $importData) {
                    if (Students::where('name', $importData[0])->orWhere('matric', $importData[1])->count() === 0) {
                        $student = \App\Models\Students::create([
                            'name' => mb_convert_case(str_replace('’', "'", $importData[0]), MB_CASE_UPPER),
                            'matric' => $importData[1],
                            // 'email' => explode(' ', str_replace('’', "'", $importData[2]))[0],
                            'gender' => $importData[2] ?? null,
                            'pob' => $importData[3] ?? null,
                            'dob' => $importData[4] ?? null,
                            // 'password' => Hash::make('12345678'),
                            'campus_id'=> $request->campus_id ?? null,
                            'program_id' => $request->program_id ?? null,
                            'admission_batch_id' => $request->batch,
                            'imported' => 1
                        ]);
                        $class = StudentClass::create([
                            'student_id' => $student->id,
                            'class_id' => $request->program_id,
                            'year_id' => $request->batch
                        ]);
                        
                        // echo ($importData[0]." Inserted Successfully<br>");
                    } else {
                        $duplicates .= $importData[1].' : '.$importData[0].', ';
                        //  echo ($importData[0]."  <b style='color:#ff0000;'> Exist already on DB and wont be added. Please verify <br></b>");
                    }
                }
                // dd($student);

                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
                // return back()->with('error', $e->getMessage());
            }
            session('message', 'Import Successful.');
            //echo("<h3 style='color:#0000ff;'>Import Successful.</h3>");
        } else {
            //echo("<h3 style='color:#ff0000;'>Invalid File Extension.</h3>");

            session('message', 'Invalid File Extension.');
        }

        return redirect()->to(route('admin.students.index', [$request->program_id]))->with('success', $duplicates == '' ? 'Student Imported successfully!' : 'Student Imported successfully! The following students where not imported because they are already in the database;\n'.$duplicates);
    }
    
    function getSubunitsOf($id){
        DB::table('school_units')->where('parent_id', '=', $id)->get(['id', 'name', 'parent_id']);
    }
    
    public static function getMainClasses()
    {
        # code...
        // added by Germanus. Loads listing of all classes accross all sections in a given school
        
        $base_units = DB::table('school_units')->where('parent_id', '>', 1)->get();
    
        // return $base_units;
        $listing = [];
        $options = [];
        $separator = ' : ';
        foreach ($base_units as $key => $value) {
            # code...
            // set current parent as key and name as value, appending from the parent_array
            if (array_key_exists($value->parent_id, $listing)) {
                $listing[$value->id] = $listing[$value->parent_id] . $separator . $value->name; 
            }else {$listing[$value->id] = $value->name;}
    
            // atatch parent units if there be any
            if ($base_units->where('id', '=', $value->parent_id)->count() > 0) {
                // return $base_units->where('id', '=', $value->parent_id)->pluck('name')[0];
                $listing[$value->id] = array_key_exists($value->parent_id, $listing) ? 
                $listing[$value->parent_id] . $separator . $value->name :
                $base_units->where('id', '=', $value->parent_id)->pluck('name')[0] . $separator . $value->name ;
            }
            // if children are obove, move over and prepend to children listing
            foreach ($base_units->where('parent_id', '=', $value->id) as $keyi => $valuei) {
                $value->id > $valuei->id ?
                $listing[$valuei->id] = $listing[$value->id] . $separator . $listing[$value->id]:
                null;
            }

            $options[$value->id] = '';

                // remove second highest child before adding
                // $options[$value->id] = mb_split(':', $listing[$value->id]); old format with complete hierarchy
                $split = mb_split(':', $listing[$value->id]);
                foreach ($split as $i => $word) {
                    # code...
                    if($i < 3)
                    $options[$value->id] .= ' : '.$word;
                }
                $options[$value->id] = substr($options[$value->id], 2);

        }
        return $options;
    }
    // get promotion base classes
    function _getBaseClasses(){
        $base_class_ids = DB::table('school_units')->whereNotNull('base_class')->get(['base_class']);
        return DB::table('school_units')->whereIn('id', $base_class_ids->pluck(('base_class')))->get();
    }
    public function initialisePromotion()
    {
        # code...
        // get main and target classes
        $classes = DB::table('school_units')->distinct()->get(['id', 'base_class', 'target_class']);
        $class_names = DB::table('school_units')->distinct()->get(['id', 'name', 'parent_id']);

        $data['base_classes'] = $this->_getBaseClasses();
        $data['class_pairs'] = $classes;
        $data['class_names'] = $class_names;
        $data['classes'] = $this->getMainClasses();
        // return config('tranzak.tranzak');
        return view('admin.student.initialise-promotion', $data);
    }

    public function promotion(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'class_from'=>'required',
            'class_to'=>'required',
        ]);
        
        if ($validator->fails()) {
            # code...
            return back()->with('error', $validator->errors()->first());
        }

        $current_year = $request->year_from ?? Helpers::instance()->getCurrentAccademicYear();
        $data['current_year'] = $current_year;
        $mainClasses = $this->getMainClasses();

        $classes = [
            'cf'=>[
                'id' => $request->class_from, 'name' => ProgramLevel::find($request->class_from)->program->name.' : Level '.ProgramLevel::find($request->class_from)->level->level
            ],
            'ct' => [
                'id' => $request->class_to, 'name' => ProgramLevel::find($request->class_to)->program->name.' : Level '.ProgramLevel::find($request->class_to)->level->level
                ]];

        // return $classes;
        if($request->action == 'promote') $data['title'] = "Student Promotion"; elseif($request->action == 'custom') $data['title'] = "Custom Promotion"; elseif($request->action == 'repeat') $data['title'] = "Repeat Students";
        $data['action'] = $request->action ?? 'promote';
        $data['request'] = $request;
        $data['classes'] = $classes;
        $data['students'] =  StudentClass::where(['year_id'=>$current_year, 'class_id'=>$request->class_from, 'student_classes.current'=>true])
                                ->join('students', ['students.id'=>'student_classes.student_id'])
                                ->where(function($q){
                                    auth()->user()->campus_id != null ? $q->where('students.campus_id', '=', auth()->user()->campus_id) : null;
                                })->distinct()->get(['students.*']);
        // return $data['students'];

        return view('admin.student.promotion', $data);
    }

    public function pend_promotion(Request $request)
    {
        // return $request->all();
        # write promotion to pending promotion for confirmation
        $valid = Validator::make($request->all(), [
            'class_from' => 'required',
            'class_to' => 'required',
            'year_from' => 'required',
            'year_to' => 'required',
            'type' => 'required',
            'students' => 'required|array'
        ]);
        if($valid->fails()){
            return back()->with('error', $valid->errors()->first());
        }
        try {
            // create pending promotion and delete it upon confirmation
            $ppromotion = [
                'from_year'=>$request->year_from,
                'to_year'=>$request->year_to,
                'from_class'=>$request->class_from,
                'to_class'=>$request->class_to,
                'type'=>$request->type,
                'students'=>json_encode($request->students),
                'user_id'=>auth()->id()
            ];
            $promotion_id = DB::table('pending_promotions')->insertGetId($ppromotion);
            $pending_promotion_students = [];
            foreach($request->students as $student){
                $pending_promotion_students[] = ['pending_promotions_id'=>$promotion_id, 'students_id'=>$student];
            }
            DB::table('pending_promotion_students')->insert($pending_promotion_students);
            return back()->with('success', 'Operation Complete');
            //code...
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('error', 'Error occured: '.$th->getMessage());
        }

    }

    public function teacherInitPromotion(Request $request)
    {
        # code...
        $classes = DB::table('school_units')->distinct()->get(['id', 'base_class', 'target_class']);
        $class_names = DB::table('school_units')->distinct()->get(['id', 'name', 'parent_id']);

        $data['base_classes'] = $this->_getBaseClasses();
        $data['class_pairs'] = $classes;
        $data['class_names'] = $class_names;
        $data['classes'] = $this->getMainClasses();
        return view('teacher.initialise-promotion', $data);
    }

    public function teacherPromotion(Request $request)
    {
    # code...
        $validator = Validator::make($request->all(), [
            'class_from'=>'required',
            'class_to'=>'required',
            'year_from'=>'required',
            'year_to'=>'required'
        ]);
        
        if ($validator->fails()) {
            # code...
            return back()->with('error', json_encode($validator->getMessageBag()->getMessages()));
        }
        // if ($request->class_from >= $request->class_to) {
        //     # code...
        //     return back()->with('error', 'next class must be higher than the current');
        // }
        // if ($request->year_from >= $request->year_to) {
        //     # code...
        //     return back()->with('error', 'next academic year must be higher than the current');
        // }
        
        $mainClasses = $this->getMainClasses();

        $classes = [
            'cf'=>[
                'id' => $request->class_from, 'name' => $mainClasses[$request->class_from]
            ],
            'ct' => [
                'id' => $request->class_to, 'name' => $mainClasses[$request->class_to]
                ]];

        $data['title'] = "Student Promotion";
        $data['request'] = $request;
        $data['classes'] = $classes;
        $data['students'] =  DB::table('student_classes')
                                ->whereIn('class_id', \App\Http\Controllers\Admin\ProgramController::subunitsOf($request->class_from))
                                ->where('year_id', '=', $request->year_from)
                                ->leftJoin('students', 'student_classes.student_id', '=', 'students.id')
                                ->get(['students.id as id', 'students.matric as matric', 'students.name as name', 'students.email as email']);
        // return $data['students'];

        return view('teacher.promotion', $data);
    }
    
    public function teacherPromote(Request $request)
    {
        # code...
        $this->pend_promotion($request);
    }
    public function trigger_approval(Request $request)
    {
        # code...
        $data['classes'] = $this->getMainClasses();
        if ($request->promotion_id != null) {
            # get and show all students
            $pending_promotion = DB::table('pending_promotions')->find($request->promotion_id);
            $students = DB::table('pending_promotion_students')
                    ->where('pending_promotions_id', '=', $request->promotion_id)
                    ->join('students', 'students.id', '=', 'pending_promotion_students.students_id')
                    ->get(['students.id as id', 'students.matric as matric', 'students.name as name']);
            $data['students'] = $students;
        }
        return view('admin.student.approve-promotion', $data);
    }
    public function approvePromotion(Request $request)
    {
        # code...
        $validity = Validator::make($request->all(), [
            'pending_promotion'=>'required',
            'students'=>'required|array'
        ]);

        if ($validity->fails()) {
            # code...
            return back()->with('error', json_encode($validity->getMessageBag()->getMessages()));
        }

        // retrieve pending promotion and perform promotion
        $ppromotion = \App\Models\PendingPromotion::find($request->pending_promotion);
        // create request object and call promote for proper promotion
        $promotion_request = new Request();
        $promotion_request->pp = $request->pending_promotion;
        $promotion_request->year_from = $ppromotion->from_year;
        $promotion_request->year_to = $ppromotion->to_year;
        $promotion_request->class_from = $ppromotion->from_class;
        $promotion_request->class_to = $ppromotion->to_class;
        $promotion_request->type = $ppromotion->type;
        $promotion_request->students = $request->students;

        // remove students from pending_promotion_students to student_promotions, update student classes and academic year
        return $this->promote($promotion_request);
    }

    public function promote(Request $request)
    {
        # code...
        try {
            DB::beginTransaction();
            //code...
            // create promotion > create student promotions > update students class and academic year
            $promotion = new Promotion([
                'from_year'=>$request->year_from,
                'to_year'=>$request->year_to,
                'from_class'=>$request->class_from,
                'to_class'=>$request->class_to,
                'type'=>'promotion',
                'user_id'=>auth()->id()
            ]);
            $promotion->save();
            $promotion_id = $promotion->id;
            if ($promotion_id != null) {
                # code...
                // create student promotions
                $students_promotion = [];
                $students_debt = [];
                if(count($request->students) > 0){
                    foreach ($request->students as $value) {
                        # code...
                        DB::table('student_promotions')->insert(['student_id'=>$value, 'promotion_id'=>$promotion_id]);
                    }
                    $students_debt[] = array_map(function($value){
                        $student = Students::find($value);
                        $balance = $student->bal($value);
                        // return $value;
                        return ['student_id'=>$value, 'next_debt' => $balance - $student->debt(Helpers::instance()->getCurrentAccademicYear())];
                    }, $request->students);
                    // return $students_debt;
                    // return $students_promotion;
    
                    // update students' class and academic year
                    // create new record on promotion  instead of updating record for previous year
                    // DB::table('student_classes')->whereIn('student_id', $request->students)->where('year_id', '=', $request->year_from)->update(['current'=>0]);
                    // foreach (DB::table('student_classes')->whereIn('student_id', $request->students)->where('year_id', '=', $request->year_from)->get() as $record){
                    //     // return $record;
                    //     // $class['class_id'] = $request->class_to;
                    //     // $class['year_id'] = $request->year_to;
                    //     // $class['student_id'] = $record->student_id;
                    //     // $class['current'] = 1;
                    //     // StudentClass::create($class);
                    // }
                    foreach ($request->students as  $value) {
                        # code...
                        DB::table('student_classes')->updateOrInsert(['student_id'=>$value, 'class_id' => $request->class_to, 'year_id' => $request->year_to], ['current' => true]);
                    }
                    DB::table('student_classes')->whereIn('student_id', $request->students)->where(['class_id' => $request->class_from, 'year_id' => $request->year_from])->update(['current' => false]);
                    // DB::table('student_classes')->whereIn('student_id', $request->students)->where('year_id', '=', $request->year_to)->update(['current'=>1]);

    
                    // update student program_id
                    DB::table('students')->whereIn('id', $request->students)->update(['program_id'=>$request->class_to]);

                    // transfer student debts
                    /**
                     * to acheive this, for every student, except for the promotion target accadmeic year, get the sum of the fee amounts for the student_class instances, get the sum of payments made for these instances; then evaluate their difference.
                     */
                    // foreach ($request->students as $value) {
                        # code...
                        // return $request->students;
                        // $student = Students::find($value);

                        // $student_class_instances = StudentClass::where('student_is', '=', $value)->where('year_id', '!=', $request->year_to)->get();
                        // $campus_program_levels = StudentClass::where('student_is', '=', $value)->where('year_id', '!=', $request->year_to)
                        //     ->join('campus_program', ['campus_programs.program_level_id' => 'student_classes.class_id'])->get();
                        // // fee amounts
                        // $fee_items = PaymentItem::whereIn('campus_program_id', $campus_program_levels->pluck('id'))->get();
                        // $fee_items_sum = $fee_items->sum('amount');
                        
                        // $fee_payments_sum = Payments::whereIn('payment_id', $fee_items->pluck('id'))->where(['student_id' => $value])->where('batch_id', '!=', $request->year_to)->sum('amount');
                        // $fee_debts_sum = Payments::whereIn('payment_id', $fee_items->pluck('id'))->where(['student_id' => $value])->where('batch_id', '!=', $request->year_to)->sum('debt');
                        // $next_debt = $fee_items_sum + $fee_debts_sum - $fee_payments_sum;


                    // }
                    
                    // delete pending_promotion_students
                    // DB::table('pending_promotion_students')
                    //     ->where('pending_promotions_id', '=', $request->pp)
                    //     ->whereIn('students_id', $request->students)
                    //     ->delete();
                }else {
                    # code...
                    return back()->with('error', 'No students selected.');
                }
            }
            DB::commit();
            return back()->with('success', 'Students promoted successfully!');
           
        } catch (\Throwable $th) {
            DB::rollBack();
            // throw $th;
            // FacadesSession::flash('error', 'Error occured. Promotion failed. Please try again later.');
            return back()->with('error', 'Error occured. Promotion failed. Please try again later. '.$th->getMessage());
        }
        

    }
    public function demote(Request $request, $promotion_id)
    {
        # code...
        try {
            //code...
            // create promotion > create student promotions > update students class and academic year
            $promotion = Promotion::find($promotion_id);
            $demotion = [
                'from_year'=>$promotion->to_year,
                'to_year'=>$promotion->to_year,
                'from_class'=>$promotion->to_class,
                'to_class'=>$promotion->from_class,
                'type'=>'demotion',
                'students'=>$promotion->students, 
                'user_id'=>auth()->id()
            ];
            // $demotion_id = DB::table('promotions')->insertGetId($demotion);
            if (!$promotion == null) {
                # code...
                // create student promotions
                // $students_promotion = [];
                // if(!json_decode($promotion->students) == null){
                // }
                
                 
                 // update students' class and academic year
                 DB::table('student_classes')->whereIn('student_id', json_decode($promotion->students) ?? [])->where('year_id', '=', $promotion->year_to)->delete();

                 // DB::table('student_classes')->whereIn('student_id', json_decode($promotion->students))->where('year_id', '=', $promotion->year_to)->distinct()->get()->each(function($rec)use($promotion){
                
                 
                 // update student program_id
                 DB::table('students')->whereIn('id', json_decode($promotion->students) ?? [])->update(['program_id'=>$promotion->class_from]);
                 foreach ($promotion->students()->get() as $key => $value) {
                     $value->delete();
                 }
                 DB::table('student_promotions')->where('promotion_id', '=', $promotion_id)->delete();
                 $promotion->delete();
                 // return '____________';

                 return back()->with('success', 'Students demoted successfully!');
                // return back()->with('message', 'No students available.');
            }

           
        } catch (\Throwable $th) {
            // throw $th;
            // FacadesSession::flash('error', 'Error occured. Promotion failed. Please try again later.');
            return back()->with('error', 'Error occured. Demotion failed. Please try again later. '.$th->getMessage());
        }
        

    }

    public function cencelPromotion(Request $request)
    {
        # code...
        $pending_promotion = DB::table('pending_promotions')->delete($request->promotion_id);
        db::table('pending_promotion_students')->where('pending_promotions_id', '=', $request->promotion_id)
        ->delete();
        return back()->with('promotion was successfully cancelled');
    }

    public function unitTarget(Request $request){
        $target_id = DB::table('school_units')->find($request->id)->target_class;
        return json_encode(DB::table('school_units')->find($target_id));
    }

    public function initialiseDemotion(Request $request){
        // get promotions for batch demote and target classes for custom demotion
        // get promotions
        // $classes = DB::table('school_units')->distinct()->get(['id', 'base_class', 'target_class']);
        // $class_names = DB::table('school_units')->distinct()->get(['id', 'name', 'parent_id']);

        $data['title'] = "Student Demotion";

        return view('admin.student.initialise-demotion', $data);
    }

    public function demotion(Request $request){
        $validator = Validator::make($request->all(), [
            'class_from'=>'required',
            'class_to'=>'required',
            'year_from'=>'required',
            'year_to'=>'required'
        ]);
        // return $request;
        if ($validator->fails()) {
            # code...
            return back()->with('error', json_encode($validator->getMessageBag()->getMessages()));
        }
        
        $mainClasses = $this->getMainClasses();

        $classes = ['cf'=>['id' => $request->class_from, 'name' => $mainClasses[$request->class_from]],'ct' => ['id' => $request->class_to, 'name' => $mainClasses[$request->class_to]]];

        $data['title'] = "Student Demotion";
        $data['request'] = $request;
        $data['classes'] = $classes;
        $data['students'] =  DB::table('student_classes')
                                ->where('class_id', '=', $request->class_from)
                                ->where('year_id', '=', $request->year_from)
                                ->leftJoin('students', 'student_classes.student_id', '=', 'students.id')
                                ->get(['students.id as id', 'students.matric as matric', 'students.name as name', 'students.email as email']);
        // return $data['students'];

        return view('admin.student.promotion', $data);
    }

    public function unitDemoteTarget(Request $request){
        return DB::table('school_units')->where('target_class', '=', $request->id)->first();
    }

    // Student results/average calculation section

    // get subjects of a given class with class id
    public function getSubjectsByClass($class_id)
    {
        # code...
        return DB::table('class_subjects')->whereNull('class_subjects.deleted_at')
                ->where('class_id', '=', $class_id)
                ->join('subjects', 'subjects.id','=', 'class_subjects.subject_id')
                ->distinct()
                ->get(['subjects.id as id', 'subjects.name as name', 'subjects.coef as coef', 'subjects.code as code']);
    }

    // get subjects of a given student with $student_id
    // here, we get the subjects for which a given student with $student_id has atleast a mark
    public function getSubjectsByStudent($student_id)
    {
        # code...
        return DB::table('student_classes')
                ->where('student_id', '=', $student_id)
                ->join('results', 'student_id', '=', $student_id)
                ->where('results.class_id', '=', 'student_classes.class_id')
                ->whereNotNull('results.score')
                ->join('subjects', 'subjects.id', '=', 'results.subject_id')
                ->distinct()
                ->get(['subjects.id as id', 'subjects.name as name', 'subjects.coef as coef', 'subjects.code as code']);
    }

    // get sum of coeficients for given class subjects with $class_id
    public function getCoeficientsSumByClass($class_id)
    {
        # code...
        $coefs = DB::table('class_subjects')->whereNull('class_subjects.deleted_at')
                ->where('class_id', '=', $class_id)
                ->join('subjects', 'subjects.id','=', 'class_subjects.subject_id')
                ->distinct()
                ->get(['subjects.id as id', 'subjects.name as name', 'subjects.coef as coef', 'subjects.code as code'])
                ->pluck('coef')
                ->toArray();
        return array_sum($coefs);
    }

    // get sum of coeficients for given student with $student_id
    public function getCoeficientsSumByStudent($student_id)
    {
        # code...
        $coefs = DB::table('student_classes')
                ->where('student_id', '=', $student_id)
                ->join('results', 'student_id', '=', $student_id)
                ->where('results.class_id', '=', 'student_classes.class_id')
                ->whereNotNull('results.score')
                ->join('subjects', 'subjects.id', '=', 'results.subject_id')
                ->distinct()
                ->get(['subjects.id as id', 'subjects.name as name', 'subjects.coef as coef', 'subjects.code as code'])
                ->pluck('coef')
                ->toArray();
        return array_sum($coefs);
    }

    // get student's score for subject with $student_id, and $subject_id in class with $class_id, in academic year with $year_id by filter[1st term : 1, 2nd term : 2, 3rd term : 3, annual : null; filter is built upon result.sequence
    public function getStudentSubjectScore($student_id, $subject_id, $class_id, $year_id = 0, $filter = 4)
    {
        # code...
        if ($year_id == null) {
            # code...
            $year_id = Helpers::instance()->getCurrentAccademicYear();
        }
        $builder = DB::table('results')
                ->where('batch_id', '=', $year_id)
                ->where('student_id', '=', $student_id)
                ->where('class_id', '=', $class_id)
                ->where('subject_id', '=', $subject_id);
        switch ($filter) {
            case '1':
                # code...
                return array_sum($builder->where('sequence', '=', 1)
                ->where('sequence', '=', 2)
                ->pluck('score')
                ->toArray());
                // break;
            case '2':
                # code...
                return array_sum($builder->where('sequence', '=', 3)
                    ->where('sequence', '=', 4)
                    ->pluck('score')
                    ->toArray());
                // break;
            case '3':
                # code...
                return array_sum($builder->where('sequence', '=', 5)
                    ->where('sequence', '=', 6)
                    ->pluck('score')
                    ->toArray());
                // break;

            default:
                # code...
                return array_sum($builder
                    ->pluck('score')
                    ->toArray());
                // break;
        }
    }

    // calculate student's average with specified term; 1,2,3; default : 4 for annual average
    public function getStudentAverage($student_id, $class_id = null, $year_id = null, $filter = null, $gradingSystem = 20)
    {
        $coeficientSum = 0;
        $subject_average = 0;
        $subjects = $this->getSubjectsByStudent($student_id);
        if ($class_id != null) {$coeficientSum = $this->getCoeficientsSumByClass($class_id);
        }else {$coeficientSum = $this->getCoeficientsSumByStudent($student_id);
        }
        foreach($subjects as $key => $subject){
            $subject_average += ($subject->coef / $coeficientSum) * $this->getStudentSubjectScore($student_id, $subject->id, $class_id, $year_id, $filter);
        }
        return $subject_average * $gradingSystem / $coeficientSum;
        
    }

    // get averages for all students in a class
    public function getClassStudentsAverageList($class_id, $year_id = null)
    {
        # code...
    }

    public function studentResultBypass(Request $request)
    {
        # code...
        $data['title'] = "Bypass Student Results";
        return view('admin.student.bypass-result', $data);
    }

    public function setStudentResultBypass(Request $request)
    {
        $check = Validator::make($request->all(), ['bypass_result_reason'=>'required', 'semester'=>'required']);
        if($check->fails()){
            return back()->with('error', $check->errors()->first());
        }
        # code...
        $student_class = StudentClass::where(['student_id'=>$request->student_id, 'year_id'=>Helpers::instance()->getCurrentAccademicYear()]);
        if(!$student_class == null){
            $student_class->update(['bypass_result'=>true, 'bypass_result_reason'=>$request->bypass_result_reason, 'result_bypass_semester'=>$request->semester ?? Helpers::instance()->getSemester($student_class->first()->class_id)->id]);

            return back()->with('success', 'Done');
        }
        else{return back()-with('error', 'Student has no class.');}
    }

    public function reset_password(Request $request, $id=null)
    {
        $student = Students::find($id);
        if ($student != null) {
            # code...
            Students::where('id', '=', $id)->update(['password'=> Hash::make('12345678')]);
            return back()->with('success', 'Done');
        }
        return back()->with('error', 'Operation Failed. Student could not be resolved.');
    }

    public function cancelResultBypass(Request $request, $id)
    {
        $std_class = StudentClass::find($id);
        if($std_class != null){
            StudentClass::where('id', '=', $id)->update(['bypass_result'=>0, 'bypass_result_reason'=>null, 'result_bypass_semester'=>null]);
            return back()->with('success', 'Done');
        }
        return back()->with('error', 'Operation failed. Bypass could not be resolved to a class.');
    }

    public function change_status($student_id)
    {
        $student = Students::find($student_id);
        if($student != null){
            $student->active = !$student->active;
            $student->save();
            return back()->with('success', __('text.word_done'));
        }
        return back()->with('error', __('text.item_not_found', __('text.word_student')));
    }

    public function promotion_history(Request $request, $program_id = null)
    {
        # code...
        $data['title'] = "Promotion History";
        if($program_id != null){
            $data['promotions'] = Promotion::join('program_levels', 'program_levels.id', '=', 'promotions.from_class')->where('program_levels.program_id', $program_id)->get('promotions.*');
        }
        return view('admin.student.promotion_history', $data);
    }

    public function custom_promotion(Request $request){
        $classes = DB::table('school_units')->distinct()->get(['id', 'base_class', 'target_class']);
        $class_names = DB::table('school_units')->distinct()->get(['id', 'name', 'parent_id']);

        $data['base_classes'] = $this->_getBaseClasses();
        $data['class_pairs'] = $classes;
        $data['class_names'] = $class_names;
        $data['classes'] = $this->getMainClasses();
        // return config('tranzak.tranzak');
        return view('admin.student.custom_promotion', $data);
    }

    public function repeat_students(Request $request){
        $classes = DB::table('school_units')->distinct()->get(['id', 'base_class', 'target_class']);
        $class_names = DB::table('school_units')->distinct()->get(['id', 'name', 'parent_id']);

        $data['base_classes'] = $this->_getBaseClasses();
        $data['class_pairs'] = $classes;
        $data['class_names'] = $class_names;
        $data['classes'] = $this->getMainClasses();
        // return config('tranzak.tranzak');
        return view('admin.student.repeat_students', $data);
    }
}
