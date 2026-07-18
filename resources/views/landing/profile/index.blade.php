@extends("landing.layout.app", ["title" => "Profil"])

@push("style")
<style>
    body {
        background-color: #f8f9ff;
        font-family: 'Inter', sans-serif;
        color: #0b1c30;
    }
    .material-symbols-outlined {
        font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    }
    .soft-shadow {
        box-shadow: 0 10px 40px -10px rgba(0, 74, 198, 0.08);
    }
    .profile-card {
        background: #ffffff;
        border-radius: 0.75rem;
        box-shadow: 0 10px 40px -10px rgba(0, 74, 198, 0.08);
    }
    .avatar-circle {
        width: 128px;
        height: 128px;
        border-radius: 9999px;
        border: 4px solid white;
        box-shadow: 0 10px 40px -10px rgba(0, 74, 198, 0.15);
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #2563eb, #7c3aed);
    }
    @media (max-width: 640px) {
        .avatar-circle { width: 110px; height: 110px; }
    }
    .info-row { 
        display: flex; 
        gap: 0.75rem; 
        align-items: flex-start; 
    }
    .info-icon { 
        width: 20px; 
        height: 20px; 
        flex: 0 0 20px; 
        color: #737686;
        margin-top: 2px;
    }
    .info-icon-filled {
        font-size: 20px;
        color: #737686;
    }

    /* Profile edit input style */
    .profile-edit-input {
        background: #f8f9ff;
        color: #0b1c30;
        border: 1px solid #c3c6d7;
        border-radius: 0.5rem;
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
        width: 100%;
        outline: none;
        transition: border 0.2s, background 0.2s;
        font-family: 'Inter', sans-serif;
    }
    .profile-edit-input:focus {
        border: 2px solid #2563eb;
        background: #ffffff;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
    }
    .profile-edit-input::placeholder {
        color: #737686;
        opacity: 0.7;
    }
    
    .view-label {
        font-size: 0.75rem;
        font-weight: 500;
        color: #737686;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.25rem;
    }
    .view-value {
        font-size: 0.875rem;
        color: #0b1c30;
        font-weight: 500;
    }
    .info-content {
        flex: 1;
    }
    
    .btn-primary {
        background: #2563eb;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
    }
    .btn-primary:hover {
        background: #1d4ed8;
        transform: scale(0.98);
    }
    .btn-success {
        background: #059669;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
    }
    .btn-success:hover {
        background: #047857;
        transform: scale(0.98);
    }
    .btn-outline {
        background: transparent;
        color: #0b1c30;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.2s;
        border: 1px solid #c3c6d7;
        cursor: pointer;
    }
    .btn-outline:hover {
        background: #f8f9ff;
        border-color: #737686;
    }
    .btn-danger-outline {
        background: transparent;
        color: #ba1a1a;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.2s;
        border: 1px solid #ba1a1a;
        cursor: pointer;
    }
    .btn-danger-outline:hover {
        background: #ba1a1a;
        color: white;
    }
    
    .message-success {
        color: #059669;
        font-size: 0.75rem;
        text-align: center;
        margin-top: 0.5rem;
    }
    .message-error {
        color: #ba1a1a;
        font-size: 0.75rem;
        text-align: center;
        margin-top: 0.5rem;
    }
</style>
@endpush

