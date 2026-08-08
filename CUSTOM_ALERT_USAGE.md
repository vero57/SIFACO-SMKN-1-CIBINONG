# Custom Alert Usage Guide

## Overview
Custom alert system menggunakan design dari `alert.blade.php` untuk menampilkan notifikasi yang konsisten di seluruh aplikasi.

## Fitur Tersedia

### 1. Success Alert
Menampilkan pesan sukses dengan ikon checkmark hijau.

```javascript
// Basic usage
showSuccess('Data berhasil disimpan', 'Sukses!');

// Dengan durasi custom (dalam ms)
showSuccess('User berhasil ditambahkan', 'Berhasil!', 5000);

// Default durasi adalah 3000ms
showSuccess('Operasi selesai');
```

### 2. Error Alert
Menampilkan pesan error dengan ikon X merah.

```javascript
// Basic usage
showError('Email sudah terdaftar', 'Error!');

// Dengan durasi custom
showError('Terjadi kesalahan pada server', 'Error!', 5000);

// Default durasi adalah 4000ms
showError('Data tidak valid');
```

### 3. Warning Alert
Menampilkan pesan peringatan dengan ikon warning kuning.

```javascript
// Basic usage
showWarning('Perhatian penting', 'Peringatan!');

// Default durasi adalah 3500ms
showWarning('Aksi ini tidak dapat dibatalkan');
```

### 4. Info Alert
Menampilkan pesan informasi dengan ikon info biru.

```javascript
// Basic usage
showInfo('Fitur baru telah ditambahkan', 'Informasi');

// Default durasi adalah 3000ms
showInfo('Sistem sedang dalam maintenance');
```

### 5. Confirmation Dialog
Menampilkan dialog konfirmasi dengan tombol "Ya" dan "Tidak".

```javascript
// Basic usage
showConfirm(
    'Apakah Anda yakin ingin menghapus data ini?',
    'Konfirmasi Penghapusan',
    function() {
        // Callback ketika user klik "Ya"
        console.log('User confirmed');
        // Jalankan aksi penghapusan di sini
    },
    function() {
        // Callback ketika user klik "Tidak" (opsional)
        console.log('User cancelled');
    }
);
```

### 6. Loading Alert
Menampilkan loading spinner tanpa timer auto-close.

```javascript
// Menampilkan loading
const loadingAlert = showLoading('Memproses data...', 'Mohon tunggu');

// Menutup loading setelah selesai
setTimeout(() => {
    loadingAlert.close();
    showSuccess('Data berhasil diproses');
}, 3000);
```

### 7. Validation Errors
Menampilkan multiple validation errors dari server.

```javascript
// Dari server response
const errors = {
    'name': 'Nama wajib diisi',
    'email': 'Email sudah terdaftar',
    'password': 'Password minimal 8 karakter'
};

showValidationErrors(errors, 'Validasi Input Gagal');
```

## Contoh Implementasi

### Dalam Form
```html
<form id="createUserForm">
    <input type="text" name="name" required />
    <input type="email" name="email" required />
    <button type="submit">Simpan</button>
</form>

<script>
    document.getElementById('createUserForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Tampilkan loading
        showLoading('Menyimpan data...', 'Mohon tunggu');
        
        // Submit form atau AJAX request
        fetch(this.action, {
            method: 'POST',
            body: new FormData(this)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccess(data.message, 'Berhasil!');
                // Redirect atau refresh halaman
                setTimeout(() => window.location.reload(), 2000);
            } else {
                showError(data.message, 'Error!');
            }
        })
        .catch(error => {
            showError('Terjadi kesalahan pada server', 'Error!');
        });
    });
</script>
```

### Dalam Server Response
```blade
@if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            showSuccess('{{ session("success") }}', 'Berhasil!');
        });
    </script>
@endif

@if($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const errors = {
                @foreach($errors->all() as $error)
                    '{{ $loop->index }}': '{{ $error }}',
                @endforeach
            };
            showValidationErrors(errors, 'Validasi Input Gagal');
        });
    </script>
@endif
```

### Untuk Action Confirmation
```javascript
function confirmDelete(id, name) {
    showConfirm(
        `Apakah Anda yakin ingin menghapus "${name}"?`,
        'Konfirmasi Penghapusan',
        function() {
            // User confirmed
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/delete/${id}`;
            form.innerHTML = '@csrf @method("DELETE")';
            document.body.appendChild(form);
            form.submit();
        }
    );
}
```

## Design Details

### Styling
- **Background**: Gradient linear dari #74aabf ke #3986a3 (top border)
- **Border Radius**: 20px
- **Shadow**: Drop shadow style
- **Font**: Sans-serif
- **Text Color**: stone-900 (dark gray) untuk title, black untuk message

### Icons
- **Success**: Green checkmark (fa-check-circle)
- **Error**: Red X (fa-times-circle)
- **Warning**: Yellow exclamation (fa-exclamation-circle)
- **Info**: Blue info (fa-info-circle)
- **Question**: Blue question (fa-question-circle)
- **Loading**: Spinning spinner (fa-spinner)

### Buttons
- **Primary Button (Ok/Ya)**: Gradient dari #74AABF ke #3986A3
- **Secondary Button (Tidak)**: Border stone-300, text stone-700

## Notes
- Semua fungsi mengembalikan object dengan properties `element` dan `close()`
- Durasi timer dalam milliseconds (1000 = 1 detik)
- Alert otomatis menutup saat user klik tombol
- Dialog konfirmasi tidak memiliki auto-close timer
- Semua alert support multiple instances (dapat menampilkan beberapa sekaligus)
