# Editable Gym Locations Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Mengganti data lokasi statis pada homepage dengan lokasi gym yang dapat dikelola dari admin, sekaligus menghapus card Profil dan lokasi gym dari halaman Tentang CukipGym.

**Architecture:** Lokasi disimpan pada tabel dan model `gym_locations` yang terpisah dari singleton `GymProfile`. Resource Filament mengelola CRUD lokasi melalui modal, sedangkan `HomeController` hanya memetakan lokasi aktif berdasarkan urutan ke struktur marker yang sudah digunakan `gym-map.js`. Enam lokasi contoh dimasukkan oleh migration hanya ketika tabel masih kosong sehingga tampilan saat ini tidak hilang.

**Tech Stack:** PHP 8.4, Laravel 13.32.0, Filament 5.8.2, Livewire, Blade, Leaflet, Tailwind CSS, PHPUnit.

## Global Constraints

- Layout hero, bagian Tentang, kolase, peta, dan footer tidak diubah.
- Card **Profil dan lokasi gym** dihapus dari halaman Tentang CukipGym.
- Lokasi aktif pada database menjadi satu-satunya sumber marker peta.
- Enam lokasi contoh dipertahankan sebagai data awal yang dapat diedit.
- Foto disimpan pada disk `public` di direktori `gym-locations`, maksimal 5 MB.
- Data lama pada `gym_profiles` tidak dihapus.
- Tidak menambah dependency baru.

---

### Task 1: Buat model, tabel, factory, dan data awal lokasi

**Files:**
- Create: `app/Models/GymLocation.php`
- Create: `database/factories/GymLocationFactory.php`
- Create: `database/seeders/GymLocationSeeder.php`
- Create: `database/migrations/2026_11_15_165801_create_gym_locations_table.php`
- Create: `tests/Feature/GymLocationManagementTest.php`

**Interfaces:**
- Produces: model `GymLocation` dengan scope `active()` dan accessor `image_url`, `maps_url`, serta `whatsapp_url`.
- Produces: tabel `gym_locations` yang dikonsumsi resource admin dan `HomeController`.

- [ ] **Step 1: Scaffold model beserta pendukungnya**

Run:

```bash
php artisan make:model GymLocation --migration --factory --seed --no-interaction
php artisan make:test --phpunit GymLocationManagementTest --no-interaction
```

Expected: model, migration, factory, seeder, dan feature test dibuat.

- [ ] **Step 2: Tulis tes model yang gagal**

Isi tes awal:

```php
<?php

namespace Tests\Feature;

use App\Models\GymLocation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GymLocationManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_location_builds_image_maps_and_whatsapp_urls(): void
    {
        $location = GymLocation::factory()->create([
            'latitude' => 0.9274,
            'longitude' => 104.4446,
            'whatsapp' => '081234567890',
            'image_path' => 'gym-locations/pusat-kota.jpg',
        ]);

        $this->assertSame(Storage::disk('public')->url('gym-locations/pusat-kota.jpg'), $location->image_url);
        $this->assertSame('https://www.google.com/maps?q=0.9274,104.4446', $location->maps_url);
        $this->assertSame('https://wa.me/6281234567890', $location->whatsapp_url);
    }

    public function test_only_active_locations_are_returned_in_display_order(): void
    {
        $second = GymLocation::factory()->create(['name' => 'Kedua', 'sort_order' => 20, 'is_active' => true]);
        $first = GymLocation::factory()->create(['name' => 'Pertama', 'sort_order' => 10, 'is_active' => true]);
        GymLocation::factory()->create(['name' => 'Nonaktif', 'sort_order' => 1, 'is_active' => false]);

        $this->assertSame([$first->id, $second->id], GymLocation::active()->ordered()->pluck('id')->all());
    }
}
```

- [ ] **Step 3: Jalankan tes untuk memastikan RED**

```bash
php artisan test --compact tests/Feature/GymLocationManagementTest.php
```

Expected: FAIL karena tabel, scope, dan accessor belum tersedia.

- [ ] **Step 4: Isi migration lokasi**

