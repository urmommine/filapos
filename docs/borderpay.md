# BorderPay API Documentation

Dokumentasi resmi integrasi API Payment Gateway & Disbursement **BorderPay** (v1).

---

## 📑 Daftar Isi

- [1. Ringkasan & Autentikasi](#1-ringkasan--autentikasi)
  - [Base URL](#base-url)
  - [Format Header & Mode Kunci](#format-header--mode-kunci)
- [2. Pembayaran (Payments)](#2-pembayaran-payments)
  - [Buat Pembayaran (`POST /api/v1/payments`)](#buat-pembayaran-post-apiv1payments)
  - [Aturan Biaya, Limit & Masa Berlaku](#aturan-biaya-limit--masa-berlaku)
  - [Daftar Metode & Bank Aktif (`GET /api/v1/payment-methods`)](#daftar-metode--bank-aktif-get-apiv1payment-methods)
  - [Cek Status Transaksi (`GET /api/v1/payments/{reference_id}`)](#cek-status-transaksi-get-apiv1paymentsreference_id)
  - [Batalkan Pembayaran (`POST /api/v1/payments/{reference_id}/cancel`)](#batalkan-pembayaran-post-apiv1paymentsreference_idcancel)
  - [Simulasi Pembayaran Sandbox (`POST /api/v1/payments/{reference_id}/simulate`)](#simulasi-pembayaran-sandbox-post-apiv1paymentsreference_idsimulate)
  - [Daftar Riwayat Transaksi (`GET /api/v1/payments`)](#daftar-riwayat-transaksi-get-apiv1payments)
  - [Cek Saldo Merchant (`GET /api/v1/balance`)](#cek-saldo-merchant-get-apiv1balance)
- [3. Pengiriman Dana (Disbursements / Payout)](#3-pengiriman-dana-disbursements--payout)
  - [Kirim Payout (`POST /api/v1/disbursements`)](#kirim-payout-post-apiv1disbursements)
  - [Cek Status Payout (`GET /api/v1/disbursements/{id}`)](#cek-status-payout-get-apiv1disbursementsid)
  - [Daftar Riwayat Payout (`GET /api/v1/disbursements`)](#daftar-riwayat-payout-get-apiv1disbursements)
- [4. Integrasi Webhook](#4-integrasi-webhook)
  - [Event Webhook](#event-webhook)
  - [4 Aturan Wajib di Server Penerima](#4-aturan-wajib-di-server-penerima)
  - [Spesifikasi Header & Data Payload](#spesifikasi-header--data-payload)
  - [Contoh Kode Verifikasi Handler](#contoh-kode-verifikasi-handler)
- [5. Referensi Kode Error](#5-referensi-kode-error)

---

## 1. Ringkasan & Autentikasi

Semua request ke API BorderPay memerlukan API Key yang dikirimkan melalui header HTTP `Authorization` dengan skema `Bearer <API_KEY>`.

> [!WARNING]
> Simpan API Key Anda secara rahasia di sisi server (backend environment). **Dilarang keras** mengekspos API Key pada aplikasi frontend, aplikasi mobile client, atau repositori publik.

### Base URL

```http
https://borderpay.id/api/v1
```

### Format Header & Mode Kunci

Prefix pada API Key menentukan environment yang dituju:
- `bp_test_` : **Sandbox Environment** (simulasi transaksi tanpa dana riil).
- `bp_live_` : **Production Environment** (transaksi live menggunakan uang nyata).

#### Contoh Header Request

```http
# Mode Sandbox (Testing)
Authorization: Bearer bp_test_xxxxxxxxxxxxxxxxxxxx
Content-Type: application/json

# Mode Production (Live)
Authorization: Bearer bp_live_xxxxxxxxxxxxxxxxxxxx
Content-Type: application/json
```

---

## 2. Pembayaran (Payments)

### Buat Pembayaran (`POST /api/v1/payments`)

Endpoint ini digunakan untuk menginisialisasi satu transaksi pembayaran baru. Respons API menyediakan instruksi bayar spesifik sesuai metode yang dipilih:

- **QRIS** : Menghasilkan `qr_string` untuk di-render menjadi gambar QR Code.
- **Virtual Account (VA)** : Menghasilkan nomor rekening virtual `va_number` dan nama bank `va_bank`.
- **E-Wallet** : Menghasilkan `checkout_url` untuk mengarahkan pengguna ke aplikasi e-wallet terkait.
- **Hosted Checkout (`pay_url`)** : Cara integrasi tercepat. Cukup arahkan customer ke URL ini untuk membuka halaman pembayaran resmi siap pakai tanpa perlu mendesain UI sendiri.

#### Endpoint

```http
POST /api/v1/payments
```

#### Request Body (JSON)

| Parameter | Tipe | Wajib? | Keterangan |
| :--- | :--- | :--- | :--- |
| `amount` | `integer` | **Wajib** | Nominal transaksi dalam satuan Rupiah (IDR). |
| `method` | `string` | **Wajib** | Pilihan metode: `qris`, `va`, atau `ewallet`. |
| `bank_code` | `string` | *Kondisional* | **Wajib** untuk metode `va` (contoh: `BCA`, `BNI`) dan `ewallet` (`DANA`, `SHOPEE`, `OVO`). Ambil daftar kode aktif dari `GET /api/v1/payment-methods`. |
| `reference_id` | `string` | Opsional | Order ID unik dari sistem merchant. Jika tidak diisi, sistem akan membuat ID unik secara otomatis. |
| `return_url` | `string` | Opsional | URL redirect tujuan setelah customer menyelesaikan pembayaran. Wajib menggunakan HTTPS (pada mode sandbox diperbolehkan `http://localhost`). URL ini akan di-echo kembali di respons API. |

#### Contoh Request (cURL - QRIS)

```bash
curl -X POST https://borderpay.id/api/v1/payments \
  -H "Authorization: Bearer bp_live_xxx" \
  -H "Content-Type: application/json" \
  -d '{
    "amount": 150000,
    "method": "qris",
    "reference_id": "ORD-123",
    "return_url": "https://toko.com/terima-kasih?order=ORD-123"
  }'
```

#### Contoh Respons (`201 Created`)

```json
{
  "reference_id": "ORD-123",
  "status": "pending",
  "amount": 150000,
  "customer_pays": 150000,
  "merchant_receives": 148500,
  "fee": 1500,
  "fee_borne_by": "merchant",
  "pay_url": "https://borderpay.id/pay/ORD-123",
  "return_url": "https://toko.com/terima-kasih?order=ORD-123",
  "qr_string": "00020101021226...",
  "expires_at": "2026-07-13T11:00:00Z"
}
```

---

### Aturan Biaya, Limit & Masa Berlaku

#### Skema Pembebanan Biaya Transaksi (Fee)
Pengaturan penanggung biaya ditentukan per-project melalui dashboard merchant. Setiap respons API menyertakan kalkulasi transparan:
- `customer_pays` : Total yang dibayar customer. **Selalu tampilkan angka ini kepada customer**.
- `merchant_receives` : Dana bersih yang masuk ke saldo merchant.
- `fee` : Biaya pemrosesan transaksi.
- `fee_borne_by` : Pihak penanggung biaya (`merchant` atau `customer`).

> [!NOTE]
> - **QRIS** : Biaya transaksi selalu ditanggung oleh **merchant** (`fee_borne_by: "merchant"`).
> - **VA & E-Wallet** : Fleksibel. Dapat diatur ditanggung **merchant** (merchant menerima `amount - fee`) atau ditanggung **customer** (customer membayar `amount + fee`, merchant menerima nominal penuh `amount`).

#### Batas Nominal (Min/Max Amount)
- **QRIS & E-Wallet** : Minimal **Rp1.000** per transaksi.
- **Virtual Account (VA)** : Minimal **Rp10.000** per transaksi.
- Transaksi di luar ambang batas akan ditolak dengan error `422 Unprocessable Entity` (`amount_below_min` atau `amount_above_max`). Sesi transaksi tanpa `method` juga divalidasi sejak awal terhadap nominal yang mustahil diproses oleh metode manapun (contoh: Rp1).

#### Pergantian Bank / Metode Pembayaran
- Selama transaksi masih berstatus `pending`, Anda dapat mengirim ulang request dengan `reference_id` yang sama untuk mengganti bank/metode.
- VA yang lama otomatis dinonaktifkan, dan respons akan menyertakan tanda `"reused": true`.
- Transaksi yang sudah lunas (`paid`) tidak dapat diubah dan akan menghasilkan error `409 Conflict`.

#### Masa Berlaku Transaksi (Expiry Time)
- **QRIS & E-Wallet** : Masa berlaku **60 menit** sejak dibuat.
- **Virtual Account (VA)** : Masa berlaku **24 jam**.
- Waktu kedaluwarsa presisi dapat dicek pada field `expires_at`. Jika terlewati, status otomatis berubah menjadi `expired` dan memicu notifikasi webhook.

#### Error Spesifik Bank Virtual Account (HTTP 422)
- `bank_required` : Request metode VA tanpa menyertakan `bank_code`.
- `amount_below_min` / `amount_above_max` : Nominal di luar limit bank tersebut (VA minimal Rp10.000).
- `bank_not_allowed` : Bank dinonaktifkan di pengaturan project Anda.
- `bank_unavailable` : Layanan bank terkait sedang tidak tersedia/gangguan dari sisi provider.

---

### Daftar Metode & Bank Aktif (`GET /api/v1/payment-methods`)

Mengambil daftar metode pembayaran, bank VA, dan dompet digital yang saat ini aktif beserta tarif fee-nya.

> [!TIP]
> **Rekomendasi Integrasi:** Render pilihan metode dan bank pembayaran di aplikasi Anda secara dinamis berdasarkan respons endpoint ini — jangan hardcode di aplikasi. Bank yang sedang dimatikan akan memiliki status `"enabled": false`.

#### Endpoint

```http
GET /api/v1/payment-methods
```

#### Contoh Request (cURL)

```bash
curl https://borderpay.id/api/v1/payment-methods \
  -H "Authorization: Bearer bp_live_xxx"
```

#### Contoh Respons (`200 OK`)

```json
{
  "qris": {
    "enabled": true,
    "fee": {
      "percent": 0.7,
      "flat": 300
    }
  },
  "va": {
    "enabled": true,
    "banks": [
      {
        "code": "BNI",
        "name": "BNI",
        "enabled": true,
        "fee": {
          "percent": 0,
          "flat": 4200
        }
      },
      {
        "code": "BCA",
        "name": "BCA",
        "enabled": false,
        "fee": {
          "percent": 0,
          "flat": 4200
        }
      }
    ]
  },
  "ewallet": {
    "enabled": true,
    "fee": {
      "percent": 1.5,
      "flat": 500
    },
    "wallets": [
      {
        "code": "DANA",
        "name": "DANA",
        "enabled": true,
        "fee": {
          "percent": 1.5,
          "flat": 500
        }
      },
      {
        "code": "SHOPEE",
        "name": "ShopeePay",
        "enabled": true,
        "fee": {
          "percent": 1.5,
          "flat": 500
        }
      },
      {
        "code": "OVO",
        "name": "OVO",
        "enabled": true,
        "fee": {
          "percent": 1.5,
          "flat": 500
        }
      }
    ]
  }
}
```

---

### Cek Status Transaksi (`GET /api/v1/payments/{reference_id}`)

Mengambil status pembayaran terkini berdasarkan `reference_id`.

#### Endpoint

```http
GET /api/v1/payments/{reference_id}
```

#### Penjelasan Field Nominal (Penting untuk VA & E-Wallet)

- `order_amount` : Nominal tagihan asli yang dikirimkan saat `POST /payments`. Gunakan nilai ini untuk mencocokkan total belanjaan di sistem Anda.
- `customer_pays` : Total nominal yang dibayarkan oleh customer (`order_amount + fee` apabila `fee_borne_by = "customer"`).
- `merchant_receives` : Dana bersih yang masuk ke saldo akun Anda.
- `fee` : Selisih biaya transaksi.
- `amount` (pada GET) : Bernilai sama dengan `customer_pays` (dipertahankan untuk kompatibilitas ke belakang). Pada QRIS nilainya selalu identik dengan `order_amount`.
- **Sesi Pembayaran Terbuka (Belum Pilih Metode)** : Jika metode belum dipilih customer, perhitungan fee bernilai 0 (angka estimasi sementara). Rincian final terkunci segera setelah customer memilih metode bayar.

> [!IMPORTANT]
> **Best Practice Keamanan:** Jangan langsung mengirimkan pesanan fisik/digital hanya berdasarkan notifikasi webhook. Selalu panggil endpoint `GET /api/v1/payments/{reference_id}` ini dari server Anda untuk memverifikasi ulang bahwa status transaksi valid bernilai `"status": "paid"`.

#### Contoh Respons (`200 OK` - Virtual Account, Biaya Ditanggung Customer)

```json
{
  "reference_id": "ORD-123",
  "amount": 154200,
  "order_amount": 150000,
  "customer_pays": 154200,
  "merchant_receives": 150000,
  "fee": 4200,
  "fee_borne_by": "customer",
  "method": "va",
  "status": "paid",
  "paid_at": "2026-07-02T10:00:00Z"
}
```

---

### Batalkan Pembayaran (`POST /api/v1/payments/{reference_id}/cancel`)

Membatalkan transaksi yang sedang berstatus `pending`. Status transaksi akan diubah menjadi `expired`.

#### Endpoint

```http
POST /api/v1/payments/{reference_id}/cancel
```

#### Contoh Respons (`200 OK`)

```json
{
  "reference_id": "ORD-123",
  "status": "expired"
}
```

---

### Simulasi Pembayaran Sandbox (`POST /api/v1/payments/{reference_id}/simulate`)

Khusus pada environment sandbox (`bp_test_`), tidak ada mutasi perbankan nyata. Gunakan endpoint ini untuk mensimulasikan pembayaran berhasil, mengubah status menjadi `paid`, dan memicu pengiriman webhook `payment.paid` ke server Anda.

> [!TIP]
> **Simulasi Tanpa Kode:** Anda juga dapat mensimulasikan pembayaran tanpa memanggil API: buka URL checkout `/pay/{reference_id}` di browser, lalu klik tombol **“Simulasikan pembayaran berhasil”**, atau gunakan menu simulasi langsung dari Dashboard BorderPay.

#### Endpoint

```http
POST /api/v1/payments/{reference_id}/simulate
```

#### Contoh Request (cURL)

```bash
curl -X POST https://borderpay.id/api/v1/payments/ORD-123/simulate \
  -H "Authorization: Bearer bp_test_xxx"
```

#### Contoh Respons (`200 OK`)

```json
{
  "reference_id": "ORD-123",
  "status": "paid"
}
```

---

### Daftar Riwayat Transaksi (`GET /api/v1/payments`)

Mengambil daftar 50 transaksi pembayaran terakhir pada project Anda.

#### Endpoint

```http
GET /api/v1/payments
```

#### Contoh Respons (`200 OK`)

```json
{
  "data": [
    {
      "reference_id": "ORD-123",
      "amount": 150000,
      "method": "qris",
      "status": "paid"
    }
  ]
}
```

---

### Cek Saldo Merchant (`GET /api/v1/balance`)

Mengambil informasi saldo akun merchant saat ini dalam mata uang Rupiah (IDR).

#### Endpoint

```http
GET /api/v1/balance
```

#### Contoh Respons (`200 OK`)

```json
{
  "available": 950000,
  "pending": 150000,
  "currency": "IDR"
}
```

- `available` : Saldo aktif yang siap ditarik atau digunakan untuk payout/disbursement.
- `pending` : Dana dari transaksi berjalan yang sedang dalam proses kliring/settlement.

---

## 3. Pengiriman Dana (Disbursements / Payout)

### Kirim Payout (`POST /api/v1/disbursements`)

Mengirimkan dana dari saldo merchant ke rekening bank pihak ketiga manapun. Saldo Anda akan langsung di-reserve secara aman saat request diproses; jika pengiriman gagal, saldo otomatis dikembalikan secara utuh ke akun Anda.

#### Endpoint

```http
POST /api/v1/disbursements
```

#### Request Body (JSON)

| Parameter | Tipe | Wajib? | Keterangan |
| :--- | :--- | :--- | :--- |
| `amount` | `integer` | **Wajib** | Nominal bersih yang **diterima oleh pihak penerima**. Saldo Anda akan terpotong sebesar `amount + fee`. Minimal **Rp10.000**. |
| `bank_code` | `string` | **Wajib** | Kode bank kanonik (contoh: `BCA`, `MANDIRI`, `BRI`, `BNI`). Gunakan nama kanonik, bukan nomor sandi kliring BI. |
| `account_number` | `string` | **Wajib** | Nomor rekening tujuan transfer (6–20 digit angka). |
| `account_name` | `string` | **Wajib** | Nama lengkap pemilik rekening tujuan. |
| `reference_id` | `string` | Opsional | **Kunci Idempotensi** unik transaksi. Jika request terkirim ulang dengan ID sama, sistem mengembalikan data payout yang sama tanpa menduplikasi transfer. **Sangat disarankan.** |

#### Bank, Status & Penanganan Error Payout
- **Cakupan Bank** : Mendukung 77 bank tujuan transfer di Indonesia (lihat PDF daftar bank resmi atau cek status/tarif live via `GET /api/v1/payment-methods`).
- **Siklus Status** : `pending` → `completed` atau `failed` (saldo otomatis di-refund). Pada mode sandbox (`bp_test_`), payout akan langsung berubah menjadi `completed`.
- **Daftar Kode Error Payout** :
  - `403 Forbidden` :
    - `kyc_required` : Akun merchant belum menyelesaikan verifikasi KYC.
    - `account_frozen` : Akun merchant sedang dalam status pembekuan.
  - `422 Unprocessable Entity` :
    - `insufficient_balance` : Saldo yang tersedia tidak mencukupi untuk nominal transfer + fee.
    - `below_min` : Nominal transfer kurang dari batas minimum Rp10.000.
    - `bank_unsupported` : Kode bank tujuan tidak didukung.
    - `invalid_account` : Nomor rekening tidak ditemukan atau nama pemilik tidak sesuai.

#### Contoh Request (cURL)

```bash
curl -X POST https://borderpay.id/api/v1/disbursements \
  -H "Authorization: Bearer bp_live_xxx" \
  -H "Content-Type: application/json" \
  -d '{
    "amount": 100000,
    "bank_code": "BCA",
    "account_number": "1234567890",
    "account_name": "Budi Santoso",
    "reference_id": "PO-123"
  }'
```

#### Contoh Respons (`201 Created`)

```json
{
  "reference_id": "PO-123",
  "status": "pending",
  "amount": 100000,
  "fee": 5000,
  "total_deducted": 105000,
  "bank_code": "BCA",
  "account_number": "••••••7890"
}
```

---

### Cek Status Payout (`GET /api/v1/disbursements/{id}`)

Mengambil rincian dan status pengiriman payout. Nilai `{id}` dapat diisi dengan ID internal BorderPay maupun `reference_id` yang Anda tentukan saat pembuatan. Jika payout gagal, keterangan penyebab dicantumkan pada field `fail_reason`.

#### Endpoint

```http
GET /api/v1/disbursements/{id}
```

#### Contoh Respons (`200 OK`)

```json
{
  "reference_id": "PO-123",
  "status": "completed",
  "amount": 100000,
  "total_deducted": 105000,
  "processed_at": "2026-07-21T04:01:30Z",
  "fail_reason": null
}
```

---

### Daftar Riwayat Payout (`GET /api/v1/disbursements`)

Mengambil daftar 50 riwayat pengiriman payout terakhir sesuai mode API key yang digunakan (test / live).

#### Endpoint

```http
GET /api/v1/disbursements
```

---

## 4. Integrasi Webhook

Webhook merupakan notifikasi otomatis secara real-time dari BorderPay ke server Anda melalui HTTP POST setiap kali status transaksi mengalami perubahan.

> [!NOTE]
> Menggunakan Webhook adalah cara paling andal untuk memastikan pembayaran lunas. **Jangan pernah hanya mengandalkan aksi customer mengklik tombol "Saya Sudah Bayar" di browser.**

### Event Webhook

Jenis event dikirimkan melalui header `x-borderpay-event`:
- `payment.paid` : Pembayaran sukses diselesaikan oleh customer.
- `payment.expired` : Masa berlaku pembayaran habis sebelum diselesaikan.
- `payment.failed` : Pembayaran mengalami kegagalan proses.

> [!TIP]
> Jika sebuah transaksi sempat tercatat `expired` namun sesaat kemudian menerima callback `paid`, selalu utamakan status **`paid`** sebagai status final transaksi.

---

### 4 Aturan Wajib di Server Penerima

Patuhi 4 prinsip keamanan berikut saat memproses webhook BorderPay:

1. **Verifikasi Pengirim**  
   Bandingkan header `x-borderpay-token` dengan **Verification Token** project Anda (didapatkan dari dashboard, berawalan `bpt_`). Jika nilai token tidak cocok, segera tolak dengan status `401 Unauthorized`.
2. **Idempotensi Pemrosesan**  
   Simpan status pemrosesan `reference_id` di database Anda. Jika webhook dengan `reference_id` yang sama masuk berulang kali, abaikan pemrosesan ulang (mencegah pengiriman produk/layanan 2x).
3. **Urutan Response HTTP**  
   Kirimkan response HTTP status `200 OK` **setelah** seluruh perubahan data transaksi berhasil disimpan ke database Anda — bukan sebelumnya.
4. **Validasi Status & Order**  
   Cocokkan order via `reference_id` dan pastikan payload status bertuliskan `data.status === "paid"` sebelum menyelesaikan pesanan.

---

### Spesifikasi Header & Data Payload

#### HTTP Headers yang Dikirimkan oleh BorderPay

| Header | Keterangan |
| :--- | :--- |
| `x-borderpay-event` | Tipe event webhook (`payment.paid`, `payment.expired`, `payment.failed`). |
| `x-borderpay-token` | Token verifikasi untuk memastikan request benar-benar berasal dari BorderPay. |
| `x-borderpay-mode` | Environment pengirim (`test` atau `live`). |

> [!WARNING]
> Jika Anda melakukan penyimpanan ulang endpoint webhook di Dashboard, sistem akan menerbitkan token verifikasi baru (**Token Rotation**). Pastikan untuk memperbarui nilai token di konfigurasi environment server Anda.

#### Field Objek `data` pada Webhook
- `slug` : Identifier slug project Anda.
- `reference_id` : ID referensi transaksi Anda.
- `order_id` : Order ID bersih milik sistem Anda (untuk paylink tanpa penambahan prefix slug/test).
- `status` : Status transaksi terkini (`paid`, `expired`, `failed`).
- `amount` : Total yang dibayarkan oleh customer (`customer_pays`).
- `fee` : Biaya pemrosesan transaksi.
- `merchant_receives` : Dana bersih yang masuk ke saldo akun Anda (`amount = fee + merchant_receives`).
- `method` : Metode pembayaran yang digunakan (`qris`, `va`, `ewallet`).
- `mode` : Environment (`test` atau `live`).

---

### Contoh Kode Verifikasi Handler

Contoh implementasi webhook receiver yang aman menggunakan Node.js / Express:

```javascript
// Express.js Webhook Handler
app.post("/api/webhook/borderpay", async (req, res) => {
  // 1. Verifikasi token pengirim dari dashboard (format: bpt_...)
  const webhookToken = req.headers["x-borderpay-token"];
  if (webhookToken !== process.env.BORDERPAY_VERIFICATION_TOKEN) {
    return res.status(401).send("Unauthorized");
  }

  const { event, mode, data } = req.body;

  // 2. Pastikan hanya memproses status pembayaran yang sukses
  if (data.status !== "paid") {
    return res.status(200).send("Ignored non-paid event");
  }

  // 3. Pastikan idempoten: jangan proses transaksi yang sudah pernah lunas
  const order = await findOrderInDatabase(data.reference_id);
  if (!order || order.isPaid) {
    return res.status(200).send("Already processed");
  }

  // 4. Lakukan update status pesanan ke database DULU
  await markOrderAsPaid({
    orderId: data.reference_id,
    amountPaid: data.amount,
    netReceived: data.merchant_receives,
    fee: data.fee,
    paymentMethod: data.method,
    paidAt: new Date()
  });

  // 5. Baru kembalikan response 200 OK ke BorderPay
  return res.status(200).json({ received: true });
});
```

#### Contoh Payload Event `payment.paid`

```json
{
  "event": "payment.paid",
  "mode": "live",
  "data": {
    "slug": "toko-budi",
    "reference_id": "ORD-123",
    "order_id": "ORD-123",
    "status": "paid",
    "amount": 150000,
    "fee": 1500,
    "merchant_receives": 148500,
    "method": "qris"
  }
}
```

---

## 5. Referensi Kode Error

Tabel respon kode HTTP standar yang digunakan di seluruh API BorderPay:

| HTTP Status | Error Code | Keterangan |
| :--- | :--- | :--- |
| `400 Bad Request` | `invalid_request` | Format JSON tidak valid atau struktur payload rusak. |
| `401 Unauthorized` | `unauthorized` | API Key tidak disertakan, tidak valid, atau header salah. |
| `403 Forbidden` | `kyc_required` | Akun belum menyelesaikan proses verifikasi KYC (khusus Payout). |
| `403 Forbidden` | `account_frozen` | Akun dibekukan oleh sistem atau regulator. |
| `404 Not Found` | `not_found` | Resource atau `reference_id` transaksi tidak ditemukan. |
| `409 Conflict` | `already_paid` | Pembayaran sudah berstatus lunas dan tidak dapat diubah atau dibatalkan. |
| `422 Unprocessable`| `amount_below_min` | Nominal pembayaran atau payout berada di bawah batas minimal. |
| `422 Unprocessable`| `amount_above_max` | Nominal transaksi melebihi limit per transaksi dari bank/metode terkait. |
| `422 Unprocessable`| `bank_required` | Metode Virtual Account tidak menyertakan parameter `bank_code`. |
| `422 Unprocessable`| `bank_not_allowed` | Bank dinonaktifkan di pengaturan project. |
| `422 Unprocessable`| `bank_unavailable` | Jalur transaksi bank terkait sedang mengalami kendala di level switching provider. |
| `422 Unprocessable`| `insufficient_balance` | Saldo aktif merchant tidak mencukupi untuk payout + fee. |
| `422 Unprocessable`| `invalid_account` | Nomor rekening tujuan tidak valid atau salah nama penerima. |
| `500 Server Error` | `internal_error` | Terjadi kesalahan pada server internal BorderPay. Hubungi tim teknis BorderPay. |