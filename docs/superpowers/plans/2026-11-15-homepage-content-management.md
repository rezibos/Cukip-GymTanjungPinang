# Homepage Content Management Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Membuat hero, bagian Tentang, lima foto kolase, footer, serta sponsor CukipGym dapat dikelola melalui dua menu admin tanpa mengubah layout publik.

**Architecture:** Kolom konten nullable ditambahkan ke singleton `GymProfile` agar data lama tetap aman dan Blade dapat memakai fallback bawaan. `ManageHomepage` menangani hero, sedangkan `ManageGymProfile` menjadi menu Tentang CukipGym yang menangani profil, Tentang, galeri, footer, dan repeater sponsor. Footer menerima profil melalui view composer yang dibungkus `rescue()` agar halaman database-unavailable tetap dapat dirender.

**Tech Stack:** PHP 8.4, Laravel 12, Filament 5.8.2, Livewire, Blade, Tailwind CSS, PHPUnit.

## Global Constraints

- Layout, animasi, palet hitam-putih-abu-abu, ukuran kolase, dan posisi footer tidak diubah.
- Gambar diunggah ke disk `public`, maksimal 5 MB, dan memiliki fallback aset saat field kosong.
- Menu admin hanya **Halaman Awal** dan **Tentang CukipGym** untuk pengelolaan konten beranda.
- Navigasi footer tetap berasal dari route aplikasi; tahun hak cipta tetap otomatis.
- Sponsor dapat ditambah, dihapus, diurutkan, dinonaktifkan, serta memiliki nama, logo, dan URL opsional.
- Tidak menambah dependency dan tidak membuat page builder umum.

---

### Task 1: Tambahkan struktur data dan helper fallback konten

**Files:**
- Create: `database/migrations/2026_11_15_155850_add_homepage_content_to_gym_profiles_table.php`
- Modify: `app/Models/GymProfile.php`
- Modify: `database/factories/GymProfileFactory.php`
- Test: `tests/Feature/HomepageContentManagementTest.php`

**Interfaces:**
- Produces: `GymProfile::content(string $key): mixed`
- Produces: `GymProfile::contentImageUrl(string $key): string`
- Produces: `GymProfile::footerSponsors(): array<int, array{name: string, tagline: ?string, logo: ?string, url: ?string, is_active: bool}>`

- [ ] **Step 1: Buat migration dengan Artisan**

Run:

```bash
php artisan make:migration add_homepage_content_to_gym_profiles_table --table=gym_profiles --no-interaction
```

Expected: satu migration baru dibuat tanpa mengubah data yang ada.

- [ ] **Step 2: Tulis pengujian fallback model yang gagal terlebih dahulu**

Create `tests/Feature/HomepageContentManagementTest.php` dengan struktur awal:

```php
<?php

namespace Tests\Feature;

use App\Models\GymProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomepageContentManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_uses_homepage_fallbacks_when_optional_content_is_empty(): void
    {
        $profile = GymProfile::factory()->create([
            'hero_title_primary' => null,
            'hero_image' => null,
            'footer_sponsors' => null,
        ]);

        $this->assertSame('Bukan cuma.', $profile->content('hero_title_primary'));
        $this->assertStringEndsWith('/images/gympinang-hero-color.png', $profile->contentImageUrl('hero_image'));
        $this->assertCount(4, $profile->footerSponsors());
    }
}
```

- [ ] **Step 3: Jalankan tes dan pastikan gagal**

Run:

```bash
php artisan test --compact tests/Feature/HomepageContentManagementTest.php
```

Expected: FAIL karena kolom dan method konten belum tersedia.

- [ ] **Step 4: Isi migration dengan kolom nullable**

Gunakan kolom berikut pada `up()`:

