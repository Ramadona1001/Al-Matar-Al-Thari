<?php

namespace App\Notifications;

use App\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CompanyStatusChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        protected Company $company,
        protected string $status,
        protected ?string $reason = null,
        protected ?int $approvedBy = null
    ) {
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
        $statusLabel = ucfirst($this->status);

        $mail = (new MailMessage)
            ->subject(__('Company :name has been :status', ['name' => $this->company->name, 'status' => $statusLabel]))
            ->greeting(__('Hello :name', ['name' => $notifiable->full_name ?? $notifiable->name ?? '']))
            ->line(__('Your company ":company" has been :status.', ['company' => $this->company->name, 'status' => strtolower($statusLabel)]));

        if ($this->reason) {
            $mail->line(__('Reason: :reason', ['reason' => $this->reason]));
        }

        $mail->action(__('View Company'), route('merchant.dashboard'))
            ->line(__('Thank you for being part of :app!', ['app' => config('app.name')]));

        return $mail;
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => __('Company status updated'),
            'message' => __('Your company ":company" has been :status.', [
                'company' => $this->company->name,
                'status' => strtolower($this->status)
            ]),
            'status' => $this->status,
            'reason' => $this->reason,
            'company_id' => $this->company->id,
            'acted_by' => $this->approvedBy,
        ];
    }
}
