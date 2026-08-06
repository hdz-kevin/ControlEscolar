<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $user_id
 * @property string $code
 * @property Carbon|null $birth_date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['user_id', 'code', 'birth_date'])]
class Student extends Model
{
    protected $primaryKey = 'user_id';

    public $incrementing = false;

    protected $keyType = 'int';

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
        ];
    }
}
