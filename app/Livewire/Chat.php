<?php

namespace App\Livewire;

use App\Models\Message;
use App\Models\User;
use Livewire\Component;

class Chat extends Component
{
    public User $user;

    public $message = '';

    public function render()
    {
        $messages = Message::where(function ($query) {
            $query->where('from_user_id', auth()->user()->id)
                  ->where('to_user_id', $this->user->id);
        })
        ->orWhere(function ($query) {
            $query->where('from_user_id', $this->user->id)
                  ->where('to_user_id', auth()->user()->id);
        })
        ->get();

        // Mark messages as read
        Message::where('to_user_id', auth()->user()->id)
        ->where('from_user_id', $this->user->id)
        ->whereNull('read_at')
        ->update(['read_at' => now()]);

        return view('livewire.chat', compact('messages'));

    }

    public function sendMessage()
    {
        Message::create([
            'from_user_id' => auth()->user()->id,
            'to_user_id' => $this->user->id,
            'message' => $this->message,
        ]);

        $this->reset('message');
    }
}
