<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['user_id'])]
class Admin extends Model
{
    protected $primaryKey = 'user_id';

    public $incrementing = false;

    protected $keyType = 'int';
}
