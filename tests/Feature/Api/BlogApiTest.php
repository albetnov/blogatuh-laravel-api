<?php

use App\Models\Blog;
use App\Models\Category;
use App\Models\User;
use Database\Seeders\BlogSeeder;
use Database\Seeders\CategorySeeder;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;

use function Pest\Laravel\delete;
use function Pest\Laravel\get;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;
use function Pest\Laravel\seed;

uses(RefreshDatabase::class);

beforeEach(function () {
    seed(CategorySeeder::class);
    seed(BlogSeeder::class);
    Sanctum::actingAs(
        User::factory()->create()
    );
});

test('Get All Blog', function () {
    seed(CategorySeeder::class);
    get("/api/blogs")->assertStatus(200);
});

test('Get Single Blog Success', function () {
    $blogRandom = Blog::inRandomOrder()->first();
    get("/api/blogs/{$blogRandom->slug}")->assertJson(
        fn (AssertableJson $json) =>
        $json->has(
            "data",
            fn ($json) =>
            $json->where("name", $blogRandom->name)
                ->where("content", $blogRandom->content)->etc()
        )->etc()
    );
});

test('Get Single Blog Failed', function () {
    get("/api/blogs/tidak-ada-pokoknya")->assertStatus(404);
});

test('Insert Blog Success', function () {
    $categories = Category::inRandomOrder()->limit(3)->get("id");
    $categoryBuilder = "{$categories[0]->id},{$categories[1]->id},{$categories[2]->id}";
    $jsonContent = [
        "name" => "Test Masukin Blog",
        "categories" => $categoryBuilder,
        "content" => "Test masukin blog"
    ];
    postJson("/api/blogs", $jsonContent)
        ->assertStatus(201)->assertJson(
            fn (AssertableJson $json) =>
            $json->has(
                "data",
                fn ($json) =>
                $json->where("name", $jsonContent['name'])->where("content", $jsonContent['content'])->etc()
            )->etc()
        );
});

test('Insert Blog Failed (Category Not Found)', function () {
    $category = Category::orderBy('id', 'DESC')->first()->id + 10;
    postJson("/api/blogs", [
        "name" => "Test Masukin Json gagal",
        "categories" => "{$category},2,3",
        "content" => "Test masukin json gagal bang"
    ])->assertStatus(404)->assertJson([
        "message" => "Category not found"
    ]);
});

test("Update Blog Success", function () {
    $blog = Blog::inRandomOrder()->first();
    $categories = Category::inRandomOrder()->limit(3)->get("id");
    $categoryBuilder = "{$categories[0]->id},{$categories[1]->id},{$categories[2]->id}";
    $jsonContent = [
        "name" => "Test Edit Blog",
        "categories" => $categoryBuilder,
        "content" => "test edit blog"
    ];
    putJson("/api/blogs/{$blog->slug}", $jsonContent)->assertStatus(200)->assertJson(
        fn (AssertableJson $json) =>
        $json->has(
            "data",
            fn ($json) =>
            $json->where("name", $jsonContent['name'])->where("content", $jsonContent['content'])->etc()
        )->etc()
    );
});

test("Update Blog Failed (No Such Blog)", function () {
    putJson("/api/blogs/tidak-ada-pokoknya", [
        "name" => "Test Edit Blog",
        "categories" => "1,2,3",
        "content" => "test edit blog"
    ])->assertStatus(404)->assertJson([
        "message" => "Blog not found"
    ]);
});

test("Update Blog Failed (No Such Category)", function () {
    $category = Category::orderBy('id', 'DESC')->first()->id + 10;
    $blog = Blog::inRandomOrder()->first();
    $jsonContent = [
        "name" => "Test Edit Blog",
        "categories" => "{$category},2,3",
        "content" => "test edit blog"
    ];
    putJson("/api/blogs/{$blog->slug}", $jsonContent)->assertStatus(404)->assertJson([
        "message" => "Category not found"
    ]);
});

test("Delete Blog Success", function () {
    $blog = Blog::inRandomOrder()->first();
    delete("/api/blogs/{$blog->slug}")->assertStatus(200)->assertJson([
        "message" => "Blog deleted successfully"
    ]);
});

test("Delete Blog Failed (No such blog)", function () {
    delete("/api/blogs/tidak-ada-pokoknya")->assertStatus(404)->assertJson([
        "message" => "Blog not found"
    ]);
});
