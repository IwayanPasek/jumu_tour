<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Destination;
use App\Models\Region;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminDestinationCrudTest extends TestCase
{
    protected ?User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['email' => 'admin-test@balitourservice.local'],
            [
                'name' => 'Admin Test Cluster 7',
                'password' => bcrypt('AdminPassword123!'),
                'is_admin' => true,
                'is_active' => true,
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Guest Protection Tests
    |--------------------------------------------------------------------------
    */

    public function test_guest_cannot_access_admin_destinations_index(): void
    {
        $response = $this->get(route('admin.destinations.index'));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_guest_cannot_access_admin_destinations_create(): void
    {
        $response = $this->get(route('admin.destinations.create'));
        $response->assertRedirect(route('admin.login'));
    }

    /*
    |--------------------------------------------------------------------------
    | Destination CRUD Tests
    |--------------------------------------------------------------------------
    */

    public function test_admin_can_view_destinations_index_with_search_and_filter(): void
    {
        $unique = uniqid();
        $region = Region::create([
            'name' => "Gianyar Test Area {$unique}",
            'slug' => "gianyar-test-area-{$unique}",
            'is_active' => true,
        ]);

        $destination = Destination::create([
            'region_id' => $region->id,
            'name' => "Tegalalang Rice Terrace {$unique}",
            'slug' => "tegalalang-rice-terrace-{$unique}",
            'latitude' => -8.4333,
            'longitude' => 115.2833,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.destinations.index', [
            'q' => "Tegalalang Rice Terrace {$unique}",
            'region_id' => $region->id,
        ]));

        $response->assertOk();
        $response->assertSee("Tegalalang Rice Terrace {$unique}");

        $destination->delete();
        $region->delete();
    }

    public function test_admin_can_create_destination_with_image_and_automatic_slug(): void
    {
        Storage::fake('public');

        $unique = uniqid();
        $region = Region::create([
            'name' => "Tabanan Test Area {$unique}",
            'slug' => "tabanan-test-area-{$unique}",
            'is_active' => true,
        ]);

        $category = Category::create([
            'name' => "Pura Test Category {$unique}",
            'slug' => "pura-test-category-{$unique}",
            'is_active' => true,
        ]);

        $image = UploadedFile::fake()->image('tanah_lot.jpg', 800, 600);

        $response = $this->actingAs($this->admin)->post(route('admin.destinations.store'), [
            'region_id' => $region->id,
            'category_id' => $category->id,
            'name' => "Tanah Lot Sunset Test {$unique}",
            'slug' => '',
            'latitude' => -8.6212,
            'longitude' => 115.0868,
            'address' => 'Beraban, Kediri, Tabanan',
            'description' => 'Pura ikonik di atas batu karang.',
            'image' => $image,
            'is_active' => '1',
            'display_order' => 5,
        ]);

        $response->assertRedirect(route('admin.destinations.index'));
        $response->assertSessionHas('success');

        $destination = Destination::where('name', "Tanah Lot Sunset Test {$unique}")->first();
        $this->assertNotNull($destination);
        $this->assertNotNull($destination->image_path);

        Storage::disk('public')->assertExists($destination->image_path);

        $destination->delete();
        $category->delete();
        $region->delete();
    }

    public function test_admin_can_update_destination_and_replace_image(): void
    {
        Storage::fake('public');

        $unique = uniqid();
        $region = Region::create([
            'name' => "Badung Test Area {$unique}",
            'slug' => "badung-test-area-{$unique}",
            'is_active' => true,
        ]);

        $oldImage = UploadedFile::fake()->image('old.jpg');
        $oldPath = $oldImage->store('destinations', 'public');

        $destination = Destination::create([
            'region_id' => $region->id,
            'name' => "Pantai Kuta Lama {$unique}",
            'slug' => "pantai-kuta-lama-{$unique}",
            'latitude' => -8.7180,
            'longitude' => 115.1690,
            'image_path' => $oldPath,
            'is_active' => true,
        ]);

        Storage::disk('public')->assertExists($oldPath);

        $newImage = UploadedFile::fake()->image('new.jpg');

        $response = $this->actingAs($this->admin)->put(route('admin.destinations.update', $destination), [
            'region_id' => $region->id,
            'name' => "Pantai Kuta Baru {$unique}",
            'slug' => "pantai-kuta-baru-{$unique}",
            'latitude' => -8.7185,
            'longitude' => 115.1695,
            'image' => $newImage,
            'is_active' => '1',
            'display_order' => 1,
        ]);

        $response->assertRedirect(route('admin.destinations.index'));

        $destination->refresh();
        $this->assertEquals("Pantai Kuta Baru {$unique}", $destination->name);
        $this->assertNotEquals($oldPath, $destination->image_path);

        // Gambar lama terhapus dan gambar baru tersimpan
        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($destination->image_path);

        $destination->delete();
        $region->delete();
    }

    public function test_admin_can_delete_destination_image_only(): void
    {
        Storage::fake('public');

        $unique = uniqid();
        $region = Region::create([
            'name' => "Klungkung Area Test {$unique}",
            'slug' => "klungkung-area-test-{$unique}",
            'is_active' => true,
        ]);

        $image = UploadedFile::fake()->image('kelingking.jpg');
        $imagePath = $image->store('destinations', 'public');

        $destination = Destination::create([
            'region_id' => $region->id,
            'name' => "Kelingking Beach Test {$unique}",
            'slug' => "kelingking-beach-test-{$unique}",
            'latitude' => -8.7500,
            'longitude' => 115.4700,
            'image_path' => $imagePath,
            'is_active' => true,
        ]);

        Storage::disk('public')->assertExists($imagePath);

        $response = $this->actingAs($this->admin)->delete(route('admin.destinations.image.destroy', $destination));
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $destination->refresh();
        $this->assertNull($destination->image_path);
        Storage::disk('public')->assertMissing($imagePath);

        $destination->delete();
        $region->delete();
    }

    public function test_admin_can_toggle_destination_status(): void
    {
        $unique = uniqid();
        $region = Region::create([
            'name' => "Buleleng Area Test {$unique}",
            'slug' => "buleleng-area-test-{$unique}",
            'is_active' => true,
        ]);

        $destination = Destination::create([
            'region_id' => $region->id,
            'name' => "Air Terjun Gitgit Test {$unique}",
            'slug' => "air-terjun-gitgit-test-{$unique}",
            'latitude' => -8.2000,
            'longitude' => 115.1333,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->patch(route('admin.destinations.status', $destination));
        $response->assertRedirect();

        $destination->refresh();
        $this->assertFalse((bool) $destination->is_active);

        $destination->delete();
        $region->delete();
    }

    public function test_admin_can_delete_destination_and_clean_up_image(): void
    {
        Storage::fake('public');

        $unique = uniqid();
        $region = Region::create([
            'name' => "Bangli Area Test {$unique}",
            'slug' => "bangli-area-test-{$unique}",
            'is_active' => true,
        ]);

        $image = UploadedFile::fake()->image('kintamani.jpg');
        $imagePath = $image->store('destinations', 'public');

        $destination = Destination::create([
            'region_id' => $region->id,
            'name' => "Kintamani View Test {$unique}",
            'slug' => "kintamani-view-test-{$unique}",
            'latitude' => -8.2500,
            'longitude' => 115.3500,
            'image_path' => $imagePath,
            'is_active' => true,
        ]);

        $destId = $destination->id;

        $response = $this->actingAs($this->admin)->delete(route('admin.destinations.destroy', $destination));
        $response->assertRedirect(route('admin.destinations.index'));

        $this->assertDatabaseMissing('destinations', ['id' => $destId]);
        Storage::disk('public')->assertMissing($imagePath);

        $region->delete();
    }

    public function test_validation_fails_on_invalid_coordinates(): void
    {
        $unique = uniqid();
        $region = Region::create([
            'name' => "Karangasem Area Test {$unique}",
            'slug' => "karangasem-area-test-{$unique}",
            'is_active' => true,
        ]);

        // Latitude > 90 atau Longitude > 180 tidak valid
        $response = $this->actingAs($this->admin)->post(route('admin.destinations.store'), [
            'region_id' => $region->id,
            'name' => "Koordinat Salah Test {$unique}",
            'latitude' => 120.000,
            'longitude' => 200.000,
        ]);

        $response->assertSessionHasErrors(['latitude', 'longitude']);

        $region->delete();
    }
}
