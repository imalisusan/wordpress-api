<?php

declare(strict_types=1);

namespace App\Traits;

use Exception;
use Illuminate\Support\Facades\Log;

trait CanLog
{
    /**
     * Log exception
     */
    public function logException(Exception $exception): void
    {
        Log::error($exception->getMessage());
    }

    /**
     * Log message
     */
    public function logMessage(?string $message): void
    {
        Log::info($message);
    }

    /**
     * Make exception message
     */
    public function makeExceptionMessage(Exception $exception): string
    {
        if ($exception->getCode() === 0) {
            return $exception->getMessage();
        } else {
            $class_type = get_class($exception);

            if ($class_type === 'Illuminate\Database\QueryException') {
                return 'Database error encounter';
            } else {
                return 'Error encountered while processing your request';
            }
        }
    }
}
