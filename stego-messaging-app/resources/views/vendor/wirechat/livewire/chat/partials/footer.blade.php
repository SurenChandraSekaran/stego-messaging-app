@php

    $hasEmojiPicker= $this->panel()->hasEmojiPicker();
    $floatingEmojiPicker=$this->panel()->emojiPickerPosition()===\Wirechat\Wirechat\Support\Enums\EmojiPickerPosition::Floating;
@endphp
<footer class="shrink-0 h-auto relative   sticky bottom-0 mt-auto">

    {{-- Check if group allows :sending messages --}}
    @if ($conversation->isGroup() && !$conversation->group?->allowsMembersToSendMessages() && !$authParticipant->isAdmin())
        <div
            class="dark:bg-[var(--wc-dark-secondary)]  bg-[var(--wc-light-secondary)] w-full text-center text-gray-600 dark:text-gray-200 justify-center text-sm flex py-4 ">
            Only admins can send messages
        </div>
    @else
        <div id="chat-footer" x-data="{ 'openEmojiPicker': false, isUploading: false, init() {
             // Turn off the loading bar once files successfully hit the backend preview collections
             this.$watch('$wire.media', value => { if (value && value.length > 0) this.isUploading = false; });
             this.$watch('$wire.files', value => { if (value && value.length > 0) this.isUploading = false; });
         }}"
            x-on:change="if ($event.target.type === 'file' && $event.target.files.length > 0) isUploading = true;"
            x-on:wirechat-toast.window="isUploading = false"
            class=" px-3 md:px-1 border-t  shadow-sm bg-[var(--wc-light-primary)]   dark:bg-[var(--wc-dark-secondary)]   z-50   border-[var(--wc-light-border)] dark:border-[var(--wc-dark-primary)] flex flex-col gap-3 items-center  w-full   mx-auto">

            {{-- Emoji section , we put it seperate to avoid interfering as overlay for form when opened --}}
            @if($hasEmojiPicker)
            {{--    If emoji picker is floading -wrap the emoji picke element into a teleport blade in order to allow proper render --}}
            {{--  --START-- TELEPORT --}}
            @if($floatingEmojiPicker) @teleport('body') @endif
            {{--  --END-- TELEPORT --}}
                <section wire:ignore  x-cloak x-show="openEmojiPicker"
                         @click.outside="openEmojiPicker=false"

                    @if($floatingEmojiPicker)
                     x-anchor.top.offset.20="document.getElementById('emojipickerbutton')"
                     x-transition:enter="transition ease-out duration-180 transform"
                     x-transition:enter-start="opacity-0 translate-y-4 scale-90"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-180 transform"
                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 scale-90"
                         dusk="floating-emojipicker"

                         @else
                    x-transition:enter="transition  ease-out duration-180 transform"
                    x-transition:enter-start=" translate-y-full" x-transition:enter-end=" translate-y-0"
                    x-transition:leave="transition ease-in duration-180 transform" x-transition:leave-start=" translate-y-0"
                    x-transition:leave-end="translate-y-full"
                         dusk="docked-emojipicker"
                    @endif
                    @class([
                            "max-w-lg h-[450px] xl:h-[520px] z-50 shadow-sm  bg-[var(--wc-light-primary)] dark:bg-[var(--wc-dark-primary)] border border-[var(--wc-light-border)] dark:border-[var(--wc-dark-border)] rounded-xl"=>$floatingEmojiPicker,
                            "min-w-full  border-b  h-96 border-[var(--wc-light-primary)] dark:border-[var(--wc-dark-primary)] "=>!$floatingEmojiPicker,
                            "w-full flex hidden sm:flex  inset-x-auto py-2 sm:px-4 py-1.5  "])>

                    <emoji-picker  dusk="emoji-picker" style="width: 100%"
                        class=" flex w-full h-full rounded-xl"></emoji-picker>

                    {{-- Clip-Arrow--}}
                    <div
                        style="
                            position: absolute;
                            top: -6px;  /* place above picker box */
                            left: 50%;  /* center horizontally */
                            transform: translateX(-50%);
                            width: 12px;
                            height: 6px;
                            z-index: 50;
                            background: transparent;
                            clip-path: polygon(50% 0%, 0% 100%, 100% 100%);
                            /* You can also use an SVG instead of clip-path */
                        "
                    ></div>
                </section>
            {{--  --START-- TELEPORT --}}
            @if($floatingEmojiPicker) @endteleport @endif
            {{--  --END-- TELEPORT --}}
            @endif

            {{-- form and detail section  --}}
            <section
                class="  sm:px-4 py-3.5   z-50     flex flex-col gap-3 items-center  w-full mx-auto">
                <div x-show="isUploading" x-transition class="w-full px-4 pt-2" x-cloak>
                    <div class="flex justify-between items-center mb-1 text-xs font-medium text-blue-600 dark:text-blue-400">
                        <span class="flex items-center gap-1.5">
                            <svg class="animate-spin h-3.5 w-3.5 text-blue-600 dark:text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Uploading and encrypting media asset...
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5 overflow-hidden relative">
                        <div class="bg-blue-600 h-1.5 rounded-full absolute top-0 left-0 bottom-0 animate-[pulse_1s_infinite] w-full origin-left"></div>
                    </div>
                </div>
                {{-- Media preview section --}}
                <section x-show="$wire.media.length>0 ||$wire.files.length>0" x-cloak
                    class="  flex flex-col w-full gap-3" wire:loading.class="animate-pulse" wire:target="sendMessage">

                    
                    @if (count($media) > 0)
                        <div x-data="attachments('media')">
                            {{-- todo: Implement error handling fromserver during file uploads --}}
                            {{--
                                @error('media')
                            <span class="flex text-sm text-red-500 pb-2 bg-gray-100 p-2 w-full justify-between">
                                    {{$message}}
                                    <button @click="$wire.resetAttachmentErrors()">X</button>
                            </span>
                            @enderror --}}
                                                {{-- todo:Show progress when uploading files --}}
                                                {{-- <div  x-show="isUploading"  class="w-full">
                                    <progress class="w-full h-1 rounded-lg" max="100" x-bind:value="progress"></progress>
                                </div> --}}
                            <section
                                class=" flex  overflow-x-scroll  ms-overflow-style-none items-center w-full col-span-12 py-2 gap-5 "
                                style=" scrollbar-width: none; -ms-overflow-style: none;">


                                {{-- Loop through media for preview --}}
                                @foreach ($media as $key => $mediaItem)
                                @if (str()->startsWith($mediaItem->getMimeType(), 'image/'))
                                    <div class="relative h-24 sm:h-36 aspect-4/3 ">
                                        
                                        {{-- Delete image --}}
                                        <button wire:loading.attr="disabled"
                                            class="disabled:cursor-progress absolute -top-2 -right-2 z-10 dark:text-gray-50"
                                            @click="removeUpload('{{ $mediaItem->getFilename() }}'); $dispatch('reset-stegano');">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle" viewBox="0 0 16 16">
                                                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                                                <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708" />
                                            </svg>
                                        </button>
                                        
                                        <img class="h-full w-full rounded-lg object-scale-down"
                                            src="{{ $mediaItem->temporaryUrl() }}" alt="mediaItem">

                                    </div>
                                @endif

                                    {{-- Attachemnt is Video/ --}}
                                    @if (str()->startsWith($mediaItem->getMimeType(), 'video/'))
                                        <div class="relative h-24 sm:h-36 ">
                                            <button wire:loading.attr="disabled"
                                                class="disabled:cursor-progress absolute -top-2 -right-2  z-10 dark:text-gray-50"
                                                @click="removeUpload('{{ $mediaItem->getFilename() }}')">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" class="bi bi-x-circle" viewBox="0 0 16 16">
                                                    <path
                                                        d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                                                    <path
                                                        d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708" />
                                                </svg>
                                            </button>
                                            <x-wirechat::video height="h-24 sm:h-36 " :cover="false"
                                                :showToggleSound="false" :source="$mediaItem->temporaryUrl()" />
                                        </div>
                                    @endif
                                @endforeach


                                <label wire:loading.class="cursor-progress"
                                    x-show="!$wire.steganoMode"
                                    class="shrink-0 cursor-pointer relative w-16 h-14 rounded-lg  bg-[var(--wc-light-secondary)] dark:bg-[var(--wc-dark-primary)]   hover:bg-[var(--wc-light-primary)] dark:hover:bg-[var(--wc-dark-primary)] border border-[var(--wc-light-secondary)] dark:border-[var(--wc-dark-secondary)]  flex text-center justify-center ">
                                    <input wire:loading.attr="disabled"
                                        @change="handleFileSelect(event,{{ count($media) }})" type="file"
                                        :multiple="!$wire.steganoMode"
                                           accept="{{ collect($this->panel()->getMediaMimes())->map(fn($ext) => '.' . $ext)->implode(',') }}"
                                           class="sr-only">
                                    <span class="m-auto ">

                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                            class="w-7 h-7 text-gray-600 dark:text-gray-100">
                                            <path fill-rule="evenodd"
                                                d="M1.5 6a2.25 2.25 0 0 1 2.25-2.25h16.5A2.25 2.25 0 0 1 22.5 6v12a2.25 2.25 0 0 1-2.25 2.25H3.75A2.25 2.25 0 0 1 1.5 18V6ZM3 16.06V18c0 .414.336.75.75.75h16.5A.75.75 0 0 0 21 18v-1.94l-2.69-2.689a1.5 1.5 0 0 0-2.12 0l-.88.879.97.97a.75.75 0 1 1-1.06 1.06l-5.16-5.159a1.5 1.5 0 0 0-2.12 0L3 16.061Zm10.125-7.81a1.125 1.125 0 1 1 2.25 0 1.125 1.125 0 0 1-2.25 0Z"
                                                clip-rule="evenodd" />
                                        </svg>

                                    </span>
                                </label>

                            </section>
                        </div>

                    @endif
                    {{-- ----------------------- --}}
                    {{-- Files preview section --}}
                    @if (count($files) > 0)
                        <section x-data="attachments('files')"
                            class="flex  overflow-x-scroll  ms-overflow-style-none items-center w-full col-span-12 py-2 gap-5 "
                            style=" scrollbar-width: none; -ms-overflow-style: none;">

                            {{-- Loop through files for preview --}}
                            @foreach ($files as $key => $file)
                                <div class="relative shrink-0">
                                    {{-- Delete file button --}}
                                    <button wire:loading.attr="disabled"
                                        class="disabled:cursor-progress absolute -top-2 -right-2  z-10"
                                        @click="removeUpload('{{ $file->getFilename() }}')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            fill="currentColor"
                                            class="bi bi-x-circle dark:text-white dark:hover:text-red-500 hover:text-red-500 transition-colors"
                                            viewBox="0 0 16 16">
                                            <path
                                                d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                                            <path
                                                d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708" />
                                        </svg>
                                    </button>

                                    {{-- File details --}}
                                    <div
                                        class="flex items-center group overflow-hidden bg-[var(--wc-light-primary)] dark:bg-[var(--wc-dark-primary)]   hover:border-[var(--wc-light-primary)] dark:hover:border-[var(--wc-dark-primary)] border border-[var(--wc-light-secondary)] dark:border-[var(--wc-dark-secondary)] rounded-xl">
                                        <span class=" p-2">
                                            {{-- document svg:HI --}}
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                fill="currentColor" class="w-8 h-8 text-gray-500 dark:text-gray-100">
                                                <path
                                                    d="M5.625 1.5c-1.036 0-1.875.84-1.875 1.875v17.25c0 1.035.84 1.875 1.875 1.875h12.75c1.035 0 1.875-.84 1.875-1.875V12.75A3.75 3.75 0 0 0 16.5 9h-1.875a1.875 1.875 0 0 1-1.875-1.875V5.25A3.75 3.75 0 0 0 9 1.5H5.625Z" />
                                                <path
                                                    d="M12.971 1.816A5.23 5.23 0 0 1 14.25 5.25v1.875c0 .207.168.375.375.375H16.5a5.23 5.23 0 0 1 3.434 1.279 9.768 9.768 0 0 0-6.963-6.963Z" />
                                            </svg>
                                        </span>

                                        <p class="mt-auto  p-2 text-gray-600 dark:text-gray-100 text-sm">
                                            {{ $file->getClientOriginalName() }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach

                            {{-- Add more files --}}
                            {{-- TODO @if "( count($media)< $MAXFILES )" to hide upload button when maz files exceeded --}}
                            <label wire:loading.class="cursor-progress"
                                class="cursor-pointer shrink-0 relative w-16 h-14 rounded-lg bg-[var(--wc-light-primary)] dark:bg-[var(--wc-dark-primary)]   hover:border-[var(--wc-light-primary)] dark:hover:border-[var(--wc-dark-primary)] border border-[var(--wc-light-secondary)] dark:border-[var(--wc-dark-secondary)]  transition-colors   flex text-center justify-center  ">
                                <input wire:loading.attr="disabled"
                                    @change="handleFileSelect(event,{{ count($files) }})" type="file" multiple
                                       accept="{{ collect($this->panel()->getFileMimes())->map(fn($ext) => '.' . $ext)->implode(',') }}"

                                       class="sr-only"
                                    hidden>
                                <span class="  m-auto">

                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                        class="w-6 h-6 dark:text-gray-50">
                                        <path fill-rule="evenodd"
                                            d="M12 3.75a.75.75 0 0 1 .75.75v6.75h6.75a.75.75 0 0 1 0 1.5h-6.75v6.75a.75.75 0 0 1-1.5 0v-6.75H4.5a.75.75 0 0 1 0-1.5h6.75V4.5a.75.75 0 0 1 .75-.75Z"
                                            clip-rule="evenodd" />
                                    </svg>


                                </span>
                            </label>

                        </section>
                    @endif
                </section>


                {{-- Replying to --}}
                @if ($replyMessage != null)
                    <section class="p-px py-1 w-full col-span-12">
                        <div class="flex justify-between items-center dark:text-white">
                            <h6 class="text-sm">
                                    {{ $replyMessage?->ownedBy($this->auth) ? __('wirechat::chat.labels.replying_to_yourself'): __('wirechat::chat.labels.replying_to',['participant'=>$replyMessage->sendable?->name])  }}
                            </h6>
                            <button wire:loading.attr="disabled" wire:click="removeReply()"
                                class="disabled:cursor-progress">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        {{-- Message being replied to --}}
                        <p class="truncate text-sm text-gray-500 dark:text-gray-200 max-w-md">
                            @if($replyMessage->body != '')
                                @php
                                    $securityService = app(\App\Services\ChatSecurityService::class);
                                    // Remember to match your conversation key column name here too (e.g., encryption_key, key, etc.)
                                    $conversationKey = $conversation?->security_key; 
                                @endphp
                                
                                {{ $securityService->decrypt($replyMessage->body, $conversationKey) }}
                            @else
                                {{ $replyMessage->hasAttachment() ? 'Attachment' : '' }}
                            @endif
                        </p>

                    </section>
                @endif



                <form x-data="{
                    'body': $wire.entangle('body'),
                    'stegano': $wire.entangle('steganoMode'),
                    insertNewLine: function(textarea) {
                        var startPos = textarea.selectionStart;
                        var endPos = textarea.selectionEnd;
                        var text = textarea.value;
                        var newText = text.substring(0, startPos) + '\n' + text.substring(endPos, text.length);
                        textarea.value = newText;
                        textarea.selectionStart = startPos + 1;
                        textarea.selectionEnd = startPos + 1;
                        textarea.style.height = 'auto';
                        textarea.style.height = textarea.scrollHeight + 'px';
                    }
                }"
                @reset-stegano.window="stegano = false;"
                x-init="
                    @if($hasEmojiPicker)
                {{-- Emoji picture click event listener --}}
                document.querySelector('emoji-picker')
                    .addEventListener('emoji-click', event => {
                        // Get the emoji unicode from the event
                        const emoji = event.detail['unicode'];

                        // Get the current value and cursor position
                        const inputField = $refs.body;
                        const inputFieldValue = inputField._x_model.get() ?? '';

                        const startPos = inputField.selectionStart;
                        const endPos = inputField.selectionEnd;
                        
                        // Insert the emoji at the current cursor position
                        const newValue = inputFieldValue.substring(0, startPos) + emoji + inputFieldValue.substring(endPos);

                        // Update the value and move cursor after the emoji
                        inputField._x_model.set(newValue);


                        inputField.setSelectionRange(startPos + emoji.length, startPos + emoji.length);
                    });
                @endif
                    "
                    @submit.prevent="((body && body?.trim().length > 0) || ($wire.media && $wire.media.length > 0)|| ($wire.files && $wire.files.length > 0)) ? $wire.sendMessage() : null"
                    method="POST" autocapitalize="off" @class(['flex  items-center col-span-12 w-full  gap-2 gap-5'])>
                    @csrf

                    <input type="hidden" autocomplete="false" style="display: none">


                    @if($hasEmojiPicker)
                    {{-- Emoji Triggger icon --}}
                    <div class="w-10 hidden sm:flex max-w-fit  items-center">
                        <button wire:loading.attr="disabled" type="button" dusk="emoji-trigger-button"
                                x-on:keydown.escape.stop=" openEmojiPicker=false"
                            @click="openEmojiPicker = ! openEmojiPicker" id="emojipickerbutton"
                            class="cursor-pointer hover:scale-105 transition-transform disabled:cursor-progress rounded-full p-px dark:border-gray-700">
                            <svg x-bind:style="openEmojiPicker && { color: 'var(--wc-brand-primary)' }"
                                viewBox="0 0 24 24" height="24" width="24"
                                preserveAspectRatio="xMidYMid meet"
                                class="w-7 h-7 text-gray-600 dark:text-gray-300 srtoke-[1.3] dark:stroke-[1.2]"
                                version="1.1" x="0px" y="0px" enable-background="new 0 0 24 24">
                                <title>smiley</title>
                                <path fill="currentColor"
                                    d="M9.153,11.603c0.795,0,1.439-0.879,1.439-1.962S9.948,7.679,9.153,7.679 S7.714,8.558,7.714,9.641S8.358,11.603,9.153,11.603z M5.949,12.965c-0.026-0.307-0.131,5.218,6.063,5.551 c6.066-0.25,6.066-5.551,6.066-5.551C12,14.381,5.949,12.965,5.949,12.965z M17.312,14.073c0,0-0.669,1.959-5.051,1.959 c-3.505,0-5.388-1.164-5.607-1.959C6.654,14.073,12.566,15.128,17.312,14.073z M11.804,1.011c-6.195,0-10.826,5.022-10.826,11.217 s4.826,10.761,11.021,10.761S23.02,18.423,23.02,12.228C23.021,6.033,17.999,1.011,11.804,1.011z M12,21.354 c-5.273,0-9.381-3.886-9.381-9.159s3.942-9.548,9.215-9.548s9.548,4.275,9.548,9.548C21.381,17.467,17.273,21.354,12,21.354z  M15.108,11.603c0.795,0,1.439-0.879,1.439-1.962s-0.644-1.962-1.439-1.962s-1.439,0.879-1.439,1.962S14.313,11.603,15.108,11.603z">
                                </path>
                            </svg>
                        </button>
                    </div>
                    @endif

                    {{-- Show  upload pop if media or file are empty --}}
                    {{-- Also only show  upload popup if allowed in configuration  --}}
                    @if (count($this->media) == 0 && count($this->files) == 0 && $this->panel()->hasAttachments())
                    {{-- ── Secure Payload Track (Steganography) ── --}}
                    <div class="flex flex-col items-center gap-0.5 shrink-0">
                        <button wire:loading.attr="disabled" type="button"
                            @click="stegano = !stegano; if(stegano) { $dispatch('trigger-media-select'); }"
                            class="cursor-pointer hover:scale-105 transition-transform p-1 rounded-full flex items-center justify-center"
                            :class="stegano ? 'text-blue-400 bg-blue-500/10 ring-1 ring-blue-500/40 shadow-[0_0_12px_rgba(59,130,246,0.2)]' : 'text-slate-400 hover:text-slate-200'"
                            title="Inject Secure Payload (Steganography)">
                            
                            <!-- Heroicon: Lock Closed (Active) / Lock Open (Inactive) -->
                            <svg xmlns="http://www.w3.org/2000/svg" :fill="stegano ? 'currentColor' : 'none'" 
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7">
                                <path stroke-linecap="round" stroke-linejoin="round" 
                                    x-bind:d="stegano 
                                    ? 'M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z' 
                                    : 'M13.5 10.5V6.75a4.5 4.5 0 1 1 9 0v3.75M3.75 21.75h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H3.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z'" />
                            </svg>
                        </button>
                        <span
                            class="font-mono text-[8px] tracking-wider uppercase leading-none pointer-events-none"
                            :class="stegano ? 'text-blue-400' : 'text-slate-600'"
                        >Secure</span>
                    </div>
                    {{-- ── Plain Media Track (standard upload) ── --}}
                        <x-wirechat::popover position="top" popoverOffset="70">

                            <x-slot name="trigger" wire:loading.attr="disabled">
                                <span dusk="upload-trigger-button" class="flex flex-col items-center gap-0.5">

                                    {{-- <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                        stroke="currentColor" class="w-7 h-7 dark:text-white/90">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                        </svg> --}}
                                    {{-- <svg  xmlns="http://www.w3.org/2000/svg"
                                            width="16" height="16" fill="currentColor"
                                            class="bi bi-plus-lg w-6 h-6 text-gray-600 dark:text-white/90" viewBox="0 0 16 16">
                                            <path fill-rule="evenodd"
                                                d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2" />
                                        </svg> --}}

                                    {{-- <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.3" stroke="currentColor" class="size-6 w-7 h-7 text-gray-600 dark:text-white/90">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m18.375 12.739-7.693 7.693a4.5 4.5 0 0 1-6.364-6.364l10.94-10.94A3 3 0 1 1 19.5 7.372L8.552 18.32m.009-.01-.01.01m5.699-9.941-7.81 7.81a1.5 1.5 0 0 0 2.112 2.13" />
                                          </svg> --}}
                                    <svg class="size-6 w-7 h-7 text-slate-400 hover:text-slate-200 transition-colors"
                                        xmlns="http://www.w3.org/2000/svg" width="36" height="36"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path
                                            d="M6 7.91V16a6 6 0 0 0 6 6v0a6 6 0 0 0 6-6V6a4 4 0 0 0-4-4v0a4 4 0 0 0-4 4v9.182a2 2 0 0 0 2 2v0a2 2 0 0 0 2-2V8" />
                                    </svg>
                                    <span class="font-mono text-[8px] tracking-wider uppercase leading-none text-slate-600">Plain</span>

                                </span>

                            </x-slot>

                            {{-- content --}}
                            <div class="grid gap-1 w-48 p-1.5 bg-slate-900 border border-slate-800 rounded-lg shadow-xl text-xs">

                                {{-- Option A: Standard Photos & Videos Track (Plain Media) --}}
                                @if ($this->panel()->hasMediaAttachments())
                                    <label wire:loading.class="cursor-progress" x-data="attachments('media')" class="w-full flex items-center gap-3 px-2.5 py-2 rounded-md hover:bg-slate-800 cursor-pointer text-slate-200 hover:text-white transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-blue-400">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                        </svg>
                                        <span>Upload Photos / Videos</span>

                                        <input dusk="media-upload-input"
                                            x-ref="steganoInput"
                                            x-on:trigger-media-select.window="$refs.steganoInput.click()"
                                            x-init="$el.addEventListener('cancel', () => { if (stegano) { stegano = false; } });"
                                            wire:loading.attr="disabled"
                                            wire:target="sendMessage"
                                            @change="
                                                if (stegano) {
                                                    if ($event.target.files.length > 1) {
                                                        $dispatch('wirechat-toast', { type: 'warning', message: 'Steganography mode only supports 1 image file at a time!' });
                                                        $event.target.value = '';
                                                        stegano = false;
                                                        return;
                                                    }
                                                    const file = $event.target.files[0];
                                                    if (file && !['image/png', 'image/jpeg', 'image/jpg'].includes(file.type)) {
                                                        $event.target.value = '';
                                                        stegano = false;
                                                        return;
                                                    }
                                                }
                                
                                                const selectedFile = $event.target.files[0];
                                                if (selectedFile) {
                                                    const maxAllowedSize = 200 * 1024 * 1024; // 200MB in Bytes
                                                    if (selectedFile.size > maxAllowedSize) {
                                                        $dispatch('wirechat-toast', { 
                                                            type: 'error', 
                                                            message: 'File is too large! Maximum allowed upload size is 200MB.' 
                                                        });
                                                        $event.target.value = ''; // Reset input to allow choosing a different file
                                                        return;
                                                    }
                                                }
                                                handleFileSelect($event, {{ count($media) }})
                                            "
                                            type="file"
                                            :multiple="!stegano"
                                            :accept="stegano ? 'image/png, image/jpeg, image/jpg' : 'image/*,video/*'"
                                            class="sr-only"
                                            style="display: none">
                                    </label>
                                @endif

                                {{-- Option B: Standard Documents Track (Files) --}}
                                @if ($this->panel()->hasFileAttachments())
                                    <label wire:loading.class="cursor-progress" x-data="attachments('files')" class="w-full flex items-center gap-3 px-2.5 py-2 rounded-md hover:bg-slate-800 cursor-pointer text-slate-200 hover:text-white transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-emerald-400">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                        </svg>
                                        <span>Upload Documents</span>

                                        <input wire:loading.attr="disabled" wire:target="sendMessage" dusk="file-upload-input"
                                            @change="handleFileSelect(event, {{ count($files) }})" type="file" multiple
                                            accept="{{ collect($this->panel()->getFileMimes())->map(fn($ext) => '.' . $ext)->implode(',') }}"
                                            class="sr-only"
                                            style="display: none">
                                    </label>
                                @endif

                            </div>
                        </x-wirechat::popover>
                    @endif

                    {{-- --------------- --}}
                    {{-- TextArea Input --}}
                    {{-- --------------- --}}

                    <div @class(['flex gap-2 sm:px-2 w-full'])>
                        <textarea @focus-input-field.window="$el.focus()" autocomplete="off" x-model='body' x-ref="body"
                            wire:loading.delay.longest.attr="disabled" wire:target="sendMessage" id="chat-input-field" autofocus
                            type="text" name="message" :placeholder="stegano ? 'Type your secret hidden message...' : '{{ __('wirechat::chat.inputs.message.placeholder') }}'" maxlength="1700" rows="1"
                            @input="$el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px';"
                            @keydown.shift.enter.prevent="insertNewLine($el)" {{-- @keydown.enter.prevent prevents the
                               default behavior of Enter key press only if Shift is not held down. --}} @keydown.enter.prevent=""
                            @keyup.enter.prevent="$event.shiftKey ? null : (((body && body?.trim().length > 0) || ($wire.media && $wire.media.length > 0)) ? $wire.sendMessage() : null)"
                            class="wc-textarea bg-inherit dark:bg-inherit w-full disabled:cursor-progress resize-none h-auto max-h-20  sm:max-h-72 flex grow border-0 outline-0 focus:border-0 focus:ring-0  hover:ring-0 rounded-lg   dark:text-white bg-none dark:bg-inherit  focus:outline-hidden   "
                            :class="stegano ? 'text-emerald-400 font-mono' : 'text-white'"
                        
                            x-init="
                        
                              @if($hasEmojiPicker)
                            document.querySelector('emoji-picker')
                                .addEventListener('emoji-click', event => {
                                    const emoji = event.detail['unicode'];
                                    const inputField = $refs.body;

                                    // Get the current cursor position (start and end)
                                    const startPos = inputField.selectionStart;
                                    const endPos = inputField.selectionEnd;

                                    // Get current value of the input field
                                    const currentValue = inputField.value;

                                    // Insert the emoji at the cursor position, preserving line breaks and spaces
                                    const newValue = currentValue.substring(0, startPos) + emoji + currentValue.substring(endPos);

                                    // Update Alpine.js model (x-model='body') with the new value
                                    inputField._x_model.set(newValue);

                                    // Set the cursor position after the inserted emoji
                                    inputField.setSelectionRange(startPos + emoji.length, startPos + emoji.length);

                                    // Ensure the textarea resizes correctly after adding the emoji
                                    inputField.style.height = 'auto';
                                    inputField.style.height = inputField.scrollHeight + 'px';
                                });
                            @endif
                                "

                        ></textarea>


                    </div>

                    {{-- --------------- --}}
                    {{-- input Actions --}}
                    {{-- --------------- --}}

                    <div x-cloak @class(['w-[5%] justify-end min-w-max  items-center gap-2 '])>

                        {{--  Submit button --}}
                        <button
                            x-show="((body?.trim()?.length>0) ||  $wire.media.length > 0 || $wire.files.length > 0 )"
                            wire:loading.attr="disabled" wire:target="sendMessage" type="submit"
                            id="sendMessageButton" class="cursor-pointer hover:text-[var(--wc-brand-primary)] transition-color ml-auto disabled:cursor-progress cursor-pointer font-bold">

                            <svg class="w-7 h-7   dark:text-gray-200" xmlns="http://www.w3.org/2000/svg"
                                width="36" height="36" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" class="ai ai-Send">
                                <path
                                    d="M9.912 12H4L2.023 4.135A.662.662 0 0 1 2 3.995c-.022-.721.772-1.221 1.46-.891L22 12 3.46 20.896c-.68.327-1.464-.159-1.46-.867a.66.66 0 0 1 .033-.186L3.5 15" />
                            </svg>

                        </button>



                        {{-- send Like button --}}
                        @if($this->panel()->hasHeart())

                        <button
                            x-show="!((body?.trim()?.length>0) || $wire.media.length > 0 || $wire.files.length > 0 )"
                            wire:loading.attr="disabled" wire:target="sendMessage" wire:click='sendLike()'
                            dusk="heart-button"
                            type="button" class="hover:scale-105 transition-transform cursor-pointer group disabled:cursor-progress">

                            <!-- outlined heart -->
                            <span class=" group-hover:hidden transition">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    class="w-7 h-7 text-gray-600 dark:text-white/90 stroke-[1.4] dark:stroke-[1.4]">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                                </svg>
                            </span>
                            <!--  filled heart -->
                            <span class="hidden group-hover:block transition " x-bounce>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                    class="size-6 w-7 h-7   text-red-500">
                                    <path
                                        d="m11.645 20.91-.007-.003-.022-.012a15.247 15.247 0 0 1-.383-.218 25.18 25.18 0 0 1-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0 1 12 5.052 5.5 5.5 0 0 1 16.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 0 1-4.244 3.17 15.247 15.247 0 0 1-.383.219l-.022.012-.007.004-.003.001a.752.752 0 0 1-.704 0l-.003-.001Z" />
                                </svg>
                            </span>

                        </button>
                        @endif


                    </div>

                </form>
            </section>



            @script
                <script>
                    Alpine.data('attachments', (type = "media") => ({
                        // State variables
                        isDropping: false, // Tracks if a file is being dragged over the drop area
                        type: type, // Type of file being uploaded (e.g., "media" or "file")
                        isUploading: false, // Indicates if files are currently uploading
                        MAXFILES: @json($this->panel()->getMaxUploads()), // Maximum number of files allowed
                        maxSize:  @json($this->panel()->getMediaMaxUploadSize()) * 1024, // Max size per file (in bytes)
                        allowedFileTypes: type === 'media' ? @json($this->panel()->getMediaMimes()) :@json($this->panel()->getFileMimes()), // Allowed MIME types based on type
                        progress: 0, // Progress of the current upload (0-100)
                        wireModel: type, // The Livewire model to bind to

                        // Handle file selection from the input field
                        handleFileSelect(event, count) {
                            const files = Array.from(event.target.files);

                            // Strict 1-file enforcement for Steganography
                            if (this.$wire.steganoMode && (files.length > 1 || count > 0)) {
                                this.$dispatch('wirechat-toast', {
                                    type: 'warning',
                                    message: 'Steganography mode only supports 1 image file at a time!'
                                });
                                event.target.value = '';
                                return;
                            }

                            if (files.length) {
                                // Validate selected files and upload if valid
                                this.validateFiles(files, count)
                                    .then((validFiles) => {
                                        if (validFiles.length > 0) {
                                            this.uploadFiles(validFiles);
                                        } else {
                                            console.log('No valid files to upload');
                                        }
                                    })
                                    .catch((error) => {
                                        console.log('Validation error:', error);
                                    });
                            }
                        },

                        // Upload files using Livewire's upload
                        uploadFiles(files) {
                            this.isUploading = true;
                            this.progress = 0;

                            // Initialize per-file progress tracking
                            const fileProgress = Array.from(files).map(() => 0);
                            files.forEach((file, index) => {
                                $wire.upload(
                                    `${this.wireModel}`, // Livewire model
                                    file, // Single file
                                    () => {
                                        fileProgress[index] = 100; // Mark this file as complete
                                        // this.isUploading = false;
                                        this.progress = Math.round((fileProgress.reduce((a, b) => a + b, 0)) / files.length);
                                    },
                                    (error) => {
                                        // this.isUploading = false;
                                        fileProgress[index] = -1; // Mark as failed
                                        $dispatch('wirechat-toast', { type: 'error', message: `Validation error: ${error}` });
                                    },
                                    (event) => {
                                        fileProgress[index] = event.detail.progress; // Update per-file progress
                                        this.progress = Math.round((fileProgress.reduce((a, b) => a + b, 0)) / files.length); // Overall progress
                                    }
                                );
                            });
                        },

                        // Upload files using Livewire's uploadMultiple method

                        // Remove an uploaded file from Livewire
                        removeUpload(filename) {
                            $wire.removeUpload(this.wireModel, filename);
                        },

                        // Validate selected files against constraints
                        validateFiles(files, count) {
                            const totalFiles = count + files.length; // Total file count including existing uploads

                            // Check if total file count exceeds the maximum allowed
                            if (totalFiles > this.MAXFILES) {
                                files = Array.from(files).slice(0, this.MAXFILES -
                                count); // Limit files to the allowed number
                                $dispatch('wirechat-toast', {
                                    type: 'warning',
                                    message: @js(__('wirechat::validation.max.array', ['attribute' => __('wirechat::chat.inputs.media.label'),'max'=>$this->panel()->getMaxUploads()]))
                                });
                            }

                            // Filter invalid files based on size and type
                            const invalidFiles = Array.from(files).filter((file) => {
                                const fileType = file.type.split('/')[1].toLowerCase(); // Extract file extension
                                return file.size > this.maxSize || !this.allowedFileTypes.includes(
                                fileType); // Check size and type
                            });

                            // Filter valid files
                            const validFiles = Array.from(files).filter((file) => {
                                const fileType = file.type.split('/')[1].toLowerCase();
                                return file.size <= this.maxSize && this.allowedFileTypes.includes(fileType);
                            });

                            // Handle invalid files by showing appropriate error messages
                            if (invalidFiles.length > 0) {
                                invalidFiles.forEach((file) => {
                                    if (file.size > this.maxSize) {
                                        $dispatch('wirechat-toast', {
                                            type: 'warning',
                                            message:this.type==='media'?
                                                    @js(__('wirechat::validation.max.file', ['attribute' => __('wirechat::chat.inputs.media.label'),'max'=>$this->panel()->getMediaMaxUploadSize()])):
                                                    @js(__('wirechat::validation.max.file', ['attribute' => __('wirechat::chat.inputs.media.label'),'max'=>$this->panel()->getFileMaxUploadSize()]))

                                         //   message: `File size exceeds the maximum limit (${this.maxSize / 1024 / 1024}MB): ${file.name}`
                                        });
                                    } else {
                                        const extension = file.name.split('.').pop().toLowerCase();
                                        $dispatch('wirechat-toast', {
                                            type: 'warning',
                                            message: this.type==='media'?
                                                    @js(__('wirechat::validation.mimes', [ 'attribute' => __('wirechat::chat.inputs.media.label'), 'values' => implode(', ', $this->panel()->getMediaMimes()) ])):
                                                    @js(__('wirechat::validation.mimes', [ 'attribute' => __('wirechat::chat.inputs.media.label'), 'values' => implode(', ', $this->panel()->getFileMimes()) ]))
                                           // message: `One or more Files not uploaded: .${extension} (type not allowed)`
                                        });

                                    }
                                });
                            }

                            return Promise.resolve(validFiles); // Return valid files for further processing
                        }
                    }));
                </script>
            @endscript
        </div>
    @endif



</footer>
