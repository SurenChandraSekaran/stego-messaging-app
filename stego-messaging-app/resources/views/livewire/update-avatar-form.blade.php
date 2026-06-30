<div class="bg-slate-900 border border-slate-800 p-6 rounded-xl shadow-sm max-w-xl" 
     x-data="avatarCropper()">
    
    <header class="mb-5">
        <h2 class="text-sm font-semibold tracking-widest text-slate-300 uppercase font-mono">Profile Picture</h2>
        <p class="mt-1 text-xs text-slate-500 font-mono">[ Upload and crop your identity asset ]</p>
    </header>

    <div class="flex items-center gap-6">
        <div class="relative w-20 h-20 shrink-0">
            <img src="{{ Auth::user()->avatar_url }}" class="w-20 h-20 rounded-full object-cover border border-slate-700 shadow-sm">
        </div>

        <div class="flex flex-col gap-2.5 items-start">
            <label class="cursor-pointer bg-slate-800 hover:bg-slate-700 text-slate-200 text-sm font-semibold py-2 px-4 rounded-xl border border-slate-700 transition-colors duration-150 inline-block">
                Choose New Image
                <input type="file" id="upload-avatar" accept="image/png, image/jpeg, image/jpg" class="sr-only" @change="handleFile">
            </label>
            @error('avatar') <span class="text-xs text-red-500 font-medium mt-1">{{ $message }}</span> @enderror
        </div>
    </div>

    <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-xs" x-cloak>
        <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl max-w-md w-full mx-4 shadow-xl">
            <h3 class="text-xs font-semibold text-slate-300 mb-4 font-mono tracking-widest uppercase">[ Crop Identity Asset ]</h3>
            
            <div class="max-h-64 overflow-hidden rounded-lg bg-slate-950 flex items-center justify-center">
                <img id="cropper-image" class="max-w-full block" src="">
            </div>

            <div class="flex justify-end gap-3 mt-5">
                <button type="button" @click="closeModal" class="bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold py-2 px-4 rounded-xl transition">
                    Cancel
                </button>
                <button type="button" @click="cropAndSave" class="bg-blue-500/15 hover:bg-blue-500/25 text-blue-400 hover:text-blue-300 border border-blue-500/40 hover:border-blue-400/70 text-xs font-bold py-2 px-4 rounded-xl transition-all shadow-[0_0_10px_rgba(59,130,246,0.15)]">
                    Crop &amp; Save
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function avatarCropper() {
    return {
        showModal: false,
        cropper: null,

        handleFile(e) {
            const files = e.target.files;
            if (files && files.length > 0) {
                const file = files[0];
                const reader = new FileReader();
                reader.onload = (event) => {
                    const image = document.getElementById('cropper-image');
                    image.src = event.target.result;
                    this.showModal = true;

                    // Initialize Cropper.js after modal opens up in canvas layout
                    setTimeout(() => {
                        if (this.cropper) this.cropper.destroy();
                        this.cropper = new Cropper(image, {
                            aspectRatio: 1, // Force perfect square ratio for standard avatars
                            viewMode: 1,
                            background: false,
                        });
                    }, 50);
                };
                reader.readAsDataURL(file);
            }
        },

        closeModal() {
            this.showModal = false;
            if (this.cropper) {
                this.cropper.destroy();
                this.cropper = null;
            }
            document.getElementById('upload-avatar').value = ''; // Reset file input element
        },

        cropAndSave() {
            if (!this.cropper) return;

            // Generate a compressed canvas copy of the cropped selection zone layout 
            const canvas = this.cropper.getCroppedCanvas({
                width: 300,  // Keep file storage sizes highly optimized
                height: 300
            });

            // Convert canvas rendering pattern down into base64 image data strings
            const base64Image = canvas.toDataURL('image/jpeg', 0.85);

            // Send base64 asset directly upstream to the Livewire Component Controller
            @this.set('avatar', base64Image);
            @this.call('saveAvatar');

            this.closeModal();
        }
    }
}
</script>