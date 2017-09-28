<?php

namespace App\Models\Chat;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = ['from_user_id', 'to_user_id'];

    public $timestamps = false;
}
