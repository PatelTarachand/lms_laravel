<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentRemarks extends Model
{
    use HasFactory;
    protected $table = 't_student_remark';
    public $timestamps = false;
}
