<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Accommodation extends Model
{
    public function casts(): array
    {
        return [
            'specifications' => 'json'
        ];
    }
}
