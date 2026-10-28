# Rencana Integrasi Pembayaran QRIS BorderPay (FilaPOS)

**Dokumen Rencana:** `docs/PLAN-borderpay-qris.md`  
**Status:** DRAFT Disetujui (Socratic Gate Cleared)  
**Tipe Proyek:** WEB / BACKEND (Laravel 12 + Livewire 3 + Filament PHP 3.3)  
**Referensi API:** [borderpay.md](file:///c:/laragon/www/a-filapos-main/docs/borderpay.md)

---

## 1. Overview & Latar Belakang

Integrasi sistem kasir FilaPOS dengan Payment Gateway **BorderPay** untuk memfasilitasi transaksi pembayaran non-tunai berbasis **QRIS Dinamis**. Kasir dapat membuat QRIS secara instan dari total belanja keranjang kasir. Layar POS akan menampilkan QR Code dengan penghitung mundur (countdown 5 menit) dan secara otomatis memeriksa (polling) status pelunasan transaksi secara real-time. Jika pembayaran berhasil, sistem akan merampungkan transaksi, memotong stok produk, mencatat pembayaran lunas, dan mencetak struk secara otomatis.

---

## 2. Parameter Keputusan Desain (Socratic Gate Results)

Berdasarkan kesepakatan dan klarifikasi requirement:
1. **Alur UI Kasir:** Modal QRIS Dinamis langsung di layar POS Kasir. String payload QRIS (`qr_string`) di-render menjadi visual QR Code interaktif di sisi browser kasir.
2. **Penyimpanan Kredensial:** Konfigurasi hybrid — Kredensial default tersimpan di `.env` dan `config/borderpay.php`, dengan opsi kustomisasi langsung via antarmuka Filament Admin di **Pengaturan Toko** (`StoreSettings`).
3. **Mekanisme Verifikasi:** **Polling Langsung** via POS Terminal ke backend Laravel yang memanggil endpoint `GET /api/v1/payments/{reference_id}` BorderPay secara berkala (interval ~3 detik) tanpa ketergantungan pada public webhook endpoint (ideal untuk environment lokal/Laragon maupun production).
4. **Batas Waktu Layar (Timeout):** Countdown 5 menit pada modal QRIS. Kasir dapat memeriksa status manual atau mengganti metode pembayaran jika waktu habis.
5. **Penanganan Pembatalan Transaksi:** Saat modal QRIS dibatalkan oleh kasir, sistem mengirim request ke `POST /api/v1/payments/{reference_id}/cancel` untuk menjaga konsistensi status transaksi di BorderPay menjadi `expired`.

---

## 3. Kriteria Keberhasilan (Success Criteria)

- [ ] Kredensial API BorderPay (`API Key`, `Environment Mode`, `Is Active`) dapat dikonfigurasi melalui `.env` dan halaman Pengaturan Toko Filament Admin.
- [ ] Service `App\Services\BorderPayService` terisolasi rapi dengan metode inisialisasi QRIS, pengecekan status, dan pembatalan transaksi sesuai dokumentasi API v1.
- [ ] Tombol metode bayar QRIS pada Modal Checkout POS Terminal memicu pembuatan invoice referensi dan menghasilkan QR Code dinamis dengan nominal pas.
- [ ] Tampilan modal QRIS di POS responsif, menampilkan nominal tagihan, masa aktif countdown 5 menit, dan animasi status pemrosesan.
- [ ] Polling otomatis berjalan setiap 3 detik mendeteksi perubahan status transaksi menjadi `paid`.
- [ ] Ketika terdeteksi lunas, order tersimpan dengan status `paid`, stok produk terpotong atomik (database transaction), dan event cetak struk otomatis terpicu.
- [ ] Pembatalan modal oleh kasir mengirim sinyal cancel ke BorderPay dan mengembalikan kasir ke keranjang belanja tanpa data korup.

---

## 4. Tech Stack & Arsitektur

- **Framework:** Laravel 12 (PHP 8.2+)
- **State Management & UI POS:** Livewire 3 + Alpine.js + Tailwind CSS
- **Admin Panel:** Filament PHP 3.3
- **HTTP Client:** Laravel HTTP Client (`Illuminate\Support\Facades\Http`)
- **QR Rendering:** Client-side JavaScript QR library (misal QRCode.js / CDN atau SVG generator) tanpa memberatkan server
- **Database:** MySQL / MariaDB (Laragon)

---

## 5. Struktur Berkas yang Terlibat / Dibuat

```text
a-filapos-main/
├── app/
│   ├── Filament/
│   │   └── Pages/
│   │       └── StoreSettings.php                 [EDIT: Tambah field BorderPay]
│   ├── Livewire/
│   │   └── PosTerminal.php                       [EDIT: Logic integrasi QRIS & polling]
│   ├── Models/
│   │   ├── Order.php                             [EDIT: Tambah field reference & helpers]
│   │   └── StoreSetting.php                      [EDIT: Tambah konstanta key setting]
│   └── Services/
│       └── BorderPayService.php                  [BARU: Client API BorderPay v1]
├── config/
│   └── borderpay.php                             [BARU: File konfigurasi BorderPay]
├── database/
│   └── migrations/
│       └── 2026_10_28_000001_add_borderpay_fields_to_orders_table.php [BARU: Migration]
├── resources/
│   └── views/
│       ├── filament/
│       │   └── pages/
│       │       └── store-settings.blade.php      [EDIT jika ada custom view, atau via form schema]
│       └── livewire/
│           └── pos-terminal/
│               ├── _modal-checkout.blade.php     [EDIT: Trigger pembayaran QRIS]
│               ├── _modal-qris.blade.php          [BARU: Komponen Modal Display QRIS Dinamis]
│               └── _scripts.blade.php            [EDIT: Inklusi lib qrcode & event listener]
└── .env.example                                  [EDIT: Dokumentasi ENV BorderPay]
```

---

## 6. Rincian Tugas (Task Breakdown)

### Tahap 1: Fondasi & Konfigurasi Kredensial (P0)

#### Task 1.1: Pembuatan File Konfigurasi & Schema Database
- **ID:** `TASK-BP-01`
- **Agent:** `backend-specialist`
- **Skills:** `clean-code`, `database-design`
- **Prioritas:** P0 (Bloker untuk semua task lain)
- **Dependencies:** Tidak ada
- **Input:** Parameter `.env` dan struktur tabel `orders`.
- **Output:**
  - File `config/borderpay.php` berisi konfigurasi `api_key`, `base_url`, `mode`, dan `timeout`.
  - Migration file `add_borderpay_fields_to_orders_table.php` (menambahkan field `qris_reference_id`, `qris_qr_string`, `qris_expires_at` ke tabel `orders`).
- **Verify:** Jalankan `php artisan migrate` dan `php artisan config:clear`.

#### Task 1.2: Pembuatan BorderPay API Service
- **ID:** `TASK-BP-02`
- **Agent:** `backend-specialist`
- **Skills:** `clean-code`, `api-patterns`
- **Prioritas:** P0
- **Dependencies:** `TASK-BP-01`
- **Input:** Dokumentasi `docs/borderpay.md`.
- **Output:** Kelas `App\Services\BorderPayService` dengan fungsi:
  - `generateQris(int $amount, string $referenceId): array`
  - `checkStatus(string $referenceId): array`
  - `cancelPayment(string $referenceId): array`
  - Pengambilan kredensial dinamis (Prioritas `StoreSetting` -> Fallback `config('borderpay')`).
- **Verify:** Unit test / tinker test memanggil endpoint sandbox simulasi BorderPay.

#### Task 1.3: Integrasi Pengaturan BorderPay di Filament Admin
- **ID:** `TASK-BP-03`
- **Agent:** `backend-specialist`
- **Skills:** `clean-code`
- **Prioritas:** P1
- **Dependencies:** `TASK-BP-01`
- **Input:** `StoreSetting.php` & `StoreSettings.php` (Filament Page).
- **Output:**
  - Konstanta key pada model `StoreSetting` (`BORDERPAY_API_KEY`, `BORDERPAY_MODE`, `BORDERPAY_ACTIVE`).
  - Section form "Payment Gateway (BorderPay QRIS)" di halaman Pengaturan Toko.
- **Verify:** Simpan data di Admin Filament dan pastikan tersimpan ke database `store_settings`.

---

### Tahap 2: Implementasi State & Logika POS Terminal (P1)

#### Task 2.1: Adaptasi Model Order & Helper
- **ID:** `TASK-BP-04`
- **Agent:** `backend-specialist`
- **Skills:** `clean-code`
- **Prioritas:** P1
- **Dependencies:** `TASK-BP-01`
- **Input:** `App\Models\Order.php`.
- **Output:** Penambahan `$fillable` untuk `qris_reference_id`, `qris_qr_string`, `qris_expires_at` dan helper method status order.
- **Verify:** Instance order dapat menyimpan data QRIS tanpa error mass-assignment.

#### Task 2.2: Logika Interaksi QRIS pada Komponen Livewire `PosTerminal`
- **ID:** `TASK-BP-05`
- **Agent:** `backend-specialist`
- **Skills:** `clean-code`
- **Prioritas:** P1
- **Dependencies:** `TASK-BP-02`, `TASK-BP-04`
- **Input:** `app/Livewire/PosTerminal.php`.
- **Output:**
  - State properti baru: `$showQrisModal`, `$qrisData` (`qr_string`, `reference_id`, `expires_at`, `amount`), `$isPollingQris`, `$qrisCountdown`.
  - Method `initiateQrisPayment()`: memvalidasi keranjang, membuat `reference_id` unik, memanggil `BorderPayService::generateQris()`, dan membuka modal QRIS.
  - Method `checkQrisPaymentStatus()`: dipanggil oleh polling Livewire/Alpine setiap 3 detik, memanggil `BorderPayService::checkStatus()`. Bila lunas, memanggil logika finalisasi order (`finalizePaidQrisOrder()`).
  - Method `cancelQrisPayment()`: memanggil `BorderPayService::cancelPayment()` dan menutup modal QRIS.
- **Verify:** Alur pemanggilan API mock / sandbox berhasil mengembalikan respons terstruktur dan state berubah sesuai skenario.

---

### Tahap 3: Antarmuka Pengguna (UI/UX) POS Terminal (P2)

#### Task 3.1: Pembuatan Komponen Blade Modal QRIS Dinamis
- **ID:** `TASK-BP-06`
- **Agent:** `frontend-specialist`
- **Skills:** `frontend-design`, `clean-code`
- **Prioritas:** P2
- **Dependencies:** `TASK-BP-05`
- **Input:** Tailwind CSS styling yang konsisten dengan tema POS FilaPOS (`resources/views/livewire/pos-terminal/`).
- **Output:**
  - File partial `resources/views/livewire/pos-terminal/_modal-qris.blade.php`.
  - Elemen visual:
    - Box QR Code beresolusi tajam dengan container putih bersih.
    - Nominal total bayar dengan formatting Rupiah yang jelas.
    - Timer countdown 5 menit berbasis Alpine.js dengan indikator visual.
    - Badge animasi loading / pulse status "Menunggu Pelanggan Memindai QRIS...".
    - Tombol "Cek Status Sekarang" (manual trigger) dan tombol "Batalkan Pembayaran".
- **Verify:** Tampilan modal muncul rapi, tidak bertabrakan dengan modal lain, dan kontras warna memenuhi standar aksesibilitas (bebas dari warna ungu terlarang/purple ban).

#### Task 3.2: Integrasi QR Code Renderer & Polling Hook
- **ID:** `TASK-BP-07`
- **Agent:** `frontend-specialist`
- **Skills:** `frontend-design`, `clean-code`
- **Prioritas:** P2
- **Dependencies:** `TASK-BP-06`
- **Input:** `_modal-checkout.blade.php`, `_scripts.blade.php`.
- **Output:**
  - Tombol pilihan "QRIS" di modal checkout terhubung dengan trigger `initiateQrisPayment()`.
  - Integrasi script QR Code renderer (misal library ringan `qrcode.min.js` dari CDN lokal / vendor asset) untuk merender `qr_string` ke elemen canvas/SVG.
  - Event listener Livewire `render-qris` untuk generate QR Code secara instan saat string QR diterima.
  - Wire polling atau Alpine timer `setInterval` yang memicu pengecekan status setiap 3 detik selama modal aktif.
- **Verify:** QR Code ter-render sempurna di browser, dapat dipindai oleh aplikasi e-wallet/mobile banking simulator, dan timer hitung mundur berjalan mulus.

---

### Tahap 4: Finalisasi Transaksi & Struk Kasir (P2)

#### Task 4.1: Finalisasi Transaksi Atomik & Pengurangan Stok
- **ID:** `TASK-BP-08`
- **Agent:** `backend-specialist`
- **Skills:** `clean-code`, `database-design`
- **Prioritas:** P2
- **Dependencies:** `TASK-BP-05`, `TASK-BP-07`
- **Input:** Transaksi status lunas dari BorderPay.
- **Output:**
  - Eksekusi `DB::transaction`: pembuatan record `Order`, penyimpanan `OrderItem`, pengurangan stok via `Product::reduceStock()`.
  - Pengaturan `payment_method = 'qris'`, `payment_status = 'paid'`, `amount_paid = total`, `change = 0`.
  - Trigger event `printReceipt` dan toast notifikasi sukses.
- **Verify:** Stok berkurang dengan benar, data tersimpan di DB, keranjang ter-reset, dan modal QRIS tertutup otomatis.

---

## 7. Penanganan Kasus Khusus & Edge Cases

1. **Jaringan Terputus / Gangguan API:**
   - Tambahkan timeout request (maksimal 10 detik) pada `BorderPayService` dengan exception handling (`try-catch`).
   - Tampilkan notifikasi error yang ramah di layar kasir ("Gagal menghubungi server BorderPay. Silakan coba kembali atau gunakan pembayaran Tunai").
2. **Countdown 5 Menit Habis:**
   - Timer berhenti, sistem menampilkan tombol pilihan: "Cek Ulang Status" atau "Ganti Metode Pembayaran / Batalkan".
   - Jika kasir membatalkan, kirimkan cancel ke BorderPay.
3. **Pembayaran Terlanjur Masuk saat Modal Hendak Ditutup:**
   - Sebelum membatalkan, method cancel dapat memverifikasi status terakhir terlebih dahulu; jika sudah lunas (`already_paid`), transaksi langsung diselesaikan, bukan dibatalkan.
4. **Idempotensi Reference ID:**
   - Menggunakan invoice number terstandarisasi (`INV-YYYYMMDD-XXXX`) atau format unik ber-timestamp untuk mencegah duplikasi order di BorderPay.

---

## 8. Phase X: Rencana Pengujian & Verifikasi (Checklist)

- [x] **Lint & Validasi Sintaks:** Sintaks PHP tervalidasi bebas error pada seluruh controller, service, livewire, dan model.
- [x] **Validasi Database:** Migrasi `add_borderpay_fields_to_orders_table` telah dijalankan ke database.
- [x] **Konfigurasi Environment & StoreSettings:** API Key live `bp_live_C_xN-6J...` aktif dan terhubung ke `https://borderpay.id/api/v1`.
- [x] **Invoice Number Generator:** Format `BORDER-{timestampMs}-{random4}` (misal: `BORDER-1793161063843-7091`) telah diimplementasikan.
- [x] **Rendering QR Code Client-side:** Menggunakan `public/js/qrcode.min.js` lokal tanpa dependensi eksternal.
- [x] **Polling Otomatis Real-time:** Interval 3 detik via Livewire & Alpine.js di modal kasir.
- [x] **Penyelesaian Order & Struk Otomatis:** Atomic database transaction saat status `paid` terdeteksi.
- [x] **Rule Compliance:** Bebas warna ungu (Purple Ban compliant), antarmuka selaras dengan tema POS.

## ✅ IMPLEMENTASI SELESAI
- Status: **BERHASIL DIIMPLEMENTASIKAN**
- API Key Mode: **Live (`bp_live_...`)**
- Format Invoice: **`BORDER-{timestampMs}-{random4}`**

