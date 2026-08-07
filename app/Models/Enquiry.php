<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

#[Fillable(['name', 'organisation', 'email', 'phone', 'event_date', 'venue', 'audience_size', 'event_type', 'service', 'budget', 'message', 'status'])]
class Enquiry extends Model
{
    use HasUuids, SoftDeletes;
}
