<?php

namespace App\Models;

use App\Exceptions\CategoryMaxDepthException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class Category extends Model
{
    use HasFactory;

    private const MAX_DEPTH = 3;

    protected $fillable = [
        'title',
        'parent_category_id',
    ];

    protected $with = ['parentCategory'];

    protected static function booted(): void
    {
        static::creating(function (Category $category) {
            if ($category->parent_category_id === null) {
                return;
            }

            $parentCategory = Category::find($category->parent_category_id);

            $level = 0;

            $currentCategory = $parentCategory;
            while ($currentCategory !== null) {
                $level += 1;

                $currentCategory = $currentCategory->parentCategory;
            }

            if ($level >= self::MAX_DEPTH) {
                throw new CategoryMaxDepthException(__('Max category depth is 3!'));
            }
        });
    }

    public function parentCategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_category_id', 'id');
    }
}
