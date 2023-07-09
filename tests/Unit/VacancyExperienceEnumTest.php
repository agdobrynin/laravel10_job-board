<?php


use App\Enums\VacancyExperienceEnum;
use Illuminate\Support\Str;
use PHPUnit\Framework\TestCase;

class VacancyExperienceEnumTest extends TestCase
{
    /**
     * @dataProvider data
     */
    public function test_make_in_trait(string $value): void
    {
        $this->assertTrue(VacancyExperienceEnum::make($value) instanceof VacancyExperienceEnum);
    }

    public static function data(): \Generator
    {
        foreach (VacancyExperienceEnum::cases() as $num => $case) {
            yield 'test ' . $num + 1 . ' for VacancyExperienceEnum with value ' . $case->value => [$case->value];
        }
    }

    public function test_invalid_value(): void
    {
        $this->assertNull(VacancyExperienceEnum::make(Str::random()));
    }

    public function test_has_method_values(): void
    {
        $this->assertSame(
            array_column(VacancyExperienceEnum::cases(), 'value'),
            VacancyExperienceEnum::values()
        );
    }
}
