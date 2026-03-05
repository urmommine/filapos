<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\StoreSetting;
use App\Services\ReceiptPrinter;
use Illuminate\Http\JsonResponse;

class ReceiptController extends Controller
{
    public function print(Order $order): JsonResponse
    {
        $order->load(['items', 'user', 'customer']);

        $deploymentMode = StoreSetting::get(StoreSetting::DEPLOYMENT_MODE, 'cloud');
        $printer = new ReceiptPrinter();

        try {
            if ($deploymentMode === 'local') {
                // Scenario A: Direct print to hardware
                $printer->printReceipt($order);
                return response()->json([
                    'success' => true,
                    'message' => 'Struk berhasil dicetak',
                ]);
            }

            // Scenario B: Generate raw Base64 ESC/POS data for QZ Tray
            $rawData = $printer->generateRawData($order);
            return response()->json([
                'success' => true,
                'message' => 'Data struk berhasil dibuat',
                'raw' => $rawData,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mencetak: ' . $e->getMessage(),
            ], 500);
        }
    }
}
