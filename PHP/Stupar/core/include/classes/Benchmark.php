<?php

class Benchmark
{
    private float $StartTimestampNanoseconds = 0;

    public function __construct()
    {
        $this->Restart();
    }

    public function Restart(): void
    {
        $this->StartTimestampNanoseconds = (float)hrtime(true);
    }

    public function GetElapsedNanoseconds(): float
    {
        return (float)hrtime(true) - $this->StartTimestampNanoseconds;
    }

    public function GetElapsedMicroseconds(): float
    {
        return number_format((float)(((float)hrtime(true) - $this->StartTimestampNanoseconds) / 1000), 2);
    }

    public function GetElapsedMilliseconds(): float
    {
        return number_format((float)(((float)hrtime(true) - $this->StartTimestampNanoseconds) / 1000 / 1000), 2);
    }

    public function GetElapsedSeconds(): float
    {
        return number_format((float)(((float)hrtime(true) - $this->StartTimestampNanoseconds) / 1000 / 1000 / 1000), 2);
    }
}