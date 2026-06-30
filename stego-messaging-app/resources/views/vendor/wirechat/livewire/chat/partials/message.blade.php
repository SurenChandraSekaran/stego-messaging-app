@use('Wirechat\Wirechat\Facades\Wirechat')

@php
   $isSameAsNext = ($message?->sendable_id === $nextMessage?->sendable_id) && ($message?->sendable_type === $nextMessage?->sendable_type);
   $isNotSameAsNext = !$isSameAsNext;

   $isSameAsPrevious = ($message?->sendable_id === $previousMessage?->sendable_id) && ($message?->sendable_type === $previousMessage?->sendable_type);
   $isNotSameAsPrevious = !$isSameAsPrevious;

   // Decrypt body using the conversation's AES-256-CBC key
   $security = app(App\Services\ChatSecurityService::class);
   $key      = $message->conversation?->security_key;
   $decryptedBody = ($message->body != '') ? $security->decrypt($message->body, $key) : '';
@endphp

{{-- @style removed: Tailwind bubble classes handle background directly --}}
<div
@class([
    'flex flex-wrap max-w-fit text-[15px] rounded-xl p-2.5 flex flex-col',

    {{-- Outgoing: deep navy blue tint — premium dark --}}
    'bg-blue-950/60 border border-blue-900/40 text-white'     => $belongsToAuth,

    {{-- Incoming: elevated slate — legible against slate-950 chat bg --}}
    'bg-slate-800/50 border border-slate-700/40 text-white'   => !$belongsToAuth,

    {{-- Outgoing corner shapes --}}
    'rounded-br-md rounded-tr-2xl' => ($isSameAsNext && $isNotSameAsPrevious && $belongsToAuth),
    'rounded-r-md'                 => ($isSameAsPrevious && $belongsToAuth),
    'rounded-br-xl rounded-r-xl'   => ($isNotSameAsPrevious && $isNotSameAsNext && $belongsToAuth),
    'rounded-br-2xl'               => ($isNotSameAsNext && $belongsToAuth),

    {{-- Incoming corner shapes --}}
    'rounded-bl-md rounded-tl-2xl' => ($isSameAsNext && $isNotSameAsPrevious && !$belongsToAuth),
    'rounded-l-md'                 => ($isSameAsPrevious && !$belongsToAuth),
    'rounded-bl-xl rounded-l-xl'   => ($isNotSameAsPrevious && $isNotSameAsNext && !$belongsToAuth),
    'rounded-bl-2xl'               => ($isNotSameAsNext && !$belongsToAuth),
])
>

{{-- Group sender name (incoming only) --}}
@if (!$belongsToAuth && $isGroup)
    <div @class([
        'shrink-0 text-xs font-semibold text-blue-400 mb-0.5',
        'hidden' => $isSameAsPrevious,
    ])>
        {{ $message?->sendable?->wirechat_name }}
    </div>
@endif

{{-- Decrypted message body --}}
<pre
    class="whitespace-pre-line tracking-normal break-all text-sm md:text-[15px] text-white lg:tracking-normal"
    style="font-family: inherit;"
>{{ $decryptedBody }}</pre>

{{-- Timestamp --}}
<span @class([
    'text-[10px] ml-auto mt-0.5 tabular-nums',
    'text-blue-300/50'  => $belongsToAuth,
    'text-slate-500'    => !$belongsToAuth,
])>
    {{ $message?->created_at->timezone(config('app.timezone', 'Asia/Kuala_Lumpur'))->format('h:i A') }}
</span>

</div>
