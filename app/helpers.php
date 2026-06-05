<?php

if (!function_exists('tglID')) {
    /**
     * Format tanggal ke Bahasa Indonesia.
     *
     * @param  \Carbon\Carbon|string  $date
     * @param  bool  $withDay  Sertakan nama hari (default: true)
     * @return string  Contoh: "Jumat, 06 Juni 2026" atau "06 Juni 2026"
     */
    function tglID($date, bool $withDay = true): string
    {
        $d = is_string($date) ? \Carbon\Carbon::parse($date) : $date;

        $hariID  = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
        $bulanID = [
            1  => 'Januari',  2  => 'Februari', 3  => 'Maret',
            4  => 'April',    5  => 'Mei',       6  => 'Juni',
            7  => 'Juli',     8  => 'Agustus',   9  => 'September',
            10 => 'Oktober',  11 => 'November',  12 => 'Desember',
        ];

        $hari  = $hariID[$d->dayOfWeek];
        $bulan = $bulanID[$d->month];
        $tgl   = $d->format('d') . ' ' . $bulan . ' ' . $d->format('Y');

        return $withDay ? $hari . ', ' . $tgl : $tgl;
    }
}
