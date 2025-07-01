<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function index($id, $year, $month)
    {
        $data = DB::table('customers')
            ->select('*', 'customers.id as customer_id')
            ->leftJoin('payments', function ($join) use ($year, $month) {
                $join->on('customers.id', '=', 'payments.customer_id')
                    ->where('payments.year', $year)
                    ->where('payments.month', $month);
            })
            ->where('customers.id', $id)
            ->first();
        $invoiceNumber = $this->generateInvoiceNumber($month, $year, $data->customer_id);
        $invoiceDate = $data->created_at ?? now()->format('Y-m-d');
        $company = [
            'name' => 'Huda Net',
            'address' => 'Jl. Merdeka No. 99, Kendal',
            'phone' => '0812-3456-7890',
            'email' => 'admin@hudanet.com',
            'bank' => [
                ['name' => 'Bank Mandiri', 'account_name' => 'Rina Lailatul Mukarromah', 'account_number' => '1350006472557'],
            ]
        ];

        $customer = [
            'name' => $data->name ?? '',
            'address' => ($data->sub_district ?? '') . ', ' . ($data->urban_village ?? ''),
            'phone' => $data->phone ?? '',
            'bandwidth' => $data->bandwidth ?? '',
        ];
        $service = [
            'speed' => $data->bandwidth ?? '',
            'type' => $data->type ?? '',
            'period' => "{$year}-{$month}-01",
            'price' => $data->price ?? 0,
            'other_fees' => [
                ['label' => 'Biaya Administrasi', 'amount' => 0],
            ],
        ];

        $total = $service['price'] + array_sum(array_column($service['other_fees'], 'amount'));

        return Pdf::loadView('components.invoice', compact('invoiceNumber', 'invoiceDate', 'company', 'customer', 'service', 'total'))->stream('invoice.pdf');
    }


    public static function generateInvoiceNumber($month, $year, $id): string
    {
        $check = DB::table('invoices')
            ->where('year', $year)
            ->where('month', $month)
            ->where('customer_id', $id)
            ->first();
        if ($check) {
            return $check->invoice_number;
        }
        $prefix = 'INV-';
        $now = Carbon::now();

        $yearMonth = $now->format('Ym');
        $lastInvoice = DB::table('invoices')
            ->whereYear('created_at', $now->year)
            ->whereMonth('created_at', $now->month)
            ->count();

        $sequence = str_pad($lastInvoice + 1, 3, '0', STR_PAD_LEFT);
        DB::table('invoices')->insert([
            'invoice_number' => $prefix . $yearMonth . $sequence,
            'year' => $year,
            'month' => $month,
            'customer_id' => $id,
            'created_at' => now(),
        ]);
        return $prefix . $yearMonth . $sequence;
    }
}
