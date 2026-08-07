<?php

namespace App\Helpers;

use App\Models\User;
use App\Models\AppNotification;

class NotifHelper
{
    /**
     * Kirim notifikasi ke semua user dengan role tertentu.
     */
    public static function kirimKeRole(array $roles, string $type, string $title, string $message, ?string $link = null)
    {
        $users = User::whereIn('role', $roles)->get();

        foreach ($users as $user) {
            AppNotification::create([
                'user_id' => $user->id,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'link' => $link,
            ]);
        }
    }
}