@extends('frontend.layouts.main')

@section('main-content')
<style>
    .profile-page {
        padding: 30px 0 50px;
        background: #f9faf9;
        min-height: 80vh;
    }
    .profile-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 0 20px;
    }
    .profile-header {
        background: linear-gradient(135deg, #2e7d32 0%, #1b5e20 100%);
        border-radius: 16px;
        padding: 24px 28px;
        color: white;
        margin-bottom: 24px;
        box-shadow: 0 8px 25px rgba(46, 125, 50, 0.15);
    }
    .profile-header h1 {
        margin: 0;
        font-size: 1.8rem;
        font-weight: 700;
    }
    .profile-header p {
        margin: 6px 0 0;
        opacity: 0.9;
        font-size: 1rem;
    }
    .profile-card {
        background: white;
        border-radius: 14px;
        padding: 28px;
        margin-bottom: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.06);
        border: 1px solid #e8f0e8;
    }
    .profile-card h2 {
        font-size: 1.2rem;
        font-weight: 700;
        color: #1f2937;
        margin: 0 0 20px;
        padding-bottom: 12px;
        border-bottom: 1px solid #e8f0e8;
    }
    .avatar-section {
        display: flex;
        align-items: center;
        gap: 24px;
        margin-bottom: 28px;
    }
    .avatar-circle {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        background: linear-gradient(135deg, #2e7d32, #1b5e20);
        color: white;
        font-size: 2rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        overflow: hidden;
        border: 3px solid #e8f0e8;
    }
    .avatar-circle img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .avatar-info h3 {
        margin: 0 0 4px;
        font-size: 1.1rem;
        font-weight: 700;
        color: #1f2937;
    }
    .avatar-info p {
        margin: 0 0 10px;
        font-size: 0.9rem;
        color: #6b7280;
    }
    .avatar-upload-label {
        display: inline-block;
        padding: 7px 14px;
        background: #f0f9f1;
        border: 1px solid #2e7d32;
        color: #2e7d32;
        border-radius: 8px;
        font-size: 0.88rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }
    .avatar-upload-label:hover {
        background: #2e7d32;
        color: white;
    }
    .form-group {
        margin-bottom: 18px;
    }
    .form-group label {
        display: block;
        font-size: 0.9rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 6px;
    }
    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 0.95rem;
        color: #1f2937;
        background: white;
        transition: border-color 0.2s, box-shadow 0.2s;
        box-sizing: border-box;
    }
    .form-group input:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: #2e7d32;
        box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.1);
    }
    .form-group input[readonly] {
        background: #f9fafb;
        color: #6b7280;
        cursor: not-allowed;
    }
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }
    .btn-save {
        padding: 10px 24px;
        background: #2e7d32;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 0.95rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
    }
    .btn-save:hover {
        background: #1b5e20;
    }
    .alert-success {
        padding: 12px 16px;
        background: #d1fae5;
        color: #065f46;
        border-radius: 8px;
        margin-bottom: 16px;
        font-size: 0.95rem;
        font-weight: 500;
        border: 1px solid #a7f3d0;
    }
    .alert-error {
        padding: 12px 16px;
        background: #fee2e2;
        color: #991b1b;
        border-radius: 8px;
        margin-bottom: 16px;
        font-size: 0.95rem;
        font-weight: 500;
        border: 1px solid #fca5a5;
    }
    @media (max-width: 600px) {
        .form-row {
            grid-template-columns: 1fr;
        }
        .avatar-section {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>

<div class="profile-page">
    <div class="profile-container">
        <div class="profile-header">
            <h1><i class="fas fa-user-circle"></i> My Account</h1>
            <p>Manage your profile information and security settings</p>
        </div>

        {{-- Profile Card --}}
        <div class="profile-card">
            <h2><i class="fas fa-id-card" style="color:#2e7d32;margin-right:8px;"></i>Profile Information</h2>

            @if(session('profile_success'))
                <div class="alert-success"><i class="fas fa-check-circle"></i> {{ session('profile_success') }}</div>
            @endif
            @if(session('profile_error'))
                <div class="alert-error"><i class="fas fa-exclamation-circle"></i> {{ session('profile_error') }}</div>
            @endif
            @if($errors->has('image') || $errors->has('full_name') || $errors->has('phone_number') || $errors->has('address'))
                <div class="alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    @foreach($errors->only(['full_name','phone_number','address','image']) as $err)
                        {{ $err[0] }}<br>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('account.profile.update') }}" enctype="multipart/form-data">
                @csrf

                <div class="avatar-section">
                    <div class="avatar-circle" id="avatarPreview">
                        @if($user->image)
                            <img src="{{ asset('storage/' . $user->image) }}" alt="Avatar" id="avatarImg">
                        @else
                            <span id="avatarInitials">{{ strtoupper(substr($user->full_name, 0, 1)) }}</span>
                        @endif
                    </div>
                    <div class="avatar-info">
                        <h3>{{ $user->full_name }}</h3>
                        <p>{{ $user->email }}</p>
                        <label class="avatar-upload-label" for="imageInput">
                            <i class="fas fa-camera"></i> Change Photo
                        </label>
                        <input type="file" id="imageInput" name="image" accept="image/jpeg,image/png,image/gif" style="display:none;">
                    </div>
                </div>

                <div class="form-group">
                    <label for="full_name">Full Name <span style="color:#dc2626;">*</span></label>
                    <input type="text" id="full_name" name="full_name" value="{{ old('full_name', $user->full_name) }}" required maxlength="100">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" value="{{ $user->email }}" readonly>
                    </div>
                    <div class="form-group">
                        <label for="phone_number">Phone Number</label>
                        <input type="text" id="phone_number" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}" maxlength="20" placeholder="e.g. +977-9800000000">
                    </div>
                </div>

                <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" id="address" name="address" value="{{ old('address', $user->address) }}" maxlength="255" placeholder="Your delivery address">
                </div>

                <button type="submit" class="btn-save"><i class="fas fa-save"></i> Save Changes</button>
            </form>
        </div>

        {{-- Password Card --}}
        <div class="profile-card">
            <h2><i class="fas fa-lock" style="color:#2e7d32;margin-right:8px;"></i>Change Password</h2>

            @if(session('password_success'))
                <div class="alert-success"><i class="fas fa-check-circle"></i> {{ session('password_success') }}</div>
            @endif
            @if(session('password_error'))
                <div class="alert-error"><i class="fas fa-exclamation-circle"></i> {{ session('password_error') }}</div>
            @endif
            @if($errors->has('current_password') || $errors->has('new_password'))
                <div class="alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    @foreach($errors->only(['current_password','new_password']) as $err)
                        {{ $err[0] }}<br>
                    @endforeach
                </div>
            @endif

            @if(!$user->password)
                <div class="alert-error" style="margin-bottom:0;">
                    <i class="fas fa-info-circle"></i> Your account uses Google sign-in and does not have a password.
                </div>
            @else
                <form method="POST" action="{{ route('account.profile.password') }}">
                    @csrf

                    <div class="form-group">
                        <label for="current_password">Current Password <span style="color:#dc2626;">*</span></label>
                        <input type="password" id="current_password" name="current_password" required autocomplete="current-password">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="new_password">New Password <span style="color:#dc2626;">*</span></label>
                            <input type="password" id="new_password" name="new_password" required minlength="8" autocomplete="new-password">
                        </div>
                        <div class="form-group">
                            <label for="new_password_confirmation">Confirm New Password <span style="color:#dc2626;">*</span></label>
                            <input type="password" id="new_password_confirmation" name="new_password_confirmation" required autocomplete="new-password">
                        </div>
                    </div>

                    <button type="submit" class="btn-save"><i class="fas fa-key"></i> Update Password</button>
                </form>
            @endif
        </div>
    </div>
</div>

<script>
(function () {
    const imageInput = document.getElementById('imageInput');
    const avatarPreview = document.getElementById('avatarPreview');

    if (!imageInput || !avatarPreview) return;

    imageInput.addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function (e) {
            avatarPreview.innerHTML = '<img src="' + e.target.result + '" alt="Avatar" style="width:100%;height:100%;object-fit:cover;">';
        };
        reader.readAsDataURL(file);
    });
})();
</script>
@endsection
