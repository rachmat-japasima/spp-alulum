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
</head>
<body>
    <h4>Resume Transaksi Per {{ $filters->date }}</h4>
    <table class="table table-bordered w-75 w-75 w-75">
        <tbody>
            <tr>
                <td colspan="3">Tingkat RA ({{ $jumlah->RA }} Transaksi)</td>
            </tr>
            <tr>
                <td>Total RA</td>
                <td>:</td>
                <td>@currency($total->RA)</td>
            </tr>
            <tr>
                <td>Total Uang Sekolah RA (Manual)</td>
                <td>:</td>
                <td>@currency($RA->manualUS)</td>
            </tr>
            <tr>
                <td>Total Uang Sekolah RA (M-Banking)</td>
                <td>:</td>
                <td>@currency($RA->mbankingUS)</td>
            </tr>
            <tr>
                <td>Total Uang Pembangunan RA</td>
                <td>:</td>
                <td>@currency($RA->UP)</td>
            </tr>
            <tr>
                <td>Total Uang Lainnya RA</td>
                <td>:</td>
                <td>@currency($RA->Lain)</td>
            </tr>
            <tr>
                <td>Total Potongan RA</td>
                <td>:</td>
                <td>@currency($RA->Pot)</td>
            </tr>
            @foreach ($potRA as $item)
                <tr>
                    <td>- Potongan {{ $item->discount->nama }}</td>
                    <td>:</td>
                    <td>@currency($item->total_pot)</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <br>
    <table class="table table-bordered w-75 w-75">
        <tbody>
            <tr>
                <td colspan="3">Tingkat SD ({{ $jumlah->SD }} Transaksi)</td>
            </tr>
            <tr>
                <td>Total SD</td>
                <td>:</td>
                <td>@currency($total->SD)</td>
            </tr>
            <tr>
                <td>Total Uang Sekolah SD (Manual)</td>
                <td>:</td>
                <td>@currency($SD->manualUS)</td>
            </tr>
            <tr>
                <td>Total Uang Sekolah SD (M-Banking)</td>
                <td>:</td>
                <td>@currency($SD->mbankingUS)</td>
            </tr>
            <tr>
                <td>Total Uang Pembangunan SD</td>
                <td>:</td>
                <td>@currency($SD->UP)</td>
            </tr>
            <tr>
                <td>Total Uang Lainnya SD</td>
                <td>:</td>
                <td>@currency($SD->Lain)</td>
            </tr>
            <tr>
                <td>Total Potongan SD</td>
                <td>:</td>
                <td>@currency($SD->Pot)</td>
            </tr>
            @foreach ($potSD as $item)
                <tr>
                    <td>- Potongan {{ $item->discount->nama }}</td>
                    <td>:</td>
                    <td>@currency($item->total_pot)</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <br>
    <table class="table table-bordered w-75 w-75">
        <tbody>
            <tr>
                <td colspan="3">Tingkat SMP ({{ $jumlah->SMP }} Transaksi)</td>
            </tr>
            <tr>
                <td>Total SMP</td>
                <td>:</td>
                <td>@currency($total->SMP)</td>
            </tr>
            <tr>
                <td>Total Uang Sekolah SMP (Manual)</td>
                <td>:</td>
                <td>@currency($SMP->manualUS)</td>
            </tr>
            <tr>
                <td>Total Uang Sekolah SMP (M-Banking)</td>
                <td>:</td>
                <td>@currency($SMP->mbankingUS)</td>
            </tr>
            <tr>
                <td>Total Uang Pembangunan SMP</td>
                <td>:</td>
                <td>@currency($SMP->UP)</td>
            </tr>
            <tr>
                <td>Total Uang Lainnya SMP</td>
                <td>:</td>
                <td>@currency($SMP->Lain)</td>
            </tr>
            <tr>
                <td>Total Potongan SMP</td>
                <td>:</td>
                <td>@currency($SMP->Pot)</td>
            </tr>
            @foreach ($potSMP as $item)
                <tr>
                    <td>- Potongan {{ $item->discount->nama }}</td>
                    <td>:</td>
                    <td>@currency($item->total_pot)</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <br>
    <table class="table table-bordered w-75 w-75">
        <tbody>
            <tr>
                <td colspan="3">Tingkat SMA ({{ $jumlah->SMA }} Transaksi)</td>
            </tr>
            <tr>
                <td>Total SMA</td>
                <td>:</td>
                <td>@currency($total->SMA)</td>
            </tr>
            <tr>
                <td>Total Uang Sekolah SMA (Manual)</td>
                <td>:</td>
                <td>@currency($SMA->manualUS)</td>
            </tr>
            <tr>
                <td>Total Uang Sekolah SMA (M-Banking)</td>
                <td>:</td>
                <td>@currency($SMA->mbankingUS)</td>
            </tr>
            <tr>
                <td>Total Uang Pembangunan SMA</td>
                <td>:</td>
                <td>@currency($SMA->UP)</td>
            </tr>
            <tr>
                <td>Total Uang Lainnya SMA </td>
                <td>:</td>
                <td>@currency($SMA->Lain)</td>
            </tr>
            <tr>
                <td>Total Potongan SMA</td>
                <td>:</td>
                <td>@currency($SMA->Pot)</td>
            </tr>
            @foreach ($potSMA as $item)
                <tr>
                    <td>- Potongan {{ $item->discount->nama }}</td>
                    <td>:</td>
                    <td>@currency($item->total_pot)</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>