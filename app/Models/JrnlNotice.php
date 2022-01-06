<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JrnlNotice extends Model
{
    use HasFactory;
    protected $table = 'jrnl_t_notifications';
    protected $primaryKey = 'notice_id';
}
