<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Appends;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Support\Facades\Storage;

#[Fillable(['slug', 'title', 'content', 'image', 'author', 'published_at'])]
#[Appends(['image_url'])]
#[Hidden(['image'])]
class Blog extends Model
{
    use HasUuids, SoftDeletes;

    public function getImageUrlAttribute(): ?string
    {
        return $this->image
            ? asset(Storage::url($this->image))
            : null;
    }
}
