<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateManPowersTable extends Migration
{
    public function up(): void
    {
        Schema::create('man_powers', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lengkap');
            $table->string('jabatan');
            $table->string('no_telepon');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('man_powers');
    }
}
