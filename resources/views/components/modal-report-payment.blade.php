<table class="min-w-full text-sm divide-y divide-gray-200">
    <thead>
        <tr>
            <th class="px-4 py-2 text-left">Nama</th>
            <th class="px-4 py-2 text-left">Tagihan</th>
            <th class="px-4 py-2 text-left">Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($customers as $customer)
            <tr class="border-t">
                <td class="px-4 py-1">{{ $customer->name }}</td>
                <td class="px-4 py-1">{{ $customer->price }}</td>
                <td class="px-4 py-1">
                <td>
                    @if ($type == 'unpaid')
                        <a href="https://api.whatsapp.com/send/?phone={{ $customer->phone }}&text=Salam+Bapak%2FIbu%0A%0AKami+informasikan+Invoice+Internet+anda+telah+terbit+dan+dapat+di+bayarkan%2C+berikut+rinciannya+%3A%0A%0ANama+Pelanggan+%3A+{{ $customer->name }}%0ATagihan+Bulan+%3A+{{ $month_name }}%0APaket%3A+Internet+{{ $customer->bandwidth }}%0ATotal+Tagihan+%3A+{{ $customer->price }}%0AJatuh+Tempo+%3A+25+{{ $month_name }}+{{ $year }}%0A%0ABisa+membayar+dengan+transfer+ke+admin+kami+di%0ABank+Mandiri+a%2Fn+Miftakhul+Huda%0A%0ATerimakasih.&type=custom_url&app_absent=0" target='_blank' style='padding:5px 10px;background-color:darkseagreen;border-radius:15px;font-size:11px;'>WA</a>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
