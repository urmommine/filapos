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
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@pos.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Create Kasir User (Hanya Yuliana)
        User::firstOrCreate(
            ['email' => 'yuliana@pos.com'],
            ['name' => 'Yuliana', 'password' => Hash::make('password'), 'role' => 'kasir']
        );

        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@pos.com',
            'password' => Hash::make('notpassword'),
            'role' => 'superadmin',
        ]);



        $this->call([
            DummyProductSeeder::class,
            RealProductAndSalesSeeder::class,
            GenerateHistoricalPosTransactionsSeeder::class,
            AddOctober2728CashTransactionsSeeder::class,
            ImportCsvPosTransactionsSeeder::class,
            ImportCsvPpobAndWalletSeeder::class,
            AddMemberBalancePurchasesSeeder::class,
            Add2300RealProductsSeeder::class,
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
