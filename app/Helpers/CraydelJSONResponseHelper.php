<?php

declare(strict_types=1);

namespace App\Helpers;

class CraydelJSONResponseHelper
{
    /**
     * Set the status of the JSON response. The possible values are true or false and should be or type boolean
     *
     * @var boolean $status
     */
    protected bool $status;

    /**
     * A string message to provided hume readable message to the response consumer.
     *
     * @var string $message
     */
    protected string $message;

    /**
     * The data if any which should be sent back to the response consumer
     *
     * @var $data
     */
    protected mixed $data;

    /**
     * Include the authentication token if its a login response
     *
     * @var string|null $authenticationToken
     */
    protected ?string $authenticationToken;

    /**
     * @return string|null
     */
    public function getAuthenticationToken(): ?string
    {
        return $this->authenticationToken;
    }

    /**
     * @param string|null $authenticationToken
     */
    public function setAuthenticationToken(?string $authenticationToken): void
    {
        $this->authenticationToken = $authenticationToken;
    }

    /**
     * CraydelJSONResponseType constructor.
     * @param bool $status
     * @param string $message
     * @param $data
     * @param $authenticationToken
     */
    public function __construct(bool $status, string $message, $data = null, $authenticationToken = null)
    {
        $this->status = $status;
        $this->message = $message;
        $this->data = $data;
        $this->authenticationToken = $authenticationToken;
    }

    /**
     * @return bool
     */
    public function isStatus(): bool
    {
        return $this->status;
    }

    /**
     * @param bool $status
     */
    public function setStatus(bool $status): void
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    /**
     * @return mixed
     */
    public function getData(): mixed
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData(mixed $data): void
    {
        $this->data = $data;
    }
}