Schema yang digunakan:

```php
Schema::create('gym_locations', function (Blueprint $table) {
    $table->id();
    $table->string('name', 150);
    $table->string('address', 255);
    $table->decimal('latitude', 10, 7);
    $table->decimal('longitude', 10, 7);
    $table->string('opening_hours', 150);
    $table->string('whatsapp', 20)->nullable();
    $table->string('image_path')->nullable();
    $table->string('image_position', 30)->default('center');
    $table->unsignedInteger('sort_order')->default(0);
    $table->boolean('is_active')->default(true);
    $table->timestamps();

    $table->index(['is_active', 'sort_order']);
});
```

Setelah schema dibuat, masukkan enam baris yang sekarang berada pada `config/gym_locations.php` dengan `DB::table('gym_locations')->insert([...])` jika `DB::table('gym_locations')->doesntExist()`. Simpan jalur aset lama seperti `images/gym-equipment-editorial.webp` pada `image_path`. `down()` hanya menjatuhkan tabel `gym_locations`.

- [ ] **Step 5: Implementasikan model**

Model menggunakan:

```php
class GymLocation extends Model
{
    /** @use HasFactory<\Database\Factories\GymLocationFactory> */
    use HasFactory;

    protected $fillable = [
        'name', 'address', 'latitude', 'longitude', 'opening_hours',
        'whatsapp', 'image_path', 'image_position', 'sort_order', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
```

`getImageUrlAttribute()` mengembalikan `asset($image_path)` jika jalur dimulai `images/`, URL disk public jika berupa upload, dan aset `images/gym-equipment-editorial.webp` jika kosong. Accessor Maps dan WhatsApp mengikuti normalisasi pada `GymProfile`.

- [ ] **Step 6: Isi factory dan seeder**

Factory menghasilkan nama, alamat Tanjungpinang, koordinat valid, jam operasional, `sort_order`, dan status aktif. Seeder menggunakan `firstOrCreate(['name' => ...], [...])` untuk enam lokasi contoh sehingga aman dijalankan berulang.

- [ ] **Step 7: Jalankan tes model**

```bash
vendor/bin/pint --dirty --format agent
php artisan test --compact tests/Feature/GymLocationManagementTest.php
```

Expected: dua tes PASS.

- [ ] **Step 8: Commit struktur data**

```bash
git add app/Models/GymLocation.php database/factories/GymLocationFactory.php database/seeders/GymLocationSeeder.php database/migrations/*_create_gym_locations_table.php tests/Feature/GymLocationManagementTest.php
git commit -m "feat: add editable gym location data"
```

---

### Task 2: Tambahkan resource admin Lokasi Gym

**Files:**
- Create: `app/Filament/Resources/GymLocations/GymLocationResource.php`
- Create: `app/Filament/Resources/GymLocations/Schemas/GymLocationForm.php`
- Create: `app/Filament/Resources/GymLocations/Tables/GymLocationsTable.php`
- Create: `app/Filament/Resources/GymLocations/Pages/ListGymLocations.php`
- Create: `app/Filament/Resources/GymLocations/Pages/CreateGymLocation.php`
- Create: `app/Filament/Resources/GymLocations/Pages/EditGymLocation.php`
- Modify: `resources/css/filament/admin/theme.css`
- Modify: `tests/Feature/AdminDashboardTest.php`
- Modify: `tests/Feature/GymLocationManagementTest.php`

**Interfaces:**
- Consumes: model `GymLocation` dari Task 1 dan `CoordinateParser::parse(string): ?array`.
- Produces: route `/admin/gym-locations` dengan create/edit modal.

- [ ] **Step 1: Scaffold resource**

```bash
php artisan make:filament-resource GymLocation --panel=admin --not-embedded --no-interaction
```

Expected: resource, schema, table, dan pages dibuat mengikuti struktur Filament 5.

- [ ] **Step 2: Tambahkan tes admin yang gagal**

Tambahkan import `ListGymLocations` serta tes:

