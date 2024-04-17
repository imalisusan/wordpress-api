<?php

declare(strict_types=1);

namespace App\Helpers;

use Illuminate\Http\Request;

class GetLoggedInUserHelper
{
    /**
     * Get current user
     *
     * @param Request $request
     *
     * @return object|null
     */
    public static function getUser(Request $request): ?object
    {
        $user = $request->user();

        if($user) {
            return $user;
        } else {
            return null;
        }
    }
}
