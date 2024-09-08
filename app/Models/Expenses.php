<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expenses extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'amount_spend',
        'date',
        'year_id'
    ];
    protected $connection = 'mysql';

    protected $dates = ['date', 'created_at', 'updated_at'];
}
