<?php

namespace App\Livewire;

use App\Models\Message;
use App\Models\User;
use Livewire\Component;

class Chat extends Component
{
    public User $user;

    public $message = '';

    public $deleteMessage;

    protected $rules = [
        'deleteMessage' => 'int',
    ];

    public $replyingTo = null;

    public $refresh = false;

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
        ->oldest()
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
        if ($this->replyingTo) {
            // Jika sedang membalas, tambahkan referensi ke pesan yang dibalas
            Message::create([
                'from_user_id' => auth()->user()->id,
                'to_user_id' => $this->user->id,
                'message' => $this->message,
                'reply_to_message_id' => $this->replyingTo,
            ]);
            $this->reset(['replyingTo', 'refresh']); // Reset properti yang diperlukan
        } else {
            // Jika tidak sedang membalas, kirim pesan baru
            Message::create([
                'from_user_id' => auth()->user()->id,
                'to_user_id' => $this->user->id,
                'message' => $this->message,
            ]);
            $this->refresh = true; // Set refresh untuk memperbarui tampilan
        }

        $this->reset('message');
    }

    public function deleteMessage($messageId)
    {
        $message = Message::findOrFail($messageId);

        // Hanya pengirim yang bisa menghapus pesan
        if ($message->from_user_id == auth()->user()->id) {
            $message->delete();
        }
    }

    public function updatedDeleteMessage()
    {
        if ($this->deleteMessage) {
            $this->deleteMessage($this->deleteMessage);
        }
    }

    public function startReply($messageId)
    {
        $this->replyingTo = $messageId;
    }

    public function updated()
    {
        if ($this->refresh) {
            $this->refresh = false; // Reset properti refresh setelah memperbarui
            $this->render(); // Memanggil kembali fungsi render untuk memperbarui tampilan
        }
    }
}
