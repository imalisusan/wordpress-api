<?php

declare(strict_types=1);

namespace App\Helpers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class JWTHelper
{
    /**
     * Get bear token without Bear prefix
     *
     * @throws Exception
     */
    public static function getBearTokenWithoutBearPrefix(Request $request): string
    {
        $token = $request->header('authorization');

        if (empty($token)) {
            throw new Exception('Missing authorization header');
        }

        if (CraydelHelperFunctions::isNull($token)) {
            throw new Exception('Missing authorization header');
        }

        if (Str::startsWith($token, 'Bearer ') === false) {
            throw new Exception('The token is not a Bearer token');
        }

        return Str::substr($token, 7);
    }

    /**
     * Decode JWT token
     */
    public static function decode(string $token): ?object
    {
        return json_decode(base64_decode(str_replace('_', '/', str_replace('-', '+', explode('.', $token)[1]))));
    }
}
