<?php

namespace App\Notifications\Channels;

use App\Models\Setting;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;

class FonnteChannel
{
    /**
     * Send the given notification via WhatsApp (Fonnte API).
     */
    public function send(object $notifiable, Notification $notification): void
    {
        if (empty($notifiable->phone)) {
            return;
        }

        $message = $notification->toWhatsApp($notifiable);
        if (empty($message)) {
            return;
        }

        $apiKey = Setting::get('fonnte_api_key');
        if (empty($apiKey)) {
            return;
        }

        Http::timeout(15)->post('https://api.fonnte.com/send', [
            'headers' => [
                'Authorization' => $apiKey,
            ],
            'json' => [
                'target'      => $notifiable->phone,
                'message'     => $message,
                'countryCode' => '62',
            ],
        ]);
    }
}
