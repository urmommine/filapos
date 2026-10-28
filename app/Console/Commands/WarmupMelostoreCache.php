<?php

namespace App\Console\Commands;

use App\Services\MeloStoreService;
use Illuminate\Console\Command;

class WarmupMelostoreCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'melostore:warmup {--force : Paksa refresh cache dari API}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pre-fetch dan hangatkan cache Melostore H2H agar load halaman /topups instan';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $force = $this->option('force');
        $this->info("Menghangatkan cache katalog Melostore H2H (force: " . ($force ? "YA" : "TIDAK") . ")...");

        // 1. Warmup Kategori & Brand List
        $t0 = microtime(true);
        $cats = MeloStoreService::getGameCategories($force);
        $dur0 = round(microtime(true) - $t0, 2);
        $this->info("✓ Kategori & Brand berhasil di-cache (" . count($cats) . " item, {$dur0}s)");

        // 2. Warmup Game & Brand Populer Utama
        $popularBrands = [
            'mobile-legends',
            'free-fire',
            'genshin-impact',
            'pubg-mobile',
            'valorant',
            'honor-of-kings',
            'roblox',
            'call-of-duty-mobile',
            'dana',
            'pln-prepaid',
        ];

        foreach ($popularBrands as $slug) {
            $t = microtime(true);
            $p = MeloStoreService::getGameProducts($slug, $force);
            $prodCount = count($p['products'] ?? []);
            $dur = round(microtime(true) - $t, 2);
            $this->line("  ✓ [{$slug}] di-cache ({$prodCount} paket, {$dur}s)");
        }

        $this->info("Selesai! Seluruh katalog utama sudah siap di cache untuk akses instan.");
        return 0;
    }
}
