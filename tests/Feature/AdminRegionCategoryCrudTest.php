<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Destination;
use App\Models\Region;
use App\Models\User;
use Tests\TestCase;

class AdminRegionCategoryCrudTest extends TestCase
{
    protected ?User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['email' => 'admin-test@balitourservice.local'],
            [
                'name' => 'Admin Test Cluster 6',
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

    public function test_guest_cannot_access_admin_regions_index(): void
    {
        $response = $this->get(route('admin.regions.index'));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_guest_cannot_access_admin_categories_index(): void
    {
        $response = $this->get(route('admin.categories.index'));
        $response->assertRedirect(route('admin.login'));
    }

    /*
    |--------------------------------------------------------------------------
    | Region CRUD Tests
    |--------------------------------------------------------------------------
    */

    public function test_admin_can_view_regions_index_with_search(): void
    {
        $region = Region::create([
            'name' => 'Ubud Sanctuary Test',
            'slug' => 'ubud-sanctuary-test',
            'regency' => 'Gianyar',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.regions.index', ['q' => 'Sanctuary']));
        $response->assertOk();
        $response->assertSee('Ubud Sanctuary Test');

        $region->delete();
    }

    public function test_admin_can_create_region_with_automatic_slug(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.regions.store'), [
            'name' => 'Nusa Lembongan Test',
            'slug' => '',
            'regency' => 'Klungkung',
            'description' => 'Pulau eksotis di tenggara Bali.',
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.regions.index'));
        $response->assertSessionHas('success');

        $region = Region::where('slug', 'nusa-lembongan-test')->first();
        $this->assertNotNull($region);
        $this->assertEquals('Nusa Lembongan Test', $region->name);

        $region->delete();
    }

    public function test_admin_can_create_region_with_custom_slug(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.regions.store'), [
            'name' => 'Sanur Sunrise Test',
            'slug' => 'sanur-custom-slug-test',
            'regency' => 'Denpasar',
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.regions.index'));

        $region = Region::where('slug', 'sanur-custom-slug-test')->first();
        $this->assertNotNull($region);

        $region->delete();
    }

    public function test_admin_can_update_region(): void
    {
        $region = Region::create([
            'name' => 'Canggu Test Lama',
            'slug' => 'canggu-test-lama',
            'regency' => 'Badung',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.regions.update', $region), [
            'name' => 'Canggu Test Baru',
            'slug' => 'canggu-test-baru',
            'regency' => 'Badung',
            'description' => 'Kawasan modern dan kafe.',
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.regions.index'));
        $this->assertDatabaseHas('regions', [
            'id' => $region->id,
            'name' => 'Canggu Test Baru',
            'slug' => 'canggu-test-baru',
        ]);

        $region->delete();
    }

    public function test_admin_can_toggle_region_status(): void
    {
        $region = Region::create([
            'name' => 'Amed Test',
            'slug' => 'amed-test',
            'regency' => 'Karangasem',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->patch(route('admin.regions.status', $region));
        $response->assertRedirect();

        $this->assertDatabaseHas('regions', [
            'id' => $region->id,
            'is_active' => false,
        ]);

        $region->delete();
    }

    public function test_cannot_delete_region_if_destinations_exist(): void
    {
        $region = Region::create([
            'name' => 'Bedugul Guard Test',
            'slug' => 'bedugul-guard-test',
            'regency' => 'Tabanan',
            'is_active' => true,
        ]);

        $category = Category::create([
            'name' => 'Danau Guard Test',
            'slug' => 'danau-guard-test',
            'is_active' => true,
        ]);

        $destination = Destination::create([
            'region_id' => $region->id,
            'category_id' => $category->id,
            'name' => 'Pura Ulun Danu Guard Test',
            'slug' => 'pura-ulun-danu-guard-test',
            'latitude' => -8.2753,
            'longitude' => 115.1668,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.regions.destroy', $region));
        $response->assertRedirect();
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('regions', [
            'id' => $region->id,
        ]);

        $destination->delete();
        $region->delete();
        $category->delete();
    }

    public function test_can_delete_region_if_no_destinations_exist(): void
    {
        $region = Region::create([
            'name' => 'Daerah Kosong Test',
            'slug' => 'daerah-kosong-test',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.regions.destroy', $region));
        $response->assertRedirect(route('admin.regions.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('regions', [
            'id' => $region->id,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Category CRUD Tests
    |--------------------------------------------------------------------------
    */

    public function test_admin_can_view_categories_index_with_search(): void
    {
        $category = Category::create([
            'name' => 'Wisata Air Terjun Test',
            'slug' => 'wisata-air-terjun-test',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.categories.index', ['q' => 'Terjun Test']));
        $response->assertOk();
        $response->assertSee('Wisata Air Terjun Test');

        $category->delete();
    }

    public function test_admin_can_create_category_with_automatic_slug(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.categories.store'), [
            'name' => 'Petualangan Alam Liar Test',
            'slug' => '',
            'description' => 'Aktivitas trekking dan rafting.',
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.categories.index'));
        $response->assertSessionHas('success');

        $category = Category::where('slug', 'petualangan-alam-liar-test')->first();
        $this->assertNotNull($category);

        $category->delete();
    }

    public function test_admin_can_update_category(): void
    {
        $category = Category::create([
            'name' => 'Pantai Santai Test',
            'slug' => 'pantai-santai-test',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.categories.update', $category), [
            'name' => 'Pantai Sunset Test',
            'slug' => 'pantai-sunset-test',
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Pantai Sunset Test',
            'slug' => 'pantai-sunset-test',
        ]);

        $category->delete();
    }

    public function test_admin_can_toggle_category_status(): void
    {
        $category = Category::create([
            'name' => 'Kuliner Malam Test',
            'slug' => 'kuliner-malam-test',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->patch(route('admin.categories.status', $category));
        $response->assertRedirect();

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'is_active' => false,
        ]);

        $category->delete();
    }

    public function test_cannot_delete_category_if_destinations_exist(): void
    {
        $region = Region::create([
            'name' => 'Uluwatu Area Guard Test',
            'slug' => 'uluwatu-area-guard-test',
            'is_active' => true,
        ]);

        $category = Category::create([
            'name' => 'Pura Guard Test',
            'slug' => 'pura-guard-test',
            'is_active' => true,
        ]);

        $destination = Destination::create([
            'region_id' => $region->id,
            'category_id' => $category->id,
            'name' => 'Pura Uluwatu Guard Test',
            'slug' => 'pura-uluwatu-guard-test',
            'latitude' => -8.8291,
            'longitude' => 115.0849,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.categories.destroy', $category));
        $response->assertRedirect();
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
        ]);

        $destination->delete();
        $category->delete();
        $region->delete();
    }

    public function test_can_delete_category_if_no_destinations_exist(): void
    {
        $category = Category::create([
            'name' => 'Kategori Tanpa Destinasi Test',
            'slug' => 'kategori-tanpa-destinasi-test',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.categories.destroy', $category));
        $response->assertRedirect(route('admin.categories.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
        ]);
    }
}
