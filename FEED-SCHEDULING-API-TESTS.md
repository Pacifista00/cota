# 🧪 Feed Scheduling API Tests

## Test Scenarios dengan cURL

### Setup

```bash
# Set your auth token
TOKEN="your-sanctum-token-here"
BASE_URL="http://localhost:8000"
```

---

## ✅ Test 1: Create Simple Daily Schedule

**Test:** Buat jadwal pakan harian sederhana

```bash
curl -X POST "${BASE_URL}/api/feed-schedule/create" \
  -H "Authorization: Bearer ${TOKEN}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Pakan Pagi",
    "waktu_pakan": "08:00:00",
    "start_date": "2025-10-06",
    "end_date": "2025-12-31"
  }'
```

**Expected Result:**

```json
{
  "message": "Jadwal pakan berhasil disimpan!",
  "status": 201,
  "data": {
    "id": 1,
    "name": "Pakan Pagi",
    "waktu_pakan": "08:00:00",
    "is_active": true,
    ...
  }
}
```

---

## ✅ Test 2: Create Schedule Without End Date

**Test:** Buat jadwal tanpa batas waktu

```bash
curl -X POST "${BASE_URL}/api/feed-schedule/create" \
  -H "Authorization: Bearer ${TOKEN}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Pakan Sore Unlimited",
    "description": "Pemberian pakan sore tanpa batas waktu",
    "waktu_pakan": "17:00:00"
  }'
```

**Expected:** Schedule created with `end_date: null`, `remaining_days: null`

---

## ✅ Test 3: Get All Schedules

**Test:** Ambil semua jadwal user

```bash
curl -X GET "${BASE_URL}/api/feed-schedule" \
  -H "Authorization: Bearer ${TOKEN}" \
  -H "Accept: application/json"
```

---

## ✅ Test 4: Get Active Schedules Only

**Test:** Ambil hanya jadwal yang aktif

```bash
curl -X GET "${BASE_URL}/api/feed-schedule/active" \
  -H "Authorization: Bearer ${TOKEN}" \
  -H "Accept: application/json"
```

---

## ✅ Test 5: Get Schedule Detail with Statistics

**Test:** Ambil detail jadwal beserta statistik

```bash
SCHEDULE_ID=1
curl -X GET "${BASE_URL}/api/feed-schedule/${SCHEDULE_ID}" \
  -H "Authorization: Bearer ${TOKEN}" \
  -H "Accept: application/json"
```

**Expected Result:**

```json
{
  "message": "Detail jadwal pakan berhasil dimuat.",
  "status": 200,
  "data": { ... },
  "statistics": {
    "total_executions": 0,
    "successful_executions": 0,
    "failed_executions": 0,
    "success_rate": 0,
    "next_execution": "2025-10-07 08:00:00",
    "remaining_days": 86,
    "is_active": true,
    "is_valid": true
  }
}
```

---

## ✅ Test 6: Update Schedule Time

**Test:** Update waktu pemberian pakan

```bash
SCHEDULE_ID=1
curl -X PUT "${BASE_URL}/api/feed-schedule/${SCHEDULE_ID}" \
  -H "Authorization: Bearer ${TOKEN}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "waktu_pakan": "08:30:00"
  }'
```

---

## ✅ Test 7: Update Schedule Range

**Test:** Update tanggal mulai dan selesai

```bash
SCHEDULE_ID=1
curl -X PUT "${BASE_URL}/api/feed-schedule/${SCHEDULE_ID}" \
  -H "Authorization: Bearer ${TOKEN}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "start_date": "2025-10-07",
    "end_date": "2026-01-31"
  }'
```

---

## ✅ Test 8: Deactivate Schedule

**Test:** Nonaktifkan jadwal tanpa menghapus

```bash
SCHEDULE_ID=1
curl -X PATCH "${BASE_URL}/api/feed-schedule/${SCHEDULE_ID}/deactivate" \
  -H "Authorization: Bearer ${TOKEN}" \
  -H "Accept: application/json"
```

