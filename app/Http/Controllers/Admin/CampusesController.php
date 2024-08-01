<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Campus;
use App\Models\CampusProgram;
use App\Models\ProgramLevel;
use App\Models\SchoolUnits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CampusesController extends Controller
{
    //

    public function index()
    {
        # code...
        $data['title'] = __("text.manage_campuses");
        if (request()->has('school_id')) {
            $data['campuses'] = \App\Models\School::find(request('school_id'))->campuses();
        }
        else {
            $data['campuses'] = \App\Models\Campus::all();
        }
        return view('admin.campuses.index', $data);
    }

    public function create()
    {
        # code...
        $data['title'] = __("text.add_new_campus");
        if (request()->has('school_id')) {
            $data['campuses'] = \App\Models\School::find(request('school_id'))->campuses();
        }
        else {
            $data['campuses'] = \App\Models\Campus::all();
        }
        return view('admin.campuses.create', $data);
    }
    
    public function store(Request $request)
    {
        # code...
        $validator = Validator::make($request->all(), [
            'school_id'=>'required',
            'name'=>'required',
            'address'=>'required',
            'telephone'=>'required'
        ]);

        if ($validator->fails()) {
            # code...
            return back()->with('error', $validator->errors()->first());
        }

        try {
            //code...
            if (\App\Models\School::find($request->school_id)->campuses()->where('name', $request->name)->count() > 0) {
                return back()->with('error', __('text.record_already_exist'));
            }
    
            (new \App\Models\Campus($request->all()))
                ->save();
            return back()->with('success', __('text.word_done'));
        } catch (\Throwable $th) {
            //throw $th;
            throw $th;
            return back()->with('error', $th->getMessage());
        }

    }

    public function edit($id)
    {
        $data['title'] = __("text.edit_campus");
        $data['campus'] = \App\Models\Campus::find($id);
        if (request()->has('school_id')) {
            $data['campuses'] = \App\Models\School::find(request('school_id'))->campuses();
        }
        else {
            $data['campuses'] = \App\Models\Campus::all();
        }
        return view('admin.campuses.edit', $data);
    }

    public function update($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'school_id'=>'required',
            'name'=>'required',
            'address'=>'required',
            'relephone'=>'required'
        ]);

        try {
            // if name and/or contact already exist, reject update
            $campus = \App\Models\Campus::find($id);
            if ($campus->name != $request->name && \App\Models\Campus::where('name', $request->name)->count() > 0) {
                # code...
                return back()->with('error', __('text.record_already_exist', ['item'=>__('text.word_campus')]));
            }
            if (isset($request->telephone) && $campus->telephone != $request->telephone && \App\Models\Campus::where('telephone', $request->telephone)->count() > 0) {
                # code...
                return back()->with('error', __('text.record_already_exist', ['item'=>$request->telephone]));
            }
            $campus->fill($request->all());
            $campus->save();
            return back()->with('success', __('text.word_done'));
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('error', $th->getMessage());
        }
    }

    public function delete($id)
    {
        # code...
        if (\App\Models\Campus::find($id)->students()->count() > 0) {
            # code...
            return back()->with('error', __('text.campus_not_deleted_phrase_1'));
        }
        \App\Models\Campus::find($id)->delete();
    }

    public function programs($id)//$id for the campus_id
    {
        # code...
        $data['title'] = __('text.manage_programs_under')." ".\App\Models\Campus::find($id)->name;
        $data['programs'] = \App\Models\CampusProgram::where('campus_id', $id)->pluck('program_level_id')->toArray();
        return view('admin.campuses.programs', $data);
    }

    public function set_program_fee($id, $program_id)
    {
        # code...
        $data['title'] = __('text.manage_fee_under')." ".Campus::find($id)->name.' '.__('text.word_for').' '.ProgramLevel::find($program_id)->program()->first()->name . ' : '.__('text.word_level').' '.ProgramLevel::find($program_id)->level()->first()->level;
        // $data['data'] = [
        //     'tution' = \App\Models\CampusProgram::where('campus_id', request('id'))->where('program_level_id', request('program_id'))->first()->payment_items()->where('name', 'TUTION')->first()->amount ?? '----',
        //     'min-1'
        // ]
        return view('admin.fee.create', $data);
    }

    public function add_program($id, $program_id)
    {
    # code...
        if (\App\Models\CampusProgram::where('campus_id', $id)->where('program_level_id', $program_id)->count()>0) {
            # code...
            return back()->with('error', __('text.record_already_exist', ['item'=>__('text.word_program')]));
        }
        $cp = new \App\Models\CampusProgram(['campus_id'=>$id, 'program_level_id'=>$program_id]);
        $cp->save();
        return back()->with('success', __('text.word_done'));
    }

    public function drop_program($id, $program_id)
    {
        # code...
        if (\App\Models\CampusProgram::where('campus_id', $id)->where('program_level_id', $program_id)->count()==0) {
            # code...
            return back()->with('error', __('text.does_not_exist_in_this_campus'));
        }

        \App\Models\CampusProgram::where('campus_id', $id)->where('program_level_id', $program_id)->first()->delete();
        return back()->with('success', __('text.word_done'));

    }

    public function save_program_fee($id, $program_id, Request $request)
    {
        # code...
        $this->validate($request, [
            'fees'=>'required|int',
            'first_instalment'=>'nullable|int',
            'second_instalment'=>'nullable|int'
        ]);

        try {
            //code...
            // dd($request->all());
            $cp_id = \App\Models\CampusProgram::where('campus_id', $id)->where('program_level_id', $program_id)->first()->id;
    
            // save tution fee
            $tution_inst = \App\Models\PaymentItem::where('campus_program_id', $cp_id)->where('name', 'TUTION')->where(['year_id' => Helpers::instance()->getCurrentAccademicYear()])->first();
            $tution_inst = $tution_inst == null ? new \App\Models\PaymentItem() : $tution_inst;
            $tution_inst->campus_program_id = $cp_id;
            $tution_inst->name = 'TUTION';
            $tution_inst->year_id = \App\Helpers\Helpers::instance()->getCurrentAccademicYear();
            $tution_inst->slug = Hash::make('TUTION');
            $tution_inst->amount = $request->fees;
            $tution_inst->first_instalment = $request->first_instalment;
            $tution_inst->second_instalment = $request->second_instalment;
            $tution_inst->save();

            return redirect(route('admin.fee_settings', $id))->with('success', __('text.word_done'));
        } catch (\Throwable $th) {
            //throw $th;
            session()->flash('error', "F:{$th->getFile()} | L:{$th->getLine()} | M:{$th->getMessage()}");
            return back()->withInput();
        }
    }
}
