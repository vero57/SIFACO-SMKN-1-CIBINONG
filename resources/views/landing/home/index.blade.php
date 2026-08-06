@extends("landing.layout.app",
    [
        "title" => "SIFARECO",
    ]
)

@section("content")
<section class="min-h-screen text-on-surface bg-background antialiased pb-24">
  @include("landing.partials.header")

  @if(session('success'))
      <script>
          Swal.fire({
              icon: 'success',
              title: 'Berhasil!',
              text: '{{ session("success") }}',
              timer: 3000,
              showConfirmButton: false
          });
      </script>
  @endif
  @if($errors->any())
      <script>
          Swal.fire({
              icon: 'error',
              title: 'Error!',
              html: '<ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>',
          });
      </script>
  @endif

  @php
      $todayAttendance = auth()->check() ? collect($attendances)->first(function($att) {
          return \Carbon\Carbon::parse($att->date)->isToday();
      }) : null;
  @endphp

  <!-- ================= DESKTOP VIEW ================= -->
  <div class="hidden md:block">
    <!-- Hero Section -->
    <section class="relative overflow-hidden pt-xl pb-24 px-md md:px-margin-desktop max-w-7xl mx-auto">
      <div class="grid lg:grid-cols-2 gap-xl items-center">
        <div class="space-y-md z-10">
          <div class="inline-flex items-center gap-xs px-base py-xs bg-primary-fixed text-on-primary-fixed rounded-full">
            <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">verified</span>
            <span class="font-label-sm text-label-sm">
              @if ($userName)
                  Selamat datang, {{ $userName }}
              @else
                  Selamat datang di SIFARECO
              @endif
            </span>
          </div>
          <h1 class="font-display-lg text-display-lg leading-tight text-on-surface">
            Kehadiran Lebih <span class="text-primary">Cerdas</span> &amp; Terpercaya.
          </h1>
          <p class="font-body-lg text-body-lg text-on-surface-variant max-w-xl">
            SIFARECO menyederhanakan kehadiran sekolah dengan pengenalan wajah tingkat lanjut. Cepat, akurat, dan terintegrasi dengan jurnal harian Anda.
          </p>
          <div class="flex flex-wrap gap-base pt-base">
            @if(auth()->check())
              <a href="{{ route('feature.absen') }}" class="bg-primary hover:bg-primary-container text-on-primary font-label-md text-label-md px-xl py-md rounded-xl transition-all active:scale-95 flex items-center gap-base shadow-lg shadow-primary/20">
                <span class="material-symbols-outlined">camera_front</span>
                Mulai Absen Sekarang
              </a>
            @else
              <a href="{{ route('auth.login-register') }}" class="bg-primary hover:bg-primary-container text-on-primary font-label-md text-label-md px-xl py-md rounded-xl transition-all active:scale-95 flex items-center gap-base shadow-lg shadow-primary/20">
                <span class="material-symbols-outlined">login</span>
                Login untuk Absen
              </a>
            @endif
            <a href="#action-cards" class="bg-surface-container-low hover:bg-surface-container-high text-primary font-label-md text-label-md px-xl py-md rounded-xl transition-all active:scale-95 border border-outline-variant flex items-center justify-center">
              Pelajari Selengkapnya
            </a>
          </div>
        </div>
        <div class="relative z-10">
          <div class="relative bg-white p-base rounded-[32px] shadow-2xl overflow-hidden aspect-[4/3] group">
            <img class="w-full h-full object-cover rounded-[24px]"
              alt="Face recognition interface mockup"
              src="{{asset('assets/images/landing/logo_sija.png')}}" />
            <div class="absolute inset-0 bg-gradient-to-t from-primary/40 to-transparent flex flex-col justify-end p-lg">
              <div class="glass-card p-md rounded-2xl flex items-center gap-md">
                <div class="bg-secondary text-on-secondary w-12 h-12 rounded-full flex items-center justify-center">
                  <span class="material-symbols-outlined">schedule</span>
                </div>
                <div>
                  <p id="clock" class="font-headline-md text-headline-md font-bold text-primary">00:00:00</p>
                  <p id="date" class="font-label-sm text-label-sm text-on-surface-variant">Loading...</p>
                </div>
              </div>
            </div>
          </div>
          <!-- Decorative element -->
          <div class="absolute -top-12 -right-12 w-64 h-64 bg-primary-fixed-dim/30 rounded-full blur-3xl -z-10"></div>
          <div class="absolute -bottom-12 -left-12 w-48 h-48 bg-secondary-fixed/30 rounded-full blur-3xl -z-10"></div>
        </div>
      </div>
    </section>

    <!-- Large Action Cards / Feature Cards -->
    <section class="py-xl px-md md:px-margin-desktop bg-surface-container-low" id="action-cards">
      <div class="max-w-7xl mx-auto">
        <div class="mb-lg">
          <h2 class="font-headline-lg text-headline-lg text-on-surface">Layanan Utama</h2>
          <p class="font-body-md text-body-md text-on-surface-variant">Pilih tindakan yang ingin Anda lakukan hari ini.</p>
        </div>

        @if(auth()->check() && (auth()->user()->role->name === 'Admin' || auth()->user()->role->name === 'Guru'))
          <div class="flex justify-center items-center py-10">
            <a href="{{ route('dashboard.dash') }}" class="group relative bg-white p-lg rounded-[32px] border border-outline-variant shadow-sm hover:shadow-xl transition-all cursor-pointer overflow-hidden max-w-md w-full text-center">
              <div class="absolute top-0 right-0 w-32 h-32 bg-primary/5 rounded-bl-full transition-all group-hover:scale-150"></div>
              <div class="relative z-10 space-y-md flex flex-col items-center">
                <div class="w-16 h-16 bg-primary-container text-on-primary-container rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                  <span class="material-symbols-outlined text-4xl" style="font-variation-settings: 'FILL' 1;">dashboard</span>
                </div>
                <div>
                  <h3 class="font-headline-md text-headline-md font-bold text-on-surface">Dashboard</h3>
                  <p class="font-body-md text-body-md text-on-surface-variant mt-xs">Kembali ke halaman dashboard Admin/Guru.</p>
                </div>
                <div class="pt-base flex items-center text-primary font-label-md text-label-md font-bold">
                  Buka Dashboard
                  <span class="material-symbols-outlined ml-xs group-hover:translate-x-1 transition-transform">arrow_forward</span>
                </div>
              </div>
            </a>
          </div>
        @else
          <div class="grid grid-cols-1 md:grid-cols-3 gap-gutter">
            <!-- Primary Action: Absen Wajah -->
            <a href="{{ route('feature.absen') }}" class="group relative bg-white p-lg rounded-[32px] border border-outline-variant shadow-sm hover:shadow-xl transition-all cursor-pointer overflow-hidden">
              <div class="absolute top-0 right-0 w-32 h-32 bg-primary/5 rounded-bl-full transition-all group-hover:scale-150"></div>
              <div class="relative z-10 space-y-md">
                <div class="w-16 h-16 bg-primary-container text-on-primary-container rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                  <span class="material-symbols-outlined text-4xl" style="font-variation-settings: 'FILL' 1;">face_retouching_natural</span>
                </div>
                <div>
                  <h3 class="font-headline-md text-headline-md font-bold text-on-surface">Absen Wajah</h3>
                  <p class="font-body-md text-body-md text-on-surface-variant mt-xs">Catat kehadiran harian dengan verifikasi biometrik wajah.</p>
                </div>
                <div class="pt-base flex items-center text-primary font-label-md text-label-md font-bold">
                  Mulai Absen
                  <span class="material-symbols-outlined ml-xs group-hover:translate-x-1 transition-transform">arrow_forward</span>
                </div>
              </div>
            </a>

            <!-- Secondary Action: Izin -->
            <a href="{{ route('feature.izin') }}" class="group relative bg-white p-lg rounded-[32px] border border-outline-variant shadow-sm hover:shadow-xl transition-all cursor-pointer overflow-hidden">
              <div class="absolute top-0 right-0 w-32 h-32 bg-secondary/5 rounded-bl-full transition-all group-hover:scale-150"></div>
              <div class="relative z-10 space-y-md">
                <div class="w-16 h-16 bg-secondary-container text-on-secondary-container rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                  <span class="material-symbols-outlined text-4xl" style="font-variation-settings: 'FILL' 1;">assignment_late</span>
                </div>
                <div>
                  <h3 class="font-headline-md text-headline-md font-bold text-on-surface">Izin</h3>
                  <p class="font-body-md text-body-md text-on-surface-variant mt-xs">Ajukan izin atau sakit dengan mengunggah bukti dokumen resmi.</p>
                </div>
                <div class="pt-base flex items-center text-secondary font-label-md text-label-md font-bold">
                  Buat Pengajuan
                  <span class="material-symbols-outlined ml-xs group-hover:translate-x-1 transition-transform">arrow_forward</span>
                </div>
              </div>
            </a>

            <!-- Secondary Action: Jurnal Harian -->
            <a href="{{ route('feature.jurnal') }}" class="group relative bg-white p-lg rounded-[32px] border border-outline-variant shadow-sm hover:shadow-xl transition-all cursor-pointer overflow-hidden">
              <div class="absolute top-0 right-0 w-32 h-32 bg-tertiary-fixed-dim/10 rounded-bl-full transition-all group-hover:scale-150"></div>
              <div class="relative z-10 space-y-md">
                <div class="w-16 h-16 bg-tertiary-container text-on-tertiary-container rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                  <span class="material-symbols-outlined text-4xl" style="font-variation-settings: 'FILL' 1;">edit_note</span>
                </div>
                <div>
                  <h3 class="font-headline-md text-headline-md font-bold text-on-surface">Jurnal Harian</h3>
                  <p class="font-body-md text-body-md text-on-surface-variant mt-xs">Laporkan kegiatan belajar dan pencapaian harian Anda.</p>
                </div>
                <div class="pt-base flex items-center text-tertiary font-label-md text-label-md font-bold">
                  Tulis Jurnal
                  <span class="material-symbols-outlined ml-xs group-hover:translate-x-1 transition-transform">arrow_forward</span>
                </div>
              </div>
            </a>
          </div>
        @endif
      </div>
    </section>

    @if(!(auth()->check() && (auth()->user()->role->name === 'Admin' || auth()->user()->role->name === 'Guru')))
      <!-- History Section -->
      <section class="py-xl px-md md:px-margin-desktop max-w-7xl mx-auto" id="history">
        <div class="flex flex-col md:flex-row justify-between items-end gap-md mb-lg">
          <div>
            <h2 class="font-headline-lg text-headline-lg text-on-surface">Riwayat Aktivitas</h2>
            <p class="font-body-md text-body-md text-on-surface-variant">Pantau rekaman kehadiran dan laporan Anda dalam 30 hari terakhir.</p>
          </div>
          <div class="bg-surface-container p-1 rounded-xl inline-flex">
            <button class="tab-btn px-md py-xs rounded-lg font-label-md text-label-md transition-all bg-white text-primary shadow-sm" data-tab="absen">Absen</button>
            <button class="tab-btn px-md py-xs rounded-lg font-label-md text-label-md transition-all text-on-surface-variant hover:bg-white/50" data-tab="izin">Izin</button>
            <button class="tab-btn px-md py-xs rounded-lg font-label-md text-label-md transition-all text-on-surface-variant hover:bg-white/50" data-tab="jurnal">Jurnal</button>
          </div>
        </div>

        <div class="bg-white rounded-[32px] border border-outline-variant shadow-sm overflow-hidden overflow-x-auto">
          @if(!auth()->check())
            <div class="text-center text-on-surface-variant py-12">
              <strong>Silahkan login terlebih dahulu untuk melihat riwayat aktivitas Anda</strong>
            </div>
          @else
            <!-- Table Absen -->
            <table id="table-absen" class="w-full text-left border-collapse min-w-[700px]">
              <thead class="bg-surface-container-low border-b border-outline-variant">
                <tr>
                  <th class="px-lg py-md font-label-md text-label-md text-on-surface uppercase tracking-wider">Nama</th>
                  <th class="px-lg py-md font-label-md text-label-md text-on-surface uppercase tracking-wider">Tanggal</th>
                  <th class="px-lg py-md font-label-md text-label-md text-on-surface uppercase tracking-wider">Jam Masuk</th>
                  <th class="px-lg py-md font-label-md text-label-md text-on-surface uppercase tracking-wider">Jam Pulang</th>
                  <th class="px-lg py-md font-label-md text-label-md text-on-surface uppercase tracking-wider">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-outline-variant">
                @forelse($attendances as $attendance)
                  <tr class="hover:bg-surface-container-lowest transition-colors">
                    <td class="px-lg py-lg">
                      <p class="font-body-md text-body-md font-bold text-on-surface">{{ auth()->user()->name }}</p>
                    </td>
                    <td class="px-lg py-lg">
                      <p class="font-body-md text-body-md font-bold text-on-surface">{{ \Carbon\Carbon::parse($attendance->date)->format('d F Y') }}</p>
                    </td>
                    <td class="px-lg py-lg">
                      <div class="flex items-center gap-base">
                        <span class="material-symbols-outlined text-primary">login</span>
                        <span class="font-body-md text-body-md">{{ $attendance->time_in ?? '-' }}</span>
                      </div>
                    </td>
                    <td class="px-lg py-lg" id="time-out-{{ $attendance->id }}">
                      <div class="flex items-center gap-base">
                        <span class="material-symbols-outlined text-tertiary">logout</span>
                        <span class="font-body-md text-body-md">{{ $attendance->time_out ?? '-' }}</span>
                      </div>
                    </td>
                    <td class="px-lg py-lg" id="action-{{ $attendance->id }}">
                      @if(!$attendance->time_out)
                        <button
                          onclick="checkOut({{ $attendance->id }})"
                          class="bg-primary hover:bg-primary-container text-on-primary font-label-sm text-label-sm px-md py-xs rounded-lg transition-all active:scale-95 shadow-sm"
                          data-class-id="{{ auth()->user()->classes()->first()->id ?? '' }}"
                        >
                          Pulang
                        </button>
                      @else
                        <span class="inline-flex items-center px-md py-xs rounded-full bg-secondary-container text-on-secondary-container font-label-sm text-label-sm">
                          <span class="w-1.5 h-1.5 rounded-full bg-on-secondary-container mr-2"></span>
                          Sudah Absensi Keluar
                        </span>
                      @endif
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="5" class="text-center text-on-surface-variant py-8">Belum ada data absen</td>
                  </tr>
                @endforelse
              </tbody>
            </table>

            <!-- Table Izin -->
            <table id="table-izin" class="w-full text-left border-collapse min-w-[700px] hidden">
              <thead class="bg-surface-container-low border-b border-outline-variant">
                <tr>
                  <th class="px-lg py-md font-label-md text-label-md text-on-surface uppercase tracking-wider">Nama</th>
                  <th class="px-lg py-md font-label-md text-label-md text-on-surface uppercase tracking-wider">Tanggal</th>
                  <th class="px-lg py-md font-label-md text-label-md text-on-surface uppercase tracking-wider">Jenis Izin</th>
                  <th class="px-lg py-md font-label-md text-label-md text-on-surface uppercase tracking-wider">Status</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-outline-variant">
                @forelse($permissions as $permission)
                  <tr class="hover:bg-surface-container-lowest transition-colors">
                    <td class="px-lg py-lg">
                      <p class="font-body-md text-body-md font-bold text-on-surface">{{ auth()->user()->name }}</p>
                    </td>
                    <td class="px-lg py-lg">
                      <p class="font-body-md text-body-md font-bold text-on-surface">{{ \Carbon\Carbon::parse($permission->created_at)->format('d F Y') }}</p>
                    </td>
                    <td class="px-lg py-lg">
                      <div class="flex items-center gap-base">
                        <span class="material-symbols-outlined text-secondary">assignment_late</span>
                        <span class="font-body-md text-body-md">{{ ucfirst($permission->type) }}</span>
                      </div>
                    </td>
                    <td class="px-lg py-lg">
                      @if($permission->status == 'approved')
                        <span class="inline-flex items-center px-md py-xs rounded-full bg-secondary-container text-on-secondary-container font-label-sm text-label-sm">
                          <span class="w-1.5 h-1.5 rounded-full bg-on-secondary-container mr-2"></span>
                          Disetujui
                        </span>
                      @elseif($permission->status == 'rejected')
                        <span class="inline-flex items-center px-md py-xs rounded-full bg-error-container text-on-error-container font-label-sm text-label-sm">
                          <span class="w-1.5 h-1.5 rounded-full bg-error mr-2"></span>
                          Ditolak
                        </span>
                      @else
                        <span class="inline-flex items-center px-md py-xs rounded-full bg-primary-fixed text-on-primary-fixed font-label-sm text-label-sm">
                          <span class="w-1.5 h-1.5 rounded-full bg-primary mr-2"></span>
                          Pending
                        </span>
                      @endif
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="4" class="text-center text-on-surface-variant py-8">Belum ada data izin</td>
                  </tr>
                @endforelse
              </tbody>
            </table>

            <!-- Table Jurnal -->
            <table id="table-jurnal" class="w-full text-left border-collapse min-w-[700px] hidden">
              <thead class="bg-surface-container-low border-b border-outline-variant">
                <tr>
                  <th class="px-lg py-md font-label-md text-label-md text-on-surface uppercase tracking-wider">Nama</th>
                  <th class="px-lg py-md font-label-md text-label-md text-on-surface uppercase tracking-wider">Tanggal</th>
                  <th class="px-lg py-md font-label-md text-label-md text-on-surface uppercase tracking-wider">Mapel</th>
                  <th class="px-lg py-md font-label-md text-label-md text-on-surface uppercase tracking-wider">Aktivitas</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-outline-variant">
                @forelse($journals as $journal)
                  <tr class="hover:bg-surface-container-lowest transition-colors">
                    <td class="px-lg py-lg">
                      <p class="font-body-md text-body-md font-bold text-on-surface">{{ auth()->user()->name }}</p>
                    </td>
                    <td class="px-lg py-lg">
                      <p class="font-body-md text-body-md font-bold text-on-surface">{{ \Carbon\Carbon::parse($journal->created_at)->format('d F Y') }}</p>
                    </td>
                    <td class="px-lg py-lg">
                      <div class="flex items-center gap-base">
                        <span class="material-symbols-outlined text-tertiary">edit_note</span>
                        <span class="font-body-md text-body-md font-bold text-on-surface">{{ $journal->subject->name ?? $journal->subject }}</span>
                      </div>
                    </td>
                    <td class="px-lg py-lg font-body-md text-body-md text-on-surface-variant">
                      {{ $journal->description }}
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="4" class="text-center text-on-surface-variant py-8">Belum ada data jurnal</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          @endif
        </div>
      </section>
    @endif
  </div>

  <!-- ================= MOBILE VIEW ================= -->
  <div class="block md:hidden px-margin-mobile mt-sm space-y-md">
    <!-- Welcome Hero -->
    <section class="relative overflow-hidden rounded-xl p-md bg-primary text-on-primary soft-shadow">
      <div class="absolute -right-12 -top-12 w-48 h-48 bg-primary-container opacity-20 rounded-full blur-3xl"></div>
      <div class="relative z-10">
        <p class="font-label-md text-label-md opacity-80 mb-xs">Selamat pagi,</p>
        <h1 class="font-headline-lg-mobile text-headline-lg-mobile font-bold mb-base">
          {{ $userName ?? 'Siswa' }}
        </h1>
        <div class="flex items-center gap-xs bg-white/10 w-fit px-sm py-1 rounded-full backdrop-blur-md">
          <span class="material-symbols-outlined text-[18px]">calendar_today</span>
          <span id="date-mobile" class="font-label-sm text-label-sm">Loading...</span>
        </div>
      </div>
    </section>

    <!-- Attendance Status Quick View -->
    <section class="grid grid-cols-2 gap-sm">
      <div class="bg-surface-container-lowest p-sm rounded-xl border border-outline-variant flex flex-col gap-xs">
        <span class="font-label-sm text-label-sm text-on-surface-variant">Absensi Hari Ini</span>
        <span class="font-headline-md text-headline-md text-secondary">
          {{ $todayAttendance ? ($todayAttendance->time_in ?? '-') : '-' }}
        </span>
        <div class="flex items-center gap-xs">
          <div class="w-2 h-2 rounded-full {{ $todayAttendance ? 'bg-secondary' : 'bg-outline' }}"></div>
          <span class="font-label-sm text-label-sm {{ $todayAttendance ? 'text-secondary' : 'text-on-surface-variant' }}">
            {{ $todayAttendance ? 'Sudah Masuk' : 'Belum Absen' }}
          </span>
        </div>
      </div>
      <div class="bg-surface-container-lowest p-sm rounded-xl border border-outline-variant flex flex-col gap-xs">
        <span class="font-label-sm text-label-sm text-on-surface-variant">Status Jurnal</span>
        <span class="font-headline-md text-headline-md text-on-surface">
          {{ auth()->check() ? $journals->count() : 0 }}
        </span>
        <span class="font-label-sm text-label-sm text-on-surface-variant">Selesai diinput</span>
      </div>
    </section>

    <!-- Quick Action Cards (Bento Style) -->
    <section class="space-y-sm">
      <h2 class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider">Layanan Cepat</h2>
      <div class="grid grid-cols-6 gap-sm">
        @if(auth()->check() && (auth()->user()->role->name === 'Admin' || auth()->user()->role->name === 'Guru'))
          <a href="{{ route('dashboard.dash') }}" class="col-span-6 bg-surface-container-lowest border border-primary/20 rounded-xl p-md flex items-center justify-between group active:scale-[0.98] transition-transform scanner-glow border-2">
            <div class="flex items-center gap-md">
              <div class="w-14 h-14 bg-primary-container/10 rounded-xl flex items-center justify-center text-primary">
                <span class="material-symbols-outlined !text-[32px]" style="font-variation-settings: 'FILL' 1;">dashboard</span>
              </div>
              <div class="text-left">
                <span class="font-headline-md text-headline-md block text-primary">Dashboard</span>
                <span class="font-body-sm text-body-sm text-on-surface-variant">Kembali ke dashboard admin/guru</span>
              </div>
            </div>
            <span class="material-symbols-outlined text-primary group-hover:translate-x-1 transition-transform">arrow_forward_ios</span>
          </a>
        @else
          <!-- Absen - Main Action -->
          <a href="{{ route('feature.absen') }}" class="col-span-6 bg-surface-container-lowest border border-primary/20 rounded-xl p-md flex items-center justify-between group active:scale-[0.98] transition-transform scanner-glow border-2">
            <div class="flex items-center gap-md">
              <div class="w-14 h-14 bg-primary-container/10 rounded-xl flex items-center justify-center text-primary">
                <span class="material-symbols-outlined !text-[32px]" style="font-variation-settings: 'FILL' 1;">shape_recognition</span>
              </div>
              <div class="text-left">
                <span class="font-headline-md text-headline-md block text-primary">Absen</span>
                <span class="font-body-sm text-body-sm text-on-surface-variant">Scan wajah untuk kehadiran</span>
              </div>
            </div>
            <span class="material-symbols-outlined text-primary group-hover:translate-x-1 transition-transform">arrow_forward_ios</span>
          </a>
          <!-- Izin -->
          <a href="{{ route('feature.izin') }}" class="col-span-3 bg-surface-container-lowest border border-outline-variant rounded-xl p-md flex flex-col gap-sm active:scale-95 transition-transform">
            <div class="w-12 h-12 bg-tertiary-container/10 rounded-lg flex items-center justify-center text-tertiary">
              <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">assignment_late</span>
            </div>
            <div class="text-left">
              <span class="font-label-md text-label-md font-bold block">Izin</span>
              <span class="font-label-sm text-label-sm text-on-surface-variant">Sakit / Keperluan</span>
            </div>
          </a>
          <!-- Jurnal -->
          <a href="{{ route('feature.jurnal') }}" class="col-span-3 bg-surface-container-lowest border border-outline-variant rounded-xl p-md flex flex-col gap-sm active:scale-95 transition-transform">
            <div class="w-12 h-12 bg-secondary-container/20 rounded-lg flex items-center justify-center text-secondary">
              <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">menu_book</span>
            </div>
            <div class="text-left">
              <span class="font-label-md text-label-md font-bold block">Jurnal</span>
              <span class="font-label-sm text-label-sm text-on-surface-variant">Laporan harian</span>
            </div>
          </a>
        @endif
      </div>
    </section>

    <!-- Riwayat Aktivitas Mobile -->
    @if(!(auth()->check() && (auth()->user()->role->name === 'Admin' || auth()->user()->role->name === 'Guru')))
      <section class="space-y-sm">
        <div class="flex justify-between items-center">
          <h2 class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider">Riwayat Aktivitas</h2>
          <a href="#history" class="text-primary font-label-md text-label-md">Lihat Semua</a>
        </div>
        <div class="space-y-base">
          @forelse(collect($attendances ?? [])->take(3) as $attendance)
            <div class="bg-surface-container-lowest p-sm rounded-xl border border-outline-variant flex items-center justify-between">
              <div class="flex items-center gap-sm">
                <div class="w-10 h-10 rounded-full bg-surface-container flex items-center justify-center text-secondary">
                  <span class="material-symbols-outlined">check_circle</span>
                </div>
                <div>
                  <p class="font-label-md text-label-md text-on-surface">Hadir - Absen Masuk</p>
                  <p class="font-body-sm text-body-sm text-on-surface-variant">
                    {{ \Carbon\Carbon::parse($attendance->date)->format('l, d M') }} • {{ $attendance->time_in ?? '-' }}
                  </p>
                </div>
              </div>
              <span class="bg-secondary/10 text-secondary px-base py-0.5 rounded-full font-label-sm text-label-sm">Hadir</span>
            </div>
          @empty
            <div class="bg-surface-container-lowest p-sm rounded-xl border border-outline-variant text-center text-on-surface-variant py-4">
              Belum ada data aktivitas
            </div>
          @endforelse
        </div>
      </section>
    @endif
  </div>

  @include("landing.partials.nav")
