<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class JrnlUser extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'jrnl_t_users';

    protected $primaryKey = 'user_id';

    public $timestamps = false;
}
