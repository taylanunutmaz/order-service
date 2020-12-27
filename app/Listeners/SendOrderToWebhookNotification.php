<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendOrderToWebhookNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  OrderCreated  $event
     * @return void
     */
    public function handle(OrderCreated $event)
    {
        try {
            $data = [
                'customer_id' => $event->customer->id,
                'order_id' => $event->order->id,
                'amount' => $event->order->total_price,
                'description' => $event->order->description,
            ];

            $client = new \GuzzleHttp\Client();

            $request = $client->post(env('WEBHOOK_URL'), [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ],
                'body' => json_encode($data)
            ]);

            $response = $request->getBody();
            $content = json_decode($response->getContents());

            if ($content->status !== 'OK') {
                throw new Exception('Error when sending data to Webhook. Response status is not OK', 500);
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
