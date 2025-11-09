<?php

namespace App\Notifications;

use App\Models\Offer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewOfferNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(protected Offer $offer)
    {
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('New offer: :title', ['title' => $this->offer->localized_title]))
            ->greeting(__('Hello :name', ['name' => $notifiable->full_name ?? $notifiable->name ?? '']))
            ->line(__('A new offer is now available from :company.', ['company' => $this->offer->company->name ?? config('app.name')]))
            ->line($this->offer->localized_description ?? '')
            ->action(__('View Offer'), route('customer.offers.show', $this->offer))
            ->line(__('Enjoy the latest discounts on :app!', ['app' => config('app.name')]));
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => __('New offer available'),
            'message' => __('":title" from :company is now available.', [
                'title' => $this->offer->localized_title,
                'company' => $this->offer->company->name ?? config('app.name')
            ]),
            'offer_id' => $this->offer->id,
            'company_id' => $this->offer->company_id,
            'starts_at' => $this->offer->start_date,
            'ends_at' => $this->offer->end_date,
        ];
    }
}
