<?php

namespace App\Models;

use App\Scopes\ReverseScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $perPage = 5;

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new ReverseScope());
    }

    /**
     * Current user of the post.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
