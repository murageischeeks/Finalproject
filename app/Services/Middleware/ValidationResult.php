<?php

namespace App\Services\Middleware;

class ValidationResult
{
    private function __construct(
        private readonly bool $passed,
        private readonly ?string $reason = null
    ) {}

    public static function pass(): self
    {
        return new self(true);
    }

    public static function fail(string $reason): self
    {
        return new self(false, $reason);
    }

    public function passes(): bool
    {
        return $this->passed;
    }

    public function reason(): ?string
    {
        return $this->reason;
    }
}