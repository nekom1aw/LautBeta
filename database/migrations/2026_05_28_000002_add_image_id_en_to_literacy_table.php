<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('literacy', function (Blueprint $table) {
            if (!Schema::hasColumn('literacy', 'image_id')) {
                $table->string('image_id')->nullable()->after('image');
            }
            if (!Schema::hasColumn('literacy', 'image_en')) {
                $table->string('image_en')->nullable()->after('image_id');
            }
        });

        DB::table('literacy')->whereNull('image_id')->update(['image_id' => DB::raw('image')]);
        DB::table('literacy')->whereNull('image_en')->update(['image_en' => DB::raw('image')]);
    }

    public function down(): void
    {
        Schema::table('literacy', function (Blueprint $table) {
            if (Schema::hasColumn('literacy', 'image_en')) {
                $table->dropColumn('image_en');
            }
            if (Schema::hasColumn('literacy', 'image_id')) {
                $table->dropColumn('image_id');
            }
        });
    }
};
