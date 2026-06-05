<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Resep Medis - SmartClinic</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            line-height: 1.4;
            font-size: 11px;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #0F4C75;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .clinic-name {
            font-size: 18px;
            font-weight: bold;
            color: #0F4C75;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0;
        }
        .clinic-sub {
            font-size: 9px;
            color: #666;
            margin: 2px 0 0 0;
        }
        .title {
            text-align: center;
            font-size: 13px;
            font-weight: bold;
            color: #333;
            margin: 10px 0 15px 0;
            text-decoration: underline;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .info-table td {
            padding: 3px 0;
            vertical-align: top;
        }
        .info-label {
            color: #666;
            width: 90px;
            font-weight: bold;
        }
        .info-value {
            color: #111;
            font-weight: bold;
        }
        .box {
            border: 1px solid #ddd;
            background-color: #fafafa;
            border-radius: 4px;
            padding: 8px 10px;
            margin-bottom: 15px;
        }
        .box-title {
            font-size: 9px;
            color: #777;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 4px;
            border-bottom: 1px solid #eee;
            padding-bottom: 2px;
        }
        .box-content {
            font-size: 11px;
            color: #111;
            font-weight: bold;
            white-space: pre-line;
        }
        .resep-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 25px;
        }
        .resep-table th {
            background-color: #0F4C75;
            color: white;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9px;
            padding: 6px 8px;
            text-align: left;
        }
        .resep-table td {
            border-bottom: 1px solid #eee;
            padding: 6px 8px;
            font-size: 10px;
        }
        .footer-table {
            width: 100%;
            margin-top: 30px;
        }
        .signature-title {
            font-size: 9px;
            color: #666;
            margin-bottom: 45px;
        }
        .signature-name {
            font-weight: bold;
            text-decoration: underline;
            color: #111;
        }
        .signature-spec {
            font-size: 9px;
            color: #666;
        }
    </style>
</head>
<body>

    <!-- Header Klinik -->
    <div class="header">
        <h1 class="clinic-name">SmartClinic</h1>
        <p class="clinic-sub">Sistem Manajemen Klinik Terintegrasi & Kesehatan Digital Terpercaya</p>
    </div>

    <!-- Judul Dokumen -->
    <div class="title">SALINAN RESEP MEDIS</div>

    <!-- Tabel Informasi Kunjungan -->
    <table class="info-table">
        <tr>
            <td class="info-label">Nama Pasien</td>
            <td class="info-value">: {{ $pemeriksaan->booking->pasien->name ?? '-' }}</td>
            <td class="info-label" style="width: 70px;">Tanggal Periksa</td>
            <td class="info-value">: {{ $pemeriksaan->created_at?->format('d M Y') ?? '-' }}</td>
        </tr>
        <tr>
            <td class="info-label">Dokter Pengirim</td>
            <td class="info-value">: dr. {{ $pemeriksaan->dokter->user->name ?? '-' }}</td>
            <td class="info-label" style="width: 70px;">Poliklinik</td>
            <td class="info-value">: {{ $pemeriksaan->booking->jadwal->poli->nama_poli ?? '-' }}</td>
        </tr>
        <tr>
            <td class="info-label">Kode Booking</td>
            <td class="info-value">: {{ $pemeriksaan->booking->kode_booking ?? '-' }}</td>
            <td class="info-label" style="width: 70px;">Status Tiket</td>
            <td class="info-value">: Selesai (Diperiksa)</td>
        </tr>
    </table>

    <!-- Diagnosis -->
    <div class="box">
        <div class="box-title">Diagnosis Medis</div>
        <div class="box-content">{{ $pemeriksaan->diagnosis }}</div>
    </div>

    <!-- Catatan -->
    @if(!empty($pemeriksaan->catatan))
        <div class="box">
            <div class="box-title">Catatan / Instruksi Dokter</div>
            <div class="box-content">{{ $pemeriksaan->catatan }}</div>
        </div>
    @endif

    <!-- Tabel Resep Obat -->
    <div style="font-weight: bold; font-size: 11px; margin-bottom: 5px; color: #0F4C75; border-bottom: 1px solid #0F4C75; padding-bottom: 2px;">
        R/ Resep Obat:
    </div>
    <table class="resep-table">
        <thead>
            <tr>
                <th style="width: 50%;">Nama Obat</th>
                <th style="width: 25%;">Dosis</th>
                <th style="width: 25%;">Aturan Pakai</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pemeriksaan->reseps as $resep)
                <tr>
                    <td style="font-weight: bold; color: #111;">{{ $resep->nama_obat }}</td>
                    <td style="font-weight: bold;">{{ $resep->dosis }}</td>
                    <td style="color: #555;">{{ $resep->aturan_pakai }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" style="text-align: center; color: #888; font-style: italic; padding: 15px;">
                        Tidak ada resep obat tertulis.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Tanda Tangan Dokter -->
    <table class="footer-table">
        <tr>
            <td style="width: 60%;"></td>
            <td style="text-align: center;">
                <p class="signature-title">Medan, {{ $pemeriksaan->created_at?->format('d M Y') }}<br>Dokter Pemeriksa,</p>
                <div style="height: 50px;"></div> <!-- Area Tanda Tangan -->
                <p class="signature-name">dr. {{ $pemeriksaan->dokter->user->name ?? '-' }}</p>
                <p class="signature-spec">NPA: IDI-{{ substr(md5($pemeriksaan->dokter->user->name ?? 'smartclinic'), 0, 8) }}</p>
            </td>
        </tr>
    </table>

</body>
</html>
