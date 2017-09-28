<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Models\Chat\Conversation;
use App\Models\Chat\Message;
use Illuminate\Http\Request;

class ConversationsController extends Controller
{
    public function __invoke($from, $to)
    {
        $conversation = Conversation::where(function($query) use ($from, $to){
            return $query->where('from_user_id', $from)->where('to_user_id', $to);
        })->orWhere(function($query) use ($from, $to){
            return $query->where('from_user_id', $to)->where('to_user_id', $from);
        })->first();

        if ($conversation) {
            $response['conversation_id'] = $conversation->id;
            $response['messages'] = Message::with('user')->where('conversation_id', $response['conversation_id'])->latest()->limit(100)->get()->toArray();
        } else {
            $response['conversation_id'] = Conversation::create(['from_user_id' => $from, 'to_user_id' => $to])->id;
            $response['messages'] = [];
        }

        return response()->json($response, 200);
    }
}
