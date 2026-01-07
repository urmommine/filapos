<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\StoreSetting;
use App\Services\ReceiptPrinter;
use Illuminate\Http\Request;

class ReceiptController extends Controller
{
    /**
     * Show receipt HTML (for browser print)
     */
    public function show(Order $order)
    {
        $order->load(['items', 'user']);
        
        $storeSettings = [
            'name' => StoreSetting::get(StoreSetting::STORE_NAME, 'POS Store'),
            'address' => StoreSetting::get(StoreSetting::STORE_ADDRESS, ''),
            'phone' => StoreSetting::get(StoreSetting::STORE_PHONE, ''),
            'footer' => StoreSetting::get(StoreSetting::RECEIPT_FOOTER, 'Terima Kasih!'),
        ];

        return view('receipt.show', compact('order', 'storeSettings'));
    }

    /**
     * Print receipt using ESC/POS
     */
    public function print(Order $order)
    {
        $order->load(['items', 'user']);
        
        try {
            $printer = new ReceiptPrinter();
            $printer->printReceipt($order);
            
            return response()->json(['success' => true, 'message' => 'Struk berhasil dicetak']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal mencetak: ' . $e->getMessage()], 500);
        }
    }
}
