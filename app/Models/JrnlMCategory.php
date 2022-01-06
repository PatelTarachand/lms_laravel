<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JrnlMCategory extends Model
{
    use HasFactory;
    protected $table = 'jrnl_m_categories';

    protected $primaryKey = 'category_id';

    public $timestamps = false;
}
