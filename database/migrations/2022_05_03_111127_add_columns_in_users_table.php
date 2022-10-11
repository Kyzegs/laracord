<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasColumn('users', 'access_token')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('access_token')->nullable()->after('remember_token');
            });
        }

        if (! Schema::hasColumn('users', 'access_token_expires_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('access_token_expires_at')->nullable()->after('access_token');
            });
        }

        if (! Schema::hasColumn('users', 'refresh_token')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('refresh_token')->nullable()->after('access_token_expires_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['access_token', 'access_token_expires_at', 'refresh_token']);
        });
    }
};
