<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\StoreSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User
        User::firstOrCreate(
            ['email' => 'admin@pos.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        // Create Kasir User (Hanya Yuliana)
        User::firstOrCreate(
            ['email' => 'yuliana@pos.com'],
            ['name' => 'Yuliana', 'password' => Hash::make('password'), 'role' => 'kasir']
        );

        User::firstOrCreate(
            ['email' => 'superadmin@pos.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('notpassword'),
                'role' => 'superadmin',
            ]
        );

        // Create Tamu User (Akses Terbatas: Dashboard, Kategori, Produk, Riwayat Transaksi >= 26 Okt, dan Kasir POS)
        User::firstOrCreate(
            ['email' => 'tamu@pos.com'],
            [
                'name' => 'Tamu (Guest)',
                'password' => Hash::make('password'),
                'role' => 'tamu',
            ]
        );



        $this->call([
            DummyProductSeeder::class,
            RealProductAndSalesSeeder::class,
            GenerateHistoricalPosTransactionsSeeder::class,
            AddOctober2728CashTransactionsSeeder::class,
            ImportCsvPosTransactionsSeeder::class,
            ImportCsvPpobAndWalletSeeder::class,
            AddMemberBalancePurchasesSeeder::class,
            Add2300RealProductsSeeder::class,
            AddOctober29TransactionsSeeder::class,
        ]);

        // Store Settings
        StoreSetting::set(StoreSetting::STORE_NAME, 'Fila POS');
        StoreSetting::set(StoreSetting::STORE_ADDRESS, 'Jl. Contoh No. 123, Kota');
        StoreSetting::set(StoreSetting::STORE_PHONE, '0812-3456-789');
        StoreSetting::set(StoreSetting::STORE_EMAIL, 'info@filapos.com');
        StoreSetting::set(StoreSetting::TAX_PERCENTAGE, '0'); // 0% tax by default
        StoreSetting::set(StoreSetting::RECEIPT_FOOTER, 'Terima Kasih Sudah Berbelanja!');
        StoreSetting::set(StoreSetting::PRINTER_TYPE, 'usb');
    }
}