```php
public function test_admin_can_create_a_gym_location_with_a_photo(): void
{
    Storage::fake('public');
    $this->signInAdmin();

    Livewire::test(ListGymLocations::class)
        ->callAction('create', [
            'name' => 'CukipGym · Dompak',
            'address' => 'Dompak, Tanjungpinang',
            'latitude' => 0.875,
            'longitude' => 104.45,
            'opening_hours' => '06:00 - 22:00',
            'whatsapp' => '6281234567890',
            'image_path' => UploadedFile::fake()->image('dompak.jpg', 1200, 900),
            'image_position' => 'center',
            'sort_order' => 7,
            'is_active' => true,
        ])
        ->assertHasNoFormErrors();

    $location = GymLocation::query()->where('name', 'CukipGym · Dompak')->firstOrFail();
    Storage::disk('public')->assertExists($location->image_path);
}
```

Tambahkan `ListGymLocations` ke provider `modalCreatePages`, `modalEditPages`, `nonClickableAdminTables`, dan `selectableAdminTables`, serta tambahkan route `gym-locations` ke pengujian tema bersama.

- [ ] **Step 3: Jalankan tes untuk memastikan RED**

```bash
php artisan test --compact tests/Feature/GymLocationManagementTest.php tests/Feature/AdminDashboardTest.php --filter=GymLocation
```

Expected: FAIL karena form dan action belum dikonfigurasi.

- [ ] **Step 4: Konfigurasikan form lokasi**

`GymLocationForm::configure()` berisi field berikut:

```php
TextInput::make('name')->label('Nama lokasi')->required()->maxLength(150),
TextInput::make('address')->label('Alamat / wilayah')->required()->maxLength(255),
TextInput::make('map_paste')
    ->label('Tempel lokasi dari Google Maps')
    ->dehydrated(false)
    ->live(onBlur: true)
    ->afterStateUpdated(function (?string $state, Set $set): void {
        $coordinates = filled($state) ? CoordinateParser::parse($state) : null;
        if ($coordinates) {
            $set('latitude', $coordinates['lat']);
            $set('longitude', $coordinates['lng']);
            $set('map_paste', null);
        }
    }),
TextInput::make('latitude')->numeric()->required()->minValue(-90)->maxValue(90),
TextInput::make('longitude')->numeric()->required()->minValue(-180)->maxValue(180),
TextInput::make('opening_hours')->label('Jam operasional')->required()->maxLength(150),
TextInput::make('whatsapp')->label('WhatsApp')->regex('/^[0-9]{8,15}$/'),
FileUpload::make('image_path')->label('Foto lokasi')->image()->disk('public')->directory('gym-locations')->visibility('public')->maxSize(5120),
Select::make('image_position')->label('Fokus foto')->options([
    'center' => 'Tengah',
    'left center' => 'Kiri',
    'right center' => 'Kanan',
    'center top' => 'Atas',
    'center bottom' => 'Bawah',
])->default('center')->required(),
TextInput::make('sort_order')->label('Urutan')->numeric()->required()->minValue(0)->default(0),
Toggle::make('is_active')->label('Tampilkan di peta')->default(true),
```

Gunakan grid dua kolom pada layar besar dan satu kolom pada mobile.

- [ ] **Step 5: Konfigurasikan resource dan table**

Resource memakai label `Lokasi Gym`, grup `Konten website`, icon `OutlinedMapPin`, urutan navigasi `3`, dan action create modal Indonesia seperti resource Fasilitas. Table menampilkan foto, nama, alamat, jam, urutan, dan status aktif; searchable pada nama/alamat; filter status aktif; `recordUrl(null)`; selectable; create/edit/delete modal; label kolom action `Action`.

- [ ] **Step 6: Sertakan resource pada layout admin bersama**

Tambahkan `.fi-resource-gym-locations` ke selector resource list pada `theme.css` agar judul, toolbar, tabel, seleksi, pagination, dan responsivitas sama dengan resource lain.

- [ ] **Step 7: Jalankan tes admin**

```bash
vendor/bin/pint --dirty --format agent
php artisan test --compact tests/Feature/GymLocationManagementTest.php tests/Feature/AdminDashboardTest.php
```