**Expected:** `is_active: false`

---

## ✅ Test 9: Activate Schedule

**Test:** Aktifkan kembali jadwal

```bash
SCHEDULE_ID=1
curl -X PATCH "${BASE_URL}/api/feed-schedule/${SCHEDULE_ID}/activate" \
  -H "Authorization: Bearer ${TOKEN}" \
  -H "Accept: application/json"
```

**Expected:** `is_active: true`

---

## ✅ Test 10: Delete Schedule

**Test:** Hapus jadwal

```bash
SCHEDULE_ID=1
curl -X DELETE "${BASE_URL}/api/feed-schedule/${SCHEDULE_ID}" \
  -H "Authorization: Bearer ${TOKEN}" \
  -H "Accept: application/json"
```

---

## ❌ Validation Error Tests

### Test 11: Missing Required Field

```bash
curl -X POST "${BASE_URL}/api/feed-schedule/create" \
  -H "Authorization: Bearer ${TOKEN}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Test"
  }'
```

**Expected:**

```json
{
    "message": "Waktu pakan wajib diisi.",
    "errors": {
        "waktu_pakan": ["Waktu pakan wajib diisi."]
    }
}
```

### Test 12: Invalid Time Format

```bash
curl -X POST "${BASE_URL}/api/feed-schedule/create" \
  -H "Authorization: Bearer ${TOKEN}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "waktu_pakan": "8:00"
  }'
```

**Expected:** Validation error - format harus `HH:MM:SS`

### Test 13: End Date Before Start Date

```bash
curl -X POST "${BASE_URL}/api/feed-schedule/create" \
  -H "Authorization: Bearer ${TOKEN}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "waktu_pakan": "08:00:00",
    "start_date": "2025-12-31",
    "end_date": "2025-10-01"
  }'
```

**Expected:** Validation error - end_date harus >= start_date

---

## 🤖 Test Command Execution

### Test 14: Manual Command Execution

```bash
php artisan feed:execute-scheduled
```

**Expected Output:**

```
🔍 Checking for ready feed schedules...
✅ No schedules ready to execute at this time
```

### Test 15: With Ready Schedule

1. Buat schedule dengan waktu saat ini:

```bash
CURRENT_TIME=$(date +%H:%M:%S)
curl -X POST "${BASE_URL}/api/feed-schedule/create" \
  -H "Authorization: Bearer ${TOKEN}" \
  -H "Content-Type: application/json" \
  -d "{
    \"name\": \"Test Schedule\",
    \"waktu_pakan\": \"${CURRENT_TIME}\"
  }"
```

2. Jalankan command dalam 1 menit:

```bash
php artisan feed:execute-scheduled
```

**Expected:**

```
🔍 Checking for ready feed schedules...
📋 Found 1 schedule(s) ready to execute:

+----+---------------+-----------+------------------------------------+
| ID | Schedule Name | Status    | Message                            |
+----+---------------+-----------+------------------------------------+
| 1  | Test Schedule | ✅ Success | Perintah pakan terjadwal berhasil  |
+----+---------------+-----------+------------------------------------+

✅ Successful: 1
```

---

## 📊 Test Sequence untuk Full Flow

### Complete Test Flow

