<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['name', 'phone_no', 'email', 'subject', 'message', 'is_replied'])]
class Consultation extends Model
{
    use HasUuids, SoftDeletes;
}
