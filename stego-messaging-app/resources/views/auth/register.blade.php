<!DOCTYPE html>
<html>
<head>
    <title>StegChat - Register</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>

<style>
body {
    font-family: Arial;
    background-color: #7185E3;
    margin: 0;
    padding: 0;
}

.container {
    width: 400px;
    margin: 50px auto;
    background: #f2f2f2;
    padding: 20px;
    border-radius: 6px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

input[type="text"], input[type="email"], input[type="password"] {
    width: 90%;
    padding: 10px;
    margin-top: 5px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 5px;
    display: block;
}

label {
    font-weight: bold;
    display: block;
    margin-top: 10px;
}

button {
    width: 100%;
    padding: 10px;
    background: #333;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-weight: bold;
    margin-top: 10px;
}

button:hover {
    background: #444;
}

.links {
    margin-top: 15px;
    text-align: center;
}

.links a {
    display: block;
    font-size: 14px;
    color: #333;
    text-decoration: none;
    margin-top: 5px;
}

.error {
    color: red;
    font-size: 13px;
    margin-top: -10px;
    margin-bottom: 10px;
}

/* Custom Crop Modal Styling matching your look */
.crop-modal {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.7);
    z-index: 9999;
    align-items: center;
    justify-content: center;
}

.crop-modal-content {
    background: white;
    padding: 20px;
    border-radius: 8px;
    width: 350px;
    max-width: 90%;
}

.img-container {
    max-height: 250px;
    overflow: hidden;
    background: #000;
    margin-bottom: 15px;
    border-radius: 4px;
}

.img-container img {
    max-width: 100%;
    display: block;
}

.modal-buttons {
    display: flex;
    gap: 10px;
}

.btn-cancel { background: #888; }
.btn-save { background: #2563eb; }
</style>
</head>

<body>

<h1 style="font-family: Lucida Handwriting; font-size: 80px; text-align: center; margin-top: 30px;">StegChat</h1>

<div class="container">

<form id="registerForm" method="POST" action="{{ route('register') }}">
    @csrf

    <label>Name</label>
    <input type="text" name="name" value="{{ old('name') }}" required autofocus>
    @error('name') <div class="error">{{ $message }}</div> @enderror

    <label>Email</label>
    <input type="email" name="email" value="{{ old('email') }}" required>
    @error('email') <div class="error">{{ $message }}</div> @enderror

    <label>Profile Picture (Optional)</label>
    <div style="display: flex; align-items: center; gap: 15px; margin: 10px 0 15px 0;">
        <div id="preview-circle" style="width: 50px; height: 50px; border-radius: 50%; background: #ddd; display: flex; align-items: center; justify-content: center; font-size: 12px; color: #666; overflow: hidden; font-weight: bold; border: 1px solid #ccc; flex-shrink: 0;">
            @if(old('avatar'))
                <img src="{{ old('avatar') }}" style="width:100%; height:100%; object-fit:cover;">
            @else
                None
            @endif
        </div>
        <input type="file" id="avatar-input" accept="image/png, image/jpeg, image/jpg" style="border: none; background: transparent; padding: 0; cursor: pointer; width: auto;">
    </div>
    @error('avatar') <div class="error">{{ $message }}</div> @enderror

    <input type="hidden" id="cropped-avatar-data" name="avatar" value="{{ old('avatar') }}">

    <label>Password</label>
    <input type="password" name="password" required>
    
    @error('password') 
        <div class="error-box" style="background: #fee2e2; border-left: 4px solid #ef4444; padding: 10px; border-radius: 4px; margin-top: -10px; margin-bottom: 15px;">
            <div style="color: #b91c1c; font-weight: bold; font-size: 13px; margin-bottom: 5px;">
                Security Policy Violation:
            </div>
            <ul style="margin: 0; padding-left: 18px; color: #b91c1c; font-size: 12px; line-height: 1.5;">
                @foreach ($errors->get('password') as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div> 
    @enderror

    <label>Confirm Password</label>
    <input type="password" name="password_confirmation" required>
    @error('password_confirmation') <div class="error">{{ $message }}</div> @enderror

    <button type="submit">Register</button>

    <div class="links">
        <a href="{{ route('login') }}">Already registered? Login here</a>
    </div>
</form>
</div>

<div id="cropModal" class="crop-modal">
    <div class="crop-modal-content">
        <h3 style="margin-top:0;">Crop Avatar Image</h3>
        <div class="img-container">
            <img id="image-to-crop" src="">
        </div>
        <div class="modal-buttons">
            <button type="button" id="btnCancelCrop" class="btn-cancel">Cancel</button>
            <button type="button" id="btnConfirmCrop" class="btn-save">Confirm Crop</button>
        </div>
    </div>
</div>

<script>
let cropper;
const avatarInput = document.getElementById('avatar-input');
const cropModal = document.getElementById('cropModal');
const imageToCrop = document.getElementById('image-to-crop');
const croppedAvatarData = document.getElementById('cropped-avatar-data');
const previewCircle = document.getElementById('preview-circle');

avatarInput.addEventListener('change', function(e) {
    const files = e.target.files;
    if (files && files.length > 0) {
        const file = files[0];
        const reader = new FileReader();
        reader.onload = function(event) {
            imageToCrop.src = event.target.result;
            cropModal.style.display = 'flex';
            
            if (cropper) cropper.destroy();
            cropper = new Cropper(imageToCrop, {
                aspectRatio: 1,
                viewMode: 1,
                background: false
            });
        };
        reader.readAsDataURL(file);
    }
});

document.getElementById('btnCancelCrop').addEventListener('click', function() {
    cropModal.style.display = 'none';
    if (cropper) cropper.destroy();
    avatarInput.value = ''; // clear input selection
});

document.getElementById('btnConfirmCrop').addEventListener('click', function() {
    if (!cropper) return;
    
    const canvas = cropper.getCroppedCanvas({
        width: 300,
        height: 300
    });
    
    const base64Data = canvas.toDataURL('image/jpeg', 0.85);
    
    // Inject data into hidden structural form variable slot
    croppedAvatarData.value = base64Data;
    
    // Update structural UI icon preview circle
    previewCircle.innerHTML = `<img src="${base64Data}" style="width:100%; height:100%; object-fit:cover;">`;
    
    // Clean up modal layers
    cropModal.style.display = 'none';
    cropper.destroy();
});
window.addEventListener('DOMContentLoaded', (event) => {
    const existingAvatarData = croppedAvatarData.value;
    
    // If a validation error happened, the old image string lives here
    if (existingAvatarData && existingAvatarData.startsWith('data:image')) {
        // Enforce synchronization by refreshing the visual container frame
        previewCircle.innerHTML = `<img src="${existingAvatarData}" style="width:100%; height:100%; object-fit:cover;">`;
    }
});
</script>

</body>
</html>