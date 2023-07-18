<?php

namespace App\Models;

use App\Dto\FilterVacancyDto;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

    public function vacancyApplications(): HasMany
    {
        return $this->hasMany(VacancyApplication::class);
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

    public function scopeRelatedVacancies(): Builder
    {
        return $this->where('employer_id', $this->employer->id)
            ->where('id', '!=', $this->id);
    }

    public function hasUserVacancyApplication(User|string $user): bool
    {
        return $this->where('id', $this->id)
            ->whereHas(
                'vacancyApplications',
                fn(Builder $builder) => $builder->where('user_id', $user->id ?? $user)
            )
            ->exists();
    }
}
