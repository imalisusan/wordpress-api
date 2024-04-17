<?php

declare(strict_types=1);

namespace App\Traits;

use App\Jobs\SendEmailJob;

trait CanSendEmail
{
    /**
     * Send email
     * @param $recipientName
     * @param $recipientEmailAddress
     * @param $subject
     * @param $message
     */
    public function sendEmail($recipientName, $recipientEmailAddress, $subject, $message): void
    {
        dispatch((new SendEmailJob(
            $recipientName,
            $recipientEmailAddress,
            $subject,
            $message
        )))->onQueue('send_email');
    }
}