```php
Schema::table('gym_profiles', function (Blueprint $table) {
    $table->string('hero_image')->nullable();
    $table->string('hero_eyebrow')->nullable();
    $table->string('hero_title_primary')->nullable();
    $table->string('hero_title_secondary')->nullable();
    $table->text('hero_description')->nullable();
    $table->string('hero_cta_label')->nullable();
    $table->string('hero_about_label')->nullable();
    $table->string('about_eyebrow')->nullable();
    $table->string('about_location_label')->nullable();
    $table->string('about_title_line_one')->nullable();
    $table->string('about_title_line_two')->nullable();
    $table->string('about_title_line_three')->nullable();
    $table->text('about_description_primary')->nullable();
    $table->text('about_description_secondary')->nullable();
    $table->string('about_gallery_image_one')->nullable();
    $table->string('about_gallery_image_two')->nullable();
    $table->string('about_gallery_image_three')->nullable();
    $table->string('about_gallery_image_four')->nullable();
    $table->string('about_gallery_image_five')->nullable();
    $table->string('about_gallery_eyebrow')->nullable();
    $table->text('about_gallery_title')->nullable();
    $table->text('footer_description')->nullable();
    $table->string('footer_location')->nullable();
    $table->json('footer_sponsors')->nullable();
});
```

Pada `down()`, panggil `dropColumn()` untuk seluruh nama kolom di atas.

- [ ] **Step 5: Implementasikan fillable, casts, defaults, dan helper model**

Tambahkan seluruh nama kolom ke `$fillable`, cast `footer_sponsors` menjadi `array`, lalu tambahkan konstanta dan method berikut dengan nilai bawaan yang sama seperti tampilan saat ini:

```php
private const CONTENT_DEFAULTS = [
    'hero_eyebrow' => 'Gym lokal Tanjungpinang',
    'hero_title_primary' => 'Bukan cuma.',
    'hero_title_secondary' => 'Latihan.',
    'hero_description' => 'Ruang untuk membangun tubuh, disiplin, dan versi terbaik dirimu. Mulai hari ini satu repetisi, satu progres.',
    'hero_cta_label' => 'Mulai sekarang',
    'hero_about_label' => 'Jelajahi GymPinang',
    'about_eyebrow' => 'Tentang CukipGym',
    'about_location_label' => 'Tanjungpinang, Indonesia',
    'about_title_line_one' => 'Tempatmu',
    'about_title_line_two' => 'menjadi',
    'about_title_line_three' => 'lebih kuat.',
    'about_description_primary' => 'Dari latihan pertamamu sampai rutinitas yang kamu banggakan. CukipGym adalah ruang untuk bergerak, membangun kekuatan, dan terus berkembang.',
    'about_description_secondary' => 'Temukan peralatan yang kamu butuhkan dan pilihan membership yang sesuai. Mulai dari kemampuanmu hari ini, lalu jalani progresmu satu sesi demi satu sesi.',
    'about_gallery_eyebrow' => 'Ruang untuk progresmu',
    'about_gallery_title' => "Latihanmu.\nRitmemu.",
    'footer_description' => 'Tempat latihan, kelas kebugaran, dan komunitas untuk membantu kamu bergerak lebih konsisten.',
    'footer_location' => 'Gym lokal Tanjungpinang',
];

private const IMAGE_DEFAULTS = [
    'hero_image' => 'images/gympinang-hero-color.png',
    'about_gallery_image_one' => 'images/gym-equipment-editorial.webp',
    'about_gallery_image_two' => 'images/gym-cable-row-editorial.webp',
    'about_gallery_image_three' => 'images/gym-kettlebell-editorial.webp',
    'about_gallery_image_four' => 'images/gympinang-hero-color.png',
    'about_gallery_image_five' => 'images/gym-equipment-editorial.webp',
];

public function content(string $key): mixed
{
    $value = $this->getAttribute($key);

    return filled($value) ? $value : (self::CONTENT_DEFAULTS[$key] ?? null);
}

public function contentImageUrl(string $key): string
{
    $path = $this->getAttribute($key);

    return filled($path)
        ? Storage::disk('public')->url($path)
        : asset(self::IMAGE_DEFAULTS[$key]);
}

public function footerSponsors(): array
{
    $sponsors = $this->footer_sponsors;

    if (! is_array($sponsors)) {
        $sponsors = [
            ['name' => 'FORMA.', 'tagline' => null, 'logo' => null, 'url' => null, 'is_active' => true],
            ['name' => 'PINANG', 'tagline' => 'ACTIVE CLUB', 'logo' => null, 'url' => null, 'is_active' => true],
            ['name' => 'replab', 'tagline' => null, 'logo' => null, 'url' => null, 'is_active' => true],
            ['name' => 'NUSA', 'tagline' => 'HYDRATE', 'logo' => null, 'url' => null, 'is_active' => true],
        ];
    }

    return array_values(array_filter($sponsors, fn (array $sponsor): bool => ($sponsor['is_active'] ?? true) && filled($sponsor['name'] ?? null)));
}
```

