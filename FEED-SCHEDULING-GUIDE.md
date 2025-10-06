# 🐟 Panduan Fitur Pakan Terjadwal (Feed Scheduling)

## 📋 Daftar Isi

-   [Gambaran Umum](#gambaran-umum)
-   [Arsitektur](#arsitektur)
-   [Instalasi](#instalasi)
-   [API Endpoints](#api-endpoints)
-   [Penggunaan](#penggunaan)
-   [Contoh Request](#contoh-request)
-   [Konfigurasi Laravel Scheduler](#konfigurasi-laravel-scheduler)
-   [Troubleshooting](#troubleshooting)

---

## 🎯 Gambaran Umum

Fitur Pakan Terjadwal memungkinkan pengguna untuk mengatur pemberian pakan secara otomatis pada waktu dan rentang tanggal tertentu. Sistem akan secara otomatis mengeksekusi pemberian pakan sesuai jadwal yang telah ditentukan.

### ✨ Fitur Utama:

-   ✅ **Penjadwalan Fleksibel**: Tentukan waktu pemberian pakan (HH:MM:SS)
-   📅 **Rentang Tanggal**: Tentukan tanggal mulai dan tanggal selesai
-   🔄 **Frekuensi Kustom**: Daily, Weekly, Monthly, atau Custom
-   ⏸️ **Aktif/Non-aktif**: Toggle jadwal tanpa menghapus data
-   📊 **Statistik Eksekusi**: Pantau riwayat dan tingkat keberhasilan
-   🔐 **Multi-user**: Setiap user dapat memiliki jadwal sendiri
-   🎯 **Eksekusi Otomatis**: Dijalankan setiap menit oleh Laravel Scheduler

---

## 🏗️ Arsitektur

### Structure

```
app/
├── Console/
│   └── Commands/
│       └── ExecuteScheduledFeeds.php      # Command untuk eksekusi otomatis
├── Enums/
│   ├── FeedExecutionStatus.php            # Enum: pending, success, failed
│   └── ScheduleFrequency.php              # Enum: daily, weekly, monthly, custom
├── Http/
│   ├── Controllers/
│   │   └── FeedScheduleController.php     # Controller untuk manage jadwal
│   ├── Requests/
│   │   ├── StoreFeedScheduleRequest.php   # Validasi untuk create
│   │   └── UpdateFeedScheduleRequest.php  # Validasi untuk update
│   └── Resources/
│       ├── FeedScheduleResource.php       # Resource untuk response
│       └── FeedExecutionResource.php      # Resource untuk execution history
├── Models/
│   ├── FeedSchedule.php                   # Model jadwal pakan
│   ├── FeedExecution.php                  # Model riwayat eksekusi
│   └── User.php                           # Model user (dengan relationship)
└── Services/
    └── FeedSchedulingService.php          # Business logic layer

database/
└── migrations/
    ├── 2025_10_06_140718_add_schedule_enhancements_to_feed_schedules_table.php
    └── 2025_10_06_140745_add_feed_schedule_id_to_feed_executions_table.php
```

### Database Schema

#### `feed_schedules` Table

| Column           | Type      | Description                                  |
| ---------------- | --------- | -------------------------------------------- |
| id               | bigint    | Primary key                                  |
| user_id          | bigint    | Foreign key ke users                         |
| name             | string    | Nama jadwal (opsional)                       |
| description      | text      | Deskripsi jadwal (opsional)                  |
| waktu_pakan      | time      | Waktu pemberian pakan (HH:MM:SS)             |
| start_date       | date      | Tanggal mulai jadwal                         |
| end_date         | date      | Tanggal selesai jadwal (nullable)            |
| is_active        | boolean   | Status aktif/non-aktif                       |
| frequency_type   | string    | Tipe frekuensi (daily/weekly/monthly/custom) |
| frequency_data   | json      | Data frekuensi kustom (nullable)             |
| last_executed_at | date      | Terakhir dieksekusi                          |
| created_at       | timestamp | Waktu dibuat                                 |
| updated_at       | timestamp | Waktu diupdate                               |

#### `feed_executions` Table

| Column           | Type      | Description                              |
| ---------------- | --------- | ---------------------------------------- |
| id               | bigint    | Primary key                              |
| feed_schedule_id | bigint    | Foreign key ke feed_schedules (nullable) |
| trigger_type     | enum      | manual, scheduled, atau api              |
| status           | enum      | pending, success, atau failed            |
| executed_at      | timestamp | Waktu eksekusi                           |
| created_at       | timestamp | Waktu dibuat                             |
| updated_at       | timestamp | Waktu diupdate                           |

---

## 📦 Instalasi

### 1. Jalankan Migrasi

```bash
php artisan migrate
```

### 2. Setup Laravel Scheduler

Tambahkan cron job berikut ke server:

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

Atau untuk development, jalankan:

```bash
php artisan schedule:work
```

### 3. Verifikasi Command

```bash
php artisan list | grep feed
```

Output yang diharapkan:

```
feed:execute-scheduled    Execute all feed schedules that are ready to run
```

---

## 🔌 API Endpoints

### Authentication Required (Sanctum)

Semua endpoint di bawah ini memerlukan autentikasi menggunakan Sanctum token.

Header yang diperlukan:

```
Authorization: Bearer {your-token}
Accept: application/json
```

### 1. **List All Schedules**

```http
GET /api/feed-schedule
```

**Response:**

```json
{
    "message": "Daftar jadwal pakan berhasil dimuat.",
    "status": 200,
    "data": [
        {
            "id": 1,
            "user_id": 1,
            "name": "Pakan Pagi",
            "description": "Pemberian pakan pagi untuk tambak A",
            "waktu_pakan": "08:00:00",
            "start_date": "2025-10-06",
            "end_date": "2025-12-31",
            "is_active": true,
            "frequency_type": "daily",
            "frequency_type_label": "Setiap Hari",
            "last_executed_at": "2025-10-06",
            "next_execution": "2025-10-07 08:00:00",
            "remaining_days": 86,
            "is_valid": true,
            "was_executed_today": true
        }
    ]
}
```

### 2. **Get Active Schedules**

```http
GET /api/feed-schedule/active
```

Mengembalikan hanya jadwal yang aktif dan masih dalam periode berlaku.

### 3. **Get Schedule Detail**

```http
GET /api/feed-schedule/{id}
```

**Response:**

```json
{
  "message": "Detail jadwal pakan berhasil dimuat.",
  "status": 200,
  "data": {
    "id": 1,
    "name": "Pakan Pagi",
    "waktu_pakan": "08:00:00",
    ...
  },
  "statistics": {
    "total_executions": 30,
    "successful_executions": 29,
    "failed_executions": 1,
    "success_rate": 96.67,
    "last_executed_at": "2025-10-06 08:00:00",
    "next_execution": "2025-10-07 08:00:00",
    "remaining_days": 86,
    "is_active": true,
    "is_valid": true
  }
}
```

### 4. **Create Schedule**

```http
POST /api/feed-schedule/create
```

**Request Body:**

```json
{
    "name": "Pakan Sore",
    "description": "Pemberian pakan sore hari",
    "waktu_pakan": "17:00:00",
    "start_date": "2025-10-07",
    "end_date": "2025-12-31",
    "is_active": true,
    "frequency_type": "daily"
}
```

**Response:**

```json
{
  "message": "Jadwal pakan berhasil disimpan!",
  "status": 201,
  "data": {
    "id": 2,
    "name": "Pakan Sore",
    ...
  }
}
```

### 5. **Update Schedule**

```http
PUT /api/feed-schedule/{id}
```

**Request Body:**

```json
{
    "waktu_pakan": "18:00:00",
    "end_date": "2026-01-31"
}
```

### 6. **Delete Schedule**

```http
DELETE /api/feed-schedule/{id}
```

**Response:**

```json
{
    "message": "Jadwal pakan berhasil dihapus!",
    "status": 200
}
```

### 7. **Activate Schedule**

```http
PATCH /api/feed-schedule/{id}/activate
```

Mengaktifkan jadwal yang non-aktif.

### 8. **Deactivate Schedule**

```http
PATCH /api/feed-schedule/{id}/deactivate
```

Menonaktifkan jadwal tanpa menghapusnya.

---

## 💡 Penggunaan

### Scenario 1: Jadwal Harian Sederhana

**Kebutuhan:** Beri pakan setiap hari jam 08:00 mulai hari ini hingga 30 hari ke depan.

```bash
curl -X POST http://localhost:8000/api/feed-schedule/create \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Pakan Pagi",
    "waktu_pakan": "08:00:00",
    "start_date": "2025-10-06",
    "end_date": "2025-11-05"
  }'
```

### Scenario 2: Jadwal Tanpa Batas Waktu

**Kebutuhan:** Beri pakan setiap hari jam 17:00 dimulai hari ini, tanpa tanggal akhir.

```bash
curl -X POST http://localhost:8000/api/feed-schedule/create \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Pakan Sore Rutin",
    "waktu_pakan": "17:00:00",
    "start_date": "2025-10-06"
  }'
```

### Scenario 3: Jadwal dengan Deskripsi

```bash
curl -X POST http://localhost:8000/api/feed-schedule/create \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Pakan Tambak A - Pagi",
    "description": "Pemberian pakan untuk tambak sektor A pada pagi hari. Durasi 3 bulan masa pembesaran.",
    "waktu_pakan": "07:30:00",
    "start_date": "2025-10-06",
    "end_date": "2026-01-06"
  }'
```

### Scenario 4: Update Waktu Pakan

```bash
curl -X PUT http://localhost:8000/api/feed-schedule/1 \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "waktu_pakan": "08:30:00"
  }'
```

### Scenario 5: Menonaktifkan Sementara

```bash
curl -X PATCH http://localhost:8000/api/feed-schedule/1/deactivate \
  -H "Authorization: Bearer {token}"
```

---

## ⚙️ Konfigurasi Laravel Scheduler

### Console Kernel

File: `app/Console/Kernel.php`

```php
protected function schedule(Schedule $schedule): void
{
    // Execute scheduled feeds every minute
    $schedule->command('feed:execute-scheduled')
             ->everyMinute()
             ->name('execute-scheduled-feeds')
             ->withoutOverlapping(2);
}
```

### Manual Testing

Untuk menguji eksekusi manual tanpa menunggu scheduler:

```bash
php artisan feed:execute-scheduled
```

Output:

```
🔍 Checking for ready feed schedules...
📋 Found 2 schedule(s) ready to execute:

+----+------------------+-----------+------------------------------------+
| ID | Schedule Name    | Status    | Message                            |
+----+------------------+-----------+------------------------------------+
| 1  | Pakan Pagi       | ✅ Success | Perintah pakan terjadwal berhasil  |
| 2  | Pakan Sore       | ✅ Success | Perintah pakan terjadwal berhasil  |
+----+------------------+-----------+------------------------------------+

✅ Successful: 2
```

---

## 🎨 Frontend Integration

### Contoh dengan Axios (JavaScript)

```javascript
// Create new schedule
async function createFeedSchedule() {
    try {
        const response = await axios.post(
            "/api/feed-schedule/create",
            {
                name: "Pakan Pagi",
                waktu_pakan: "08:00:00",
                start_date: "2025-10-06",
                end_date: "2025-12-31",
                is_active: true,
            },
            {
                headers: {
                    Authorization: `Bearer ${token}`,
                    "Content-Type": "application/json",
                },
            }
        );

        console.log("Schedule created:", response.data);
    } catch (error) {
        console.error("Error:", error.response.data);
    }
}

// Get all schedules
async function getSchedules() {
    try {
        const response = await axios.get("/api/feed-schedule", {
            headers: {
                Authorization: `Bearer ${token}`,
            },
        });

        return response.data.data;
    } catch (error) {
        console.error("Error:", error.response.data);
    }
}

// Toggle schedule active status
async function toggleSchedule(scheduleId, isActive) {
    const endpoint = isActive
        ? `/api/feed-schedule/${scheduleId}/activate`
        : `/api/feed-schedule/${scheduleId}/deactivate`;

    try {
        const response = await axios.patch(
            endpoint,
            {},
            {
                headers: {
                    Authorization: `Bearer ${token}`,
                },
            }
        );

        console.log("Schedule toggled:", response.data);
    } catch (error) {
        console.error("Error:", error.response.data);
    }
}
```

---

## 🔍 Troubleshooting

### 1. Jadwal Tidak Dieksekusi

**Penyebab:**

-   Laravel Scheduler tidak berjalan
-   Jadwal sudah dieksekusi hari ini
-   Jadwal tidak aktif (`is_active = false`)
-   Tanggal di luar rentang `start_date` - `end_date`

**Solusi:**

```bash
# Cek apakah scheduler berjalan
php artisan schedule:list

# Test eksekusi manual
php artisan feed:execute-scheduled

# Cek log
tail -f storage/logs/laravel.log
```

### 2. MQTT Connection Failed

**Penyebab:**

-   Koneksi internet bermasalah
-   MQTT broker down
-   Kredensial salah

**Solusi:**

```php
// Update kredensial di FeedSchedulingService.php
private const MQTT_SERVER = 'your-mqtt-server.com';
private const MQTT_PORT = 8883;
private const MQTT_USERNAME = 'your-username';
private const MQTT_PASSWORD = 'your-password';
```

### 3. Validasi Error

**Error:** `"Waktu pakan wajib diisi"`

**Solusi:**
Pastikan format waktu benar: `HH:MM:SS` (contoh: `08:00:00`)

**Error:** `"Tanggal selesai harus setelah atau sama dengan tanggal mulai"`

**Solusi:**
Pastikan `end_date >= start_date`

### 4. Permission Denied

**Error:** `401 Unauthorized`

**Solusi:**
Pastikan token Sanctum valid dan dikirim di header:

```
Authorization: Bearer {your-token}
```

---

## 📊 Model Methods Reference

### FeedSchedule Model

#### Scopes:

-   `active()` - Get only active schedules
-   `shouldRunToday()` - Get schedules that should run today
-   `readyToExecute()` - Get schedules ready to execute now

#### Methods:

-   `isValid()` - Check if schedule is currently valid
-   `shouldRunOnDate($date)` - Check if schedule should run on specific date
-   `markAsExecuted()` - Mark schedule as executed
-   `wasExecutedToday()` - Check if executed today

#### Attributes:

-   `next_execution` - Get next execution datetime
-   `remaining_days` - Get remaining days until end_date

### FeedExecution Model

#### Scopes:

-   `successful()` - Get only successful executions
-   `failed()` - Get only failed executions
-   `pending()` - Get only pending executions
-   `scheduled()` - Get only scheduled executions
-   `manual()` - Get only manual executions

#### Methods:

-   `isSuccessful()` - Check if execution was successful
-   `isFailed()` - Check if execution failed
-   `isPending()` - Check if execution is pending

---

## 🚀 Best Practices

1. **Gunakan Nama Deskriptif**

    ```json
    {
        "name": "Pakan Pagi Tambak A",
        "description": "Pemberian pakan pagi untuk tambak sektor A"
    }
    ```

2. **Set End Date untuk Siklus Budidaya**

    - Tentukan `end_date` sesuai masa pembesaran ikan/udang
    - Jadwal akan otomatis berhenti setelah `end_date`

3. **Monitoring Statistik**

    - Gunakan endpoint `/api/feed-schedule/{id}` untuk cek statistik
    - Pantau `success_rate` untuk mendeteksi masalah

4. **Gunakan Deactivate, Bukan Delete**

    - Untuk jadwal sementara tidak digunakan, gunakan deactivate
    - Data historis tetap tersimpan untuk analisis

5. **Setup Alerting**
    - Monitor log Laravel untuk failed executions
    - Setup notifikasi untuk success rate < 90%

---

## 📝 Notes

-   Sistem menggunakan **timezone server** untuk eksekusi
-   Jadwal di-check setiap menit (toleransi 1 menit)
-   Satu jadwal hanya bisa dieksekusi 1x per hari
-   Jadwal expired akan otomatis di-deactivate

---

## 🤝 Support

Jika ada pertanyaan atau menemukan bug, silakan hubungi tim development.

**Happy Scheduling! 🐟**
