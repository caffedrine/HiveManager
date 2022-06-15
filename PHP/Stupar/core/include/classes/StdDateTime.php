<?php

class StdDateTime
{
    private DateTime $dt;
    public bool $IsValid = false;

    public function __construct($time_string = null)
    {
        try
        {
            if( !$time_string )
            {
                $this->dt = new DateTime();
            }
            else
            {
                $this->dt = new DateTime($time_string);
            }
            $this->IsValid = true;
        }
        catch (Exception $e)
        {
            $this->dt = new DateTime("0-0-0000 00:00:00");
            $this->IsValid = false;
        }
    }

    public static function Curr(): self
    {
        return new self();
    }

    public static function FromStr($time_string = null): self
    {
        return new self($time_string);
    }

    public function AddSeconds(int $seconds): StdDateTime
    {
        $this->dt->modify("+{$seconds} second");
        return $this;
    }

    public function SubstractSeconds(int $seconds): StdDateTime
    {
        $this->dt->modify("-{$seconds} second");
        return $this;
    }

    public function AddHours(int $seconds): StdDateTime
    {
        $this->dt->modify("+{$seconds} hours");
        return $this;
    }

    public function Substracthours(int $seconds): StdDateTime
    {
        $this->dt->modify("-{$seconds} hours");
        return $this;
    }

    public function AddDays(int $days): StdDateTime
    {
        $this->dt->modify("+{$days} day");
        return $this;
    }

    public function SubstractDays(int $days): StdDateTime
    {
        $this->dt->modify("-{$days} day");
        return $this;
    }

    public function AddMonths(int $months): StdDateTime
    {
        $this->dt->modify("+{$months} month");
        return $this;
    }

    public function SubstractMonths(int $months): StdDateTime
    {
        $this->dt->modify("-{$months} month");
        return $this;
    }

    public function AddYears(int $years): StdDateTime
    {
        $this->dt->modify("+{$years} year");
        return $this;
    }

    public function SubstractYears(int $years): StdDateTime
    {
        $this->dt->modify("-{$years} year");
        return $this;
    }

    public function GetDate(): string
    {
        return $this->dt->format( Utils_GetDateFormatString() );
    }

    public function GetYear(): int
    {
        return (int)$this->dt->format( "Y" );
    }

    public function GetMonth(): int
    {
        return (int)$this->dt->format( "m" );
    }

    public function GetDay(): int
    {
        return (int)$this->dt->format( "d" );
    }

    public function GetTime(): string
    {
        return $this->dt->format( Utils_GetTimeFormatString() );
    }

    public function GetDateTime(): string
    {
        return $this->dt->format( Utils_GetDateTimeFormatString() );
    }

    public function GetTimestamp(): int
    {
        return $this->dt->getTimestamp();
    }

    public function Format(string $format): string
    {
        return $this->dt->format( $format );
    }
}