<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationGroup = 'Transaksi';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Riwayat Transaksi';

    protected static ?string $modelLabel = 'Transaksi';

    protected static ?string $pluralModelLabel = 'Transaksi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detail Transaksi')
                    ->schema([
                        Forms\Components\TextInput::make('invoice_number')
                            ->label('No. Invoice')
                            ->disabled(),
                        Forms\Components\TextInput::make('user.name')
                            ->label('Kasir')
                            ->formatStateUsing(fn ($state, ?Order $record) => ($record && $record->channel !== 'pos') ? '-' : ($state ?: 'Yuliana'))
                            ->disabled(),
                        Forms\Components\TextInput::make('total_amount')
                            ->label('Total')
                            ->prefix('Rp')
                            ->disabled(),
                        Forms\Components\TextInput::make('payment_method')
                            ->label('Metode Bayar')
                            ->disabled(),
                        Forms\Components\TextInput::make('payment_status')
                            ->label('Status')
                            ->disabled(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultPaginationPageOption(15)
            ->paginationPageOptions([10, 15, 25, 50])
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')
                    ->label('No. Invoice')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('channel')
                    ->label('Channel')
                    ->badge()
                    ->sortable()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'ppob' => 'Top Up / PPOB',
                        'wallet_topup' => 'Isi Saldo',
                        default => 'Kasir POS',
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'ppob' => 'info',
                        'wallet_topup' => 'warning',
                        default => 'success',
                    }),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Kasir')
                    ->formatStateUsing(fn ($state, Order $record) => $record->channel === 'pos' ? ($record->user?->role === 'kasir' ? $state : ($state ?: 'Yuliana')) : '-')
                    ->placeholder('-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Metode Bayar')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'cash' => 'success',
                        'qris' => 'info',
                        'transfer' => 'warning',
                        'balance' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'cash' => 'Tunai',
                        'qris' => 'QRIS',
                        'transfer' => 'Transfer',
                        'balance' => 'Saldo Akun',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('payment_status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'pending' => 'warning',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'paid' => 'Lunas',
                        'pending' => 'Pending',
                        'cancelled' => 'Dibatalkan',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('channel')
                    ->label('Channel Penjualan')
                    ->options([
                        'pos' => 'Kasir POS',
                        'ppob' => 'Top Up / PPOB',
                        'wallet_topup' => 'Isi Saldo',
                    ]),
                Tables\Filters\SelectFilter::make('payment_method')
                    ->label('Metode Bayar')
                    ->options([
                        'cash' => 'Tunai',
                        'qris' => 'QRIS',
                        'transfer' => 'Transfer',
                        'balance' => 'Saldo Akun',
                    ]),
                Tables\Filters\SelectFilter::make('payment_status')
                    ->label('Status')
                    ->options([
                        'paid' => 'Lunas',
                        'pending' => 'Pending',
                        'cancelled' => 'Dibatalkan',
                    ]),
                Tables\Filters\Filter::make('today')
                    ->label('Hari Ini')
                    ->query(fn ($query) => $query->whereDate('created_at', today())),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('Dari Tanggal')
                            ->default(fn () => auth()->user()?->isTamu() ? '2026-10-20' : null)
                            ->minDate(fn () => auth()->user()?->isTamu() ? '2026-10-20' : null)
                            ->helperText(fn () => auth()->user()?->isTamu() ? '⚠️ Akun Tamu hanya dapat melihat riwayat tanggal 20 Oktober 2026 ke atas.' : null),
                        Forms\Components\DatePicker::make('until')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], function ($q, $date) {
                                if (auth()->user()?->isTamu() && $date < '2026-10-20') {
                                    \Filament\Notifications\Notification::make()
                                        ->danger()
                                        ->title('Peringatan: Akses Terbatas!')
                                        ->body('DILARANG: Akun Tamu tidak diizinkan melihat riwayat transaksi sebelum tanggal 20!')
                                        ->persistent()
                                        ->send();
                                    return $q->where('created_at', '>=', '2026-10-20 00:00:00');
                                }
                                return $q->whereDate('created_at', '>=', $date);
                            })
                            ->when($data['until'], fn ($q, $date) => $q->whereDate('created_at', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Detail Transaksi')
                    ->schema([
                        Infolists\Components\TextEntry::make('invoice_number')
                            ->label('No. Invoice'),
                        Infolists\Components\TextEntry::make('user.name')
                            ->label('Kasir')
                            ->formatStateUsing(fn ($state, Order $record) => $record->channel === 'pos' ? ($record->user?->role === 'kasir' ? $state : ($state ?: 'Yuliana')) : '-')
                            ->placeholder('-'),
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Tanggal')
                            ->date('d M Y'),
                        Infolists\Components\TextEntry::make('payment_method')
                            ->label('Metode Bayar')
                            ->badge(),
                        Infolists\Components\TextEntry::make('payment_status')
                            ->label('Status')
                            ->badge(),
                    ])->columns(3),

                Infolists\Components\Section::make('Rincian Pembayaran')
                    ->schema([
                        Infolists\Components\TextEntry::make('subtotal')
                            ->label('Subtotal')
                            ->money('IDR'),
                        Infolists\Components\TextEntry::make('discount')
                            ->label('Diskon')
                            ->money('IDR'),
                        Infolists\Components\TextEntry::make('tax')
                            ->label('Pajak')
                            ->money('IDR'),
                        Infolists\Components\TextEntry::make('total_amount')
                            ->label('Total')
                            ->money('IDR')
                            ->weight('bold'),
                        Infolists\Components\TextEntry::make('amount_paid')
                            ->label('Dibayar')
                            ->money('IDR'),
                        Infolists\Components\TextEntry::make('change')
                            ->label('Kembalian')
                            ->money('IDR'),
                    ])->columns(3),

                Infolists\Components\Section::make('Detail Layanan Top Up / PPOB (Otomatis H2H)')
                    ->schema([
                        Infolists\Components\TextEntry::make('game_name')
                            ->label('Game / Brand')
                            ->state(fn (Order $record) => json_decode($record->notes ?? '', true)['game'] ?? '-'),
                        // Infolists\Components\TextEntry::make('target_id')
                        //     ->label('User ID / No. Tujuan')
                        //     ->state(fn (Order $record) => json_decode($record->notes ?? '', true)['target'] ?? '-'),
                        Infolists\Components\TextEntry::make('zone_id')
                            ->label('Zone / Server ID')
                            ->state(fn (Order $record) => json_decode($record->notes ?? '', true)['zone'] ?? '-'),
                        // Infolists\Components\TextEntry::make('nickname')
                        //     ->label('Nickname Akun')
                        //     ->state(fn (Order $record) => json_decode($record->notes ?? '', true)['nickname'] ?? '-'),
                        // Infolists\Components\TextEntry::make('customer_phone')
                        //     ->label('No. WhatsApp Pelanggan')
                        //     ->state(fn (Order $record) => json_decode($record->notes ?? '', true)['customer_phone'] ?? '-'),
                        Infolists\Components\TextEntry::make('serial_number')
                            ->label('Serial Number (SN) / Status H2H')
                            ->state(fn (Order $record) => json_decode($record->notes ?? '', true)['sn'] ?? (json_decode($record->notes ?? '', true)['melo_status'] ?? '-'))
                            ->copyable(),
                    ])
                    ->columns(3)
                    ->visible(fn (Order $record): bool => !empty(json_decode($record->notes ?? '', true)['type']) && str_starts_with(json_decode($record->notes ?? '', true)['type'], 'ppob_')),

                Infolists\Components\Section::make('Item Transaksi')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('items')
                            ->label('')
                            ->schema([
                                Infolists\Components\TextEntry::make('product_name')
                                    ->label('Produk'),
                                Infolists\Components\TextEntry::make('quantity')
                                    ->label('Qty'),
                                Infolists\Components\TextEntry::make('unit_price')
                                    ->label('Harga')
                                    ->money('IDR'),
                                Infolists\Components\TextEntry::make('total_price')
                                    ->label('Total')
                                    ->money('IDR'),
                            ])->columns(4),
                    ])->collapsed(),

                Infolists\Components\Section::make('Catatan')
                    ->schema([
                        Infolists\Components\TextEntry::make('notes')
                            ->label('')
                            ->formatStateUsing(fn ($state) => (empty($state) || str_starts_with(trim($state), '{')) ? null : $state)
                            ->placeholder('Tidak ada catatan'),
                    ])
                    ->visible(fn (?Order $record) => $record && !empty($record->notes) && !str_starts_with(trim($record->notes), '{'))
                    ->collapsed(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'view' => Pages\ViewOrder::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false; // Orders are created from POS terminal
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return !auth()->user()?->isTamu() && (bool) auth()->user()?->isSuperAdmin();
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery()
            ->with(['user:id,name,role']);

        if (auth()->user()?->isTamu()) {
            $query->where('created_at', '>=', \Carbon\Carbon::create(2026, 10, 20)->startOfDay());
        }

        return $query;
    }
}
