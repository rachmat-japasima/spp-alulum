<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Print Invoice</title>
    <style>
        body {
            font-family: 'Calibri','Gill Sans', 'Gill Sans MT', 'Trebuchet MS', sans-serif;
        }
        img {
            pointer-events: none;
            position: absolute;
            width: 100%;
            height: 100%;
            z-index: -1;
        }

        .noBukti{
            position: absolute;
            margin-top: 4cm;
            margin-left: 3.8cm;
        }

        .nis{
            position: absolute;
            margin-top: 4.8cm;
            margin-left: 3.8cm;
        }

        .nama{
            position: absolute;
            margin-top: 5.5cm;
            margin-left: 3.8cm;
        }

        .tingkat{
            position: absolute;
            margin-top: 6.28cm;
            margin-left: 3.8cm;
        }

        .kelas{
            position: absolute;
            margin-top: 7.1cm;
            margin-left: 3.8cm;
        }

        .terbilang{
            position: absolute;
            margin-top: 8.7cm;
            margin-left: 3.8cm;
            text-transform: capitalize;
            width: 8cm;
        }

        .jenis{
            position: absolute;
            margin-top: 10cm;
            margin-left: 3.8cm;
            text-transform: capitalize;
            width: 8cm;
            font-weight: bold;
        }

        .tanggal{
            position: absolute;
            margin-top: 4cm;
            margin-left: 9.4cm;
        }

        .tahun{
            position: absolute;
            margin-top: 4.7cm;
            margin-left: 9.4cm;
        }

        .pembayaran{
            position: absolute;
            margin-top: 4cm;
            margin-left: 15.3cm;
        }

        .detail{
            position: absolute;
            margin-top: 4.7cm;
            margin-left: 13cm;
            width: 7cm;
        }

        br{
            margin-top: 100px;
        }

        .jumlah {
            position: absolute;
            margin-top: 8.7cm;
            margin-left: 15.3cm;
        }

        .tanggal-invoice{
            position: absolute;
            margin-top: 9.5cm;
            margin-left: 15.3cm;
        }

        .admin{
            position: absolute;
            margin-top: 10.9cm;
            margin-left: 12.9cm;
        }

    </style>
</head>
<body style="margin: 0;">
    @php
        if ($student->tingkat == '0'){
            $tingkat = 'SD';
        }elseif ($student->tingkat == '1') {
            $tingkat = 'SMP';
        }elseif ($student->tingkat == '2') {
            $tingkat = 'SMA';
        }elseif ($student->tingkat == 'RA') {
            $tingkat = 'RA';
        }else {
            $tingkat = '';
        }

        if ($student->kelas == 'RA'){
            $kelas = 'ra';
        }else {
            $kelas = 'kelas_'.$student->kelas;
        }

        $uang_sekolah = $schoolFeeAmount[$kelas] * $data->jumlah_bulan;

        function getRomawi($bln){
            switch ($bln){
                    case 1:
                        return "I";
                        break;
                    case 2:
                        return "II";
                        break;
                    case 3:
                        return "III";
                        break;
                    case 4:
                        return "IV";
                        break;
                    case 5:
                        return "V";
                        break;
                    case 6:
                        return "VI";
                        break;
                    case 7:
                        return "VII";
                        break;
                    case 8:
                        return "VIII";
                        break;
                    case 9:
                        return "IX";
                        break;
                    case 10:
                        return "X";
                        break;
                    case 11:
                        return "XI";
                        break;
                    case 12:
                        return "XII";
                        break;
            }
        }

        function penyebut($nilai) {
            $nilai = abs($nilai);
            $huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
            $temp = "";
            if ($nilai < 12) {
                $temp = " ". $huruf[$nilai];
            } else if ($nilai <20) {
                $temp = penyebut($nilai - 10). " belas";
            } else if ($nilai < 100) {
                $temp = penyebut($nilai/10)." puluh". penyebut($nilai % 10);
            } else if ($nilai < 200) {
                $temp = " seratus" . penyebut($nilai - 100);
            } else if ($nilai < 1000) {
                $temp = penyebut($nilai/100) . " ratus" . penyebut($nilai % 100);
            } else if ($nilai < 2000) {
                $temp = " seribu" . penyebut($nilai - 1000);
            } else if ($nilai < 1000000) {
                $temp = penyebut($nilai/1000) . " ribu" . penyebut($nilai % 1000);
            } else if ($nilai < 1000000000) {
                $temp = penyebut($nilai/1000000) . " juta" . penyebut($nilai % 1000000);
            } else if ($nilai < 1000000000000) {
                $temp = penyebut($nilai/1000000000) . " milyar" . penyebut(fmod($nilai,1000000000));
            } else if ($nilai < 1000000000000000) {
                $temp = penyebut($nilai/1000000000000) . " trilyun" . penyebut(fmod($nilai,1000000000000));
            }     
            return $temp;
        }

        function terbilang($nilai) {
            if($nilai<0) {
                $hasil = "minus ". trim(penyebut($nilai));
            } else {
                $hasil = trim(penyebut($nilai));
            }     		
            return $hasil;
        }
    @endphp
    <img src="{{ url('/assets/img/template-invoice-alulum.png') }}" style="width: 21cm; height: 14cm;display:none;" alt="">
    <p class="noBukti">{{ $data->no_bukti }}</p>
    <p class="nis">{{ $data->nis }}</p>
    <p class="nama">{{ $student->nama }}</p>
    <p class="tingkat">{{ $tingkat }}</p>
    <p class="kelas">{{ getRomawi($student->kelas) }} - {{ $student->grup }}</p>
    <p class="terbilang">{{ terbilang($data->total) }}</p>
    @if ($data->jenis == 'M-Banking')
        <p class="jenis">[{{ $data->jenis }}]</p>
    @endif
    <p class="tanggal">{{ \Carbon\Carbon::parse($data->tgl_transaksi)->format('d-M-Y') }}</p>
    <p class="tahun">{{ $data->tahun_ajaran }}</p>
    <p class="pembayaran">{{ $data->bulan }}</p>
    <p class="detail">
        @if ($data->schoolDevFee->count() > 0)
            Pembangunan : @currency($data->jumlah_up)
            <br/>
        @elseif ($data->jumlah_up > 0)
            Pembangunan : @currency($data->jumlah_up)
            <br/>
        @endif
        @if ($data->schoolFee->count() >0)
            Uang Sekolah &nbsp; : @currency($uang_sekolah)
            <br/>
        @elseif ($data->jumlah_us)
            Uang Sekolah : @currency($data->jumlah_us)
            <br/>
        @endif
        @if ($data->discounts->count() > 0)
            @foreach ($data->discounts as $item)
                Potongan {{$item->discount->nama}} ({{$item->discount->besaran}}%)  &nbsp; : @currency($item->total)<br>
            @endforeach
        @elseif ($data->jumlah_potongan > 0)
            Potongan : @currency($data->jumlah_potongan)
            <br/>
        @endif
        @if ($data->otherTransactions->count() > 0)
            @foreach ($data->otherTransactions as $item)
                {{$item->keterangan}} &nbsp; : @currency($item->total)<br>
            @endforeach
        @elseif ($data->jumlah_lainnya > 0)
            Lain-lain : @currency($data->jumlah_lainnya)
        @endif
    </p>
    <p class="jumlah">@currency($data->total)</p>
    <p class="tanggal-invoice">{{ \Carbon\Carbon::now()->format('d F Y') }}</p>
    <p class="admin">{{ $data->user->name }}</p>
</body>
</html>