Expected: seluruh tes lokasi dan dashboard PASS.

- [ ] **Step 8: Commit resource admin**

```bash
git add app/Filament/Resources/GymLocations resources/css/filament/admin/theme.css tests/Feature/AdminDashboardTest.php tests/Feature/GymLocationManagementTest.php
git commit -m "feat: add gym location admin management"
```

---

### Task 3: Gunakan lokasi database pada peta homepage

**Files:**
- Modify: `app/Http/Controllers/HomeController.php`
- Modify: `resources/views/home.blade.php`
- Delete: `config/gym_locations.php`
- Modify: `tests/Feature/HomeLocationExplorerTest.php`
- Modify: `tests/Feature/GymLocationManagementTest.php`

**Interfaces:**
- Consumes: `GymLocation::active()->ordered()` dan accessor model Task 1.
- Produces: array marker dengan key `name`, `region`, `lat`, `lng`, `image`, `image_position`, `hours`, `maps_url`, `whatsapp_url`, dan `url`.

- [ ] **Step 1: Ganti tes explorer menjadi berbasis `GymLocation`**

Hapus manipulasi `config(['gym_locations.preview_branches' => []])` dan penggunaan `GymProfileFactory` sebagai lokasi. Buat lokasi melalui `GymLocation::factory()`. Tambahkan tes berikut:

```php
public function test_homepage_only_maps_active_locations_in_display_order(): void
{
    GymLocation::factory()->create(['name' => 'Kedua', 'sort_order' => 20, 'is_active' => true]);
    GymLocation::factory()->create(['name' => 'Pertama', 'sort_order' => 10, 'is_active' => true]);
    GymLocation::factory()->create(['name' => 'Tidak tampil', 'sort_order' => 1, 'is_active' => false]);

    $response = $this->get(route('home'));

    $this->assertSame(['Pertama', 'Kedua'], array_column($response->viewData('marker'), 'name'));
    $response->assertDontSeeText('Tidak tampil');
}
```

- [ ] **Step 2: Jalankan tes untuk memastikan RED**

```bash
php artisan test --compact tests/Feature/HomeLocationExplorerTest.php tests/Feature/GymLocationManagementTest.php
```

Expected: FAIL karena controller masih membaca `GymProfile` dan config.

- [ ] **Step 3: Ubah query `HomeController`**

Ganti blok pembentukan `$marker` dengan:

```php
$marker = GymLocation::query()
    ->active()
    ->ordered()
    ->get()
    ->map(fn (GymLocation $location): array => [
        'name' => $location->name,
        'region' => $location->address,
        'district' => null,
        'rating' => null,
        'reviews' => null,
        'lat' => $location->latitude,
        'lng' => $location->longitude,
        'image' => $location->image_url,
        'image_position' => $location->image_position,
        'hours' => $location->opening_hours,
        'maps_url' => $location->maps_url,
        'whatsapp_url' => $location->whatsapp_url,
        'url' => null,
    ])
    ->all();
```

Hapus seluruh pembacaan `config('gym_locations.preview_branches')`.

- [ ] **Step 4: Bersihkan label preview pada Blade**

Hapus kondisi `Cabang contoh · Tanjungpinang`. Saat marker kosong, gunakan fallback kartu netral:

```php
[
    'name' => 'Lokasi belum tersedia',
    'region' => 'Tambahkan lokasi melalui panel admin.',
    'image' => asset('images/gym-equipment-editorial.webp'),
    'image_position' => 'center',
    'hours' => '-',
    'maps_url' => null,
    'whatsapp_url' => null,
]
```

Pertahankan layout section dan komponen `<x-gym-map>` tanpa perubahan visual.

- [ ] **Step 5: Hapus config preview yang tidak lagi digunakan**

Hapus `config/gym_locations.php`, lalu pastikan `rg "preview_branches|gym_locations" app resources tests config` tidak menemukan referensi config lama selain nama tabel/model baru.

- [ ] **Step 6: Jalankan tes homepage**

