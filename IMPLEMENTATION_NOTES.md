# Custom Alert Implementation Guide

## Deskripsi
Implementasi custom alert yang menggunakan design dari `alert.blade.php` untuk menampilkan notifikasi yang konsisten di seluruh aplikasi. Design ini menggunakan gradient warna teal (#74aabf ke #3986a3) dengan styling modern.

## File-File yang Diubah

### 1. JavaScript Files
- **`public/js/custom-alert.js`** - Script alert standalone yang dapat digunakan langsung
- **`resources/js/custom-alert.js`** - Script alert untuk module system
- **`resources/js/app.js`** - Updated untuk import custom-alert

### 2. Blade Templates
- **`resources/views/dashboard/layout/app.blade.php`** - Layout utama yang meload custom-alert.js
- **`resources/views/dashboard/page/users_page/create.blade.php`** - Form create user dengan alert
- **`resources/views/dashboard/page/users_page/index.blade.php`** - List user dengan confirm dialog

### 3. Dokumentasi
- **`CUSTOM_ALERT_USAGE.md`** - Panduan lengkap penggunaan custom alert

## Fitur Yang Tersedia

### 1. Success Alert
```javascript
showSuccess('Data berhasil disimpan', 'Sukses!', 3000);
```
- Icon: Green checkmark
- Timer: 3000ms (default)
- Otomatis tertutup setelah timer selesai

### 2. Error Alert
```javascript
showError('Terjadi kesalahan', 'Error!', 4000);
```
- Icon: Red X mark
- Timer: 4000ms (default)
- Otomatis tertutup setelah timer selesai

### 3. Warning Alert
```javascript
showWarning('Perhatian penting', 'Peringatan!', 3500);
```
- Icon: Yellow exclamation mark
- Timer: 3500ms (default)

### 4. Info Alert
```javascript
showInfo('Informasi tambahan', 'Informasi', 3000);
```
- Icon: Blue info circle
- Timer: 3000ms (default)

### 5. Confirmation Dialog
```javascript
showConfirm(
    'Apakah Anda yakin?',
    'Konfirmasi',
    function() { 
        // Aksi ketika user klik "Ya"
    },
    function() { 
        // Aksi ketika user klik "Tidak"
    }
);
```
- Icon: Blue question mark
- Tombol: "Ya" dan "Tidak"
- Tidak memiliki timer auto-close

### 6. Loading Alert
```javascript
const loading = showLoading('Memproses...', 'Mohon tunggu');
// Setelah selesai
loading.close();
```
- Icon: Spinning spinner
- Tidak memiliki tombol close
- Harus di-close secara manual

### 7. Validation Errors
```javascript
const errors = {
    'name': 'Nama wajib diisi',
    'email': 'Email sudah terdaftar'
};
showValidationErrors(errors, 'Validasi Gagal');
```
- Icon: Red X mark
- Menampilkan multiple errors

## Design Details

### Styling
- **Background Modal**: White (#ffffff)
- **Top Border Gradient**: Linear dari #74aabf ke #3986a3
- **Border Radius**: 20px
- **Shadow**: Drop shadow style
- **Modal Width**: 560px max

### Button Styling
- **Primary (Ok/Ya)**: 
  - Background: Gradient dari #74AABF ke #3986A3
  - Text: White
  - Padding: px-10 py-2 untuk OK, px-6 py-2 untuk Ya
  - Border-radius: 5px

- **Secondary (Tidak)**:
  - Background: Transparent
  - Border: stone-300 (gray border)
  - Text: stone-700 (gray text)
  - Padding: px-6 py-2
  - Border-radius: 8px

### Icons
Semua menggunakan FontAwesome icons:
- Success: `fa-check-circle` (hijau)
- Error: `fa-times-circle` (merah)
- Warning: `fa-exclamation-circle` (kuning)
- Info: `fa-info-circle` (biru)
- Question: `fa-question-circle` (biru)
- Loading: `fa-spinner` (biru, dengan animate-spin)

## Contoh Implementasi

### Di Form Submission
```blade
@section('content')
<form id="userForm" action="{{ route('users.store') }}" method="POST">
    @csrf
    <input type="text" name="name" required />
    <button type="submit">Simpan</button>
</form>

<script>
    @if(session('success'))
        document.addEventListener('DOMContentLoaded', function() {
            showSuccess('{{ session("success") }}', 'Berhasil!');
        });
    @endif

    @if($errors->any())
        document.addEventListener('DOMContentLoaded', function() {
            const errors = {
                @foreach($errors->all() as $error)
                    '{{ $loop->index }}': '{{ $error }}',
                @endforeach
            };
            showValidationErrors(errors, 'Validasi Input Gagal');
        });
    @endif
</script>
@endsection
```

### Untuk Delete Confirmation
```javascript
function confirmDelete(id, name) {
    showConfirm(
        `Apakah Anda yakin ingin menghapus "${name}"?`,
        'Konfirmasi Penghapusan',
        function() {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/items/${id}`;
            form.innerHTML = '@csrf @method("DELETE")';
            document.body.appendChild(form);
            form.submit();
        }
    );
}
```

### Untuk AJAX Request
```javascript
document.getElementById('createBtn').addEventListener('click', function() {
    const loading = showLoading('Menyimpan data...', 'Mohon tunggu');
    
    fetch('/api/users', {
        method: 'POST',
        body: JSON.stringify(data),
        headers: {'Content-Type': 'application/json'}
    })
    .then(response => response.json())
    .then(data => {
        loading.close();
        if (data.success) {
            showSuccess(data.message, 'Berhasil!');
        } else {
            showError(data.message, 'Error!');
        }
    })
    .catch(error => {
        loading.close();
        showError('Terjadi kesalahan', 'Error!');
    });
});
```

## API Reference

### showSuccess(message, title, timer)
- `message` (string): Pesan yang ditampilkan
- `title` (string, default: 'Sukses!'): Judul alert
- `timer` (number, default: 3000): Durasi tampil dalam ms

**Returns**: Object dengan properties `element` dan `close()`

### showError(message, title, timer)
- `message` (string): Pesan error
- `title` (string, default: 'Error!'): Judul alert
- `timer` (number, default: 4000): Durasi tampil dalam ms

**Returns**: Object dengan properties `element` dan `close()`

### showWarning(message, title, timer)
- `message` (string): Pesan peringatan
- `title` (string, default: 'Peringatan!'): Judul alert
- `timer` (number, default: 3500): Durasi tampil dalam ms

**Returns**: Object dengan properties `element` dan `close()`

### showInfo(message, title, timer)
- `message` (string): Pesan informasi
- `title` (string, default: 'Informasi'): Judul alert
- `timer` (number, default: 3000): Durasi tampil dalam ms

**Returns**: Object dengan properties `element` dan `close()`

### showConfirm(message, title, onConfirm, onCancel)
- `message` (string): Pesan konfirmasi
- `title` (string, default: 'Konfirmasi'): Judul dialog
- `onConfirm` (function): Callback ketika user klik "Ya"
- `onCancel` (function, optional): Callback ketika user klik "Tidak"

**Returns**: Object dengan properties `element` dan `close()`

### showLoading(message, title)
- `message` (string, default: 'Memproses...'): Pesan loading
- `title` (string, default: 'Mohon tunggu'): Judul alert

**Returns**: Object dengan properties `element` dan `close()`

### showValidationErrors(errors, title)
- `errors` (object|string): Object errors atau string pesan
- `title` (string, default: 'Validasi Gagal'): Judul alert

**Returns**: Object dengan properties `element` dan `close()`

### closeAlert()
Menutup semua alert yang sedang tampil

## Tips & Tricks

### 1. Multiple Alerts
Anda dapat menampilkan multiple alerts sekaligus:
```javascript
showSuccess('Alert 1');
setTimeout(() => showError('Alert 2'), 500);
```

### 2. Conditional Timer
```javascript
const timer = isError ? 5000 : 3000;
showInfo('Pesan', 'Info', timer);
```

### 3. Chain Operations
```javascript
showLoading('Processing...');
setTimeout(() => {
    closeAlert();
    showSuccess('Done!');
}, 2000);
```

### 4. Custom Handling
```javascript
const alert = showConfirm('Continue?', 'Question', 
    () => console.log('Confirmed'),
    () => console.log('Cancelled')
);
// Manually close later if needed
setTimeout(() => alert.close(), 5000);
```

## Browser Support
- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Requires ES6 JavaScript support

## Notes
- Semua fungsi menggunakan FontAwesome icons (pastikan CSS FontAwesome sudah loaded)
- Tailwind CSS harus tersedia untuk styling
- Alert menggunakan z-index 50 untuk modal dan bg-black/40 untuk overlay
- Multiple alerts dapat ditampilkan sekaligus dengan unique IDs
