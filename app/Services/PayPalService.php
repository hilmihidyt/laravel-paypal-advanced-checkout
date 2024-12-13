<?php

namespace App\Services;

use App\Models\User;
use App\Models\Order;
use App\Models\Ticket;
use Core\Request\Request;
use Illuminate\Support\Facades\Http;

class PayPalService
{
    private string $clientId;
    private string $secret;
    private string $apiUrl;

    public function __construct()
    {
        $this->clientId = config('services.paypal.client_id');
        $this->secret = config('services.paypal.secret');
        $this->apiUrl = config('services.paypal.api_url');
    }

    private function getAccessToken(): string
    {
        $response = Http::withBasicAuth($this->clientId, $this->secret)
            ->asForm()
            ->post("{$this->apiUrl}/v1/oauth2/token", [
                'grant_type' => 'client_credentials',
            ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to get PayPal access token');
        }

        return $response->json()['access_token'];
    }

    public function createOrder($request)
    {
        $accessToken = $this->getAccessToken();

        $ticket = Ticket::first();

        $user = User::updateOrCreate([
            'email' => $request->email,
        ], [
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt('password'),
        ]);

        $order = Order::create([
            'ticket_id'      => $ticket->id,
            'user_id'        => $user->id,
            'order_code'     => uniqid(),
            'amount'         => $ticket->price,
            'payment_method' => 'paypal',
        ]);

        $response = Http::withToken($accessToken)
            ->post("{$this->apiUrl}/v2/checkout/orders", [
                'intent' => 'CAPTURE',
                'purchase_units' => [[
                    'amount' => [
                        'value' => number_format($ticket->price, 2, '.', ''),
                        'currency_code' => 'USD',
                    ],
                ]],
            ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to create PayPal order');
        }

        return $response->json();
    }

    public function captureOrder($orderId)
    {
        $accessToken = $this->getAccessToken();

        $response = Http::withToken($accessToken)
            ->post("{$this->apiUrl}/v2/checkout/orders/{$orderId}/capture");

        \Log::info(['captureOrder' => $response]);

        if (!$response->successful()) {
            throw new \Exception('Failed to capture PayPal order');
        }

        return $response->json();
    }
}
