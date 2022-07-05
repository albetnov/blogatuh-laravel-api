<?php

use App\Models\Blog;
use App\Models\Category;
use App\Models\User;
use Database\Seeders\BlogSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\delete;
use function Pest\Laravel\get;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;
use function Pest\Laravel\seed;

uses(RefreshDatabase::class);

beforeEach(function () {
    seed(CategorySeeder::class);
    Sanctum::actingAs(
        User::factory()->create()
    );
});

test("Get All Categories", function () {
    get("/api/categories")->assertStatus(200)->assertJson(
        fn (AssertableJson $json) =>
        $json->has("categories")->etc()
    );
});

test("Get Single Category", function () {
    $category = Category::inRandomOrder()->first();
    get("/api/categories/{$category->slug}")->assertStatus(200)->assertJson(
        fn (AssertableJson $json) =>
        $json->has("category", fn ($json) => $json->where("name", $category->name)->etc())->etc()
    );
});

test("Insert Category Success", function () {
    $name = "Test Masukin Category";
    postJson("/api/categories", [
        "name" => $name
    ])->assertStatus(201)->assertJson(
        fn (AssertableJson $json) =>
        $json->has("category", fn ($json) => $json->where("name", $name)->etc())->etc()
    );
});

test("Insert Category Failed", function () {
    $name = "Test Masukin Category";
    postJson("/api/categories", [
        "name" => $name
    ])->assertStatus(201)->assertJson(
        fn (AssertableJson $json) =>
        $json->has("category", fn ($json) => $json->where("name", $name)->etc())->etc()
    );
    $name = "Test Masukin Category";
    postJson("/api/categories", [
        "name" => $name
    ])->assertStatus(422);
});

test("Update Category Success", function () {
    $category = Category::inRandomOrder()->first();
    $name = 'Test ubah category';
    putJson("/api/categories/{$category->slug}", [
        'name' => $name
    ])->assertStatus(200)->assertJson(
        fn (AssertableJson $json) =>
        $json->has("category", fn ($json) => $json->where("name", $name)->etc())->etc()
    );
});

test("Update Category Failed", function () {
    putJson("/api/categories/gak-ada-ini", [
        'name' => 'test ubah'
    ])->assertStatus(404)->assertJson([
        'message' => 'Category not found'
    ]);
});

test("Delete Category Success", function () {
    $category = Category::inRandomOrder()->first();
    delete("/api/categories/{$category->slug}")->assertStatus(200)->assertJson(fn (AssertableJson $json) =>
    $json->where('message', "BlogAtuh | Category deleted")->etc());
});

test("Delete Category Failed (Not Found)", function () {
    delete("/api/categories/gak-ada-ini")->assertStatus(404)->assertJson([
        'message' => 'Category not found'
    ]);
});

test("Delete Category Failed (In Use)", function () {
    seed(BlogSeeder::class);
    $blog = Blog::inRandomOrder()->first();
    $ids = explode(",", $blog->categories);
    $slug = Category::find($ids[0])->slug;
    delete("/api/categories/{$slug}")->assertStatus(422);
});
