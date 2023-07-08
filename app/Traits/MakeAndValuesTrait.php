<?php
declare(strict_types=1);

namespace App\Traits;

trait MakeAndValuesTrait
{
    public static function values(): array
    {
        return array_column(static::cases(), 'value');
    }

    public static function make(string $value): static
    {
        foreach (static::cases() as $case) {
            if ($case->value === $value) {
                return $case;
            }
        }

        throw new \LogicException('Enum value ' . $value . ' not support');
    }
}
