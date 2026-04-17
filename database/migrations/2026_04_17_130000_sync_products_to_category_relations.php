<?php

use App\Models\Category;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role')->default('buyer')->after('email');
            });
        }

        if (!Schema::hasTable('products') || !Schema::hasTable('categories') || !Schema::hasColumn('products', 'category')) {
            return;
        }

        DB::table('products')
            ->select('category')
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->orderBy('category')
            ->get()
            ->each(function ($row) {
                $name = trim((string) $row->category);

                if ($name === '') {
                    return;
                }

                $baseSlug = Str::slug($name);
                $slug = $baseSlug !== '' ? $baseSlug : 'category';
                $suffix = 1;

                while (Category::where('slug', $slug)->where('name', '!=', $name)->exists()) {
                    $slug = $baseSlug . '-' . $suffix;
                    $suffix++;
                }

                Category::firstOrCreate(
                    ['name' => $name],
                    ['slug' => $slug]
                );
            });

        DB::table('products')
            ->whereNull('category_id')
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->orderBy('id')
            ->get()
            ->each(function ($product) {
                $category = Category::where('name', $product->category)->first();

                if ($category) {
                    DB::table('products')
                        ->where('id', $product->id)
                        ->update(['category_id' => $category->id]);
                }
            });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('products') || Schema::hasColumn('products', 'category')) {
            return;
        }

        Schema::table('products', function (Blueprint $table) {
            $table->string('category')->nullable()->after('name');
        });

        DB::table('products')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->update(['products.category' => DB::raw('categories.name')]);
    }
};
