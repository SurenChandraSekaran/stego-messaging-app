<div class="grid grid-cols-1 sm:grid-cols-3 gap-4" wire:poll.8s>

    {{-- Card 1: Secure Channels --}}
    <div class="bg-slate-900 border border-slate-800/80 rounded-xl p-5 flex items-start gap-4 hover:border-slate-700/80 transition-colors duration-200">
        <div class="shrink-0 flex items-center justify-center w-11 h-11 rounded-lg bg-emerald-500/10 border border-emerald-500/20">
            <svg class="w-5 h-5 text-emerald-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244" />
            </svg>
        </div>
        <div class="min-w-0">
            <p class="text-[11px] font-medium text-slate-400 uppercase tracking-wider mb-0.5">Secure Channels</p>
            <p class="text-3xl font-extrabold text-white tabular-nums leading-none">{{ $secureChannels }}</p>
            <p class="text-xs text-slate-500 mt-1.5">Verified handshakes established</p>
        </div>
    </div>

    {{-- Card 2: Images Encrypted --}}
    <div class="bg-slate-900 border border-slate-800/80 rounded-xl p-5 flex items-start gap-4 hover:border-slate-700/80 transition-colors duration-200">
        <div class="shrink-0 flex items-center justify-center w-11 h-11 rounded-lg bg-blue-500/10 border border-blue-500/20">
            <svg class="w-5 h-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
            </svg>
        </div>
        <div class="min-w-0">
            <p class="text-[11px] font-medium text-slate-400 uppercase tracking-wider mb-0.5">Images Encrypted</p>
            <p class="text-3xl font-extrabold text-white tabular-nums leading-none">{{ $imagesTransmitted }}</p>
            <p class="text-xs text-slate-500 mt-1.5">LSB steganographic payloads sent</p>
        </div>
    </div>

    {{-- Card 3: Payload Integrity (static) --}}
    <div class="bg-slate-900 border border-slate-800/80 rounded-xl p-5 flex items-start gap-4 hover:border-slate-700/80 transition-colors duration-200">
        <div class="shrink-0 flex items-center justify-center w-11 h-11 rounded-lg bg-violet-500/10 border border-violet-500/20">
            <svg class="w-5 h-5 text-violet-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
            </svg>
        </div>
        <div class="min-w-0">
            <p class="text-[11px] font-medium text-slate-400 uppercase tracking-wider mb-0.5">Payload Integrity</p>
            <p class="text-3xl font-extrabold text-white leading-none">
                100<span class="text-xl text-violet-400 font-bold">%</span>
            </p>
            <p class="text-xs text-slate-500 mt-1.5">AES-256-CBC · No integrity failures</p>
        </div>
    </div>

</div>
