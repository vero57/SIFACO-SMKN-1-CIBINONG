@extends(
    "dashboard.layout.app",
    [
        "title" => "Absen PRojek",
    ]
)

@section('content')
    <div id="contentArea" class="w-full h-full">
        <div id="dashboard-content" class="content-section">
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-2 sm:gap-3 md:gap-4 mb-4 md:mb-6">
                <div class="bg-slate-800/50 backdrop-blur-sm rounded-lg sm:rounded-xl p-3 sm:p-4 md:p-6 border border-slate-700">
                    <div class="flex items-center justify-between gap-2 sm:gap-3">
                        <div class="min-w-0">
                            <p class="text-slate-400 text-xs sm:text-sm">Total Siswa</p>
                            <p class="text-lg sm:text-xl md:text-2xl font-bold text-white">{{ $totalSiswa }}</p>
                        </div>
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-500/20 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-users text-blue-400 text-base sm:text-lg md:text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-800/50 backdrop-blur-sm rounded-lg sm:rounded-xl p-3 sm:p-4 md:p-6 border border-slate-700">
                    <div class="flex items-center justify-between gap-2 sm:gap-3">
                        <div class="min-w-0">
                            <p class="text-slate-400 text-xs sm:text-sm">Siswa Masuk</p>
                            <p class="text-lg sm:text-xl md:text-2xl font-bold text-white">{{ $siswaMasuk }}</p>
                        </div>
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-green-500/20 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-check-circle text-green-400 text-base sm:text-lg md:text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-800/50 backdrop-blur-sm rounded-lg sm:rounded-xl p-3 sm:p-4 md:p-6 border border-slate-700">
                    <div class="flex items-center justify-between gap-2 sm:gap-3">
                        <div class="min-w-0">
                            <p class="text-slate-400 text-xs sm:text-sm">Siswa Sakit</p>
                            <p class="text-lg sm:text-xl md:text-2xl font-bold text-white">{{ $siswaSakit }}</p>
                        </div>
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-red-500/20 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-medkit text-red-400 text-base sm:text-lg md:text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-800/50 backdrop-blur-sm rounded-lg sm:rounded-xl p-3 sm:p-4 md:p-6 border border-slate-700">
                    <div class="flex items-center justify-between gap-2 sm:gap-3">
                        <div class="min-w-0">
                            <p class="text-slate-400 text-xs sm:text-sm">Siswa Izin</p>
                            <p class="text-lg sm:text-xl md:text-2xl font-bold text-white">{{ $siswaIzin }}</p>
                        </div>
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-yellow-500/20 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-envelope text-yellow-400 text-base sm:text-lg md:text-xl"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-slate-800/50 backdrop-blur-sm rounded-lg sm:rounded-xl p-3 sm:p-4 md:p-6 border border-slate-700">
                    <div class="flex items-center justify-between gap-2 sm:gap-3">
                        <div class="min-w-0">
                            <p class="text-slate-400 text-xs sm:text-sm">Siswa Dispen</p>
                            <p class="text-lg sm:text-xl md:text-2xl font-bold text-white">{{ $siswaDispen }}</p>
                        </div>
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-orange-500/20 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-clock text-orange-400 text-base sm:text-lg md:text-xl"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-slate-800/50 backdrop-blur-sm rounded-lg sm:rounded-xl p-3 sm:p-4 md:p-6 border border-slate-700">
                    <div class="flex items-center justify-between gap-2 sm:gap-3">
                        <div class="min-w-0">
                            <p class="text-slate-400 text-xs sm:text-sm">Tanpa Keterangan/Alfa</p>
                            <p class="text-lg sm:text-xl md:text-2xl font-bold text-white">{{ $siswaAlfa }}</p>
                        </div>
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gray-500/20 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-question-circle text-gray-400 text-base sm:text-lg md:text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- <div class="bg-slate-800/50 backdrop-blur-sm rounded-xl p-4 md:p-6 border border-slate-700 mb-6">
                <h3 class="text-base md:text-lg font-semibold text-white mb-2 md:mb-4">Siswa dengan Izin Approved</h3>
                @if($approvedPermissionsToday->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-slate-300">
                            <thead class="text-xs text-slate-400 uppercase bg-slate-700">
                                <tr>
                                    <th class="px-4 py-3">Nama Siswa</th>
                                    <th class="px-4 py-3">Tipe Izin</th>
                                    <th class="px-4 py-3">Deskripsi</th>
                                    <th class="px-4 py-3">Tanggal Dibuat</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($approvedPermissionsToday as $permission)
                                    <tr class="border-b border-slate-700 hover:bg-slate-700/50">
                                        <td class="px-4 py-3">{{ $permission->student->name ?? 'N/A' }}</td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                                @if($permission->type == 'sakit') bg-red-500/20 text-red-400
                                                @elseif($permission->type == 'izin') bg-yellow-500/20 text-yellow-400
                                                @else bg-orange-500/20 text-orange-400 @endif">
                                                {{ ucfirst($permission->type) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">{{ $permission->description ?? '-' }}</td>
                                        <td class="px-4 py-3">{{ $permission->created_at->format('d/m/Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-slate-400">Tidak ada siswa dengan izin approved saat ini.</p>
                @endif
            </div> -->

            <div class="bg-slate-800/50 backdrop-blur-sm rounded-xl p-4 md:p-6 border border-slate-700">
                <h3 class="text-base md:text-lg font-semibold text-white mb-2 md:mb-4">Analytics Overview</h3>
                <div class="h-40 md:h-64">
                    <canvas id="attendanceChart"></canvas>
                </div>
            </div>

            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const ctx = document.getElementById('attendanceChart').getContext('2d');
                    const attendanceChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: ['Total Siswa', 'Siswa Masuk', 'Siswa Sakit', 'Siswa Izin', 'Siswa Dispen', 'Tanpa Keterangan'],
                            datasets: [{
                                label: 'Jumlah',
                                data: [
                                    {{ $totalSiswa }},
                                    {{ $siswaMasuk }},
                                    {{ $siswaSakit }},
                                    {{ $siswaIzin }},
                                    {{ $siswaDispen }},
                                    {{ $siswaAlfa }}
                                ],
                                backgroundColor: [
                                    'rgba(59, 130, 246, 0.8)', // blue
                                    'rgba(34, 197, 94, 0.8)',  // green
                                    'rgba(239, 68, 68, 0.8)',  // red
                                    'rgba(245, 158, 11, 0.8)', // yellow
                                    'rgba(249, 115, 22, 0.8)', // orange
                                    'rgba(107, 114, 128, 0.8)' // gray
                                ],
                                borderColor: [
                                    'rgba(59, 130, 246, 1)',
                                    'rgba(34, 197, 94, 1)',
                                    'rgba(239, 68, 68, 1)',
                                    'rgba(245, 158, 11, 1)',
                                    'rgba(249, 115, 22, 1)',
                                    'rgba(107, 114, 128, 1)'
                                ],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            indexAxis: 'y', // Horizontal bar chart
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                            scales: {
                                x: {
                                    beginAtZero: true,
                                    ticks: {
                                        color: '#cbd5e1'
                                    },
                                    grid: {
                                        color: '#374151'
                                    }
                                },
                                y: {
                                    ticks: {
                                        color: '#cbd5e1'
                                    },
                                    grid: {
                                        color: '#374151'
                                    }
                                }
                            }
                        }
                    });
                });
            </script>
        </div>
    </div>
@endsection