- [ ] **Step 6: Tambahkan atribut nullable pada factory**

Tambahkan semua field baru ke `GymProfileFactory::definition()` dengan nilai `null`. Hal ini membuat fixture eksplisit dan stabil.

- [ ] **Step 7: Jalankan migration dan tes model**

Run:

```bash
php artisan migrate --no-interaction
php artisan test --compact tests/Feature/HomepageContentManagementTest.php
```

Expected: migration berhasil dan tes PASS.

- [ ] **Step 8: Format dan commit task pertama**

```bash
vendor/bin/pint --dirty --format agent
git add app/Models/GymProfile.php database/factories/GymProfileFactory.php database/migrations/*_add_homepage_content_to_gym_profiles_table.php tests/Feature/HomepageContentManagementTest.php
git commit -m "feat: add editable homepage content fields"
```

---

### Task 2: Buat menu admin Halaman Awal

**Files:**
- Create: `app/Filament/Pages/ManageHomepage.php`
- Create: `resources/views/filament/pages/manage-homepage.blade.php`
- Modify: `resources/css/filament/admin/theme.css`
- Modify: `tests/Feature/HomepageContentManagementTest.php`

**Interfaces:**
- Consumes: `GymProfile::current()` dan field `hero_*` dari Task 1.
- Produces: route Filament `/admin/manage-homepage` dan method Livewire `save(): void`.

- [ ] **Step 1: Scaffold halaman Filament**

Run:

```bash
php artisan make:filament-page ManageHomepage --panel=admin --no-interaction
```

Expected: class page dan view Blade dibuat.

- [ ] **Step 2: Tambahkan tes akses dan penyimpanan hero yang gagal**

Tambahkan import `App\Filament\Pages\ManageHomepage`, `App\Models\User`, `Illuminate\Http\UploadedFile`, `Illuminate\Support\Facades\Storage`, dan `Livewire\Livewire`, lalu tambahkan:

```php
public function test_admin_can_update_homepage_hero_content(): void
{
    Storage::fake('public');
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    Livewire::test(ManageHomepage::class)
        ->fillForm([
            'hero_eyebrow' => 'Gym lokal pilihan',
            'hero_title_primary' => 'Bergerak lebih.',
            'hero_title_secondary' => 'Kuat setiap hari.',
            'hero_description' => 'Deskripsi hero terbaru.',
            'hero_cta_label' => 'Mulai latihan',
            'hero_about_label' => 'Kenali CukipGym',
            'hero_image' => UploadedFile::fake()->image('hero.jpg', 1600, 1000),
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $profile = GymProfile::current()->refresh();
    $this->assertSame('Bergerak lebih.', $profile->hero_title_primary);
    Storage::disk('public')->assertExists($profile->hero_image);
}

public function test_homepage_management_is_only_available_to_admins(): void
{
    $this->get('/admin/manage-homepage')->assertRedirect('/admin/login');

    $user = User::factory()->create(['role' => 'user']);
    $this->actingAs($user)->get('/admin/manage-homepage')->assertForbidden();
}
```

