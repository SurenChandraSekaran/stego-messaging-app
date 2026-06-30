<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Change 'conversations' to 'wirechat_conversations'
        Schema::table('wirechat_conversations', function (Blueprint $table) {
            $table->text('security_key')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('wirechat_conversations', function (Blueprint $table) {
            $table->dropColumn('security_key');
        });
    }
};
