<?php

declare(strict_types=1);

namespace App\Helpers;

use Exception;

class CraydelInternalResponseHelper
{
    /**
     * @var bool $status
    */
    public bool $status;

    /**
     * @var string $message
    */
    public string $message;

    /**
     * @var mixed $data
    */
    public mixed $data;

    /**
     * @var Exception|null $exception
     */
    public ?Exception $exception;

    /**
     * CraydelInternalResponseHelper constructor.
     * @param bool $status
     * @param string $message
     * @param mixed|null $data
     * @param Exception|null $exception
     */
    public function __construct(bool $status, string $message, mixed $data = null, Exception $exception = null)
    {
        $this->status = $status;
        $this->message = $message;
        $this->data = $data;
        $this->exception = $exception;
    }
}
