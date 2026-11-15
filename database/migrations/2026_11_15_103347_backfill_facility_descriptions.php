<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $descriptions = [
            'ac' => 'Area berpendingin untuk menjaga kenyamanan selama kamu berlatih.',
            'air minum' => 'Lengkapi kebutuhan minummu di sela aktivitas dan setelah latihan.',
            'barbell' => 'Beban bebas untuk menyusun variasi gerakan dan latihan kekuatan.',
            'bench press' => 'Bangku latihan untuk mendukung gerakan dorong dan latihan beban.',
            'dumbbell' => 'Peralatan beban bebas yang fleksibel untuk berbagai kelompok otot.',
            'locker' => 'Tempat menyimpan barang agar kamu bisa lebih fokus selama latihan.',
            'parkir' => 'Area untuk memarkir kendaraan sebelum memulai sesi latihanmu.',
            'ruang ganti' => 'Ruang untuk berganti pakaian sebelum dan setelah beraktivitas.',
            'toilet' => 'Fasilitas sanitasi yang melengkapi kebutuhan selama berada di gym.',
            'treadmill' => 'Atur kecepatan langkahmu untuk sesi berjalan atau berlari.',
        ];

        DB::table('facilities')
            ->select(['id', 'name'])
            ->where(function (Builder $query): void {
                $query->whereNull('description')->orWhere('description', '');
            })
            ->orderBy('id')
            ->each(function (object $facility) use ($descriptions): void {
                $description = $descriptions[mb_strtolower(trim($facility->name))]
                    ?? 'Fasilitas untuk melengkapi kebutuhan selama sesi latihanmu.';

                DB::table('facilities')->where('id', $facility->id)->update(['description' => $description]);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        /**
         * Keterangan dipertahankan karena migrasi tidak dapat membedakan data
         * hasil backfill dari data yang kemudian disunting oleh admin.
         */
    }
};
