<?php


use App\Contracts\GroupBoxEnumInterface;
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
        $enum = VacancyExperienceEnum::make($value);
        $this->assertTrue($enum instanceof VacancyExperienceEnum);
        $this->assertInstanceOf(GroupBoxEnumInterface::class, $enum);
        $this->assertEquals(Str::ucfirst($enum->value), $enum->label());
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
