import './bootstrap';

import focus from '@alpinejs/focus'

// Use Livewire's Alpine instance
document.addEventListener('alpine:init', () => {
    Alpine.plugin(focus)
})
document.addEventListener('livewire:navigated', () => {
    document.body.style.overflowY = 'auto';
});