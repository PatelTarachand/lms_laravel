<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JrnlMPaperType extends Model
{
    use HasFactory;
    protected $table = 'jrnl_m_paper_types';

    protected $primaryKey = 'paper_type_id';

    public $timestamps = false;
}
