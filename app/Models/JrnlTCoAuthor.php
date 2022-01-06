<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JrnlTCoAuthor extends Model
{
    use HasFactory;
    protected $table = 'jrnl_t_co_authors';
    protected $primaryKey = 'co_author_id';
    public $timestamps = false;
}