- [ ] **Step 3: Jalankan tes dan pastikan gagal**

```bash
php artisan test --compact tests/Feature/HomepageContentManagementTest.php
```

Expected: FAIL karena schema halaman belum dibuat.

- [ ] **Step 4: Implementasikan `ManageHomepage`**

Gunakan properti navigasi berikut:

```php
protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-home';
protected static ?string $navigationLabel = 'Halaman Awal';
protected static ?string $title = 'Halaman Awal';
protected static string|\UnitEnum|null $navigationGroup = 'Konten website';
protected static ?int $navigationSort = 4;
```

Form menggunakan `Section::make('Hero beranda')`, satu `FileUpload::make('hero_image')` dengan `disk('public')`, `directory('homepage/hero')`, `image()`, `maxSize(5120)`, dan input teks untuk seluruh field `hero_*`. `mount()` mengisi form dari `GymProfile::current()->toArray()`. `save()` memperbarui hanya field hero dan mengirim notifikasi `Halaman awal berhasil disimpan`.

- [ ] **Step 5: Gunakan view dengan form dan tombol simpan**

Isi view:

```blade
<x-filament-panels::page>
    <form wire:submit="save" class="homepage-content-form">
        {{ $this->form }}

        <div class="mt-6 flex justify-end">
            <x-filament::button type="submit">Simpan perubahan</x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
```

- [ ] **Step 6: Tambahkan styling konsisten dengan kartu Profil Gym**

Tambahkan selector `.homepage-content-page` dan `.homepage-content-form` pada `theme.css` dengan warna putih, border `#dedede`, radius yang sama dengan halaman admin lain, serta jarak responsif. Jangan mengubah selector halaman tabel lain.

- [ ] **Step 7: Jalankan tes halaman admin**

```bash
php artisan test --compact tests/Feature/HomepageContentManagementTest.php tests/Feature/AdminDashboardTest.php
```

Expected: tes halaman baru PASS dan tes dashboard lama tetap PASS.

- [ ] **Step 8: Format dan commit task kedua**

```bash
vendor/bin/pint --dirty --format agent
git add app/Filament/Pages/ManageHomepage.php resources/views/filament/pages/manage-homepage.blade.php resources/css/filament/admin/theme.css tests/Feature/HomepageContentManagementTest.php
git commit -m "feat: add homepage hero management page"
```

---

### Task 3: Ubah Profil Gym menjadi menu Tentang CukipGym dengan galeri dan sponsor

**Files:**
- Modify: `app/Filament/Pages/ManageGymProfile.php`
- Modify: `resources/views/filament/pages/manage-gym-profile.blade.php`
- Modify: `resources/css/filament/admin/theme.css`
- Modify: `tests/Feature/AdminDashboardTest.php`
- Modify: `tests/Feature/HomepageContentManagementTest.php`

**Interfaces:**
- Consumes: field `about_*`, `footer_*`, dan profil lama dari Task 1.
- Produces: form Tentang CukipGym dan array JSON `footer_sponsors` dalam urutan repeater.

- [ ] **Step 1: Tambahkan tes form Tentang dan sponsor yang gagal**

Tambahkan tes berikut:

