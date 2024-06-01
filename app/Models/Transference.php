<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Transference extends Model
{
    use HasFactory;
    use HasUlids;

    protected $fillable = [
        'payer_id',
        'payee_id',
        'amount'
    ];

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'foreign_key');
    }
}
