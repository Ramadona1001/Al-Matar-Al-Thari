<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    /**
     * The password reset token.
     *
     * @var string
     */
    public $token;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        // Get locale from user preference, session, or default
        $locale = $notifiable->locale ?? session('locale') ?? app()->getLocale() ?? 'en';
        
        $resetUrl = url(($locale === 'ar' ? '/ar' : '/en') . '/reset-password/' . $this->token . '?email=' . urlencode($notifiable->getEmailForPasswordReset()));

        $userName = $notifiable->first_name ?? $notifiable->name ?? 'User';
        $appName = config('app.name');

        return (new MailMessage)
            ->subject($locale === 'ar' ? 'إعادة تعيين كلمة المرور - ' . $appName : 'Reset Password - ' . $appName)
            ->view('emails.reset-password', [
                'userName' => $userName,
                'resetUrl' => $resetUrl,
                'locale' => $locale,
                'token' => $this->token,
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
