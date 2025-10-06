# 🚀 Quick Start Guide - Frontend Jadwal Pakan Terjadwal

## ⚡ 3 Langkah untuk Mulai

### 1️⃣ Pastikan Server Berjalan

```bash
# Development server
php artisan serve

# Atau jika menggunakan Laravel Valet/Homestead
# Akses langsung di browser
```

✅ Done! Server ready.

---

### 2️⃣ Login ke Aplikasi

1. Buka browser: `http://localhost:8000/login`
2. Login dengan akun Anda
3. Anda akan diarahkan ke Dashboard

---

### 3️⃣ Akses Jadwal Terjadwal

**Via Menu Sidebar:**

1. Lihat menu di sidebar kiri
2. Klik **"Jadwal Pakan Terjadwal"**
3. Halaman jadwal terjadwal akan terbuka

**Via URL Langsung:**

```
http://localhost:8000/jadwal-terjadwal
```

---

## 📝 Tutorial Penggunaan

### 🆕 Membuat Jadwal Baru

1. **Klik tombol "Tambah Jadwal"** di kanan atas
2. **Isi Form:**
    ```
    Nama Jadwal    : Pakan Pagi Tambak A
    Waktu Pakan    : 08:00  (gunakan time picker)
    Deskripsi      : Pemberian pakan untuk tambak A (opsional)
    Tanggal Mulai  : 2025-10-06 (default hari ini)
    Tanggal Selesai: 2025-12-31 (kosongkan untuk unlimited)
    ```
3. **Klik "Simpan Jadwal"**
4. ✅ Success! Jadwal baru muncul di list

**Tips:**

-   Nama jadwal bisa dikosongkan (sistem akan buat nama default)
-   Tanggal selesai opsional (kosongkan = tidak terbatas)
-   Waktu pakan harus format HH:MM (contoh: 08:00, 17:30)

---

### ✏️ Edit Jadwal

1. **Klik icon ⋮ (titik tiga)** pada jadwal yang ingin diedit
2. **Pilih "Edit"** dari dropdown
3. **Ubah field yang diinginkan**
4. **Klik "Simpan Perubahan"**
5. ✅ Success! Jadwal ter-update

**Yang Bisa Diedit:**

-   ✅ Nama jadwal
-   ✅ Waktu pakan
-   ✅ Deskripsi
-   ✅ Tanggal mulai & selesai

---

### ⏸️ Nonaktifkan/Aktifkan Jadwal

**Nonaktifkan jadwal (tanpa menghapus):**

1. Klik icon ⋮ pada jadwal
2. Pilih **"Nonaktifkan"**
3. ✅ Jadwal menjadi non-aktif (tidak akan dieksekusi)

**Aktifkan kembali:**

1. Klik icon ⋮ pada jadwal non-aktif
2. Pilih **"Aktifkan"**
3. ✅ Jadwal aktif kembali

**Kapan Digunakan:**

-   ⏸️ **Nonaktifkan**: Pause sementara tanpa hapus data
-   ▶️ **Aktifkan**: Resume jadwal yang di-pause

---

### 🗑️ Hapus Jadwal

1. **Klik icon ⋮** pada jadwal yang ingin dihapus
2. **Pilih "Hapus"** (warna merah)
3. **Konfirmasi** pada modal yang muncul
4. **Klik "Ya, Hapus"**
5. ✅ Jadwal terhapus permanen

⚠️ **Warning:** Tindakan ini tidak dapat dibatalkan!

---

## 🎯 Memahami Status Jadwal

### 📊 Badge Status

| Badge                       | Arti                      | Penjelasan                         |
| --------------------------- | ------------------------- | ---------------------------------- |
| 🟢 **Aktif**                | Jadwal aktif & siap jalan | Akan dieksekusi otomatis           |
| ✅ **Tereksekusi Hari Ini** | Sudah jalan hari ini      | Akan jalan lagi besok              |
| ⚠️ **Expired**              | Sudah melewati end_date   | Tidak akan jalan lagi              |
| ⚫ **Non-aktif**            | Di-pause manual           | Tidak akan jalan sampai diaktifkan |