```php
public function test_admin_can_update_about_gallery_footer_and_sponsors(): void
{
    Storage::fake('public');
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    Livewire::test(\App\Filament\Pages\ManageGymProfile::class)
        ->fillForm([
            'name' => 'CukipGym',
            'description' => 'Profil gym.',
            'address' => 'Tanjungpinang',
            'opening_hours' => '06:00 - 22:00',
            'about_eyebrow' => 'Tentang CukipGym',
            'about_location_label' => 'Tanjungpinang, Indonesia',
            'about_title_line_one' => 'Tempatmu',
            'about_title_line_two' => 'menjadi',
            'about_title_line_three' => 'lebih hebat.',
            'about_description_primary' => 'Paragraf pertama.',
            'about_description_secondary' => 'Paragraf kedua.',
            'about_gallery_eyebrow' => 'Ruang untuk progresmu',
            'about_gallery_title' => "Latihanmu.\nRitmemu.",
            'about_gallery_image_one' => UploadedFile::fake()->image('gallery-1.jpg'),
            'footer_description' => 'Footer terbaru.',
            'footer_location' => 'Gym lokal Tanjungpinang',
            'footer_sponsors' => [[
                'name' => 'Sponsor Utama',
                'tagline' => 'Partner latihan',
                'logo' => UploadedFile::fake()->image('sponsor.png'),
                'url' => 'https://example.com',
                'is_active' => true,
            ]],
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $profile = GymProfile::current()->refresh();
    $this->assertSame('lebih hebat.', $profile->about_title_line_three);
    $this->assertSame('Sponsor Utama', $profile->footer_sponsors[0]['name']);
    Storage::disk('public')->assertExists($profile->about_gallery_image_one);
    Storage::disk('public')->assertExists($profile->footer_sponsors[0]['logo']);
}
```

- [ ] **Step 2: Jalankan tes dan pastikan gagal**

```bash
php artisan test --compact tests/Feature/HomepageContentManagementTest.php
```

Expected: FAIL karena field belum ada di schema form.

- [ ] **Step 3: Ubah identitas navigasi halaman**

Set `navigationLabel` dan `title` menjadi `Tentang CukipGym`, `navigationIcon` menjadi `heroicon-o-information-circle`, dan `navigationSort` menjadi `5`. Pertahankan URL `/admin/manage-gym-profile` agar tautan lama tetap kompatibel.

- [ ] **Step 4: Bagi schema menjadi section yang fokus**

Pertahankan seluruh field profil/lokasi saat ini, lalu tambahkan:

- `Section::make('Konten Tentang')` untuk label, tiga baris judul, dan dua deskripsi;
- `Section::make('Galeri Tentang')` untuk lima `FileUpload` terpisah pada direktori `homepage/about`, semuanya `image()->maxSize(5120)`;
- `Section::make('Footer')` untuk deskripsi dan lokasi;
- `Section::make('Sponsor & partner')` dengan `Repeater::make('footer_sponsors')`.

Schema repeater menggunakan:

```php
Repeater::make('footer_sponsors')
    ->label('Daftar sponsor')
    ->schema([
        TextInput::make('name')->label('Nama sponsor')->required()->maxLength(100),
        TextInput::make('tagline')->label('Tagline')->maxLength(100),
        FileUpload::make('logo')->label('Logo')->image()->disk('public')->directory('homepage/sponsors')->maxSize(5120),
        TextInput::make('url')->label('Tautan')->url()->maxLength(255),
        Toggle::make('is_active')->label('Tampilkan')->default(true),
    ])
    ->itemLabel(fn (array $state): ?string => $state['name'] ?? 'Sponsor baru')
    ->reorderable()
    ->collapsible()
    ->addActionLabel('Tambah sponsor')
    ->columnSpanFull();
```

- [ ] **Step 5: Batasi data yang disimpan dan pertahankan parser koordinat**

`save()` tetap memanggil `$this->form->getState()` dan memperbarui `GymProfile::current()`. Field `map_paste` tetap `dehydrated(false)`. Notifikasi berubah menjadi `Tentang CukipGym berhasil disimpan`.

- [ ] **Step 6: Perbarui view dan CSS**

Pertahankan class `gym-profile-form`, ubah heading/breadcrumb menjadi `Tentang CukipGym`, dan gunakan kartu/section putih yang konsisten. Pastikan form satu kolom pada mobile dan section dua kolom hanya pada layar besar.

