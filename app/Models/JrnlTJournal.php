<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JrnlTJournal extends Model
{
    use HasFactory;
    protected $table = 'jrnl_t_journals';
    protected $primaryKey = 'journal_id';
    public $timestamps = false;
}
