<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use YieldStudio\LaravelExpoNotifier\ExpoNotificationsChannel;
use YieldStudio\LaravelExpoNotifier\Dto\ExpoMessage;

class NewNotification extends Notification
{
    protected $data;
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function via($notifiable): array
    {
        return [ExpoNotificationsChannel::class];
    }

    public function toExpoNotification($notifiable): ExpoMessage
    {
        $token =$notifiable->custom_options['token'];
        // var_dump( $this->data);
        return (new ExpoMessage())
            ->to([$token])
            ->title($this->data["message"])
            ->body($this->data["description"]);
    }
}