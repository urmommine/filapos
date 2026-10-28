<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    public function mount(int | string $record): void
    {
        parent::mount($record);

        $cutoff = Carbon::create(2026, 10, 20)->startOfDay();

        if (auth()->user()?->isTamu() && $this->record->created_at < $cutoff) {
            Notification::make()
                ->danger()
                ->title('Peringatan: Akses Ditolak!')
                ->body('DILARANG: Akun Tamu tidak diizinkan melihat detail transaksi sebelum tanggal 20!')
                ->persistent()
                ->send();

            $this->redirect(OrderResource::getUrl('index'));
        }
    }
}
