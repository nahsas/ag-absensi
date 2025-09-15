<!DOCTYPE html>
<html>
<head>
    <title>Rekap Absensi</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        h1, h2 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: center; }
        th { background-color: #f2f2f2; }
        .legend { margin-top: 30px; }
        .legend span { display: inline-block; margin-right: 20px; }
        .v { color: green; font-weight: bold; }
        .i { color: blue; font-weight: bold; }
        .k { color: orange; font-weight: bold; }
        .s { color: red; font-weight: bold; }
        .t { color: grey; font-weight: bold; }
        .dl { color: purple; font-weight: bold; }
        .libur { color: grey; font-style: italic; }
    </style>
</head>
<body>

    <h1>Rekap Absensi</h1>
    <h2>Periode: {{ $startDate }} - {{ $endDate }}</h2>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nip</th>
                <th>Nama</th>
                <th>Masuk</th>
                <th>Izin</th>
                <th>Alpha</th>
                <th>Lembur</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $index => $user)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><small>{{ $user['nip'] }}</small></td>
                    <td style="text-align: left;">{{ $user['name'] }}<br></td>
                    <td>{{ $user['total']['hadir'] }}</td>
                    <td>{{ $user['total']['sakit'] }}</td>
                    <td>{{ $user['total']['tanpa_keterangan'] }}</td>
                    <td>{{ $user['total']['lembur'] }} Jam</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>