@extends('admin.layouts.app')

@section('title', 'Xabar almashinuvi')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-200">
                {{ $telegramUser->display_name }} bilan xabar almashinuvi
            </h1>
            <p class="text-gray-600 dark:text-gray-400">
                {{ $restaurant->name }} - Telegram bot
            </p>
        </div>
        <div class="flex items-center space-x-3">
            <button onclick="markAsRead()" 
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                <i class="fas fa-check"></i>
                <span>O'qildi deb belgilash</span>
            </button>
            <a href="{{ route('admin.bots.users', $restaurant) }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                <i class="fas fa-arrow-left"></i>
                <span>Orqaga</span>
            </a>
        </div>
    </div>

    <!-- User Info -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center space-x-4">
            <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                <span class="text-white font-semibold text-xl">{{ substr($telegramUser->first_name ?? 'U', 0, 1) }}</span>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">{{ $telegramUser->full_name }}</h3>
                <p class="text-gray-600 dark:text-gray-400">{{ $telegramUser->display_name }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-500">ID: {{ $telegramUser->telegram_id }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-500">
                    So'nggi faollik: {{ $telegramUser->last_activity ? $telegramUser->last_activity->diffForHumans() : 'Noma'lum' }}
                </p>
            </div>
            <div class="text-right">
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    <span class="font-medium">{{ $telegramUser->messages()->count() }}</span> xabar
                </div>
                @if($telegramUser->unread_messages_count > 0)
                    <div class="mt-1">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 dark:bg-red-900/20 text-red-800 dark:text-red-200">
                            {{ $telegramUser->unread_messages_count }} o'qilmagan
                        </span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Messages Container -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Xabarlar</h3>
            <span class="text-xs text-gray-500 dark:text-gray-400">Vaqt bo'yicha guruhlangan</span>
        </div>
        
        <style>
            .day-separator { position: sticky; top: 0.25rem; z-index: 5; display: flex; justify-content: center; margin: 0.5rem 0; }
            .day-separator span { background: rgba(107,114,128,0.12); color: #374151; font-size: 0.75rem; padding: 0.25rem 0.6rem; border-radius: 9999px; }
            html.dark .day-separator span { background: rgba(55,65,81,0.6); color: #e5e7eb; }
            .bubble { max-width: 72%; padding: 0.6rem 0.8rem; border-radius: 14px; line-height: 1.35; word-break: break-word; }
            .bubble-out { background: #2563eb; color: #fff; border-bottom-right-radius: 6px; }
            .bubble-in { background: #f3f4f6; color: #111827; border-bottom-left-radius: 6px; }
            html.dark .bubble-in { background: #374151; color: #e5e7eb; }
            .msg-meta { font-size: 0.70rem; opacity: 0.8; margin-top: 0.25rem; }
            .custom-scroll { scrollbar-width: thin; }
            .new-indicator { position: absolute; left: 50%; transform: translateX(-50%); bottom: 72px; background: #2563eb; color:#fff; padding: 6px 10px; border-radius: 9999px; font-size: 0.75rem; box-shadow: 0 6px 18px rgba(37,99,235,.35); display:none; }
            .scroll-bottom-btn { position: absolute; right: 12px; bottom: 64px; width: 38px; height: 38px; border-radius: 9999px; background:#111827; color:#fff; display:none; align-items:center; justify-content:center; box-shadow: 0 8px 20px rgba(0,0,0,.25); }
            html.dark .scroll-bottom-btn { background:#374151; }
        </style>
        
        <div class="relative">
            <div id="messagesContainer" class="custom-scroll max-h-[70vh] overflow-y-auto p-4 space-y-2">
                @php $lastDateKey = null; @endphp
                @if($messages->count() > 0)
                    @foreach($messages as $message)
                        @php
                            $date = $message->created_at->startOfDay();
                            $key = $date->toDateString();
                            $label = $date->isToday() ? 'Bugun' : ($date->isYesterday() ? 'Kecha' : $date->format('d.m.Y'));
                        @endphp
                        @if($key !== $lastDateKey)
                            <div class="day-separator" data-day="{{ $key }}"><span>{{ $label }}</span></div>
                            @php $lastDateKey = $key; @endphp
                        @endif
                        <div class="flex {{ $message->direction === 'incoming' ? 'justify-start' : 'justify-end' }}">
                            <div class="bubble {{ $message->direction === 'incoming' ? 'bubble-in' : 'bubble-out' }}">
                                <div class="text-sm"></div>
                                <div class="msg-meta {{ $message->direction === 'incoming' ? 'text-gray-500 dark:text-gray-300' : 'text-blue-100' }}">
                                    {{ $message->created_at->format('H:i') }}
                                    @if($message->direction === 'incoming' && !$message->is_read)
                                        <span class="ml-1">•</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-10">
                        <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-comments text-gray-400 text-xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-600 dark:text-gray-400 mb-2">Xabarlar yo'q</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-500">Foydalanuvchi bilan hali xabar almashinmagan</p>
                    </div>
                @endif
            </div>
            <div id="newIndicator" class="new-indicator">Yangi xabarlar</div>
            <button id="scrollBottomBtn" class="scroll-bottom-btn" title="Pastga o'tish" onclick="scrollToBottom(true)"><i class="fas fa-arrow-down"></i></button>
        </div>

        <!-- Message Input / Composer -->
        <div class="p-4 border-t border-gray-200 dark:border-gray-700">
            <div class="flex items-end space-x-3">
                <textarea id="messageInput" rows="1"
                          class="flex-1 resize-none px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                          placeholder="Xabar yozing... (Yuborish: Enter, yangi qatordan boshlash: Shift+Enter)"></textarea>
                <button id="sendButton" onclick="sendMessage()" 
                        class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors flex items-center space-x-2 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span id="sendButtonText">Yuborish</span>
                    <div id="sendButtonSpinner" class="hidden">
                        <i class="fas fa-spinner fa-spin"></i>
                    </div>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let lastMessageTime = '{{ $messages->last() ? $messages->last()->created_at->toISOString() : now()->toISOString() }}';
let isPolling = false;

const container = document.getElementById('messagesContainer');
const newIndicator = document.getElementById('newIndicator');
const scrollBtn = document.getElementById('scrollBottomBtn');

function showNotification(message, type = 'info') {
    const el = document.createElement('div');
    el.className = `fixed top-4 right-4 px-4 py-2 rounded-lg text-white z-50 transform transition-all duration-300 translate-x-full ${type==='success'?'bg-green-600':type==='error'?'bg-red-600':type==='warning'?'bg-yellow-600':'bg-blue-600'}`;
    el.textContent = message; document.body.appendChild(el);
    setTimeout(()=>el.classList.remove('translate-x-full'), 50);
    setTimeout(()=>{ el.classList.add('translate-x-full'); setTimeout(()=>el.remove(), 250); }, 4000);
}

function isAtBottom(){ return container.scrollHeight - container.scrollTop - container.clientHeight < 40; }
function scrollToBottom(force=false){ if(force || isAtBottom()){ container.scrollTop = container.scrollHeight; newIndicator.style.display='none'; scrollBtn.style.display='none'; } }

container.addEventListener('scroll', ()=>{
    if(isAtBottom()){ newIndicator.style.display='none'; scrollBtn.style.display='none'; }
    else { scrollBtn.style.display='flex'; }
});

function formatTime(iso){ try{ return new Date(iso).toLocaleTimeString('uz-UZ',{hour:'2-digit',minute:'2-digit'}); }catch(_){ return ''; } }
function dayKey(iso){ const d=new Date(iso); d.setHours(0,0,0,0); return d.toISOString().slice(0,10); }
function dayLabel(iso){ const d=new Date(iso); const t=new Date(); t.setHours(0,0,0,0); const y=new Date(t); y.setDate(t.getDate()-1); d.setHours(0,0,0,0); if(d.getTime()===t.getTime()) return 'Bugun'; if(d.getTime()===y.getTime()) return 'Kecha'; return d.toLocaleDateString('uz-UZ'); }

function ensureDaySeparator(iso){ const key=dayKey(iso); const lastSep = Array.from(container.querySelectorAll('.day-separator')).slice(-1)[0];
    const lastKey = lastSep?.getAttribute('data-day');
    if(key!==lastKey){ const div=document.createElement('div'); div.className='day-separator'; div.setAttribute('data-day', key); div.innerHTML=`<span>${dayLabel(iso)}</span>`; container.appendChild(div); }
}

function addBubble({direction, text, created_at, is_read}){
    ensureDaySeparator(created_at);
    const row = document.createElement('div');
    row.className = 'flex ' + (direction==='incoming' ? 'justify-start' : 'justify-end');
    row.innerHTML = `
        <div class="bubble ${direction==='incoming' ? 'bubble-in' : 'bubble-out'}">
            <div class="text-sm"></div>
            <div class="msg-meta ${direction==='incoming' ? 'text-gray-500 dark:text-gray-300' : 'text-blue-100'}">${formatTime(created_at)}${direction==='incoming' && !is_read ? '<span class="ml-1">•</span>' : ''}</div>
        </div>`;
    row.querySelector('.text-sm').textContent = text || '';
    container.appendChild(row);
}

function addIncomingMessageToContainer(message){
    addBubble({direction:'incoming', text: message.message_text, created_at: message.created_at, is_read: message.is_read});
}
function addOutgoingMessageToContainer(text){
    addBubble({direction:'outgoing', text: text, created_at: new Date().toISOString(), is_read: true});
}

function pollNewMessages(){ if(isPolling) return; isPolling=true;
    fetch(`/admin/bots/{{ $restaurant->id }}/users/{{ $telegramUser->id }}/messages/new?last_message_time=${lastMessageTime}`)
        .then(r=>r.json())
        .then(data=>{
            if(data.success && data.messages.length>0){
                const shouldScroll = isAtBottom();
                data.messages.reverse().forEach(m=>addIncomingMessageToContainer(m));
                const last = data.messages[data.messages.length-1];
                lastMessageTime = new Date(last.created_at).toISOString();
                if(!shouldScroll){ newIndicator.style.display='block'; }
                scrollToBottom(shouldScroll);
            }
        })
        .catch(()=>{})
        .finally(()=>{ isPolling=false; });
}

// Message composer
const msgInput = document.getElementById('messageInput');
const sendBtn = document.getElementById('sendButton');
const sendBtnText = document.getElementById('sendButtonText');
const sendBtnSpinner = document.getElementById('sendButtonSpinner');

function autoResize(){ msgInput.style.height='auto'; msgInput.style.height=Math.min(msgInput.scrollHeight, 160)+'px'; }
msgInput.addEventListener('input', autoResize);
autoResize();

msgInput.addEventListener('keydown', function(e){
    if(e.key==='Enter' && !e.shiftKey){ e.preventDefault(); sendMessage(); }
});

function sendMessage(){
    const text = (msgInput.value||'').trim();
    if(!text) return;
    sendBtn.disabled=true; sendBtnText.classList.add('hidden'); sendBtnSpinner.classList.remove('hidden');
    fetch(`/admin/bots/{{ $restaurant->id }}/users/{{ $telegramUser->id }}/send`, {
        method:'POST',
        headers:{ 'Content-Type':'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
        body: JSON.stringify({ message: text })
    }).then(r=>r.json()).then(data=>{
        if(data.success){ addOutgoingMessageToContainer(text); msgInput.value=''; autoResize(); showNotification('✅ '+data.message,'success'); scrollToBottom(true); }
        else { showNotification('❌ '+(data.message||'Xatolik'),'error'); }
    }).catch(()=> showNotification('Xabar yuborishda xatolik','error'))
      .finally(()=>{ sendBtn.disabled=false; sendBtnText.classList.remove('hidden'); sendBtnSpinner.classList.add('hidden'); });
}

function markAsRead(){
    fetch(`/admin/bots/{{ $restaurant->id }}/users/{{ $telegramUser->id }}/read`, { method:'POST', headers:{ 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') } })
        .then(r=>r.json()).then(data=>{ if(data.success){ showNotification('✅ '+data.message,'success'); document.querySelectorAll('.bubble-in .msg-meta span').forEach(el=>el.remove()); } else { showNotification('❌ '+data.message,'error'); } })
        .catch(()=> showNotification('Xatolik yuz berdi','error'));
}

// Start polling every 3s
setInterval(pollNewMessages, 3000);

document.addEventListener('DOMContentLoaded', ()=>{ scrollToBottom(true); });
</script>
@endsection 