<?php

namespace Rusty\Executor;

class Result
{
    private $succes;

    private $output;

    private $errorOutput;

    public function __construct(bool $success, string $output, string $errorOutput)
    {
        $this->succes = $success;
        $this->output = $output;
        $this->errorOutput = $errorOutput;
    }

    public function isSuccessful(): bool
    {
        return $this->succes;
    }

    public function getOutput(): string
    {
        return $this->output;
    }

    public function getErrorOutput(): string
    {
        return $this->errorOutput;
    }
}
