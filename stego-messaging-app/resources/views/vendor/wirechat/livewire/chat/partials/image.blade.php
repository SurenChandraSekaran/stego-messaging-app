@php
   $isSameAsNext     = ($message?->sendable_id === $nextMessage?->sendable_id) && ($message?->sendable_type === $nextMessage?->sendable_type);
   $isNotSameAsNext  = !$isSameAsNext;
   $isSameAsPrevious = ($message?->sendable_id === $previousMessage?->sendable_id) && ($message?->sendable_type === $previousMessage?->sendable_type);
   $isNotSameAsPrevious = !$isSameAsPrevious;

   $extension        = strtolower(pathinfo($attachment?->file_path, PATHINFO_EXTENSION));
   $isStegoCandidate = in_array($extension, ['png', 'jpg', 'jpeg']);
   $isPng            = $extension === 'png';
   // stego_ prefix is set by CustomChat::sendMessage() — confirms the image
   // went through the LSB embed pipeline. Plain uploads have no such prefix.
   $isStego          = $isPng && str_starts_with(basename($attachment?->file_path ?? ''), 'stego_');
@endphp

<div class="relative group">

    {{-- Image (all corner-radius logic preserved exactly) --}}
    <img @class([
        'max-w-max h-[200px] min-h-[210px] bg-[var(--wc-light-secondary)] dark:bg-[var(--wc-dark-secondary)] object-scale-down grow-0 shrink overflow-hidden rounded-3xl',
        'rounded-br-md rounded-tr-2xl' => ($isSameAsNext && $isNotSameAsPrevious && $belongsToAuth),
        'rounded-r-md'                 => ($isSameAsPrevious && $belongsToAuth),
        'rounded-br-xl rounded-r-xl'   => ($isNotSameAsPrevious && $isNotSameAsNext && $belongsToAuth),
        'rounded-br-2xl'               => ($isNotSameAsNext && $belongsToAuth),
        'rounded-bl-md rounded-tl-2xl' => ($isSameAsNext && $isNotSameAsPrevious && !$belongsToAuth),
        'rounded-l-md'                 => ($isSameAsPrevious && !$belongsToAuth),
        'rounded-bl-xl rounded-l-xl'   => ($isNotSameAsPrevious && $isNotSameAsNext && !$belongsToAuth),
        'rounded-bl-2xl'               => ($isNotSameAsNext && !$belongsToAuth),
    ])
    loading="lazy"
    src="{{ $attachment?->url }}"
    alt="{{ __('wirechat::chat.labels.attachment') }}">

    {{-- ── LSB Payload Badge (stego images only) ──────────────────────── --}}
    @if($message->body === 'STEGO_ACTIVE')
        <div class="absolute bottom-2 inset-x-2 flex justify-center pointer-events-none z-10">
            <span class="font-mono text-[8px] tracking-widest uppercase
                         text-emerald-400/80 bg-slate-950/85 border border-emerald-900/50
                         px-2 py-0.5 rounded-md backdrop-blur-sm whitespace-nowrap select-none">
                
            </span>
        </div>
    @endif

    {{-- ── Stego Extraction Overlay (wire:click binding preserved) ─────── --}}
    @if($isStegoCandidate)
        <button
            type="button"
            wire:click="extractSecret('{{ $attachment->id }}')"
            wire:loading.attr="disabled"
            class="absolute inset-0 flex items-center justify-center
                   bg-slate-950/55 backdrop-blur-[2px]
                   opacity-0 group-hover:opacity-100
                   transition-all duration-200 rounded-3xl"
        >
            {{-- Loading state: monospace decryption indicator --}}
            <div wire:loading wire:target="extractSecret('{{ $attachment->id }}')">
                <div class="flex items-center gap-2.5 bg-slate-900/95 border border-emerald-800/50
                            px-4 py-2.5 rounded-xl shadow-[0_0_20px_rgba(52,211,153,0.15)]">
                    <svg class="animate-spin h-3.5 w-3.5 text-emerald-400 shrink-0"
                         xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    <span class="font-mono text-[11px] text-emerald-400 tracking-widest">DECRYPTING LSB…</span>
                </div>
            </div>

            {{-- Idle state: tactical scan button --}}
            <div wire:loading.remove wire:target="extractSecret('{{ $attachment->id }}')">
                <div class="flex flex-col items-center gap-2">
                    <div class="flex items-center gap-2.5 bg-slate-900/95 border border-emerald-500/30
                                px-4 py-2.5 rounded-xl
                                shadow-[0_0_24px_rgba(52,211,153,0.18)]
                                hover:border-emerald-400/50 hover:shadow-[0_0_32px_rgba(52,211,153,0.28)]
                                transition-all duration-150">
                        {{-- Eye / scan icon --}}
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <span class="font-mono text-[11px] text-emerald-400 tracking-widest uppercase">Scan Payload</span>
                    </div>
                    <span class="font-mono text-[9px] text-emerald-600/70 tracking-wider">LSB channel active</span>
                </div>
            </div>
        </button>
    @endif

</div>

{{-- Transmission timestamp — matches text bubble style --}}
<span @class([
    'text-[10px] block mt-1 tabular-nums text-right',
    'text-blue-300/50' => $belongsToAuth,
    'text-slate-500'   => !$belongsToAuth,
])>
    {{ $message->created_at->timezone(config('app.timezone', 'Asia/Kuala_Lumpur'))->format('h:i A') }}
</span>
