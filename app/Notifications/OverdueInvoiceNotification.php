<?php

namespace App\Notifications;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OverdueInvoiceNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $invoice;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Payment Reminder: Invoice #{$this->invoice->salesOrder->sales_number}")
            ->greeting("Hello!")
            ->line("This is a reminder that your invoice #{$this->invoice->salesOrder->sales_number} is overdue.")
            ->line("Amount Due: $" . number_format($this->invoice->remaining_balance, 2))
            ->line("Due Date: " . $this->invoice->due_date->format('M d, Y'))
            ->action('View Invoice', route('invoices.show', $this->invoice))
            ->line('Please settle the outstanding balance as soon as possible.');
    }

    public function toArray($notifiable): array
    {
        return [
            'invoice_id' => $this->invoice->id,
            'amount' => $this->invoice->remaining_balance,
            'message' => "Invoice #{$this->invoice->salesOrder->sales_number} is overdue.",
        ];
    }
}
