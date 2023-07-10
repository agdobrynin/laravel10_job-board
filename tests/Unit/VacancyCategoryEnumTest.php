<?php

namespace Tests\Unit;

use App\Contracts\GroupBoxEnumInterface;
use App\Enums\VacancyCategoryEnum;
use Illuminate\Support\Str;
use PHPUnit\Framework\TestCase;

class VacancyCategoryEnumTest extends TestCase
{
    /**
     * @dataProvider data
     */
    public function test_make_in_trait(string $value): void
    {
        $enum = VacancyCategoryEnum::make($value);
        $this->assertTrue($enum instanceof VacancyCategoryEnum);
        $this->assertInstanceOf(GroupBoxEnumInterface::class, $enum);
        $this->assertEquals(Str::ucfirst($enum->value), $enum->label());
    }

    public static function data(): \Generator
    {
        foreach (VacancyCategoryEnum::cases() as $num => $case) {
            yield 'test ' . $num + 1 . ' for VacancyCategoryEnum with value ' . $case->value => [$case->value];
        }
    }

    public function test_invalid_value(): void
    {
        $this->assertNull(VacancyCategoryEnum::make(Str::random()));
    }

    public function test_has_method_values(): void
    {
        $this->assertSame(
            array_column(VacancyCategoryEnum::cases(), 'value'),
            VacancyCategoryEnum::values()
        );
    }
}
