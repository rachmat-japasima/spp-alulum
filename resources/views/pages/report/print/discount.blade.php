<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Print Data Transaksi Potngan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <script>
        window.print();
    </script>
    <style type="text/css" media="print">
        @page { size: landscape;}
      </style>
</head>
<body>
    <div class="text-center">
        <h4>Laporan Pembayaran Dengan<br> Potongan RA / SD / SMP / SMA</h4>
        <h5>Dari {{ \Carbon\Carbon::parse($filters->startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($filters->endDate)->format('d/m/Y') }}</h5>
    </div>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No.</th>
                <th>Potongan</th>
                <th>Jenis Potongan</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @php
                $no = 1;
            @endphp
            @foreach ($dataTotalPot as $item)
                <tr>
                    <td>{{ $no++ }}</td>
                    <td>{{ $item->discount->nama }}</td>
                    <td>{{ $item->discount->jenis }}</td>
                    <td>@currency($item->total_pot)</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <h5>Rincian Pembayaran Dengan Potongan</h5>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No.</th>
                <th>No. Bukti</th>
                <th>Potongan</th>
                <th>NIS/Nama</th>
                <th>Tingkat</th>
                <th>Kelas</th>
                <th>Tanggal</th>
                <th>Tahun Ajaran</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($dataTable as $key => $itemTable)
                <tr>
                    <td>{{ $itemTable['no'] }}</td>
                    <td>{{$itemTable['no_bukti'] }}</td>
                    <td>{{$itemTable['potongan'] }}</td>
                    <td>{{$itemTable['name'] }}</td>
                    <td>{{$itemTable['tingkat'] }}</td>
                    <td>{{$itemTable['kelas'] }}</td>
                    <td>{{$itemTable['tanggal'] }}</td>
                    <td>{{$itemTable['tahun_ajaran'] }}</td>
                    <td>{{$itemTable['total']}}</td>
                </tr>
                @php
                    $no++;
                @endphp
            @endforeach
        </tbody>
    </table>
</body>
</html>