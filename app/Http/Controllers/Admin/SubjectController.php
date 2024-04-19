<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subjects;
use App\Models\Topic;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use function Ramsey\Uuid\v1;

class SubjectController extends Controller
{

    public function next(Request $request)
    {
        # code...
        return redirect(route('admin.courses._create', [$request->semester]));
    }

    public function _create(Request $request)
    {
        $data['title'] = 'Create '.\App\Models\Semester::find($request->semester)->name.' Course';
        return view('admin.subject._create', $data);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'coef' => 'required',
            'code'=>'required',
            'level'=>'required',
            'semester'=>'required',
            'status'=>'required',
        ]);
        if(\App\Models\Subjects::where('code', $request->input('code'))->count()>0){
            return back()->with('error', "Course code ".$request->input('code').' already exist');
        }
        $subject = new \App\Models\Subjects();
        $subject->name = $request->input('name');
        $subject->coef = $request->input('coef');
        $subject->code = $request->input('code');
        $subject->status = $request->input('status');
        $subject->level_id = $request->input('level');
        $subject->semester_id = $request->input('semester');
        $subject->save();
        return back()->with('success', "Subject Created!");
    }

    public function edit(Request $request, $id)
    {
        $data['subject'] = \App\Models\Subjects::find($id);
        $data['title'] = "Edit " . $data['subject']->name;
        return view('admin.subject.edit')->with($data);
    }

    public function show(Request $request, $id)
    {
        return redirect(route(
            'admin.subjects.index'
        ));
    }

    public function create()
    {
        $data['title'] = "Create Subject";
        return view('admin.subject.create')->with($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'coef' => 'required',
            'code'=>'required',
            'level'=>'required',
            'semester'=>'required',
            'status'=>'required',
        ]);
        if(Subjects::where('code', $request->input('code'))->count()>0 && Subjects::find($id)->code != $request->input('code')){
            return back()->with('error', "Course code ".$request->input('code').' already exist');
        }
        $subject = Subjects::find($id);
        $subject->name = $request->input('name');
        $subject->coef = $request->input('coef');
        $subject->code = $request->input('code');
        $subject->status = $request->input('status');
        $subject->level_id = $request->input('level');
        $subject->semester_id = $request->input('semester');
        $subject->objective = $request->input('objective');
        $subject->outcomes = $request->input('outcomes');
        $subject->save();
        return back()->with('success', "Subject Updated Successfully!");
    }

    /**
     * Delete the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $subject = Subjects::find($id);
        if ($subject->units->count() > 0) {
            return redirect()->to(route('admin.subjects.index'))->with('error', "Subject cant be deleted");
        }
        $subject->delete();
        return redirect()->to(route('admin.subjects.index'))->with('success', "subject deleted");
    }

    public function index(Request $request)
    {
        $data['title'] = "List of all Subjects";
        $data['subjects'] = Subjects::orderBy('name')->get();
        return view('admin.subject.index')->with($data);
    }

    public function course_content($user_id, $subject_id, $content_id=null)
    {
        $campus_id = auth()->user()->campus_id;
        $user = User::find($user_id);
        if($campus_id == null){
            $campus_id = $user->campus_id;
        }
        $subject = Subjects::find($subject_id);
        $data['level'] =  1;
        $data['campus_id'] =  $campus_id;
        $data['subject_id'] =  $subject_id;
        $data['teacher_id'] =  $user_id;
        $data['content'] = Topic::where('subject_id', $subject_id)->where('campus_id', $campus_id)->where('level', 1)->get();
        $data['title'] = "Course content for ".($subject->name??'').' - '.($user->name??'');

        if($content_id != null){
            $topic = Topic::find($content_id);
            $data['topic'] = $topic;
            $data['parent_id'] = $content_id;
            $data['level'] = 2;
            $data['title'] .= ' - Main Topic: '.$topic->title;
            $data['content'] = Topic::where('subject_id', $subject_id)->where('campus_id', $topic->campus_id)->where('level', 2)->where('parent_id', $content_id)->get();
        }
        return view('admin.content.content', $data);
    }

    public function save_course_content(Request $request, $user_id, $subject_id, $content_id=null)
    {
        $validity = Validator::make($request->all(), ['title'=>'required', 'campus_id'=>'required']);

        if($validity->fails()){
            session()->flash('error', $validity->errors()->first());
            return back()->withInput();
        }
        if($content_id == null){
            $data = ['title'=>nl2br($request->title), 'campus_id'=>$request->campus_id, 'level'=>1, 'subject_id'=>$subject_id];
            if(Topic::where($data)->count() > 0){
                session()->flash('error', 'Topic already exist');
                return back()->withInput();
            }
            (new Topic($data))->save();
        }else{
            $data = ['title'=>nl2br($request->title), 'campus_id'=>$request->campus_id, 'level'=>2, 'subject_id'=>$subject_id, 'teacher_id'=>$user_id, 'parent_id'=>$content_id, 'duration'=>$request->duration??null, 'week'=>$request->week??null];
            if(Topic::where($data)->count() > 0){
                session()->flash('error', 'Topic already exist');
                return back()->withInput();
            }
            (new Topic($data))->save();
        }
        return back()->with('success', 'Record successfully added');
    }

    public function edit_course_content($user_id, $subject_id, $content_id)
    {
        $topic = Topic::find($content_id);
        $campus_id = $topic->campus_id;
        $user = User::find($user_id);
        $subject = Subjects::find($subject_id);
        $data['level'] =  $topic->level;
        $data['campus_id'] =  $campus_id;
        $data['subject_id'] =  $subject_id;
        $data['teacher_id'] =  $user_id;
        $data['content'] = Topic::where('subject_id', $subject_id)->where('campus_id', $campus_id)->where('level', 1)->get();
        $data['title'] = "Edit course content for ".($subject->name??'').' - '.($user->name??'');
        $data['topic'] = $topic;
        $data['parent_id'] = $content_id;
        
        if($topic->level == 2){
            $data['content'] = Topic::where('subject_id', $subject_id)->where('campus_id', $topic->campus_id)->where('level', $topic->level)->where('teacher_id', $user_id)->get();
        }else{
            $data['content'] = Topic::where('subject_id', $subject_id)->where('campus_id', $topic->campus_id)->where('level', $topic->level)->get();
        }
        return view('admin.content.edit', $data);
    }
    
    public function update_course_content(Request $request, $user_id, $subject_id, $content_id)
    {
        $validity = Validator::make($request->all(), ['title'=>'required', 'campus_id'=>'required']);

        $topic = Topic::find($content_id);
        if($validity->fails()){
            session()->flash('error', $validity->errors()->first());
            return back()->withInput();
        }
        
        if($content_id == null){
            $data = ['title'=>nl2br($request->title), 'campus_id'=>$request->campus_id, 'level'=>1, 'subject_id'=>$subject_id];
            if(Topic::where($data)->count() > 0){
                session()->flash('error', 'Topic already exist');
                return back()->withInput();
            }
            $topic->update($data);
        }else{
            $data = ['title'=>nl2br($request->title), 'campus_id'=>$request->campus_id, 'level'=>2, 'subject_id'=>$subject_id, 'teacher_id'=>$user_id, 'parent_id'=>$content_id, 'duration'=>$request->duration??null, 'week'=>$request->week??null];
            if(Topic::where($data)->count() > 0){
                session()->flash('error', 'Topic already exist');
                return back()->withInput();
            }
            $topic->update($data);
        }
        return back()->with('success', 'Record successfully added');
    }

    public function delete_course_content($user_id, $subject_id, $content_id)
    {
        $campus_id = auth()->user()->campus_id;
        $user = User::find($user_id);
        if($campus_id == null){
            $campus_id = $user->campus_id;
        }
        if(($topic = Topic::where(['subject_id'=>$subject_id, 'id'=>$content_id, 'campus_id'=>$campus_id])->first()) != null){
            $topic->delete();
        }
        return back()->with('message', 'Operation complete');
    }
}