### 📅 Informasi Tambahan

-   **Remaining Days**: Sisa hari hingga jadwal berakhir

    -   "Tidak terbatas" = Jadwal tanpa end_date
    -   "5 hari lagi" = Jadwal berakhir 5 hari lagi
    -   "Berakhir hari ini" = Hari terakhir jadwal

-   **Eksekusi Berikutnya**: Kapan jadwal akan jalan next time
    -   Format: `DD/MM/YYYY HH:MM`
    -   Contoh: `07/10/2025 08:00`

---

## 📊 Membaca Statistics

### 4 Kartu Statistik di Atas

1. **Total Jadwal** 📅

    - Jumlah semua jadwal yang pernah dibuat
    - Termasuk aktif, non-aktif, dan expired

2. **Aktif** ✅

    - Jumlah jadwal yang sedang berjalan
    - Akan dieksekusi otomatis sesuai waktu

3. **Non-Aktif** ⏸️

    - Jumlah jadwal yang di-pause
    - Tidak akan dieksekusi sampai diaktifkan

4. **Hari Ini** 🕐
    - Jumlah jadwal yang sudah dieksekusi hari ini
    - Reset setiap tengah malam

---

## 💡 Use Case Examples

### Scenario 1: Jadwal Pagi & Sore Daily

**Goal:** Beri pakan 2x sehari (pagi & sore) selamanya

**Steps:**

1. Tambah Jadwal Pakan Pagi

    ```
    Nama: Pakan Pagi
    Waktu: 08:00
    Tanggal Mulai: (hari ini)
    Tanggal Selesai: (kosongkan)
    ```

2. Tambah Jadwal Pakan Sore
    ```
    Nama: Pakan Sore
    Waktu: 17:00
    Tanggal Mulai: (hari ini)
    Tanggal Selesai: (kosongkan)
    ```

✅ **Result:** Sistem akan beri pakan otomatis setiap hari jam 08:00 & 17:00

---

### Scenario 2: Jadwal untuk Siklus Budidaya

**Goal:** Beri pakan untuk periode pembesaran 90 hari

**Steps:**

1. Tambah Jadwal
    ```
    Nama: Pakan Siklus Oktober-Desember
    Waktu: 09:00
    Tanggal Mulai: 2025-10-06
    Tanggal Selesai: 2026-01-05 (90 hari kemudian)
    ```

✅ **Result:** Sistem akan beri pakan otomatis dari 6 Okt s/d 5 Jan, lalu otomatis berhenti

---

### Scenario 3: Jadwal Sementara untuk Liburan

**Goal:** Pause jadwal selama liburan, lalu aktifkan lagi

**Steps:**

1. Sebelum liburan:
    - Klik ⋮ pada jadwal
    - Pilih "Nonaktifkan"
2. Setelah liburan:
    - Klik ⋮ pada jadwal
    - Pilih "Aktifkan"

✅ **Result:** Jadwal tidak akan jalan selama di-nonaktifkan, data tetap tersimpan

---

## 🔍 Troubleshooting

### Problem: "Jadwal tidak muncul"

**Solution:**

1. Refresh halaman (F5 atau Ctrl+R)
2. Pastikan sudah login
3. Cek apakah ada error message
4. Clear browser cache

---

### Problem: "Tombol tidak bisa diklik"

**Solution:**

1. Pastikan modal sudah tertutup
2. Tunggu hingga loading selesai
3. Refresh halaman
4. Cek koneksi internet

---

### Problem: "Validation error"

**Common Errors & Fixes:**

| Error                              | Penyebab              | Solusi                           |
| ---------------------------------- | --------------------- | -------------------------------- |
| "Waktu pakan wajib diisi"          | Field waktu kosong    | Isi waktu dengan time picker     |
| "Format waktu harus HH:MM"         | Format salah          | Gunakan format 08:00, bukan 8:00 |
| "Tanggal selesai harus setelah..." | End date < start date | End date harus lebih besar       |

---

### Problem: "Error 404 Not Found"

**Solution:**

