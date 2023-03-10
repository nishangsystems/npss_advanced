<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Charge extends Model
{
    use HasFactory;

    protected $fillable = ['student_id', 'year_id', 'semester_id', 'type', 'item_id', 'amount', 'financialTransactionId', 'used'];

    protected $table = 'charges';

}
