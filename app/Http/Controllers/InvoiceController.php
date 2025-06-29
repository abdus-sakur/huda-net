<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function index($id, $year, $month)
    {
        $payment = DB::table('payments')
            ->join('customers', 'payments.customer_id', '=', 'customers.id')
            ->where('customer_id', $id)
            ->where('year', $year)
            ->where('month', $month)
            ->first();
        $invoiceNumber = $this->generateInvoiceNumber();
        $invoiceDate = $payment->created_at ?? now()->format('Y-m-d');
        $company = [
            'name' => 'Huda Net',
            'address' => 'Jl. Merdeka No. 99, Kendal',
            'phone' => '0812-3456-7890',
            'email' => 'admin@hudanet.com',
        ];

        $customer = [
            'name' => $payment->name ?? '',
            'address' => ($payment->sub_district ?? '') . ', ' . ($payment->urban_village ?? ''),
            'phone' => $payment->phone ?? '',
            'bandwidth' => $payment->bandwidth ?? '',
        ];

        $service = [
            'speed' => $payment->bandwidth ?? '',
            'type' => $payment->type ?? '',
            'period' => "{$year}-{$month}-01",
            'price' => $payment->price ?? 0,
            'other_fees' => [
                ['label' => 'Biaya Administrasi', 'amount' => 0],
            ],
        ];

        $total = $service['price'] + array_sum(array_column($service['other_fees'], 'amount'));

        return Pdf::loadView('components.invoice', compact('invoiceNumber', 'invoiceDate', 'company', 'customer', 'service', 'total'))->stream('invoice.pdf');
    }

    function generateInvoiceNumber(): string
    {
        $prefix = 'INV-';
        $date = now()->format('Ym'); // contoh: 20250627
        $random = random_int(1000, 9999);

        return $prefix . $date .  $random;
    }
}
