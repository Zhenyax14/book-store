<?php

declare(strict_types=1);

namespace App\Domain\Reading\ValueObjects;

final readonly class HighlightColor
{
    private const array ALLOWED = [
        'yellow' => '#FFFF00',
        'green'  => '#90EE90',
        'blue'   => '#ADD8E6',
        'pink'   => '#FFB6C1',
        'orange' => '#FFD700',
    ];

    private function __construct(
        public string $name,
        public string $hex,
    ) {}

    public static function from(string $name): self
    {
        if ( ! isset(self::ALLOWED[$name])) {
            throw new \InvalidArgumentException(
                "Invalid color: {$name}. Allowed values: " . implode(', ', array_keys(self::ALLOWED)),
            );
        }

        return new self($name, self::ALLOWED[$name]);
    }

    public static function yellow(): self
    {
        return self::from('yellow');
    }

    public static function green(): self
    {
        return self::from('green');
    }

    public static function blue(): self
    {
        return self::from('blue');
    }

    public static function pink(): self
    {
        return self::from('pink');
    }

    public static function orange(): self
    {
        return self::from('orange');
    }

    public static function allowedNames(): array
    {
        return array_keys(self::ALLOWED);
    }
}
