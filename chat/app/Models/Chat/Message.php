<?php

namespace App\Models\Chat;

use App\Models\Chat\Conversation;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = ['body', 'conversation_id'];

    protected $appends = ['selfOwned'];

    public function getBodyAttribute()
    {
        return decrypt($this->attributes['body']);
    }

    public function setBodyAttribute($value)
    {
        $this->attributes['body'] = encrypt($value);
    }

    public function getSelfOwnedAttribute()
    {
        return $this->user_id === auth()->user()->id;
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
