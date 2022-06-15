<?php

define('ERR_OK', true);
define('ERR_NOK', false);

class StdRet
{
    public bool $Status = false;
    public string $Message = "";

    public function __construct($status = false, $message = "")
    {
        $this->Status = $status;
        $this->Message = $message;
    }

    public function GetStatus(): bool
    {
        return $this->Status;
    }

    public function SetMessage($message): void
    {
        $this->Message = $message;
    }

    public function GetMessage(): string
    {
        return $this->Message;
    }

    public function SetStatusMessage($status, $message): void
    {
        $this->Status = $status;
        $this->Message = $message;
    }
}