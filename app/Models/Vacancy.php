<?php

namespace App\Models;

use App\Dto\FilterVacancyDto;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\PostgresConnection;

class Vacancy extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = ['title', 'description', 'salary', 'location', 'category', 'experience'];


    protected $casts = [
        'salary' => 'integer',
    ];

    public function employer(): BelongsTo
    {
        return $this->belongsTo(Employer::class);
    }

    public function scopeFilter(Builder $builder, FilterVacancyDto $dto): Builder
    {
        return $builder->when(
            $dto->search,
            function (Builder $query, string $search) {
                $likeOperator = $query->getConnection() instanceof PostgresConnection
                    ? 'ilike'
                    : 'like';

                $query->where(function (Builder $query) use ($search, $likeOperator) {
                    $query->where('title', $likeOperator, "%{$search}%")
                        ->orWhere('description', $likeOperator, "%{$search}%")
                        ->orWhereHas('employer', function (Builder $query) use ($search, $likeOperator) {
                            $query->where('name', $likeOperator, "%{$search}%");
                        });
                });
            }
        )
            ->when(
                $dto->salary_min,
                fn(Builder $query) => $query->where('salary', '>=', $dto->salary_min)
            )
            ->when(
                $dto->salary_max,
                fn(Builder $query) => $query->where('salary', '<=', $dto->salary_max)
            )
            ->when(
                $dto->category,
                fn(Builder $query) => $query->where('category', $dto->category->value)
            )
            ->when(
                $dto->experience,
                function ($query, $value) {
                    $query->where('experience', $value);
                }
            );
    }
}
