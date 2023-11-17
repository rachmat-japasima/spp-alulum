<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Print Data Tunggakan</title>
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
        <h4>Laporan Presentase <br> Tunggakan Uang Sekolah RA / SD / SMP / SMA</h4>
        <h5>PER {{ date('d/m/Y') }}</h5>
    </div>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No.</th>
                <th>Unit</th>
                <th>Jumlah Siswa</th>
                <th>Jumlah Tunggakan US</th>
                <th>Jumlah US Diterima</th>
                <th>Jumlah US Seharusnya</th>
                <th>Persenase Tunggakan Per {{ date('d/m/Y') }}</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>RA</td>
                <td>{{ $siswa->RA }}</td>
                <td>@currency($total->RA)</td>
                <td>@currency($totalUS->RA)</td>
                <td>@currency($total->RA + $totalUS->RA)</td>
                <td>{{ number_format($total->RA / ($total->RA + $totalUS->RA) * 100, 2, '.', ',') }}%</td>
            </tr>
            <tr>
                <td>2</td>
                <td>SD</td>
                <td>{{ $siswa->SD }}</td>
                <td>@currency($total->SD)</td>
                <td>@currency($totalUS->SD)</td>
                <td>@currency($total->SD + $totalUS->SD)</td>
                <td>{{ number_format($total->SD / ($total->SD + $totalUS->SD) * 100, 2, '.', ',') }}%</td>
            </tr>
            <tr>
                <td>3</td>
                <td>SMP</td>
                <td>{{ $siswa->SMP }}</td>
                <td>@currency($total->SMP)</td>
                <td>@currency($totalUS->SMP)</td>
                <td>@currency($total->SMP + $totalUS->SMP)</td>
                <td>{{ number_format($total->SMP / ($total->SMP + $totalUS->SMP) * 100, 2, '.', ',') }}%</td>
            </tr>
            <tr>
                <td>4</td>
                <td>SMA</td>
                <td>{{ $siswa->SMA }}</td>
                <td>@currency($total->SMA)</td>
                <td>@currency($totalUS->SMA)</td>
                <td>@currency($total->SMA + $totalUS->SMA)</td>
                <td>{{ number_format($total->SMA / ($total->SMA + $totalUS->SMA) * 100, 2, '.', ',') }}%</td>
            </tr>
            <tr>
                <td colspan="2">Total</td>
                <td>{{ $siswa->RA + $siswa->SD + $siswa->SMP + $siswa->SMA }}</td>
                <td>@currency($total->RA + $total->SD + $total->SMP + $total->SMA)</td>
                <td>@currency($totalUS->RA + $totalUS->SD + $totalUS->SMP + $totalUS->SMA)</td>
                <td>@currency($total->RA + $totalUS->RA + $total->SD + $totalUS->SD + $total->SMP + $totalUS->SMP + $total->SMA + $totalUS->SMA)</td>
                <td ></td>
            </tr>
        </tbody>
    </table>
    <h5>Rincian Siswa Menunggak</h5>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No.</th>
                <th>NIS/Nama</th>
                <th>Kelas</th>
                <th>Sisa UP</th>
                <th>Uang Sekolah</th>
                <th>Jumlah Bulan</th>
                <th>Jumlah</th>
                <th>Keterangan Bulan US</th>
            </tr>
        </thead>
        <tbody>
            @php
                $no = 1;
            @endphp
            @foreach ($data as $item)
                <tr>
                    <td>{{$no }}</td>
                    <td>{{$item->nis }} / {{ $item->nama }}</td>
                    <td>{{$item->kelas }}</td>
                    <td>@currency($item->jumlah_up)</td>
                    <td>@currency($item->uang_sekolah)</td>
                    <td>{{$item->jumlah_bulan}}</td>
                    <td>@currency($item->total_tunggakan)</td>
                    <td>{{$item->bulan}}</td>
                </tr>
                @php
                    $no++;
                @endphp
            @endforeach
        </tbody>
    </table>
</body>
</html>