```bash
vendor/bin/pint --dirty --format agent
php artisan test --compact tests/Feature/HomeLocationExplorerTest.php tests/Feature/GymLocationManagementTest.php tests/Feature/HomeCommunitySectionsTest.php
```

Expected: seluruh tes PASS.

- [ ] **Step 7: Commit integrasi peta**

```bash
git add app/Http/Controllers/HomeController.php resources/views/home.blade.php tests/Feature/HomeLocationExplorerTest.php tests/Feature/GymLocationManagementTest.php config/gym_locations.php
git commit -m "feat: render homepage map from gym locations"
```

---

### Task 4: Hapus card profil lama dan verifikasi akhir

**Files:**
- Modify: `app/Filament/Pages/ManageGymProfile.php`
- Modify: `resources/css/filament/admin/theme.css`
- Modify: `tests/Feature/AdminDashboardTest.php`
- Modify: `tests/Feature/HomepageContentManagementTest.php`

**Interfaces:**
- Consumes: resource Lokasi Gym dari Task 2.
- Produces: halaman Tentang tanpa field profil/lokasi lama.

- [ ] **Step 1: Tulis tes bahwa card lama tidak tampil**

Perbarui tes halaman Tentang:

```php
$this->actingAs($admin)
    ->get('/admin/manage-gym-profile')
    ->assertOk()
    ->assertDontSee('Profil dan lokasi gym')
    ->assertDontSee('Nama Gym')
    ->assertDontSee('Jam Operasional')
    ->assertDontSee('Tempel Lokasi dari Google Maps')
    ->assertSee('Konten Tentang')
    ->assertSee('Galeri Tentang')
    ->assertSee('Footer')
    ->assertSee('Sponsor &amp; partner', false);
```

- [ ] **Step 2: Jalankan tes untuk memastikan RED**

```bash
php artisan test --compact tests/Feature/AdminDashboardTest.php --filter=about_page
```

Expected: FAIL karena card lama masih dirender.

- [ ] **Step 3: Hapus section dan import yang tidak digunakan**

Hapus `Section::make('Profil dan lokasi gym')` beserta seluruh field nama, jam, telepon, WhatsApp, deskripsi profil, alamat, parser Google Maps, latitude, longitude, dan foto gym dari `ManageGymProfile::form()`. Hapus import `CoordinateParser`, `Set`, serta notification cabang parser yang tidak lagi dipakai. Pertahankan section Konten Tentang, Galeri, Footer, Sponsor, serta method `save()`.

- [ ] **Step 4: Sesuaikan tes penyimpanan Tentang**

Pada `HomepageContentManagementTest`, hapus field profil lama dari `fillForm()`. Tes tetap menyimpan konten Tentang, galeri, footer, dan sponsor.

- [ ] **Step 5: Terapkan migration ke MySQL lokal**

```bash
php artisan migrate --no-interaction
php artisan migrate:status --no-interaction
```

Expected: migration `create_gym_locations_table` berstatus `Ran` dan enam lokasi awal tersedia tanpa mengubah `gym_profiles`.

- [ ] **Step 6: Jalankan formatter, seluruh tes, dan build**

```bash
vendor/bin/pint --dirty --format agent
php artisan test --compact
node node_modules/vite/bin/vite.js build
git diff --check
```

Expected: seluruh tes PASS, build Vite berhasil, dan tidak ada whitespace error.

- [ ] **Step 7: Verifikasi tampilan**

Buka `/admin/gym-locations`, tambah satu lokasi dengan foto, nonaktifkan satu lokasi lama, lalu muat `/`. Pastikan marker baru tampil sesuai urutan, lokasi nonaktif hilang, klik marker mengganti foto/informasi, dan halaman Tentang tidak lagi memiliki card profil lama.

- [ ] **Step 8: Commit penyelesaian**

```bash
git add app/Filament/Pages/ManageGymProfile.php resources/css/filament/admin/theme.css tests/Feature/AdminDashboardTest.php tests/Feature/HomepageContentManagementTest.php
git commit -m "refactor: focus about page on homepage content"
```
