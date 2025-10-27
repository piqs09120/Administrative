<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class CompanyPolicy extends Model
{
    use HasFactory;

    protected $fillable = [
        'policy_code',
        'title',
        'description',
        'content',
        'category',
        'department',
        'version',
        'effective_date',
        'review_date',
        'status',
        'keywords',
        'related_laws',
        'applicable_roles',
        'created_by',
        'approved_by',
        'approved_at',
        'metadata'
    ];

    protected $casts = [
        'effective_date' => 'date',
        'review_date' => 'date',
        'approved_at' => 'datetime',
        'keywords' => 'array',
        'related_laws' => 'array',
        'applicable_roles' => 'array',
        'metadata' => 'array'
    ];

    /**
     * Scope for active policies
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for policies by category
     */
    public function scopeByCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }

    /**
     * Scope for policies by department
     */
    public function scopeByDepartment(Builder $query, string $department): Builder
    {
        return $query->where('department', $department);
    }

    /**
     * Search policies by keywords
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('content', 'like', "%{$search}%")
              ->orWhereJsonContains('keywords', $search);
        });
    }

    /**
     * Get policies that are due for review
     */
    public function scopeDueForReview(Builder $query): Builder
    {
        return $query->where('review_date', '<=', now()->addDays(30))
                    ->where('status', 'active');
    }

    /**
     * Check if policy is applicable to user role
     */
    public function isApplicableToRole(string $role): bool
    {
        if (empty($this->applicable_roles)) {
            return true; // If no specific roles, applies to all
        }
        
        return in_array($role, $this->applicable_roles);
    }

    /**
     * Get related Philippine laws
     */
    public function getRelatedLawsAttribute($value)
    {
        return $this->related_laws ?? [];
    }

    /**
     * Add keyword for AI linking
     */
    public function addKeyword(string $keyword): void
    {
        $keywords = $this->keywords ?? [];
        if (!in_array($keyword, $keywords)) {
            $keywords[] = $keyword;
            $this->update(['keywords' => $keywords]);
        }
    }

    /**
     * Link to Philippine law
     */
    public function linkToLaw(string $lawCode, string $lawTitle): void
    {
        $laws = $this->related_laws ?? [];
        $laws[] = [
            'code' => $lawCode,
            'title' => $lawTitle,
            'linked_at' => now()->toISOString()
        ];
        $this->update(['related_laws' => $laws]);
    }

    /**
     * Archive policy (no deletion)
     */
    public function archive(string $reason = 'Policy superseded'): void
    {
        $this->update([
            'status' => 'archived',
            'metadata' => array_merge($this->metadata ?? [], [
                'archived_at' => now()->toISOString(),
                'archive_reason' => $reason
            ])
        ]);
    }

    /**
     * Get policy status color for display
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'active' => 'text-green-600 bg-green-100',
            'draft' => 'text-yellow-600 bg-yellow-100',
            'archived' => 'text-gray-600 bg-gray-100',
            'superseded' => 'text-red-600 bg-red-100',
            default => 'text-gray-600 bg-gray-100'
        };
    }
}