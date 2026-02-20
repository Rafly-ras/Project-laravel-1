<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FinancialSummaryNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $summary;

    public function __construct(array $summary)
    {
        $this->summary = $summary;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Monthly Financial Summary - " . now()->subMonth()->format('F Y'))
            ->greeting("Monthly Performance Update")
            ->line("Here is the financial summary for the previous month.")
            ->line("Total Revenue: $" . number_format($this->summary['revenue'], 2))
            ->line("Gross Profit: $" . number_format($this->summary['gross_profit'], 2))
            ->line("Total Expenses: $" . number_format($this->summary['expenses'], 2))
            ->line("Net Profit: $" . number_format($this->summary['net_profit'], 2))
            ->line("Average Margin: " . number_format($this->summary['margin'], 1) . "%")
            ->action('View Dashboard', route('dashboard'))
            ->line('Thank you for using our ERP system!');
    }

    public function toArray($notifiable): array
    {
        return $this->summary;
    }
}
