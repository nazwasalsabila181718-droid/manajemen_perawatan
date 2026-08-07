@extends('layouts.app')

@section('title', 'Chat')
@section('page_title', 'Chat')
@section('page_subtitle', 'Komunikasi langsung antara driver dan manager.')

@section('content')
<div class="container-fluid p-0">
    <div class="card-premium p-0" style="height: 70vh; overflow: hidden;">
        <div class="d-flex h-100">

            @if (isset($drivers))
            <!-- Sidebar daftar driver (khusus manager) -->
            <div class="border-end" style="width: 300px; overflow-y: auto;">
                <div class="p-3 border-bottom fw-bold">Daftar Driver</div>
                @forelse ($drivers as $driver)
                    <a href="{{ route('chat.index', ['driver_id' => $driver->id]) }}"
                       class="d-flex justify-content-between align-items-center p-3 border-bottom text-decoration-none text-dark {{ optional($otherUser)->id === $driver->id ? 'bg-light' : '' }}">
                        <div>
                            <div class="fw-semibold">{{ $driver->name }}</div>
                            <div class="text-muted small text-truncate" style="max-width: 180px;">
                                {{ optional($driver->last_message)->message ?? 'Belum ada pesan' }}
                            </div>
                        </div>
                        @if ($driver->unread_count > 0)
                            <span class="badge bg-danger rounded-pill">{{ $driver->unread_count }}</span>
                        @endif
                    </a>
                @empty
                    <div class="p-3 text-muted small">Belum ada driver terdaftar.</div>
                @endforelse
            </div>
            @endif

            <!-- Jendela chat -->
            <div class="d-flex flex-column flex-grow-1">
                @if (!empty($noManager))
                    <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                        Belum ada akun manager terdaftar. Hubungi administrator.
                    </div>
                @elseif (isset($drivers) && !$otherUser)
                    <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                        Pilih driver di sebelah kiri untuk mulai chat.
                    </div>
                @else
                    <div class="p-3 border-bottom fw-bold">
                        {{ $otherUser->name }}
                        <span class="badge bg-secondary-subtle text-secondary ms-2" style="font-size: 10px;">
                            {{ ucfirst($otherUser->role) }}
                        </span>
                    </div>

                    <div id="chat-window" class="flex-grow-1 p-3" style="overflow-y: auto; background: #f8f9fb;">
                        @foreach ($messages as $msg)
                            <div class="d-flex mb-2 {{ $msg->sender_id === auth()->id() ? 'justify-content-end' : 'justify-content-start' }}">
                                <div class="px-3 py-2 rounded-3 {{ $msg->sender_id === auth()->id() ? 'bg-primary text-white' : 'bg-white border' }}" style="max-width: 65%;">
                                    <div>{{ $msg->message }}</div>
                                    <div class="small {{ $msg->sender_id === auth()->id() ? 'text-white-50' : 'text-muted' }}" style="font-size: 10px;">
                                        {{ $msg->created_at->format('H:i') }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <form id="chat-form" class="p-3 border-top d-flex gap-2">
                        @csrf
                        <input type="hidden" id="receiver_id" value="{{ $otherUser->id }}">
                        <input type="text" id="message_input" class="form-control-premium flex-grow-1"
                               placeholder="Ketik pesan..." autocomplete="off" required>
                        <button type="submit" class="btn-premium primary">
                            <i class="bi bi-send-fill"></i>
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@if (empty($noManager) && $otherUser)
<script>
document.addEventListener('DOMContentLoaded', function () {
    const chatWindow = document.getElementById('chat-window');
    const chatForm = document.getElementById('chat-form');
    const messageInput = document.getElementById('message_input');
    const receiverId = document.getElementById('receiver_id').value;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content
        || document.querySelector('input[name="_token"]').value;

    let lastMessageId = {{ $messages->last()->id ?? 0 }};

    function scrollToBottom() {
        chatWindow.scrollTop = chatWindow.scrollHeight;
    }
    scrollToBottom();

    function appendMessage(msg) {
        const wrapper = document.createElement('div');
        wrapper.className = 'd-flex mb-2 ' + (msg.is_mine ? 'justify-content-end' : 'justify-content-start');

        const bubble = document.createElement('div');
        bubble.className = 'px-3 py-2 rounded-3 ' + (msg.is_mine ? 'bg-primary text-white' : 'bg-white border');
        bubble.style.maxWidth = '65%';

        const textDiv = document.createElement('div');
        textDiv.textContent = msg.message;

        const timeDiv = document.createElement('div');
        timeDiv.className = 'small ' + (msg.is_mine ? 'text-white-50' : 'text-muted');
        timeDiv.style.fontSize = '10px';
        timeDiv.textContent = msg.created_at;

        bubble.appendChild(textDiv);
        bubble.appendChild(timeDiv);
        wrapper.appendChild(bubble);
        chatWindow.appendChild(wrapper);
    }

    chatForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const text = messageInput.value.trim();
        if (!text) return;

        fetch('{{ route("chat.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({ receiver_id: receiverId, message: text }),
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                appendMessage({
                    ...data.message,
                    is_mine: true,
                });
                lastMessageId = data.message.id;
                messageInput.value = '';
                scrollToBottom();
            }
        });
    });

    setInterval(function () {
        fetch(`{{ route('chat.poll') }}?with=${receiverId}&after_id=${lastMessageId}`)
            .then(res => res.json())
            .then(data => {
                if (data.messages && data.messages.length > 0) {
                    data.messages.forEach(msg => {
                        if (!msg.is_mine) {
                            appendMessage(msg);
                        }
                        lastMessageId = Math.max(lastMessageId, msg.id);
                    });
                    scrollToBottom();
                }
            });
    }, 3000);
});
</script>
@endif
@endsection