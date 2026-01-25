<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    
public function up(): void
    {
        DB::statement("USE `parking-management`");
        
        Schema::create('metode_pembayaran', function (Blueprint $table) {

            $table->id('id_metode'); 
            $table->string('metode_bayar', 30);

            $table->softDeletes(); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('metode_pembayaran');
    }
};