```bash
#!/bin/bash

TOKEN="your-token"
BASE_URL="http://localhost:8000"

echo "=== Test 1: Create Schedule ==="
RESPONSE=$(curl -s -X POST "${BASE_URL}/api/feed-schedule/create" \
  -H "Authorization: Bearer ${TOKEN}" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Full Test Schedule",
    "waktu_pakan": "10:00:00",
    "start_date": "2025-10-06",
    "end_date": "2025-10-20"
  }')

SCHEDULE_ID=$(echo $RESPONSE | jq -r '.data.id')
echo "Created schedule ID: $SCHEDULE_ID"

echo -e "\n=== Test 2: Get Schedule Detail ==="
curl -s -X GET "${BASE_URL}/api/feed-schedule/${SCHEDULE_ID}" \
  -H "Authorization: Bearer ${TOKEN}" | jq

echo -e "\n=== Test 3: Update Schedule ==="
curl -s -X PUT "${BASE_URL}/api/feed-schedule/${SCHEDULE_ID}" \
  -H "Authorization: Bearer ${TOKEN}" \
  -H "Content-Type: application/json" \
  -d '{
    "waktu_pakan": "11:00:00"
  }' | jq

echo -e "\n=== Test 4: Deactivate Schedule ==="
curl -s -X PATCH "${BASE_URL}/api/feed-schedule/${SCHEDULE_ID}/deactivate" \
  -H "Authorization: Bearer ${TOKEN}" | jq

echo -e "\n=== Test 5: Activate Schedule ==="
curl -s -X PATCH "${BASE_URL}/api/feed-schedule/${SCHEDULE_ID}/activate" \
  -H "Authorization: Bearer ${TOKEN}" | jq

echo -e "\n=== Test 6: Get Active Schedules ==="
curl -s -X GET "${BASE_URL}/api/feed-schedule/active" \
  -H "Authorization: Bearer ${TOKEN}" | jq

echo -e "\n=== Test 7: Delete Schedule ==="
curl -s -X DELETE "${BASE_URL}/api/feed-schedule/${SCHEDULE_ID}" \
  -H "Authorization: Bearer ${TOKEN}" | jq

echo -e "\n=== All tests completed! ==="
```

Save script ini sebagai `test-feed-schedule.sh` dan jalankan:

```bash
chmod +x test-feed-schedule.sh
./test-feed-schedule.sh
```

---

## 🔍 Postman Collection

### Import ke Postman

Buat environment:

-   Variable: `base_url` = `http://localhost:8000`
-   Variable: `token` = `your-sanctum-token`

### Requests:

1. **Create Schedule**

    - Method: POST
    - URL: `{{base_url}}/api/feed-schedule/create`
    - Headers:
        - `Authorization: Bearer {{token}}`
        - `Content-Type: application/json`
    - Body (JSON):
        ```json
        {
            "name": "Pakan Pagi",
            "waktu_pakan": "08:00:00",
            "start_date": "2025-10-06",
            "end_date": "2025-12-31"
        }
        ```

2. **Get All Schedules**

    - Method: GET
    - URL: `{{base_url}}/api/feed-schedule`
    - Headers: `Authorization: Bearer {{token}}`

3. **Update Schedule**
    - Method: PUT
    - URL: `{{base_url}}/api/feed-schedule/{{schedule_id}}`
    - Headers: Same as above
    - Body: Update fields

---

## ✅ Expected Behavior

| Scenario                        | Expected Result               |
| ------------------------------- | ----------------------------- |
| Create dengan semua field valid | Status 201, schedule created  |
| Create tanpa waktu_pakan        | Status 422, validation error  |
| Update waktu_pakan              | Status 200, waktu updated     |
| Deactivate schedule             | Status 200, is_active = false |
| Delete schedule                 | Status 200, schedule deleted  |
| Get detail non-existent ID      | Status 404, not found         |
| Unauthorized request            | Status 401, unauthorized      |

---

## 🎯 Testing Checklist

-   [ ] Create schedule dengan data lengkap
-   [ ] Create schedule minimal (hanya waktu_pakan)
-   [ ] Create schedule tanpa end_date
-   [ ] Get all schedules
-   [ ] Get active schedules only
-   [ ] Get schedule detail dengan statistics
-   [ ] Update schedule time
-   [ ] Update schedule dates
-   [ ] Activate/Deactivate schedule
-   [ ] Delete schedule
-   [ ] Validation errors
-   [ ] Manual command execution
-   [ ] Automated execution (scheduler)

---

**Happy Testing! 🚀**
