<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Str;

/**
 * @property \App\Models\Entry $entry
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\Comment[] $comments
 *
 * @mixin \App\Models\Model
 */
trait Entryable
{
    public static function bootEntryable()
    {
        static::created(function ($entryable) {
            if (Entry::$entryableShouldAutoCreate) {
                $entryable->entry()->create();
            }
        });
    }

    public function entry(): MorphOne
    {
        return $this->morphOne(Entry::class, 'entryable');
    }

    public function comments(): HasManyThrough
    {
        return $this->hasManyThrough(Comment::class, Entry::class, 'entryable_id', 'parent_entry_id')
            ->where('entryable_type', $this->getMorphClass());
    }

    public function canHaveComments(): bool
    {
        if (property_exists($this, 'allowCommentsOnEntry')) {
            return $this->allowCommentsOnEntry;
        }

        return false;
    }

    public function entryableResource(): string
    {
        return Str::plural(basename(static::class));
    }

    public function entryableIndexRoute(): string
    {
        return route($this->entryableResource() . '.index');
    }

    public function entryableShowRoute(): string
    {
        return route($this->entryableResource() . '.show', $this);
    }
}
