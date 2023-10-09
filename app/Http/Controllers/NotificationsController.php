<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Campus;
use App\Models\ClassMaster;
use App\Models\Level;
use App\Models\Notification;
use App\Models\ProgramLevel;
use App\Models\SchoolUnits;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class NotificationsController extends Controller
{

    
    public function index(Request $request, $layer, $layer_id, $campus_id=0 )
    {
        // get the layer type and id from the request
        // layer types: ['S=>SCHOOL', 'F=>FACULTY', 'D=>DEPARTMENT', 'P=>PROGRAM', 'L=>LEVEL', 'C=>CLASS']
        
        $notifications = Notification::where(function($q) use($campus_id){
            $campus_id == 0 ? null : $q->where('campus_id', $campus_id);
        })->get();
        
        switch ($layer) {
            case 'S':
                # code...
                $data['notifications'] = $notifications->whereNull('school_unit_id');
                break;
            
            case 'F':
                # code...
                break;
            
            case 'D':
                # code...
                // if the user is a class master
                $department_ids = ClassMaster::where(['user_id'=>auth()->id()])->pluck('department_id')->toArray();
                
                if (in_array($layer_id, $department_ids)) {
                    # code...
                    $data['notifications'] = $notifications->where('unit_id', 3)->where('school_unit_id', request('layer_id'));

                }else {
                    $data['notifications'] = $notifications->empty();
                }
                
                break;
            
            case 'P':
                # code...
                // if the user is a class master
                $department_ids = ClassMaster::where(['user_id'=>auth()->id()])->pluck('department_id')->toArray();
                $program_ids = SchoolUnits::where(['unit_id'=>4])->whereIn('parent_id', $department_ids)->pluck('id');
                if (in_array($layer_id, $program_ids)) {
                    # code...
                    $data['notifications'] = $notifications->where('unit_id',4)->where('school_unit_it',$layer_id);
                } else {
                    # code...
                    $data['notifications'] = $notifications->empty();
                }
                
                break;
            
            case 'L':
                # code...
                // if user is class master
                $department_ids = ClassMaster::where(['user_id'=>auth()->id()])->pluck('department_id')->toArray();
                $program_ids = SchoolUnits::where(['unit_id'=>4])->whereIn('parent_id', $department_ids)->pluck('id');
                $level_ids = Level::join('program_levels', ['program_levels.level_id'=>'levels.id'])
                            ->join('school_units', ['school_units.id'=>'program_levels.program_id'])
                            ->where(['school_units.uint_id'=>4])
                            ->whereIn('school_units.id', $program_ids)
                            ->distinct()->pluck('levels.id')->toArray();

                if (in_array($layer_id, $level_ids)) {
                    # code...
                    $data['notifications'] = $notifications->where('level_id', $layer_id);
                }else {
                    # code...
                    $data['notifications'] = $notifications->empty();
                }
                break;
            
            case 'C':
                # code...
                // for a class master
                $class = ProgramLevel::find(request('layer_id'));
                if(ClassMaster::where(['user_id'=>auth()->id()])->count() > 0){
                    // return 777;
                    $department_ids = ClassMaster::where(['user_id'=>auth()->id()])->pluck('department_id')->toArray();
                    $class_ids = SchoolUnits::where(['unit_id'=>4])->whereIn('parent_id', $department_ids)
                                ->join('program_levels', ['program_levels.program_id'=>'school_units.id'])
                                ->pluck('program_levels.id')->toArray();
    
                    if(in_array($layer_id, $class_ids)){
                        $data['notifications'] = $notifications->where('school_unit_id',$class->program_id)->where('level_id',$class->level_id);
                    }else {
                        $data['notifications'] = $notifications->empty();
                    }
                }
                else {
                    // For a normal teacher to view class notifications
                    $classes  = \App\Models\TeachersSubject::where(['teacher_id'=>auth()->id()])
                                ->pluck('class_id')->toArray();
                    if (in_array($layer_id, $classes)) {
                        # code...
                        $data['notifications'] = $notifications->where('school_unit_id',$class->program_id)->where('level_id', $class->level_id);
                    } else {
                        # code...
                        $data['notifications'] = $notifications->empty();
                    }
                    
                }
                break;
            
            default:
                # code...
                break;
        }
        // $data['notifications'] = $data['notifications']->where(function($c){
        //     $c->where('user_id', auth()->id())
        //         ->where('visibility', 'teacher'||'general');
        // });
        $data['title'] = ($request->has('type') ? "Departmental Notifications For ".SchoolUnits::find($request->_d)->name ?? '' : '')
                        .($request->has('program_level_id') ? "Notifications For ".ProgramLevel::find(request('program_level_id'))->program()->first()->name.' : Level '.ProgramLevel::find(request('program_level_id'))->level()->first()->level : '')
                        .((!$request->has('campus_id') || $request('campus_id') == 0) ? '' : ' : '.(Campus::find(request('campus_id'))->name ?? 'All').' Campus');
        
        return auth()->user()->type == 'admin' ?
        view('admin.notification.index', $data) :
        view('teacher.notification.index', $data) ;
    }

    public function create()
    {
        # code...
        $data['title'] = (request('type') != null ? auth()->user()->classes()->first()->name : '')
                        .(request('program_level_id') != null ? ProgramLevel::find(request('program_level_id'))->program()->first()->name.' : Level '.ProgramLevel::find(request('program_level_id'))->level()->first()->level : '')
                        .(request('campus_id') != null ? Campus::find(request('campus_id'))->name ?? '' : '');
        // return auth()->user()->roles()->first()->slug;
        return auth()->user()->type == 'admin' ?
        view('admin.notification.create', $data):
        view('teacher.notification.create', $data) ;
    }

    public function save(Request $request, $layer, $layer_id, $campus_id = null)
    {
        # code...
        $request->validate([
            'title'=>'required',
            'date'=>'required',
            'visibility'=>'required|in:general,students,teachers,admins',
            'message'=>'required'
        ]);
        // dd($request->all());
        try {
            $app_title = '';
            $data = $request->all();
            $data['campus_id'] = $campus_id;
            $data['user_id'] = auth()->id();
            $data['school_unit_id'] = $request->school_unit_id == 0 ? null : $request->school_unit_id;
            switch ($layer) {
                case 'S': case 'SCHOOL':
                    # code...
                    // $data['unit_id'] = 1;
                    // $data['school_unit_id'] = $layer_id;
                    break;
                case 'F': case 'FACULTY':
                    # code...
                    $app_title = 'faculty_'.$request->school_unit_id;
                    $data['unit_id'] = 2;
                    $data['school_unit_id'] = $layer_id;
                    break;
                case 'D': case 'DEPARTMENT':
                    # code...
                    $app_title = 'department_'.$request->school_unit_id;
                    $data['unit_id'] = 3;
                    $data['school_unit_id'] = $layer_id;
                    break;
                case 'P': case 'PROGRAM':
                    # code...
                    $app_title = 'program_'.$request->school_unit_id;
                    $data['unit_id'] = 4;
                    $data['school_unit_id'] = $layer_id;
                    break;
                case 'C': case 'CLASS':
                    # code...
                    $class = ProgramLevel::find($layer_id == 0 ? $request->program_level_id : $layer_id);
                    $app_title = 'class_'.$class->id;
                    $data['unit_id'] = 4;
                    $data['school_unit_id'] = $class->program_id;
                    $data['level_id'] = $class->level_id;
                    break;
                case 'L': case 'LEVEL':
                    # code...
                    $app_title = 'level_'.$request->level_id;
                    $data['unit_id'] = 4;
                    $data['level_id'] = $layer_id;
                    break;
                
                default:
                    # code...
                    return back()->with('error', 'Unknown notification type.');
                    break;
            }
            if($campus_id != 0 || auth()->user()->campus_id != null){
                $app_title .= '_campus_'.($campus_id == 0 ? auth()->user()->campus_id : $campus_id);
            }
            $app_data = ['to'=>$app_title, 'title'=>$request->title, 'body'=>$request->message];
            $this->notify_app($app_data);
            Notification::create($data);
            
            return redirect(route('notifications.index', [$layer, $layer_id, $campus_id]))->with('success', 'Done');
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('error', 'Operation failed '.$th->getMessage());
        }
    }
    
    public function edit($layer, $layer_id, $campus_id, $id)
    {
        # code...
        $data['item'] = Notification::find($id);
        $data['title'] = 'Edit '.$data['item']->title;
        
        return auth()->user()->type == 'admin' ?
             view('admin.notification.edit', $data) :
             view('teacher.notification.edit', $data);
    }
    
    public function update(Request $request, $layer, $layer_id, $campus_id, $id)
    {
        # code...
        $request->validate([
            'title'=>'required',
            'date'=>'required',
            'visibility'=>'required|in:general,students,teachers,admins',
            'message'=>'required'
        ]);
        try {
            //code...
            $not = Notification::find($id);
            $not->fill($request->all());
            $not->save();
            return redirect(route('notifications.index', [$layer, $layer_id, $campus_id]))->with('success', 'Done');
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('error', 'Operation failed '.$th->getMessage());
        }
    }
    
    public function show($layer, $layer_id, $campus_id, $id)
    {
        # code...
        $data['notification'] = Notification::find($id);
        $data['title'] = $data['notification']->title;

        return auth()->user()->type == 'admin' ?
             view('admin.notification.show', $data) :
             view('teacher.notification.show', $data);
    }

    public function drop(Request $request, $layer, $layer_id, $campus_id, $id)
    {
        # code...
        Notification::find($id)->delete();
        return back()->with('success', 'Done');
    }

    public function create_message()
    {
        # code...
        $data['title'] = "Send Message";
        return view('admin.messages.create', $data);
    }

    public function create_message_save(Request $request)
    {
        # code...
        $validator = Validator::make($request->all(), [
            'year_id'=>'required|numeric',
            'class_id'=>'required|numeric',
            'message'=>'required', 
            'recipients'=>'required|in:students,teachers,parents'
        ]);
        if($validator->fails()){
            return back()->with('error', $validator->errors()->first());
        }
        // SEND MESSAGE, 
        // SAVE MESSAGE TO DATABASE WITH STATUS PENDING, 
        // GET RESPONSE AND UPDATE STATUS TO SENT OR FAILED 

        // send message here and wait for response
        $message_id = rand(10000000000001, 98990998075609).'_'.time();

        // save pending message message 
        $message_instance = new \App\Models\Message($request->all());
        $message_instance->status = 'PENDING';
        $message_instance->message_id = $message_id;
        $message_instance->save();

        // update message status on response
        return $request->all();
    }

    public function sent_messages()
    {
        # code...
        $data['title'] = "Sent Messages";
        return view('admin.messages.sent', $data);
    }
}
