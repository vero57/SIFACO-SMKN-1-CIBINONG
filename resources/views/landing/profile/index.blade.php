@extends("landing.layout.app", ["title" => "Profil"])

@push("style")
<style>
    .profile-card {
        background: rgba(255,255,255,0.04);
        border-radius: 1.5rem;
        box-shadow: 0 10px 40px rgba(2,6,23,0.6);
        backdrop-filter: blur(10px);
    }
    .avatar-circle {
        width: 140px;
        height: 140px;
        border-radius: 9999px;
        display: grid;
        place-items: center;
        background: linear-gradient(135deg, rgba(99,102,241,0.9), rgba(139,92,246,0.85));
        box-shadow: 0 12px 30px rgba(99,102,241,0.15);
    }
    @media (max-width: 640px) {
        .avatar-circle { width: 110px; height: 110px; }
    }
    .info-row { display: flex; gap: .75rem; align-items: center; }
    .info-icon { width: 18px; height: 18px; flex: 0 0 18px; color: #94a3b8; }

    /* Transparent, blending input style */
    .profile-edit-input {
        background: rgba(30,41,59,0.5); /* semi-transparent slate-800 */
        color: #f1f5f9;
        border: 1px solid rgba(148,163,184,0.15);
        border-radius: 0.5rem;
        padding: 0.25rem 0.5rem;
        font-size: 0.95rem;
        width: 100%;
        outline: none;
        transition: border 0.2s, background 0.2s;
        box-shadow: none;
    }
    .profile-edit-input:focus {
        border: 1.5px solid #a78bfa;
        background: rgba(67,56,202,0.10);
    }
    .profile-edit-input::placeholder {
        color: #94a3b8;
        opacity: 0.7;
    }
</style>
@endpush

@section("content")
<section class="min-h-screen text-slate-200">
    <div class="container mx-auto px-6 py-8">
        @include("landing.partials.header")
        <div class="mx-auto">
            <div class="profile-card p-6">
                <div class="flex flex-col items-center">
                    <div class="avatar-circle mb-6">
                        @if($user && $user->studentDetail && $user->studentDetail->photo)
                            <img src="{{ asset('storage/' . $user->studentDetail->photo) }}" alt="Foto Siswa" class="max-sm:w-28 max-sm:h-28 w-36 h-36 object-cover rounded-full" />
                        @else
                        <svg class="w-16 h-16 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 20C3.732 16.943 6.79 15 12 15s8.268 1.943 9.542 5" />
                        </svg>
                        @endif
                    </div>

                    <h1 class="text-2xl font-semibold text-slate-100 mb-1" id="profile-username">
                        {{ $user->username ?? $user->name }}
                    </h1>
                    <p class="text-sm text-slate-400 mb-6" id="profile-class">
                        {{ $user->studentDetail->class_name ?? '-' }}
                    </p>

                    <form id="profile-edit-form" class="w-full space-y-4" autocomplete="off">
                        @csrf
                        <div class="bg-slate-800/40 rounded-lg p-4">
                            <div class="info-row">
                                <svg class="info-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 12a4 4 0 10-8 0 4 4 0 008 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 14v7" />
                                </svg>
                                <div>
                                    <div class="text-xs text-slate-400">Nama</div>
                                    <div class="text-sm text-slate-100 font-medium" id="profile-name-view">
                                        {{ $user->name }}
                                    </div>
                                    <input type="text" name="name" id="profile-name-input" class="profile-edit-input hidden" value="{{ $user->name }}" placeholder="Nama Lengkap">
                                </div>
                            </div>
                        </div>

                        <div class="bg-slate-800/40 rounded-lg p-4">
                            <div class="info-row">
                                <svg class="info-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 12v6a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2h6" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 12v-.5a2.5 2.5 0 00-2.5-2.5H18" />
                                </svg>
                                <div>
                                    <div class="text-xs text-slate-400">Email</div>
                                    <div class="text-sm text-slate-100 font-medium" id="profile-email-view">
                                        {{ $user->email }}
                                    </div>
                                    <input type="email" name="email" id="profile-email-input" class="profile-edit-input hidden" value="{{ $user->email }}" placeholder="Email">
                                </div>
                            </div>
                        </div>

                        <div class="bg-slate-800/40 rounded-lg p-4">
                            <div class="info-row">
                                <svg class="info-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5h12M9 3v2m0 4v12" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 8v6a2 2 0 01-2 2h-3l-4 3v-5H5a2 2 0 01-2-2V8a2 2 0 012-2h14a2 2 0 012 2z" />
                                </svg>
                                <div>
                                    <div class="text-xs text-slate-400">Nomor Telepon</div>
                                    <div class="text-sm text-slate-100 font-medium" id="profile-phone-view">
                                        {{ $user->phone_number ?? '-' }}
                                    </div>
                                    <input type="text" name="phone_number" id="profile-phone-input" class="profile-edit-input hidden" value="{{ $user->phone_number }}" placeholder="Nomor Telepon">
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <!-- <button type="button" id="profile-edit-btn" class="w-full text-center bg-purple-600 hover:bg-purple-700 text-white py-2 rounded-lg text-sm">Edit Profil</button> -->
                            <button type="submit" id="profile-save-btn" class="w-full text-center bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg text-sm hidden">Simpan</button>
                            <button type="button" id="profile-cancel-btn" class="w-full text-center border border-slate-700 text-slate-100 py-2 rounded-lg text-sm hidden">Batal</button>
                            <a href="{{ route('landing.home') }}" id="profile-back-btn" class="w-full text-center border border-slate-700 text-slate-100 py-2 rounded-lg text-sm">Kembali</a>
                        </div>
                        <!-- <div id="profile-message" class="text-center text-xs mt-2"></div> -->
                    </form>
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
        document.getElementById('profile-name-view').classList.toggle('hidden', editing);
        document.getElementById('profile-name-input').classList.toggle('hidden', !editing);
        document.getElementById('profile-email-view').classList.toggle('hidden', editing);
        document.getElementById('profile-email-input').classList.toggle('hidden', !editing);
        document.getElementById('profile-phone-view').classList.toggle('hidden', editing);
        document.getElementById('profile-phone-input').classList.toggle('hidden', !editing);

        editBtn.classList.toggle('hidden', editing);
        saveBtn.classList.toggle('hidden', !editing);
        cancelBtn.classList.toggle('hidden', !editing);
        message.textContent = '';
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
                document.getElementById('profile-name-view').textContent = data.user.name;
                document.getElementById('profile-email-view').textContent = data.user.email;
                document.getElementById('profile-phone-view').textContent = data.user.phone_number || '-';
                toggleEditMode(false);
                message.textContent = data.message;
                message.classList.remove('text-red-500');
                message.classList.add('text-green-500');
            } else {
                message.textContent = data.message || 'Gagal memperbarui profil.';
                message.classList.remove('text-green-500');
                message.classList.add('text-red-500');
            }
        })
        .catch(() => {
            saveBtn.disabled = false;
            message.textContent = 'Terjadi kesalahan.';
            message.classList.remove('text-green-500');
            message.classList.add('text-red-500');
        });
    });

    // Tombol kembali pakai JS agar selalu kembali ke halaman sebelumnya
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
