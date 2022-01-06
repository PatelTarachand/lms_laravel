<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JrnlTPublishPaper extends Model
{
    use HasFactory;
    protected $table = 'jrnl_t_published_papers';
    protected $primaryKey = 'pub_paper_id';
    public $timestamps = false;
}
