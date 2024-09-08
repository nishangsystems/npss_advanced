<?php

namespace App\Http\Controllers\Admin\Expense;

use App\Http\Controllers\Controller;
use App\Models\Expenses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    private $select = [
        'name',
        'amount_spend',
        'user_id',
        'id',
        'date',

    ];

    /**
     * list all expenses of a school
     * I use the is of the current authenticated admin, to get the expenses for his/her school
     */
    public function index()
    {
        $user_id = Auth::id();
        $data['expenses'] = Expenses::join('users', 'users.id', '=', 'expenses.user_id')
        ->where(function($query){
            auth()->user()->campus_id != null ? $query->where('users.campus_id', '=', auth()->user()->campus_id):null;
        })
        ->orderBy('expenses.id', 'DESC')
        ->select('expenses.*')->get();
        
        $data['title'] = __('text.school_expenses');

        return view('admin.expense.index')->with($data);
    }

    /**
     * show form to create an expense for a school
     */
    public function create()
    {
        $data['title'] = __('text.create_new_expense');
        return view('admin.expense.create')->with($data);
    }


    /**
     * validate data to creat income
     *  @param Illuminate\Http\Request
     */
    private function validateData($request)
    {
        $validateData = $request->validate([
            'name' => 'required|max:255|string',
            'amount_spend' => 'required|numeric',
            'year_id'=>'required|numeric',
            'date' => 'required|date',

        ]);
        return $validateData;
    }


    /**
     * store an expense for a school
     * 
     * @param Illuminate\Http\Request
     * @return string
     */
    public function store(Request $request)
    {
        $expense = new Expenses();
        $expense->name = nl2br($request->name);
        $expense->amount_spend = $request->amount_spend;
        $expense->user_id = Auth::id();
        $expense->date = $request->date;
        $expense->year_id = $request->year_id;
        $expense->save();
        return redirect()->route('admin.expense.index')->with('success', __('text.word_done'));
    }

    /**
     * show form to edit expense
     * 
     */
    public function edit($id)
    {
        $data['expense'] = Expenses::findOrFail($id);
        $data['title'] = __('text.edit_expense');
        return view('admin.expense.edit')->with($data);
    }

    /**
     * update an expense of a school
     * 
     * @param int $id
     * @param Illuminate\Http\Request
     */
    public function update(Request $request, $id)
    {
        //$this->validateData($request);
        $updated_expense = Expenses::findOrFail($id)->update($request->all());
        return  redirect()->route('admin.expense.index')->with('success', __('text.word_done'));
    }

    /**
     * delete an expense of a school
     * 
     * @param int $id
     */
    public function destroy($id)
    {
        $deleted = Expenses::findOrFail($id)->delete();
        return back()->with('success', __('text.word_done'));
    }

    /**
     * show details of an expense
     * 
     * @param int $id
     */
    public function show($id)
    {
        $data['expense'] = Expenses::findOrFail($id);
        $data['title'] = __('text.expense_details');
        return view('admin.expense.show')->with($data);
    }
}
