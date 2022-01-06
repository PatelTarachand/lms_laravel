<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JrnlTVolume extends Model
{
    use HasFactory;
    protected $table = 'jrnl_t_volumes';
    protected $primaryKey = 'volume_id';
    public $timestamps = false;
}
