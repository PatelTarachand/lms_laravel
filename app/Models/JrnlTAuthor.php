<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JrnlTAuthor extends Model
{
    use HasFactory;
    protected $table = 'jrnl_t_authors';
    protected $primaryKey = 'author_id';
    public $timestamps = false;
}