- [ ] **Step 7: Perbarui ekspektasi tes dashboard**

Pada `AdminDashboardTest`, ganti ekspektasi judul `Profil gym` dengan `Tentang CukipGym`, tambahkan `/admin/manage-homepage` pada data route bertema, dan assert sidebar menampilkan tepat `Halaman Awal` serta `Tentang CukipGym`.

- [ ] **Step 8: Jalankan tes admin**

```bash
php artisan test --compact tests/Feature/HomepageContentManagementTest.php tests/Feature/AdminDashboardTest.php
```

Expected: seluruh tes PASS.

- [ ] **Step 9: Format dan commit task ketiga**

```bash
vendor/bin/pint --dirty --format agent
git add app/Filament/Pages/ManageGymProfile.php resources/views/filament/pages/manage-gym-profile.blade.php resources/css/filament/admin/theme.css tests/Feature/AdminDashboardTest.php tests/Feature/HomepageContentManagementTest.php
git commit -m "feat: manage about gallery and footer content"
```

---

### Task 4: Hubungkan data admin ke hero, Tentang, galeri, dan footer publik

**Files:**
- Modify: `resources/views/home.blade.php`
- Modify: `resources/views/components/footer.blade.php`
- Modify: `app/Providers/AppServiceProvider.php`
- Modify: `tests/Feature/HomepageContentManagementTest.php`
- Verify: `tests/Feature/DatabaseUnavailablePageTest.php`
- Verify: `tests/Feature/HomeCommunitySectionsTest.php`

**Interfaces:**
- Consumes: `GymProfile::content()`, `contentImageUrl()`, dan `footerSponsors()`.
- Produces: view variable `$footerProfile` berupa `?GymProfile` untuk `components.footer`.

- [ ] **Step 1: Tambahkan tes render konten tersimpan yang gagal**

Tambahkan:

```php
public function test_homepage_and_footer_render_saved_content(): void
{
    $profile = GymProfile::factory()->create([
        'hero_title_primary' => 'Gerak bersama.',
        'hero_title_secondary' => 'Tumbuh bersama.',
        'about_title_line_three' => 'lebih konsisten.',
        'footer_description' => 'Deskripsi footer khusus.',
        'footer_sponsors' => [
            ['name' => 'Sponsor Aktif', 'tagline' => null, 'logo' => null, 'url' => null, 'is_active' => true],
            ['name' => 'Sponsor Nonaktif', 'tagline' => null, 'logo' => null, 'url' => null, 'is_active' => false],
        ],
    ]);

    $this->get(route('home'))
        ->assertOk()
        ->assertSeeText('Gerak bersama.')
        ->assertSeeText('Tumbuh bersama.')
        ->assertSeeText('lebih konsisten.')
        ->assertSeeText('Deskripsi footer khusus.')
        ->assertSeeText('Sponsor Aktif')
        ->assertDontSeeText('Sponsor Nonaktif');
}
```

- [ ] **Step 2: Jalankan tes dan pastikan gagal**

```bash
php artisan test --compact tests/Feature/HomepageContentManagementTest.php
```

Expected: FAIL karena Blade masih memakai teks statis.

- [ ] **Step 3: Ganti konten hero dan Tentang dengan helper model**

Pada hero gunakan `contentImageUrl('hero_image')` untuk kedua layer, tambahkan `grayscale` pada layer abu-abu, dan gunakan `content()` untuk seluruh teks. Pada Tentang, gunakan tiga field judul, dua deskripsi, label, lokasi, caption, dan lima URL galeri. Pertahankan seluruh class animasi dan layout saat ini.

Setiap `<img>` unggahan galeri dan sponsor diberi penanganan `onerror` yang mengganti sumbernya ke aset bawaan terkait, lalu menghapus handler agar tidak membentuk loop jika aset fallback juga gagal.

- [ ] **Step 4: Sediakan data footer secara aman**

