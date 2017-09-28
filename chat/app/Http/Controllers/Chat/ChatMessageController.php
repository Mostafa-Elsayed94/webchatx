<?php

namespace App\Http\Controllers\Chat;

use App\Events\Chat\MessageCreated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Chat\StoreMessageRequest;
use App\Models\Chat\Conversation;
use App\Models\Chat\Message;
use Illuminate\Http\Request;

class ChatMessageController extends Controller
{
    private $attributes = ['body', 'conversation_id'];

    public function index()
    {
        $messages = Message::with('user')->latest()->limit(100)->get();
        return response()->json($messages, 200);
    }

    public function store(StoreMessageRequest $request)
    {
        $inputs = $request->only($this->attributes);
        
        $message = $request->user()->messages()->create($inputs);

        broadcast(new MessageCreated($message, $inputs['conversation_id']))->toOthers();

        return response()->json($message, 200);
    }
}
