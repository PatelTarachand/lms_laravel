<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class JrnlAuthorUser extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'jrnl_t_author_users';

    protected $primaryKey = 'author_id';

    public $timestamps = false;
}
