<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Orders;
use App\Services\AbaPaywayService;
use App\Traits\ApiResponse;

class AbaPaywayController extends Controller
{
    use ApiResponse;

    protected $paywayService;

    public function __construct(AbaPaywayService $paywayService)
    {
        $this->paywayService = $paywayService;
    }

    /**
     * Initiate a checkout to get the hash for ABA PayWay
     */
    public function checkout(Request $request)
    {
        // Typically, the client sends order_id or items
        $request->validate([
            'order_id' => 'required|string',
        ]);

        $order = Orders::find($request->order_id);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        // Generate tran_id for ABA (must be unique). e.g., Timestamp + internal Order ID
        $tranId = time() . '_' . $order->id;
        $reqTime = date('YmdHis');
        $amount = number_format($order->total_amount, 2, '.', '');
        
        // Items base64 encoded JSON
        $items = [];
        foreach ($order->orderItems ?? [] as $item) {
            $items[] = [
                'name' => 'Product ' . $item->product_id, // Adjust based on your relationship
                'quantity' => $item->quantity,
                'price' => number_format($item->price, 2, '.', '')
            ];
        }

        // Generate the Hash
        $hash = $this->paywayService->getHash(
            $reqTime,
            $tranId,
            $amount,
            $items,
            '', // firstName
            '', // lastName
            '', // email
            '', // phone
            env('APP_URL') . '/api/payway/return', // returnUrl
            env('APP_URL') . '/api/payway/success', // continueSuccessUrl
            '' // returnDeeplink
        );

        // Save ABA tran_id inside the order loosely (since it's MongoDB, dynamic schema)
        $order->update([
            'aba_tran_id' => $tranId,
            'payment_method' => 'aba_payway',
            'status' => 'pending'
        ]);

        return $this->success([
            'hash' => $hash,
            'tran_id' => $tranId,
            'req_time' => $reqTime,
            'merchant_id' => config('payway.merchant_id'),
            'amount' => $amount,
            'items' => base64_encode(json_encode($items)),
            'return_url' => env('APP_URL') . '/api/payway/return',
            'continue_success_url' => env('APP_URL') . '/api/payway/success',
            'api_url' => config('payway.api_url')
        ], 'Checkout initiated successfully');
    }

    /**
     * Webhook listener for Server-to-Server callback
     */
    public function webhook(Request $request)
    {
        $tranId = $request->input('tran_id');
        $hash = $request->input('hash');
        $status = $request->input('status');

        $isValid = $this->paywayService->verifyHash($hash, $tranId, '');

        if (!$isValid) {
            return response()->json(['message' => 'Invalid Signature'], 403);
        }

        $order = Orders::where('aba_tran_id', $tranId)->first();

        if ($order) {
            if ($status == 0 || $status == 'APPROVED') { // Check ABA Docs for exact status value
                $order->update(['status' => 'success']);
            } else {
                $order->update(['status' => 'failed']);
            }
        }

        return $this->success(null, 'Webhook received');
    }

    /**
     * Fixed version of createPayment from snippet.
     * Use this for flexible payments not tied to a specific internal Order model yet.
     */
    public function createPayment(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
        ]);

        $reqTime = date('YmdHis');
        $tranId = time(); 
        $amount = number_format($request->amount, 2, '.', '');
        $returnUrl = url('/api/payway/return');
        $successUrl = url('/api/payway/success');
        
        $items = []; // Ensuring empty array for consistency
        $firstName = $request->firstName ?? 'User';
        $lastName = $request->lastName ?? 'Demo';
        $email = $request->email ?? '';
        $phone = $request->phone ?? '';

        // Generate the Hash using the robust service
        $hash = $this->paywayService->getHash(
            $reqTime,
            $tranId,
            $amount,
            $items, 
            $firstName,
            $lastName,
            $email,
            $phone,
            $returnUrl,
            $successUrl,
            ''
        );

        return $this->success([
            'hash' => $hash,
            'tran_id' => $tranId,
            'req_time' => $reqTime,
            'merchant_id' => config('payway.merchant_id'),
            'amount' => $amount,
            'api_url' => config('payway.api_url'),
            'return_url' => $returnUrl,
            'continue_success_url' => $successUrl,
            'firstName' => $firstName,
            'lastName' => $lastName,
            'email' => $email,
            'phone' => $phone,
            'items' => base64_encode(json_encode($items)),
            'return_deeplink' => '',
        ], 'Payment created successfully');
    }

    /**
     * Fixed version of callback from snippet (Alias for webhook).
     */
    public function callback(Request $request)
    {
        return $this->webhook($request);
    }

    /**
     * Render a self-submitting HTML form to bridge GET request from mobile to POST request for ABA.
     */
    public function renderCheckout(Request $request)
    {
        $apiUrl = config('payway.api_url');
        $params = $request->all();

        // Build the auto-submitting form
        $html = '<html><head><title>Redirecting to ABA Payway...</title></head>';
        $html .= '<body onload="document.forms[0].submit()">';
        $html .= '<form action="' . $apiUrl . '" method="POST">';
        
        foreach ($params as $key => $value) {
            $html .= '<input type="hidden" name="' . htmlspecialchars($key) . '" value="' . htmlspecialchars($value) . '">';
        }
        
        $html .= '</form>';
        $html .= '<div style="text-align:center; margin-top:50px; font-family:sans-serif;">';
        $html .= '<h2>Redirecting to Secure Payment Page...</h2>';
        $html .= '<p>If you are not redirected automatically, please wait a moment.</p>';
        $html .= '</div>';
        $html .= '</body></html>';

        return response($html)->header('Content-Type', 'text/html');
    }
}
