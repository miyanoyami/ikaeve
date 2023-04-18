<?php

namespace App\Service;

class FlashMessageService
{
    public static function success($message)
    {
        session()->flash('flash_message', [
            'type' => 'info',
            'message' => $message,
        ]);
    }

    public static function error($message)
    {
        session()->flash('flash_message', [
            'type' => 'danger',
            'message' => $message,
        ]);
    }
}
