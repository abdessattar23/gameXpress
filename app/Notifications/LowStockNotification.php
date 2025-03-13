<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LowStockNotification extends Notification implements ShouldQueue
    {
        use Queueable;

        protected $products;

        public function __construct($products)
        {
            $this->products = $products;
        }

        public function via($notifiable)
        {
            return ['mail'];
        }

        public function toMail($notifiable)
        {
            $mailMessage = (new MailMessage)
                ->subject('Low Stock Alert')
                ->line('The following products are low in stock:');

            foreach ($this->products as $product) {
                $mailMessage->line("Product: {$product->name} - Current Stock: {$product->stock}");
            }

            // $mailMessage->action('View Products', url('/admin/products'))
            //     ->line('Thank you for your attention to this matter.');

            return $mailMessage;
        }
    }
