<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as GuzzleHttpRequest;
use App\Models\Message;

class SendMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $data;
    /**
     * Create a new job instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $message = Message::findOrFail(1);

        $client = new Client();
        $headers = [
            'Content-Type' => 'application/x-www-form-urlencoded'
        ];
        $options = [
            'form_params' => [
                'api_key' => $message->api_key,
                'sender' => $message->sender,
                'number' => $this->data['telp'],
                'message' => $this->data['pesan']
            ]
        ];
        $endPoint = new GuzzleHttpRequest('POST', env('WHATSAPP_ENDPOINT') . '/send-message', $headers);
        $res = $client->sendAsync($endPoint, $options)->wait();
        $response = json_decode($res->getBody());

        echo $response->msg;
    }
}
