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
                <th>Total Kehadiran</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $index => $user)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><small>{{ $user['nip'] }}</small></td>
                    <td style="text-align: left;">{{ $user['name'] }}<br></td>
                    <td>
                        Hadir: {{ $user['total']['hadir'] }}v<br>
                        Izin: {{ $user['total']['izin'] }}i<br>
                        Sakit: {{ $user['total']['sakit'] }}s<br>
                        Dinas Luar: {{ $user['total']['dinas_luar'] }}dl<br>
                        Tanpa Keterangan: {{ $user['total']['tanpa_keterangan'] }}t
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="legend">
        <h2>Keterangan:</h2>
        <span><b>v</b> = Hadir</span>
        <span><b>i</b> = Izin</span>
        <span><b>s</b> = Sakit</span>
        <span><b>dl</b> = Dinas Luar</span>
        <span><b>t</b> = Tanpa Keterangan</span>
        <span><b>-</b> = Libur / Tidak Absen</span>
    </div>

</body>
</html>