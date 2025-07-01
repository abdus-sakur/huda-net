<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $invoiceNumber }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            font-size: 14px;
        }

        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
        }

        .heading {
            font-size: 24px;
            margin-bottom: 10px;
        }

        .sub-heading {
            color: #666;
            font-size: 12px;
        }

        table {
            width: 100%;
            line-height: inherit;
            text-align: left;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table td {
            padding: 8px;
            vertical-align: top;
        }

        table th {
            background: #eee;
            font-weight: bold;
            border-bottom: 1px solid #ddd;
        }

        .total {
            text-align: right;
            font-weight: bold;
        }
    </style>
</head>

<body style="position: relative;">
    <div class="invoice-box">
        @if ($service['type'])
            <div style="
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                font-size: 60px;
                color: rgba(43, 145, 185, 0.2);
                font-weight: bold;
                z-index: 1;
                pointer-events: none;
                user-select: none;">
                LUNAS
            </div>
        @endif
        <div style="text-align: center; margin-bottom: 20px;">
            <img src="{{ public_path('img/adau.jpg') }}" alt="" height="75px">
        </div>
        <div style="display: flex; justify-content: space-between;">
            <div>
                <div class="heading">INVOICE</div>
                <div class="sub-heading">No: {{ $invoiceNumber }}</div>
                <div class="sub-heading">Tanggal: {{ \Carbon\Carbon::parse($invoiceDate)->format('d F Y') }}</div>
            </div>
            <div style="text-align: right;">
                <strong>{{ $company['name'] }}</strong><br>
                {{ $company['address'] }}<br>
                {{ $company['phone'] }}<br>
                {{ $company['email'] }}
            </div>
        </div>

        <hr>

        <div style="margin-top: 20px;">
            <strong>Tagihan Kepada:</strong><br>
            {{ $customer['name'] }}<br>
            {{ $customer['address'] }}<br>
            {{ $customer['phone'] }}<br>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Deskripsi</th>
                    <th style="text-align: right;">Harga</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Layanan Internet {{ $service['speed'] }} ({{ \Carbon\Carbon::parse($service['period'])->translatedFormat('F Y') }})</td>
                    <td style="text-align: right;">Rp {{ number_format($service['price'], 0, ',', '.') }}</td>
                </tr>
                @if (!empty($service['other_fees']))
                    @foreach ($service['other_fees'] as $fee)
                        <tr>
                            <td>{{ $fee['label'] }}</td>
                            <td style="text-align: right;">Rp {{ number_format($fee['amount'], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
            <tfoot>
                <tr>
                    <td class="total">Total</td>
                    <td class="total">Rp {{ number_format($total, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        <div style="margin-top: 30px;">
            <strong>Metode Pembayaran:</strong><br>
            @if ($service['type'])
                {{ $service['type'] }}<br>
            @else
                @foreach ($company['bank'] as $bank)
                    {{ $bank['name'] }} - {{ $bank['account_number'] }} (a.n. {{ $bank['account_name'] }})<br>
                @endforeach
            @endif
        </div>

        <div style="margin-top: 40px; font-size: 12px; color: #777;">
            Terima kasih telah menggunakan layanan internet kami.
        </div>
    </div>
</body>

</html>
