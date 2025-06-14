<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    public const ADMIN_ROLE_ID = 1;
    public const SERVICE_PROVIDER_ROLE_ID = 2;
    public const SERVICE_CONSUMER_ROLE_ID = 3;

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
}
