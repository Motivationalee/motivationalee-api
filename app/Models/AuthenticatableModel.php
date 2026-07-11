<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Foundation\Auth\User as Authenticatable;

abstract class AuthenticatableModel extends Authenticatable
{
    use HasUuids;
}
