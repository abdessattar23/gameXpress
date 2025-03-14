<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use App\Models\Image;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Exception;
use PHPUnit\Framework\Attributes\Test;

class StoreProductTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    #[Test]
    public function user_without_permission_cannot_create_product()
    {

        $user = User::factory()->create();


        Gate::define('create_products', function () {
            return false;
        });

        $this->actingAs($user);

        $response = $this->postJson('/api/products', [
            'name' => 'Test Product'
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'You do not have permission to create products'
            ]);
    }

   #[Test]
    public function it_validates_required_fields()
    {
        $user = User::factory()->create();

        // Mock Gate to allow 'create_products' permission
        Gate::define('create_products', function () {
            return true;
        });

        $this->actingAs($user);

        $response = $this->postJson('/api/products', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'slug', 'price', 'stock', 'category_id', 'images', 'primary_index']);
    }

   #[Test]
    public function it_creates_a_product_with_images_successfully()
    {
        // Create a user with permission
        $user = User::factory()->create();

        // Mock Gate to allow 'create_products' permission
        Gate::define('create_products', function () {
            return true;
        });

        $this->actingAs($user);

        // Create a category for the product
        $category = Category::factory()->create();

        // Create test images
        $image1 = UploadedFile::fake()->image('product1.jpg');
        $image2 = UploadedFile::fake()->image('product2.jpg');

        $data = [
            'name' => 'Test Product',
            'slug' => 'test-product',
            'description' => 'This is a test product',
            'price' => 99.99,
            'stock' => 10,
            'category_id' => $category->id,
            'images' => [$image1, $image2],
            'primary_index' => 0,
        ];

        $response = $this->postJson('/api/products', $data);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'message' => 'Product created successfully',
                'name' => 'Test Product',
                'slug' => 'test-product',
                'price' => 99.99,
                'stock' => 10,
            ]);


        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
            'slug' => 'test-product',
            'description' => 'This is a test product',
            'price' => 99.99,
            'stock' => 10,
            'category_id' => $category->id,
        ]);


        $product = Product::where('name', 'Test Product')->first();
        $this->assertEquals(2, $product->images->count());


        $this->assertDatabaseHas('images', [
            'product_id' => $product->id,
            'is_primary' => true,
        ]);


        Storage::disk('public')->assertExists('products/' . $image1->hashName());
        Storage::disk('public')->assertExists('products/' . $image2->hashName());
    }

   #[Test]
    public function it_validates_unique_product_name_and_slug()
    {

        $existingProduct = Product::factory()->create([
            'name' => 'Existing Product',
            'slug' => 'existing-product'
        ]);

        $user = User::factory()->create();


        Gate::define('create_products', function () {
            return true;
        });

        $this->actingAs($user);

        $category = Category::factory()->create();
        $image = UploadedFile::fake()->image('product.jpg');

        $data = [
            'name' => 'Existing Product',
            'slug' => 'existing-product',
            'description' => 'Test description',
            'price' => 19.99,
            'stock' => 5,
            'category_id' => $category->id,
            'images' => [$image],
            'primary_index' => 0,
        ];

        $response = $this->postJson('/api/products', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'slug']);
    }

   #[Test]
    public function it_validates_image_files()
    {
        $user = User::factory()->create();


        Gate::define('create_products', function () {
            return true;
        });

        $this->actingAs($user);

        $category = Category::factory()->create();


        $invalidFile = UploadedFile::fake()->create('document.pdf', 100);

        $data = [
            'name' => 'Test Product',
            'slug' => 'test-product',
            'description' => 'This is a test product',
            'price' => 99.99,
            'stock' => 10,
            'category_id' => $category->id,
            'images' => [$invalidFile],
            'primary_index' => 0,
        ];

        $response = $this->postJson('/api/products', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['images.0']);
    }

   #[Test]
    public function it_handles_database_exceptions()
    {
        $user = User::factory()->create();


        Gate::define('create_products', function () {
            return true;
        });

        $this->actingAs($user);

        $category = Category::factory()->create();
        $image = UploadedFile::fake()->image('product.jpg');

        $data = [
            'name' => 'Test Product',
            'slug' => 'test-product',
            'description' => 'This is a test product',
            'price' => 99.99,
            'stock' => 10,
            'category_id' => $category->id,
            'images' => [$image],
            'primary_index' => 0,
        ];


        DB::shouldReceive('beginTransaction')->once();
        DB::shouldReceive('commit')->never();
        DB::shouldReceive('rollback')->once();


        Product::shouldReceive('create')
            ->once()
            ->andThrow(new Exception('Database error'));

        $response = $this->postJson('/api/products', $data);

        $response->assertStatus(500)
            ->assertJsonFragment([
                'message' => 'Failed To crrate the Product',
            ]);
    }
}
