<div class="flex gap-x-2 items-center">

    {{-- Only show if AUTH is owner of message --}}
    @if ($belongsToAuth)
        <span class="font-bold text-xs dark:text-white/90 dark:font-normal">
            @lang('wirechat::chats.labels.you'):
        </span>
    @elseif(!$belongsToAuth && $group !== null)
        <span class="font-bold text-xs dark:text-white/80 dark:font-normal">
            {{ $lastMessage->sendable?->wirechat_name }}:
        </span>
    @endif

    <p @class([
        'truncate text-sm dark:text-white  gap-2 items-center',
        'font-semibold text-white' =>
            !$isReadByAuth && !$lastMessage?->ownedBy($this->auth),
        'font-normal text-slate-400' =>
            $isReadByAuth && !$lastMessage?->ownedBy($this->auth),
        'font-normal text-slate-400' =>
            $isReadByAuth && $lastMessage?->ownedBy($this->auth),
    ])>
        @php
            // 1. Prepare the Security Service
            $security = app(App\Services\ChatSecurityService::class);
            
            // 2. Get the key from the conversation associated with this last message
            $key = $lastMessage->conversation?->security_key;
            
            // 3. Determine what to display
            if ($lastMessage->body != '') {
                // Decrypt the encrypted gibberish back to plain text
                $displayBody = $security->decrypt($lastMessage->body, $key);
            } else {
                // Handle attachments
                $displayBody = $lastMessage->isAttachment() ? '📎 '.__('wirechat::chats.labels.attachment') : '';
            }
        @endphp

        {{ $displayBody }}
    </p>

    <span class="font-medium px-1 text-xs shrink-0 text-slate-400">
        @if ($lastMessage->created_at->timezone(config('app.timezone', 'Asia/Kuala_Lumpur'))->diffInMinutes(now()) < 1)
          @lang('wirechat::chats.labels.now')
        @else
            {{ $lastMessage->created_at->timezone(config('app.timezone', 'Asia/Kuala_Lumpur'))->shortAbsoluteDiffForHumans() }}
        @endif
    </span>

</div>