1. Pastikan URL benar: `/jadwal-terjadwal`
2. Cek apakah server masih running
3. Restart Laravel server:
    ```bash
    php artisan serve
    ```

---

### Problem: "Error 500 Server Error"

**Solution:**

1. Cek Laravel logs:
    ```bash
    tail -f storage/logs/laravel.log
    ```
2. Pastikan database connection OK
3. Pastikan migrations sudah dijalankan:
    ```bash
    php artisan migrate
    ```

---

## 📱 Mobile View

### Mengakses dari Mobile

1. **Connect ke same network** dengan laptop/PC
2. **Cek IP laptop**:
    ```bash
    ipconfig  # Windows
    ifconfig  # Mac/Linux
    ```
3. **Buka browser mobile**: `http://192.168.x.x:8000/jadwal-terjadwal`
4. **Login** dengan akun Anda

### Mobile Optimizations

✅ **Touch-friendly buttons** - Ukuran tombol optimal untuk touch
✅ **Responsive layout** - Auto-adjust untuk screen kecil
✅ **Scrollable table** - Horizontal scroll untuk table lebar
✅ **Modal full-screen** - Modal menyesuaikan layar mobile

---

## 🎨 Tips & Tricks

### 🔥 Pro Tips

1. **Naming Convention**

    ```
    ✅ Good: "Pakan Pagi Tambak A"
    ✅ Good: "Feed Sore Kolam 1"
    ❌ Bad: "jadwal1"
    ```

2. **Deskripsi Informatif**

    ```
    ✅ Good: "Pemberian pakan untuk periode pembesaran udang,
             target 90 hari dari benur ke ukuran konsumsi"
    ❌ Bad: "pakan"
    ```

3. **Gunakan Tanggal Selesai**

    - Untuk siklus budidaya: Set end_date
    - Untuk operasional rutin: Kosongkan end_date

4. **Manfaatkan Toggle**

    - Pause jadwal saat maintenance
    - Pause jadwal saat panen
    - Resume setelah selesai

5. **Monitor Statistik**
    - Cek "Hari Ini" untuk confirm eksekusi
    - Cek "Aktif" untuk jumlah jadwal running

---

## ⌨️ Keyboard Shortcuts

| Shortcut          | Action                          |
| ----------------- | ------------------------------- |
| `Ctrl + R` / `F5` | Refresh halaman                 |
| `Esc`             | Close modal                     |
| `Tab`             | Navigate form fields            |
| `Enter`           | Submit form (dalam input field) |

---

## 📚 Related Documentation

-   **Backend API**: `FEED-SCHEDULING-GUIDE.md`
-   **API Testing**: `FEED-SCHEDULING-API-TESTS.md`
-   **Frontend Details**: `FRONTEND-IMPLEMENTATION.md`
-   **Implementation**: `IMPLEMENTATION-SUMMARY.md`

---

## ✅ Quick Checklist

**Sebelum Mulai:**

-   [ ] Server Laravel running
-   [ ] Database ter-migrate
-   [ ] User sudah terdaftar & bisa login
-   [ ] Bisa akses dashboard

**First Time Setup:**

-   [ ] Login ke aplikasi
-   [ ] Akses `/jadwal-terjadwal`
-   [ ] Buat jadwal pertama
-   [ ] Test toggle active/inactive
-   [ ] Test edit & delete

**Daily Usage:**

-   [ ] Cek statistics cards
-   [ ] Cek status jadwal aktif
-   [ ] Monitor "Hari Ini" untuk eksekusi
-   [ ] Update jadwal jika perlu

---

## 🎯 Next Steps

1. **Buat Jadwal Pertama** - Mulai dengan 1 jadwal simple
2. **Test Eksekusi** - Buat jadwal dengan waktu sekarang untuk testing
3. **Monitor Logs** - Cek Laravel logs untuk confirm execution
4. **Explore Features** - Coba semua fitur yang tersedia
5. **Read Documentation** - Pahami detail di doc lengkap

---

**Status: ✅ Ready to Use!**

Fitur Jadwal Pakan Terjadwal siap digunakan. Selamat mencoba! 🚀

**Happy Farming! 🐟**
