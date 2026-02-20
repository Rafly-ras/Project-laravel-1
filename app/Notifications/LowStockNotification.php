<?php

namespace App\Notifications;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LowStockNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Low Stock Alert: {$this->product->name}")
            ->line("The stock for product '{$this->product->name}' has dropped below the threshold.")
            ->line("Current Stock: {$this->product->stock}")
            ->action('View Product', route('products.show', $this->product))
            ->line('Please restock as soon as possible to avoid fulfillment issues.');
    }

    public function toArray($notifiable): array
    {
        return [
            'product_id' => $this->product->id,
            'stock' => $this->product->stock,
            'message' => "Low stock alert for {$this->product->name} (Current: {$this->product->stock})",
        ];
    }
}
