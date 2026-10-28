<?php

namespace App\Filament\Pages;

use App\Models\StoreSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Cache;

class StoreSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $view = 'filament.pages.store-settings';

    protected static ?string $navigationGroup = 'Pengaturan';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Pengaturan Toko';

    public static function canAccess(): bool
    {
        return auth()->user()?->isAdmin() || auth()->user()?->isSuperAdmin();
    }

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'store_name' => StoreSetting::get(StoreSetting::STORE_NAME, ''),
            'store_address' => StoreSetting::get(StoreSetting::STORE_ADDRESS, ''),
            'store_phone' => StoreSetting::get(StoreSetting::STORE_PHONE, ''),
            'store_email' => StoreSetting::get(StoreSetting::STORE_EMAIL, ''),
            'tax_percentage' => StoreSetting::get(StoreSetting::TAX_PERCENTAGE, '0'),
            'printer_name' => StoreSetting::get(StoreSetting::PRINTER_NAME, ''),
            'printer_type' => StoreSetting::get(StoreSetting::PRINTER_TYPE, 'usb'),
            'printer_ip' => StoreSetting::get(StoreSetting::PRINTER_IP, ''),
            'receipt_footer' => StoreSetting::get(StoreSetting::RECEIPT_FOOTER, ''),
            'borderpay_active' => (bool) StoreSetting::get(StoreSetting::BORDERPAY_ACTIVE, true),
            'borderpay_api_key' => StoreSetting::get(StoreSetting::BORDERPAY_API_KEY, config('borderpay.api_key', '')),
            'borderpay_mode' => StoreSetting::get(StoreSetting::BORDERPAY_MODE, config('borderpay.mode', 'live')),
            'melostore_api_key' => StoreSetting::get(StoreSetting::MELOSTORE_API_KEY, config('melostore.api_key', '')),
            'melostore_secret_key' => StoreSetting::get(StoreSetting::MELOSTORE_SECRET_KEY, config('melostore.secret_key', '')),
            'melostore_mode' => StoreSetting::get(StoreSetting::MELOSTORE_MODE, config('melostore.sandbox') ? 'sandbox' : 'live'),
            'melostore_min_price' => StoreSetting::get(StoreSetting::MELOSTORE_MIN_PRICE, config('melostore.min_price', 5000)),
            'melostore_margin_percent' => StoreSetting::get(StoreSetting::MELOSTORE_MARGIN_PERCENT, config('melostore.margin_percent', 5)),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Toko')
                    ->description('Informasi ini akan ditampilkan pada struk')
                    ->schema([
                        Forms\Components\TextInput::make('store_name')
                            ->label('Nama Toko')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('store_address')
                            ->label('Alamat')
                            ->rows(2)
                            ->maxLength(500),
                        Forms\Components\TextInput::make('store_phone')
                            ->label('Nomor Telepon')
                            ->tel()
                            ->maxLength(20),
                        Forms\Components\TextInput::make('store_email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('Pengaturan Pajak')
                    ->schema([
                        Forms\Components\TextInput::make('tax_percentage')
                            ->label('Persentase Pajak (%)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->suffix('%')
                            ->helperText('Set 0 jika tidak ada pajak'),
                    ]),

                Forms\Components\Section::make('Pengaturan Printer')
                    ->description('Konfigurasi untuk thermal printer')
                    ->schema([
                        Forms\Components\Select::make('printer_type')
                            ->label('Tipe Koneksi Printer')
                            ->options([
                                'usb' => 'USB / Local',
                                'network' => 'Network (IP)',
                            ])
                            ->default('usb')
                            ->live(),
                        Forms\Components\TextInput::make('printer_name')
                            ->label('Nama Printer')
                            ->helperText('Nama printer di Windows, misal: POS-58')
                            ->visible(fn(Forms\Get $get) => $get('printer_type') === 'usb'),
                        Forms\Components\TextInput::make('printer_ip')
                            ->label('IP Address Printer')
                            ->helperText('Contoh: 192.168.1.100')
                            ->visible(fn(Forms\Get $get) => $get('printer_type') === 'network'),
                    ])->columns(2),

                Forms\Components\Section::make('Pengaturan Struk')
                    ->schema([
                        Forms\Components\Textarea::make('receipt_footer')
                            ->label('Footer Struk')
                            ->rows(2)
                            ->placeholder('Terima Kasih Sudah Berbelanja!')
                            ->helperText('Teks yang muncul di bagian bawah struk'),
                    ]),

                Forms\Components\Section::make('Payment Gateway (BorderPay QRIS)')
                    ->description('Integrasi pembayaran QRIS Dinamis melalui BorderPay API v1')
                    ->icon('heroicon-o-qr-code')
                    ->schema([
                        Forms\Components\Toggle::make('borderpay_active')
                            ->label('Aktifkan Pembayaran QRIS BorderPay')
                            ->default(true),
                        Forms\Components\Select::make('borderpay_mode')
                            ->label('Mode Environment')
                            ->options([
                                'live' => 'Live (Production - Uang Nyata)',
                                'sandbox' => 'Sandbox (Testing - Simulasi)',
                            ])
                            ->default('live')
                            ->required(),
                        Forms\Components\TextInput::make('borderpay_api_key')
                            ->label('BorderPay API Key')
                            ->password()
                            ->revealable()
                            ->helperText('Prefix bp_live_... untuk live atau bp_test_... untuk sandbox')
                            ->placeholder('bp_live_xxxxxxxxxxxxxxxxxxxx')
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('PPOB Game Provider (Melostore H2H)')
                    ->description('Integrasi Top Up Game Otomatis via Melostore Public API')
                    ->icon('heroicon-o-puzzle-piece')
                    ->schema([
                        Forms\Components\Select::make('melostore_mode')
                            ->label('Mode Environment')
                            ->options([
                                'sandbox' => 'Sandbox (Testing - Simulasi Aman)',
                                'live' => 'Live (Production - Potong Saldo Akun)',
                            ])
                            ->default('sandbox')
                            ->required(),
                        Forms\Components\TextInput::make('melostore_min_price')
                            ->label('Harga Minimal Top Up (Rp)')
                            ->numeric()
                            ->default(5000)
                            ->helperText('Seluruh item top up di bawah nilai ini otomatis dibulatkan ke nominal ini')
                            ->required(),
                        Forms\Components\TextInput::make('melostore_margin_percent')
                            ->label('Margin Keuntungan (%)')
                            ->numeric()
                            ->default(5)
                            ->helperText('Persentase keuntungan di atas harga modal provider')
                            ->required(),
                        Forms\Components\TextInput::make('melostore_api_key')
                            ->label('Melostore API Key')
                            ->password()
                            ->revealable()
                            ->placeholder('Masukkan API Key Melostore')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('melostore_secret_key')
                            ->label('Melostore Secret Key')
                            ->password()
                            ->revealable()
                            ->placeholder('Masukkan Secret Key Melostore')
                            ->columnSpanFull(),
                    ])->columns(3),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan Pengaturan')
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        StoreSetting::set(StoreSetting::STORE_NAME, $data['store_name'] ?? '');
        StoreSetting::set(StoreSetting::STORE_ADDRESS, $data['store_address'] ?? '');
        StoreSetting::set(StoreSetting::STORE_PHONE, $data['store_phone'] ?? '');
        StoreSetting::set(StoreSetting::STORE_EMAIL, $data['store_email'] ?? '');
        StoreSetting::set(StoreSetting::TAX_PERCENTAGE, $data['tax_percentage'] ?? '0');
        StoreSetting::set(StoreSetting::PRINTER_NAME, $data['printer_name'] ?? '');
        StoreSetting::set(StoreSetting::PRINTER_TYPE, $data['printer_type'] ?? 'usb');
        StoreSetting::set(StoreSetting::PRINTER_IP, $data['printer_ip'] ?? '');
        StoreSetting::set(StoreSetting::RECEIPT_FOOTER, $data['receipt_footer'] ?? '');
        StoreSetting::set(StoreSetting::BORDERPAY_ACTIVE, !empty($data['borderpay_active']) ? '1' : '0');
        StoreSetting::set(StoreSetting::BORDERPAY_API_KEY, $data['borderpay_api_key'] ?? '');
        StoreSetting::set(StoreSetting::BORDERPAY_MODE, $data['borderpay_mode'] ?? 'live');
        StoreSetting::set(StoreSetting::MELOSTORE_API_KEY, $data['melostore_api_key'] ?? '');
        StoreSetting::set(StoreSetting::MELOSTORE_SECRET_KEY, $data['melostore_secret_key'] ?? '');
        StoreSetting::set(StoreSetting::MELOSTORE_MODE, $data['melostore_mode'] ?? 'sandbox');
        StoreSetting::set(StoreSetting::MELOSTORE_MIN_PRICE, $data['melostore_min_price'] ?? 5000);
        StoreSetting::set(StoreSetting::MELOSTORE_MARGIN_PERCENT, $data['melostore_margin_percent'] ?? 5);

        // Clear all setting caches
        Cache::forget('store_setting_' . StoreSetting::STORE_NAME);
        Cache::forget('store_setting_' . StoreSetting::STORE_ADDRESS);
        Cache::forget('store_setting_' . StoreSetting::STORE_PHONE);
        Cache::forget('store_setting_' . StoreSetting::STORE_EMAIL);
        Cache::forget('store_setting_' . StoreSetting::TAX_PERCENTAGE);
        Cache::forget('store_setting_' . StoreSetting::PRINTER_NAME);
        Cache::forget('store_setting_' . StoreSetting::PRINTER_TYPE);
        Cache::forget('store_setting_' . StoreSetting::PRINTER_IP);
        Cache::forget('store_setting_' . StoreSetting::RECEIPT_FOOTER);
        Cache::forget('store_setting_' . StoreSetting::BORDERPAY_ACTIVE);
        Cache::forget('store_setting_' . StoreSetting::BORDERPAY_API_KEY);
        Cache::forget('store_setting_' . StoreSetting::BORDERPAY_MODE);
        Cache::forget('store_setting_' . StoreSetting::MELOSTORE_API_KEY);
        Cache::forget('store_setting_' . StoreSetting::MELOSTORE_SECRET_KEY);
        Cache::forget('store_setting_' . StoreSetting::MELOSTORE_MODE);
        Cache::forget('store_setting_' . StoreSetting::MELOSTORE_MIN_PRICE);
        Cache::forget('store_setting_' . StoreSetting::MELOSTORE_MARGIN_PERCENT);
        Cache::forget('melostore_game_categories_v2');

        Notification::make()
            ->title('Pengaturan berhasil disimpan')
            ->success()
            ->send();
    }
}
