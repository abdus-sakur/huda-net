<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customers extends Model
{
    use HasFactory;
    protected $table = 'customers';
    protected $guarded = ['id'];

    public function payment(): HasMany
    {
        return $this->hasMany(Payments::class, 'customer_id', 'id');
    }

    public function scopeFilterYear($query, $year)
    {
        return $query->with(['payment' => function ($query) use ($year) {
            $query->where('year', $year);
        }]);
    }
}