</section>
@endsection

@push("script")
<script>
    // Clock & Live Date
    function updateClock() {
      const now = new Date();
      const clockEl = document.getElementById('clock');
      const dateEl = document.getElementById('date');
      const dateMobileEl = document.getElementById('date-mobile');

      if (clockEl) {
        clockEl.textContent = now.toLocaleTimeString('id-ID', { hour12: false });
      }
      if (dateEl) {
        dateEl.textContent = now.toLocaleDateString('id-ID', {
          weekday: 'long',
          year: 'numeric',
          month: 'long',
          day: 'numeric'
        });
      }
      if (dateMobileEl) {
        dateMobileEl.textContent = now.toLocaleDateString('id-ID', {
          weekday: 'long',
          year: 'numeric',
          month: 'long',
          day: 'numeric'
        });
      }
    }
    setInterval(updateClock, 1000);
    updateClock();

    // Tab Switching
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabTables = {
      absen: document.getElementById('table-absen'),
      izin: document.getElementById('table-izin'),
      jurnal: document.getElementById('table-jurnal')
    };

    tabButtons.forEach(btn => {
      btn.addEventListener('click', () => {
        Object.values(tabTables).forEach(t => {
          if (t) t.classList.add('hidden');
        });
        tabButtons.forEach(b => {
          b.classList.remove('bg-white', 'text-primary', 'shadow-sm');
          b.classList.add('text-on-surface-variant', 'hover:bg-white/50');
        });

        const target = btn.getAttribute('data-tab');
        if (tabTables[target]) {
          tabTables[target].classList.remove('hidden');
        }
        btn.classList.add('bg-white', 'text-primary', 'shadow-sm');
        btn.classList.remove('text-on-surface-variant', 'hover:bg-white/50');
      });
    });

    // Set first tab active if exists
    if (tabButtons.length > 0) {
        tabButtons[0].click();
    }

    // Micro-interactions for buttons
    document.querySelectorAll("button").forEach((button) => {
        button.addEventListener("touchstart", function () {
            this.classList.add("scale-95");
        });
        button.addEventListener("touchend", function () {
            this.classList.remove("scale-95");
        });
    });

    // Check Out Function
    async function checkOut(attendanceId) {
        const button = document.querySelector(`#action-${attendanceId} button`);

        if (!button) {
            console.error('Button not found');
            return;
        }

        const classId = button.getAttribute('data-class-id');

        // Tampilkan loading
        const originalText = button.innerHTML;
        button.innerHTML = '<span class="flex items-center justify-center"><svg class="animate-spin h-4 w-4 mr-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Processing...</span>';
        button.disabled = true;
        button.classList.add('opacity-75');

        try {
            const response = await fetch('{{ route("feature.absen.checkout") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    attendance_id: attendanceId,
                    _token: '{{ csrf_token() }}'
                })
            });

            const result = await response.json();

            if (result.success) {
                // Update tampilan
                const timeOutContainer = document.getElementById(`time-out-${attendanceId}`);
                if (timeOutContainer) {
                    timeOutContainer.innerHTML = `<div class="flex items-center gap-base"><span class="material-symbols-outlined text-tertiary">logout</span><span class="font-body-md text-body-md">${result.time_out}</span></div>`;
                }

                const actionContainer = document.getElementById(`action-${attendanceId}`);
                if (actionContainer) {
                    actionContainer.innerHTML = `<span class="inline-flex items-center px-md py-xs rounded-full bg-secondary-container text-on-secondary-container font-label-sm text-label-sm"><span class="w-1.5 h-1.5 rounded-full bg-on-secondary-container mr-2"></span>✓ Check Out</span>`;
                }

                // Notifikasi sukses
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: result.message || 'Check out berhasil',
                    timer: 3000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end',
                });

            } else {
                // Notifikasi error
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: result.message || 'Terjadi kesalahan',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3b82f6'
                });

                // Reset button
                button.innerHTML = originalText;
                button.disabled = false;
                button.classList.remove('opacity-75');
            }
        } catch (error) {
            console.error('Error:', error);

            Swal.fire({
                icon: 'error',
                title: 'Koneksi Error!',
                text: 'Tidak dapat terhubung ke server. Periksa koneksi internet Anda.',
                confirmButtonText: 'OK',
                confirmButtonColor: '#3b82f6'
            });

            // Reset button
            button.innerHTML = originalText;
            button.disabled = false;
            button.classList.remove('opacity-75');
        }
    }
</script>
@endpush
