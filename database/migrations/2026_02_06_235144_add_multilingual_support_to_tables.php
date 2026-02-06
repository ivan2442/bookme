<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Pridanie flagu pre multijazyčnosť
        if (!Schema::hasColumn('profiles', 'is_multilingual')) {
            Schema::table('profiles', function (Blueprint $table) {
                $table->boolean('is_multilingual')->default(false)->after('status');
            });
        }

        // 2. Konverzia dát na JSON formát v existujúcich stĺpcoch
        $this->convertDataToMultilingual();

        // 3. Odstránenie indexu, ktorý by robil problémy pri zmene typu na TEXT/JSON
        Schema::table('profiles', function (Blueprint $table) {
            $indexes = Schema::getIndexes('profiles');
            $hasIndex = collect($indexes)->contains(function ($index) {
                return $index['name'] === 'profiles_city_category_index';
            });
            if ($hasIndex) {
                $table->dropIndex(['city', 'category']);
            }
        });

        // 4. Zmena typov stĺpcov na TEXT (pre Laravel cast array)
        Schema::table('profiles', function (Blueprint $table) {
            $table->text('name')->change();
            $table->text('category')->nullable()->change();
            $table->text('description')->nullable()->change();
        });

        Schema::table('services', function (Blueprint $table) {
            $table->text('name')->change();
            $table->text('category')->nullable()->change();
            $table->text('description')->nullable()->change();
        });

        Schema::table('service_variants', function (Blueprint $table) {
            $table->text('name')->change();
            $table->text('description')->nullable()->change();
        });

        Schema::table('articles', function (Blueprint $table) {
            $table->text('title')->change();
            $table->text('excerpt')->nullable()->change();
            $table->longText('content')->change();
        });
    }

    private function convertDataToMultilingual()
    {
        foreach (['profiles', 'services', 'service_variants', 'articles'] as $table) {
            if (!Schema::hasTable($table)) continue;

            $items = DB::table($table)->get();
            foreach ($items as $item) {
                $updates = [];
                $fields = match($table) {
                    'profiles', 'services' => ['name', 'category', 'description'],
                    'service_variants' => ['name', 'description'],
                    'articles' => ['title', 'excerpt', 'content'],
                    default => []
                };

                foreach ($fields as $field) {
                    if (property_exists($item, $field) && !empty($item->$field)) {
                        $value = $item->$field;
                        // Skontrolujeme či to už nie je JSON
                        if (!str_starts_with($value, '{') || json_decode($value) === null) {
                            $updates[$field] = json_encode(['sk' => $value], JSON_UNESCAPED_UNICODE);
                        }
                    }
                }

                if (!empty($updates)) {
                    DB::table($table)->where('id', $item->id)->update($updates);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn('is_multilingual');
            $table->string('name')->change();
            $table->string('category')->nullable()->change();
            $table->text('description')->nullable()->change();
        });

        Schema::table('services', function (Blueprint $table) {
            $table->string('name')->change();
            $table->string('category')->nullable()->change();
            $table->text('description')->nullable()->change();
        });

        Schema::table('service_variants', function (Blueprint $table) {
            $table->string('name')->change();
            $table->text('description')->nullable()->change();
        });

        Schema::table('articles', function (Blueprint $table) {
            $table->string('title')->change();
            $table->text('excerpt')->nullable()->change();
            $table->longText('content')->change();
        });
    }
};
