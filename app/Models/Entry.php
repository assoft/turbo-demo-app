<?php

namespace App\Models;

use App\Models\Entries\Commentable;
use App\Models\Entries\Reactionable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Entry extends Model
{
    use HasFactory;
    use Commentable;
    use Reactionable;

    public static $entryableShouldAutoCreate = true;

    public static function withoutEntryableAutoCreation(callable $scope)
    {
        try {
            static::$entryableShouldAutoCreate = false;

            return $scope();
        } finally {
            static::$entryableShouldAutoCreate = true;
        }
    }

    public function prune()
    {
        $this->reactions()->oldest()->cursor()
            ->each(function (Reaction $reaction) {
                $reaction->users()->detach();
                $reaction->delete();
            });

        $this->comments()->oldest()->cursor()
            ->each(function (Comment $comment) {
                $comment->entry->prune();
                $comment->delete();
            });
    }

    public function setEntryableAttribute($model)
    {
        $this->entryable()->associate($model);
    }

    public function entryable()
    {
        return $this->morphTo();
    }

    public function entryableResourceName()
    {
        return (string) Str::of($this->entryable->entryableResource())
            ->snake()
            ->explode('_')
            ->map(fn ($word) => ucfirst($word))
            ->join(' ');
    }

    public function entryableIndexRoute()
    {
        return $this->entryable->entryableIndexRoute();
    }

    public function entryableShowRoute()
    {
        return $this->entryable->entryableShowRoute();
    }

    public function getTitleAttribute()
    {
        return $this->entryable->entryableTitle();
    }

    public function entryableTeam()
    {
        return $this->entryable->entryableTeam();
    }

    public function belongsToTeam(User $user)
    {
        return $user->belongsToTeam($this->entryableTeam());
    }

    public function entryableRedirectAfterReaction()
    {
        return $this->entryable->entryableRedirectAfterReaction();
    }

    public function entryableStreamableForReactions()
    {
        return $this->entryable->entryableStreamableForReactions();
    }
}