@section("content")
<section class="min-h-screen bg-background">
    @include("landing.partials.header")
    <div class="container mx-auto px-4 md:px-6 py-8 max-w-3xl">
        <div class="animate-fade-in">
            <!-- Hero Profile Card -->
            <div class="profile-card p-6 md:p-8 flex flex-col items-center text-center mb-6">
                <div class="relative mb-4">
                    <div class="avatar-circle">
                        @if($user && $user->studentDetail && $user->studentDetail->photo)
                            <img src="{{ asset('storage/' . $user->studentDetail->photo) }}" alt="Foto Siswa" class="w-full h-full object-cover" />
                        @else
                            <svg class="w-16 h-16 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 20C3.732 16.943 6.79 15 12 15s8.268 1.943 9.542 5" />
                            </svg>
                        @endif
                    </div>
                    <div class="absolute bottom-1 right-1 bg-primary text-on-primary p-1.5 rounded-full border-2 border-white flex items-center justify-center">
                        <span class="material-symbols-outlined text-[16px]">edit</span>
                    </div>
                </div>

                <div class="inline-flex items-center px-3 py-1 bg-secondary-container text-on-secondary-container rounded-full mb-2">
                    <span class="text-xs font-semibold uppercase tracking-wider">
                        {{ $user->studentDetail->class_name ?? 'Class' }}
                    </span>
                </div>
                
                <h1 class="text-2xl md:text-3xl font-bold text-on-surface mb-0.5" id="profile-username">
                    {{ $user->username ?? $user->name }}
                </h1>
                
                <div class="flex flex-col md:flex-row gap-4 mt-2 text-on-surface-variant">
                    <div class="flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-primary text-[18px]">mail</span>
                        <span class="text-sm">{{ $user->email }}</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-primary text-[18px]">phone_iphone</span>
                        <span class="text-sm">{{ $user->phone_number ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <!-- Edit Form -->
            <form id="profile-edit-form" class="space-y-4" autocomplete="off">
                @csrf
                
                <!-- Nama Field -->
                <div class="bg-surface-container-lowest rounded-xl p-4 soft-shadow">
                    <div class="info-row">
                        <span class="material-symbols-outlined info-icon-filled">person_outline</span>
                        <div class="info-content">
                            <div class="view-label">Nama Lengkap</div>
                            <div class="view-value" id="profile-name-view">
                                {{ $user->name }}
                            </div>
                            <input type="text" name="name" id="profile-name-input" class="profile-edit-input hidden" value="{{ $user->name }}" placeholder="Nama Lengkap">
                        </div>
                    </div>
                </div>

                <!-- Email Field -->
                <div class="bg-surface-container-lowest rounded-xl p-4 soft-shadow">
                    <div class="info-row">
                        <span class="material-symbols-outlined info-icon-filled">mail_outline</span>
                        <div class="info-content">
                            <div class="view-label">Email</div>
                            <div class="view-value" id="profile-email-view">
                                {{ $user->email }}
                            </div>
                            <input type="email" name="email" id="profile-email-input" class="profile-edit-input hidden" value="{{ $user->email }}" placeholder="Email">
                        </div>
                    </div>
                </div>

                <!-- Phone Field -->
                <div class="bg-surface-container-lowest rounded-xl p-4 soft-shadow">
                    <div class="info-row">
                        <span class="material-symbols-outlined info-icon-filled">phone_iphone</span>
                        <div class="info-content">
                            <div class="view-label">Nomor Telepon</div>
                            <div class="view-value" id="profile-phone-view">
                                {{ $user->phone_number ?? '-' }}
                            </div>
                            <input type="text" name="phone_number" id="profile-phone-input" class="profile-edit-input hidden" value="{{ $user->phone_number }}" placeholder="Nomor Telepon">
                        </div>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex flex-wrap gap-3 mt-6">
                    <button type="button" id="profile-edit-btn" class="btn-primary flex-1 min-w-[120px]">
                        Edit Profil
                    </button>
                    <button type="submit" id="profile-save-btn" class="btn-success flex-1 min-w-[120px] hidden">
                        Simpan
                    </button>
                    <button type="button" id="profile-cancel-btn" class="btn-outline flex-1 min-w-[120px] hidden">
                        Batal
                    </button>
                    <a href="{{ route('landing.home') }}" id="profile-back-btn" class="btn-outline flex-1 min-w-[120px] text-center">
                        Kembali
                    </a>
                </div>
                
                <div id="profile-message" class="text-center text-xs mt-2"></div>
            </form>

            <!-- Academic Snapshot -->
            <div class="mt-6 bg-primary-container text-on-primary-container p-6 rounded-xl flex flex-col md:flex-row justify-between items-center gap-4">
                <div>
                    <h3 class="text-xl font-semibold mb-1">Academic Integrity</h3>
                    <p class="text-sm opacity-90">Your attendance is currently at 98.5%. Keep up the excellent work!</p>
                </div>
                <div class="flex items-center justify-center bg-white/20 backdrop-blur-md rounded-lg px-4 py-2 border border-white/30">
                    <div class="text-center">
                        <span class="block text-4xl font-bold leading-tight">98%</span>
                        <span class="text-xs font-semibold uppercase opacity-80">This Semester</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @include("landing.partials.nav")
</section>
@endsection

@push("script")
<script>
document.addEventListener('DOMContentLoaded', function() {
    const editBtn = document.getElementById('profile-edit-btn');
    const saveBtn = document.getElementById('profile-save-btn');
    const cancelBtn = document.getElementById('profile-cancel-btn');
    const form = document.getElementById('profile-edit-form');
    const message = document.getElementById('profile-message');

    function toggleEditMode(editing) {
        // Toggle view/input for name
        document.getElementById('profile-name-view').classList.toggle('hidden', editing);
        document.getElementById('profile-name-input').classList.toggle('hidden', !editing);
        
        // Toggle view/input for email
        document.getElementById('profile-email-view').classList.toggle('hidden', editing);
        document.getElementById('profile-email-input').classList.toggle('hidden', !editing);
        
        // Toggle view/input for phone
        document.getElementById('profile-phone-view').classList.toggle('hidden', editing);
        document.getElementById('profile-phone-input').classList.toggle('hidden', !editing);

        // Toggle buttons
        editBtn.classList.toggle('hidden', editing);
        saveBtn.classList.toggle('hidden', !editing);
        cancelBtn.classList.toggle('hidden', !editing);
        
        // Clear message
        message.textContent = '';
        message.className = 'text-center text-xs mt-2';
    }

    editBtn.addEventListener('click', function() {
        toggleEditMode(true);
    });

    cancelBtn.addEventListener('click', function() {
        toggleEditMode(false);
    });

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        saveBtn.disabled = true;
        message.textContent = 'Menyimpan...';
        message.className = 'text-center text-xs mt-2 text-slate-500';

        fetch("{{ route('landing.profile.update') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                name: document.getElementById('profile-name-input').value,
                email: document.getElementById('profile-email-input').value,
                phone_number: document.getElementById('profile-phone-input').value
            })
        })
        .then(res => res.json())
        .then(data => {
            saveBtn.disabled = false;
            if (data.success) {
                // Update view values
                document.getElementById('profile-name-view').textContent = data.user.name;
                document.getElementById('profile-email-view').textContent = data.user.email;
                document.getElementById('profile-phone-view').textContent = data.user.phone_number || '-';
                
                // Update username if it exists
                const usernameEl = document.getElementById('profile-username');
                if (usernameEl && data.user.username) {
                    usernameEl.textContent = data.user.username;
                }
                
                toggleEditMode(false);
                message.textContent = data.message || 'Profil berhasil diperbarui!';
                message.className = 'text-center text-xs mt-2 message-success';
            } else {
                message.textContent = data.message || 'Gagal memperbarui profil.';
                message.className = 'text-center text-xs mt-2 message-error';
            }
        })
        .catch(() => {
            saveBtn.disabled = false;
            message.textContent = 'Terjadi kesalahan. Silakan coba lagi.';
            message.className = 'text-center text-xs mt-2 message-error';
        });
    });

    // Back button with history
    document.getElementById('profile-back-btn').addEventListener('click', function(e) {
        e.preventDefault();
        if (window.history.length > 1) {
            window.history.back();
        } else {
            window.location.href = '/';
        }
    });
});
</script>
@endpush