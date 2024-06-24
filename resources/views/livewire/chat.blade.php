<div>
    <div>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div wire:poll.5000ms>
                            @foreach ($messages as $message)
                                <div class="chat @if($message->from_user_id == auth()->user()->id) chat-end @else chat-start @endif">
                                    <div class="chat-image avatar">
                                        <div class="w-10 rounded-full">
                                            <img alt="Avatar"
                                                 src="https://img.daisyui.com/images/stock/photo-1534528741775-53994a69daeb.jpg" />
                                        </div>
                                    </div>
                                    <div class="chat-header">
                                        {{ $message->fromUser->name }}
                                        <time class="text-xs opacity-50">{{ $message->created_at->diffForHumans() }}</time>
                                    </div>
                                    <div class="chat-bubble">{{ $message->message }}</div>
                                    <div class="chat-footer opacity-50">
                                        @if($message->read_at)
                                            Read
                                        @else
                                            Delivered
                                        @endif
                                    </div>
                                    @if($message->from_user_id == auth()->user()->id)
                                        <button type="button" onclick="confirmDelete({{ $message->id }})" class="btn btn-danger btn-sm">Hapus</button>
                                    @else
                                        <button type="button" wire:click="startReply({{ $message->id }})" class="btn btn-primary btn-sm">Reply</button>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        @if ($replyingTo)
                            <div class="chat chat-reply-preview">
                                <div class="chat-image avatar">
                                    <div class="w-10 rounded-full">
                                        <img alt="Avatar"
                                            src="https://img.daisyui.com/images/stock/photo-1534528741775-53994a69daeb.jpg" />
                                    </div>
                                </div>
                                <div class="chat-header">
                                    You are replying to: {{ $messages->firstWhere('id', $replyingTo)->fromUser->name }}
                                </div>
                                <div class="chat-bubble">{{ $messages->firstWhere('id', $replyingTo)->message }}</div>
                            </div>
                        @endif
                        <div class="form-control">
                            <form wire:submit.prevent="sendMessage">
                                <textarea class="textarea textarea-bordered w-full" placeholder="send your message..." wire:model='message'></textarea>
                                <button type="submit" class="btn btn-primary">Send</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('livewire:load', function () {
        var chatContainer = document.getElementById('chat-container');
        chatContainer.scrollTop = chatContainer.scrollHeight;
    });
    document.addEventListener('livewire:update', function () {
        var chatContainer = document.getElementById('chat-container');
        chatContainer.scrollTop = chatContainer.scrollHeight;
    });
</script>

<script>
    function confirmDelete(messageId) {
    console.log('Attempting to delete message with ID:', messageId); // Debugging output
    if (confirm('Apakah Anda yakin ingin menghapus pesan ini?')) {
        @this.set('deleteMessage', messageId);
        console.log('Delete event emitted for message ID:', messageId); // Debugging output
    }
}
</script>
