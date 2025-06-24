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

    public static function queryForYear($year): Builder
    {
        $sql = <<<SQL
                WITH RECURSIVE months AS (
                    SELECT 1 AS month
                    UNION ALL
                    SELECT month + 1 FROM months WHERE month < 12
                ),
                customer_months AS (
                    SELECT 
                        c.id AS customer_id,
                        c.price,
                        m.month,
                        c.subscribe,
                        ? AS year
                    FROM customers c
                    CROSS JOIN months m
                    WHERE c.subscribe <= CONCAT(?, '-', LPAD(m.month, 2, '0'))
                ),
                payments_check AS (
                    SELECT
                        cm.customer_id,
                        cm.month,
                        cm.year,
                        CAST(cm.price AS DECIMAL(12, 2)) AS price,
                        CASE 
                            WHEN p.id IS NULL THEN 0 ELSE 1
                        END AS has_paid
                    FROM customer_months cm
                    LEFT JOIN payments p
                        ON p.customer_id = cm.customer_id
                        AND p.month = cm.month
                        AND p.year = cm.year
                )
                SELECT 
                    CONCAT(year, LPAD(month, 2, '0')) AS id,
                    month,
                    year,
                    COUNT(*) AS total_customers,
                    SUM(has_paid) AS paid_customers,
                    COUNT(*) - SUM(has_paid) AS unpaid_customers,
                    SUM(CASE WHEN has_paid = 1 THEN price ELSE 0 END) AS total_paid,
                    SUM(CASE WHEN has_paid = 0 THEN price ELSE 0 END) AS total_unpaid
                FROM payments_check
                GROUP BY year, month
            SQL;

        // Gunakan DB::table()->fromSub(string, alias)
        return static::query()
            ->fromSub("($sql)", 'customer_monthly_report')
            ->addBinding([$year, $year], 'select');
    }
}
