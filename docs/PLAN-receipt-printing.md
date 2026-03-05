# Receipt Printing Refactor

Refactor the receipt printing system to use **direct ESC/POS thermal printing** without the browser print dialog, following the [dev.to article approach](https://dev.to/ianstudios_ac9bc/solved-direct-thermal-printing-from-a-web-app-without-the-print-dialog-phplaravel-18ad).

**Two deployment scenarios:**

| Scenario | How it works |
|----------|-------------|
| **A: Local Server** | PHP connects directly to printer via USB/Network IP → instant print |
| **B: Cloud/Shared Hosting** | PHP generates Base64 ESC/POS data → returns to Alpine.js → QZ Tray forwards to printer |

**User preference:** Scenario B with QZ Tray + Alpine.js as the primary approach.

---

## Proposed Changes

### 1. StoreSetting Model

#### [MODIFY] [StoreSetting.php](file:///c:/laragon/www/a-filapos-main/app/Models/StoreSetting.php)

- Add constant: `DEPLOYMENT_MODE = 'deployment_mode'` (values: `local` | `cloud`)

---

### 2. ReceiptPrinter Service

#### [MODIFY] [ReceiptPrinter.php](file:///c:/laragon/www/a-filapos-main/app/Services/ReceiptPrinter.php)

Refactor for dual-mode:

- Add `CapabilityProfile::load('default')` to Printer init (per article)
- Extract receipt composition into shared `composeReceipt(Printer $printer, Order $order)` method
- **`printReceipt(Order)`** — Scenario A: connects to hardware, prints directly
- **`generateRawData(Order): string`** — Scenario B: uses `DummyPrintConnector`, returns Base64 ESC/POS data

---

### 3. New Controller & Route

#### [NEW] [ReceiptController.php](file:///c:/laragon/www/a-filapos-main/app/Http/Controllers/ReceiptController.php)

```
POST /pos/receipt/{order}/print
├── deployment_mode == 'local'
│   → ReceiptPrinter::printReceipt($order)
│   → { success: true }
└── deployment_mode == 'cloud'
    → ReceiptPrinter::generateRawData($order)
    → { success: true, raw: "<base64>" }
```

#### [MODIFY] [web.php](file:///c:/laragon/www/a-filapos-main/routes/web.php)

- Add `POST /pos/receipt/{order}/print` route inside auth middleware

---

### 4. POS Frontend (Alpine.js + QZ Tray)

#### [MODIFY] [_scripts.blade.php](file:///c:/laragon/www/a-filapos-main/resources/views/livewire/pos-terminal/_scripts.blade.php)

Replace iframe `window.print()` with:

1. `fetch()` POST to `/pos/receipt/{order}/print`
2. If response has `raw` (Scenario B) → send Base64 data to QZ Tray via `qz.print()`
3. If no `raw` (Scenario A) → server already printed, show success toast only

#### QZ Tray Integration

- Include `qz-tray.js` library (CDN or local)
- Connect to QZ Tray on page load (`qz.websocket.connect()`)
- On print: find printer config from StoreSetting → `qz.print(config, data)`

---

### 5. StoreSettings Admin UI

#### [MODIFY] [StoreSettings.php](file:///c:/laragon/www/a-filapos-main/app/Filament/Pages/StoreSettings.php)

- Add `deployment_mode` select in Printer section: `Local Server` | `Cloud/Shared Hosting`
- Wire up `mount()` and `save()`

---

### 6. Keep Existing (No Changes)

- `app/Livewire/Receipt.php` — HTML receipt view (fallback/preview)
- `resources/views/livewire/struk/receipt.blade.php` — receipt Blade view
- `resources/views/layouts/receipt.blade.php` — receipt layout

---

## Verification Plan

### Manual Test — Scenario A (Local Server)
1. Admin → Pengaturan Toko → Set Deployment Mode to **Local Server**
2. Configure printer type (USB/Network) and name/IP
3. POS → Add items → Process payment
4. ✅ Receipt prints directly to thermal printer, no browser dialog
5. ✅ Browser DevTools Network shows `{ success: true }`

### Manual Test — Scenario B (Cloud + QZ Tray)
1. Install and run [QZ Tray](https://qz.io/download/) on cashier PC
2. Admin → Pengaturan Toko → Set Deployment Mode to **Cloud/Shared Hosting**
3. POS → Add items → Process payment
4. ✅ Server returns `{ success: true, raw: "<base64>" }`
5. ✅ QZ Tray receives data and prints to configured printer
6. ✅ No browser print dialog appears

### Manual Test — Error Handling
1. Set deployment to **Local**, leave printer settings empty
2. Process a payment
3. ✅ Toast notification shows error message, no app crash
4. Set deployment to **Cloud**, stop QZ Tray
5. Process a payment
6. ✅ JS catches QZ Tray connection error, shows user-friendly message
