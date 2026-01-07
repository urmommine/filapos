<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\StoreSetting;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

class Receipt extends Component
{
    public Order $order;
    public array $storeSettings;

    public function mount(Order $order)
    {
        $this->order = $order;
        $this->order->load(['items', 'user']);

        $this->storeSettings = [
            'name' => StoreSetting::get(StoreSetting::STORE_NAME, 'POS Store'),
            'address' => StoreSetting::get(StoreSetting::STORE_ADDRESS, ''),
            'phone' => StoreSetting::get(StoreSetting::STORE_PHONE, ''),
            'footer' => StoreSetting::get(StoreSetting::RECEIPT_FOOTER, 'Terima Kasih!'),
        ];
    }

    #[Layout('layouts.receipt')]
    public function render()
    {
        return view('livewire.receipt');
    }
}
