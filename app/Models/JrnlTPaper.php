<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JrnlTPaper extends Model
{
    use HasFactory;
    protected $table = 'jrnl_t_papers';
    protected $primaryKey = 'paper_id';
    public $timestamps = false;
}
