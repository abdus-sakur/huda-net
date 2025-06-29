<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class ReportPayments extends Model
{
  protected $table = 'payment_summaries'; // Tabel virtual

  protected $fillable = [
    'month',
    'year',
    'total_customers',
    'paid_customers',
    'unpaid_customers',
    'total_paid',
    'total_unpaid'
  ];

  protected $casts = [
    'month' => 'integer',
    'year' => 'string',
    'total_customers' => 'integer',
    'paid_customers' => 'integer',
    'unpaid_customers' => 'integer',
    'total_paid' => 'decimal:2',
    'total_unpaid' => 'decimal:2',
  ];

  public static function getPaymentSummary($year)
  {
    $query = "
            WITH RECURSIVE months AS (
              SELECT 1 AS month, ? AS year
              UNION ALL
              SELECT month + 1, ? FROM months WHERE month < 12
            ),
            customer_months AS (
              SELECT 
                c.id AS customer_id,
                c.price,
                m.month,
                m.year,
                c.subscribe
              FROM customers c
              CROSS JOIN months m
              WHERE c.subscribe <= CONCAT(m.year, '-', LPAD(m.month, 2, '0')) 
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
              month,
              year,
              COUNT(*) AS total_customers,
              SUM(has_paid) AS paid_customers,
              COUNT(*) - SUM(has_paid) AS unpaid_customers,
              SUM(CASE WHEN has_paid = 1 THEN price ELSE 0 END) AS total_paid,
              SUM(CASE WHEN has_paid = 0 THEN price ELSE 0 END) AS total_unpaid
            FROM payments_check
            GROUP BY year, month
            ORDER BY year, month
        ";

    $results = DB::select($query, [$year, $year]);
    $results = collect($results)->map(fn($row) => (array) $row)->toArray();
    DB::table('payment_summaries')->truncate();
    DB::table('payment_summaries')->insert($results);
    return true;
  }
}