Pada `AppServiceProvider::boot()` tambahkan composer:

```php
View::composer('components.footer', function (ViewContract $view): void {
    $profile = rescue(
        fn (): GymProfile => GymProfile::current(),
        rescue: null,
        report: false,
    );

    $view->with('footerProfile', $profile);
});
```

Gunakan alias import berbeda untuk `Illuminate\Contracts\View\View` agar tidak bentrok dengan facade `Illuminate\Support\Facades\View`.

- [ ] **Step 5: Render footer dinamis dengan fallback aman**

Di awal footer tentukan:

```blade
@php
    $footerName = $footerProfile?->name ?: 'CukipGym';
    $footerDescription = $footerProfile?->content('footer_description') ?: 'Tempat latihan, kelas kebugaran, dan komunitas untuk membantu kamu bergerak lebih konsisten.';
    $footerLocation = $footerProfile?->content('footer_location') ?: 'Gym lokal Tanjungpinang';
    $footerSponsors = $footerProfile?->footerSponsors() ?? [];
@endphp
```

Render sponsor dengan `@forelse`. Jika `logo` terisi, tampilkan `Storage::disk('public')->url($sponsor['logo'])` pada `<img>` ber-alt nama sponsor. Jika tidak, tampilkan nama dan tagline sebagai teks. Bungkus dengan `<a>` hanya jika URL terisi. Gunakan grid responsif yang menyesuaikan jumlah item dan jangan menampilkan sponsor nonaktif.

- [ ] **Step 6: Jalankan tes render dan halaman database unavailable**

```bash
php artisan test --compact tests/Feature/HomepageContentManagementTest.php tests/Feature/HomeCommunitySectionsTest.php tests/Feature/DatabaseUnavailablePageTest.php
```

Expected: seluruh tes PASS; footer statis fallback tetap muncul ketika profil tidak tersedia.

- [ ] **Step 7: Format dan commit task keempat**

```bash
vendor/bin/pint --dirty --format agent
git add app/Providers/AppServiceProvider.php resources/views/home.blade.php resources/views/components/footer.blade.php tests/Feature/HomepageContentManagementTest.php
git commit -m "feat: render editable homepage and footer content"
```

---

### Task 5: Verifikasi akhir dan build frontend

**Files:**
- Verify: seluruh file yang disentuh pada Task 1-4.

**Interfaces:**
- Consumes: implementasi lengkap Task 1-4.
- Produces: migrasi, halaman admin, dan beranda yang siap dijalankan.

- [ ] **Step 1: Jalankan formatter PHP terakhir**

```bash
vendor/bin/pint --dirty --format agent
```

Expected: tidak ada error dan file PHP mengikuti gaya proyek.

- [ ] **Step 2: Jalankan pengujian terfokus**

```bash
php artisan test --compact tests/Feature/HomepageContentManagementTest.php tests/Feature/AdminDashboardTest.php tests/Feature/HomeCommunitySectionsTest.php tests/Feature/DatabaseUnavailablePageTest.php tests/Feature/ExampleTest.php
```

Expected: seluruh tes PASS.

- [ ] **Step 3: Build aset frontend**

```bash
node node_modules/vite/bin/vite.js build
```

Expected: Vite selesai tanpa error dan memperbarui manifest build.

- [ ] **Step 4: Periksa status dan diff terbatas**

```bash
git status --short
git diff --check
git diff --stat
```

Expected: tidak ada whitespace error; perubahan hanya mencakup konten beranda serta perubahan admin lain yang memang sudah ada sebelumnya tetap tidak tersentuh.

- [ ] **Step 5: Verifikasi manual**

Buka `/admin/manage-homepage`, simpan hero percobaan, lalu buka `/admin/manage-gym-profile`, ganti satu foto galeri dan satu sponsor. Muat `/` dan pastikan hero, kolase, serta footer berubah tanpa menggeser layout pada desktop dan mobile.
