<?php

namespace App\Models;

use App\Dto\FilterVacancyDto;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\PostgresConnection;

class Vacancy extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = ['title'];

    public function scopeFilter(Builder $builder, FilterVacancyDto $dto): Builder
    {
        return $builder->when(
            $dto->search,
            function (Builder $query) use ($dto) {
                $query->where(function (Builder $query) use ($dto) {
                    $likeOperator = $query->getConnection() instanceof PostgresConnection
                        ? 'ilike'
                        : 'like';

                    return $query->where('title', $likeOperator, "%{$dto->search}%")
                        ->orWhere('description', $likeOperator, "%{$dto->search}%");
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
                fn(Builder $query) => $query->where('experience', $dto->experience->value)
            );
    }
}
