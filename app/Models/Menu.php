<?php

namespace App\Models;

use Database\Factories\MenuFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model
{
    /** @use HasFactory<MenuFactory> */
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'stand_id',
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'cost_price',
        'image',
        'stock',
        'is_available',
        'is_popular',
        'is_recommended',
        'preparation_time_minutes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'cost_price' => 'integer',
            'stock' => 'integer',
            'is_available' => 'boolean',
            'is_popular' => 'boolean',
            'is_recommended' => 'boolean',
            'preparation_time_minutes' => 'integer',
        ];
    }

    /**
     * Stand selling this menu.
     *
     * @return BelongsTo<Stand, $this>
     */
    public function stand(): BelongsTo
    {
        return $this->belongsTo(Stand::class);
    }

    /**
     * Category of this menu.
     *
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Order items referencing this menu.
     *
     * @return HasMany<OrderItem, $this>
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Favorites by students/users.
     *
     * @return HasMany<Favorite, $this>
     */
    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    /**
     * Scope to available items.
     *
     * @param  Builder<Menu>  $query
     */
    public function scopeAvailable(Builder $query): void
    {
        $query->where('is_available', true);
    }

    /**
     * Scope to popular items.
     *
     * @param  Builder<Menu>  $query
     */
    public function scopePopular(Builder $query): void
    {
        $query->where('is_popular', true);
    }

    /**
     * Format price in Rupiah.
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp '.number_format($this->price, 0, ',', '.');
    }
}
