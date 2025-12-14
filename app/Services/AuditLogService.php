<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

class AuditLogService
{
    /**
     * Log an action.
     */
    public static function log(
        string $action,
        Model $model,
        string $description,
        array $oldValues = null,
        array $newValues = null
    ): AuditLog {
        return AuditLog::log(
            $action,
            $model,
            $description,
            $oldValues,
            $newValues,
            auth()->user()
        );
    }

    /**
     * Log points earned.
     */
    public static function logPointsEarned(Model $source, int $points, string $type, array $metadata = []): AuditLog
    {
        return self::log(
            'points_earned',
            $source,
            "{$type} points earned: {$points} points",
            null,
            array_merge(['points' => $points, 'type' => $type], $metadata)
        );
    }

    /**
     * Log points redeemed.
     */
    public static function logPointsRedeemed(Model $source, int $points, string $type): AuditLog
    {
        return self::log(
            'points_redeemed',
            $source,
            "{$type} points redeemed: {$points} points",
            ['points' => $points],
            ['points' => 0]
        );
    }

    /**
     * Log card frozen.
     */
    public static function logCardFrozen(Model $card, string $reason, User $admin): AuditLog
    {
        return self::log(
            'card_frozen',
            $card,
            "Card frozen. Reason: {$reason}",
            ['is_frozen' => false],
            ['is_frozen' => true, 'frozen_reason' => $reason, 'frozen_by' => $admin->id]
        );
    }

    /**
     * Log account frozen.
     */
    public static function logAccountFrozen(Model $account, string $reason, \App\Models\User $admin): AuditLog
    {
        return self::log(
            'account_frozen',
            $account,
            "Account frozen. Reason: {$reason}",
            ['is_frozen' => false],
            ['is_frozen' => true, 'frozen_reason' => $reason, 'frozen_by' => $admin->id]
        );
    }

    /**
     * Log ticket resolved.
     */
    public static function logTicketResolved(Model $ticket, string $resolution, \App\Models\User $admin): AuditLog
    {
        return self::log(
            'ticket_resolved',
            $ticket,
            "Ticket resolved. Resolution: {$resolution}",
            ['status' => 'open'],
            ['status' => 'resolved', 'resolved_by' => $admin->id]
        );
    }
}

