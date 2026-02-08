@extends('landing.layout.app', ['title' => 'Login / Register'])

@push('style')
<style>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');

body {
    font-family: 'Plus Jakarta Sans', sans-serif;
}

/* ================= CONTAINER ================= */
.auth-container {
    min-height: 700px;
    background: radial-gradient(circle at top, #1e293b 0%, #020617 60%);
}

/* ================= CARD ================= */
.flip-card {
    width: 100%;
    max-width: 460px;
    height: auto;
    perspective: 1200px;
}

.flip-card-inner {
    width: 100%;
    min-height: 620px;
    transition: transform 0.7s cubic-bezier(.4,2,.3,1);
    transform-style: preserve-3d;
    position: relative;
}

.flip-card.flipped .flip-card-inner {
    transform: rotateY(180deg);
}

.flip-card-front,
.flip-card-back {
    position: absolute;
    inset: 0;
    background: linear-gradient(
        180deg,
        rgba(255,255,255,0.12),
        rgba(255,255,255,0.05)
    );
    backdrop-filter: blur(18px);
    border-radius: 1.5rem;
    padding: 2.5rem 2.2rem;
    box-shadow:
        0 20px 50px rgba(0,0,0,0.45),
        inset 0 1px 0 rgba(255,255,255,0.15);
    backface-visibility: hidden;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.flip-card-back {
    transform: rotateY(180deg);
}

/* ================= SWITCH BUTTON ================= */
.switch-btn {
    position: absolute;
    top: 1.25rem;
    right: 1.25rem;
    background: linear-gradient(135deg, #6366f1, #4f46e5);
    color: #fff;
    border: none;
    border-radius: 999px;
    padding: 0.45rem 1.2rem;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    z-index: 10;
    box-shadow: 0 6px 20px rgba(99,102,241,0.35);
}

.switch-btn:hover {
    background: linear-gradient(135deg, #4f46e5, #4338ca);
}

/* ================= TITLE ================= */
.auth-title {
    text-align: center;
    font-size: 1.75rem;
    font-weight: 700;
    color: #e0e7ff;
    margin-bottom: 1.6rem;
}

/* ================= FORM ================= */
.auth-form label {
    color: #c7d2fe;
    font-size: 0.85rem;
    font-weight: 500;
    margin-bottom: 0.3rem;
    display: block;
}

.auth-form input,
.auth-form select {
    width: 100%;
    padding: 0.75rem 0.9rem;
    border-radius: 0.8rem;
    border: 1px solid rgba(255,255,255,0.15);
    background: rgba(15,23,42,0.7);
    color: #e5e7eb;
    font-size: 0.95rem;
    margin-bottom: 0.75rem;
}

.auth-form input::placeholder {
    color: #94a3b8;
}

.auth-form input:focus,
.auth-form select:focus {
    outline: none;
    border-color: #6366f1;
    box-shadow: 0 0 0 2px rgba(99,102,241,0.25);
}

/* ================= SELECT ================= */
.auth-form select {
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg width='16' height='16' fill='none' stroke='%2399a2ff' stroke-width='2' viewBox='0 0 24 24'%3E%3Cpath d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 0.9rem center;
    background-size: 1rem;
}

.auth-form option {
    background: #020617;
    color: #e5e7eb;
}

/* ================= BUTTON ================= */
.auth-form button {
    margin-top: 0.8rem;
    width: 100%;
    padding: 0.8rem;
    border-radius: 0.9rem;
    border: none;
    background: linear-gradient(135deg, #6366f1, #4f46e5);
    color: white;
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    box-shadow: 0 10px 30px rgba(99,102,241,0.35);
}

.auth-form button:hover {
    background: linear-gradient(135deg, #4f46e5, #4338ca);
}

/* ================= ERROR ================= */
.auth-form .error-text {
    color: #f87171;
    font-size: 0.8rem;
    margin-top: -0.4rem;
    margin-bottom: 0.6rem;
}

/* ================= RESPONSIVE ================= */
@media (max-width: 480px) {
    .flip-card-inner {
        min-height: 580px;
    }

    .auth-title {
        font-size: 1.5rem;
    }

    .flip-card-front,
    .flip-card-back {
        padding: 2rem 1.5rem;
    }
}
</style>
@endpush


@section('content')

<div class="auth-container max-sm:mb-20 lg:h-[800px] flex justify-center max-sm:items-center lg:pt-10">
    <div class="flip-card" id="flipCard">
        <button class="switch-btn" id="switchBtn"
            type="button"
            onclick="switchPanel()">
            Register
        </button>
        <div class="flip-card-inner">
            <!-- Login Form -->
            <div class="flip-card-front">
                <div class="auth-title">Login</div>
                <form class="auth-form" method="POST" action="{{ route('auth.loginsiswa') }}">
                    @csrf
                    <label for="login-email">Email</label>
                    <input type="email" id="login-email" name="email" required autocomplete="email" placeholder="Email" value="{{ old('email') }}">
                    @error('email')
                        <div style="color:#f87171; font-size:0.875rem;">{{ $message }}</div>
                    @enderror

                    <label for="login-password">Password</label>
                    <input type="password" id="login-password" name="password" required autocomplete="current-password" placeholder="Password">
                    @error('password')
                        <div style="color:#f87171; font-size:0.875rem;">{{ $message }}</div>
                    @enderror

                    <button type="submit">Login</button>
                    <div class="mt-2 text-center">
                        <a href="{{ route('auth.login-dash') }}" style="font-size: 0.9rem; color:#6366f1;">Login sebagai Admin/Guru</a>
                    </div>
                </form>
            </div>
            <!-- Register Form -->
            <div class="flip-card-back h-fit lg:pb-20">
                <div class="auth-title">Register</div>
                <form class="auth-form" method="POST" action="{{ route('auth.registersiswa') }}">
                    @csrf
                    <label for="register-name">Nama</label>
                    <input type="text" id="register-name" name="nama_siswa" required autocomplete="name" placeholder="Nama Lengkap" value="{{ old('nama_siswa') }}">
                    @error('nama_siswa')
                        <div style="color:#f87171; font-size:0.875rem;">{{ $message }}</div>
                    @enderror

                    <label for="register-email">Email</label>
                    <input type="email" id="register-email" name="email" required autocomplete="email" placeholder="Email" value="{{ old('email') }}">
                    @error('email')
                        <div style="color:#f87171; font-size:0.875rem;">{{ $message }}</div>
                    @enderror

                    <label for="register-password">Password</label>
                    <input type="password" id="register-password" name="password" required autocomplete="new-password" placeholder="Password">
                    @error('password')
                        <div style="color:#f87171; font-size:0.875rem;">{{ $message }}</div>
                    @enderror

                    <label for="register-password-confirm">Konfirmasi Password</label>
                    <input type="password" id="register-password-confirm" name="password_confirmation" required autocomplete="new-password" placeholder="Konfirmasi Password">
                    @error('password_confirmation')
                        <div style="color:#f87171; font-size:0.875rem;">{{ $message }}</div>
                    @enderror

                    <label for="register-phone">No. Telp</label>
                    <input type="text" id="register-phone" name="no_telp" required autocomplete="tel" placeholder="Nomor Telepon" value="{{ old('no_telp') }}">
                    @error('no_telp')
                        <div style="color:#f87171; font-size:0.875rem;">{{ $message }}</div>
                    @enderror

                    <label for="register-class">Kelas</label>
                    <select id="register-class" name="class_id" required class="auth-form-select">
                        <option value="">Pilih Kelas</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                        @endforeach
                    </select>
                    @error('class_id')
                        <div style="color:#f87171; font-size:0.875rem;">{{ $message }}</div>
                    @enderror

                    <button type="submit">Register</button>
                </form>
            </div>
        </div>
    </div>
</div>
</div>
@endsection

@push('script')
<script>
    const flipCard = document.getElementById('flipCard');
    const switchBtn = document.getElementById('switchBtn');
    let isLogin = true;

    function switchPanel() {
        if (isLogin) {
            window.location.search = '?panel=register';
        } else {
            window.location.search = '?panel=login';
        }
    }

    // Panel logic on load
    let panel = 'login';
    const params = new URLSearchParams(window.location.search);
    if (params.get('panel') === 'register') {
        flipCard.classList.add('flipped');
        isLogin = false;
        switchBtn.textContent = 'Login';
        panel = 'register';
    } else {
        flipCard.classList.remove('flipped');
        isLogin = true;
        switchBtn.textContent = 'Register';
        panel = 'login';
    }
</script>
@endpush
