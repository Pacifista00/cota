# 🚀 Quick Start - Fitur Pakan Terjadwal

## ⚡ 3 Langkah untuk Mulai

### 1️⃣ Pastikan Database Sudah Migrate

```bash
php artisan migrate
```

✅ Done! Database schema siap.

---

### 2️⃣ Jalankan Laravel Scheduler

**Untuk Development:**

```bash
php artisan schedule:work
```

**Untuk Production (Tambahkan ke crontab):**

```bash
crontab -e
```

Tambahkan:

```
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

---

### 3️⃣ Test Fitur

#### A. Via API (Postman/cURL)

**1. Login dulu untuk dapat token:**

```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "your@email.com",
    "password": "yourpassword"
  }'
```

**2. Simpan token dari response, lalu create schedule:**

```bash
curl -X POST http://localhost:8000/api/feed-schedule/create \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Pakan Pagi",
    "waktu_pakan": "08:00:00",
    "start_date": "2025-10-06",
    "end_date": "2025-12-31"
  }'
```

**3. Lihat semua schedule:**

```bash
curl -X GET http://localhost:8000/api/feed-schedule \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

#### B. Via Command Line

**Test eksekusi manual:**

```bash
php artisan feed:execute-scheduled
```

---

## 📋 Contoh Use Case

### Use Case 1: Jadwal Pagi & Sore

```bash
# Token dari login
TOKEN="your-token-here"

# Pakan Pagi - 08:00
curl -X POST http://localhost:8000/api/feed-schedule/create \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Pakan Pagi",
    "description": "Pemberian pakan pagi rutin",
    "waktu_pakan": "08:00:00"
  }'

# Pakan Sore - 17:00
curl -X POST http://localhost:8000/api/feed-schedule/create \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Pakan Sore",
    "description": "Pemberian pakan sore rutin",
    "waktu_pakan": "17:00:00"
  }'
```

### Use Case 2: Jadwal dengan Durasi Terbatas

```bash
# Pakan untuk 1 bulan pembesaran
curl -X POST http://localhost:8000/api/feed-schedule/create \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Pakan Siklus Oktober",
    "waktu_pakan": "10:00:00",
    "start_date": "2025-10-06",
    "end_date": "2025-11-06"
  }'
```

---

## 🔍 Monitoring

### 1. Check Scheduler Status

```bash
php artisan schedule:list
```

### 2. Check Logs

```bash
tail -f storage/logs/laravel.log | grep -i feed
```

### 3. Manual Test

```bash
# Buat jadwal dengan waktu sekarang
CURRENT_TIME=$(date +%H:%M:%S)
echo "Creating schedule for $CURRENT_TIME"

curl -X POST http://localhost:8000/api/feed-schedule/create \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d "{
    \"name\": \"Test Now\",
    \"waktu_pakan\": \"$CURRENT_TIME\"
  }"

# Tunggu 1-2 menit, lalu:
php artisan feed:execute-scheduled
```

---

## 📱 Integrasi Frontend

### JavaScript/React Example:

```javascript
// feedScheduleService.js
import axios from "axios";

const API_URL = "http://localhost:8000/api";
const token = localStorage.getItem("auth_token");

const api = axios.create({
    baseURL: API_URL,
    headers: {
        Authorization: `Bearer ${token}`,
        "Content-Type": "application/json",
    },
});

export const feedScheduleService = {
    // Create schedule
    create: async (data) => {
        const response = await api.post("/feed-schedule/create", data);
        return response.data;
    },

    // Get all schedules
    getAll: async () => {
        const response = await api.get("/feed-schedule");
        return response.data;
    },

    // Get active schedules
    getActive: async () => {
        const response = await api.get("/feed-schedule/active");
        return response.data;
    },

    // Update schedule
    update: async (id, data) => {
        const response = await api.put(`/feed-schedule/${id}`, data);
        return response.data;
    },

    // Delete schedule
    delete: async (id) => {
        const response = await api.delete(`/feed-schedule/${id}`);
        return response.data;
    },

    // Toggle active
    toggleActive: async (id, isActive) => {
        const endpoint = isActive ? "activate" : "deactivate";
        const response = await api.patch(`/feed-schedule/${id}/${endpoint}`);
        return response.data;
    },
};

// Usage in component
async function createSchedule() {
    try {
        const result = await feedScheduleService.create({
            name: "Pakan Pagi",
            waktu_pakan: "08:00:00",
            start_date: "2025-10-06",
            end_date: "2025-12-31",
        });
        console.log("Schedule created:", result);
    } catch (error) {
        console.error("Error:", error.response?.data);
    }
}
```

---

## ⚠️ Troubleshooting

### Problem: "Scheduler not running"

**Solution:**

```bash
# Development
php artisan schedule:work

# Check if running
ps aux | grep schedule
```

### Problem: "Command not found"

**Solution:**

```bash
php artisan clear-compiled
php artisan optimize
composer dump-autoload
```

### Problem: "401 Unauthorized"

**Solution:**

-   Pastikan token valid
-   Token format: `Bearer {token}`
-   Cek expire time token

### Problem: "Validation error"

**Solution:**

-   Format waktu harus `HH:MM:SS` (contoh: `08:00:00`)
-   `end_date` harus >= `start_date`
-   `waktu_pakan` wajib diisi

---

## 📚 Dokumentasi Lengkap

-   **FEED-SCHEDULING-GUIDE.md** - Panduan lengkap fitur
-   **FEED-SCHEDULING-API-TESTS.md** - Contoh testing
-   **IMPLEMENTATION-SUMMARY.md** - Ringkasan implementasi

---

## ✅ Checklist Setup

-   [ ] Database migrated
-   [ ] Scheduler running (schedule:work atau cron)
-   [ ] Test create schedule via API
-   [ ] Test manual execution
-   [ ] Check logs
-   [ ] Monitor first automated execution

---

## 🎯 Next Steps

1. **Setup Production Cron** - Tambahkan cron job di server
2. **Setup Monitoring** - Monitor logs untuk failed executions
3. **Frontend Integration** - Integrate dengan UI
4. **Testing** - Test berbagai scenarios
5. **Documentation** - Share dengan team

---

**Status: ✅ Ready to Use!**

Fitur pakan terjadwal siap digunakan. Silakan test dan sesuaikan dengan kebutuhan! 🚀
