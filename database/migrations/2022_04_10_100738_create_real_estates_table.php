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
        Schema::create('real_estates', function (Blueprint $table) {
            $table->id();
            $table->double('long')->default(0.0);
            $table->double('lat')->default(0.0);
            $table->enum('type', ['منزل', 'عقار','شقة','محل','ارض'])->default('منزل');
            $table->foreignId('user_id')->references('id')->on('users');
            $table->string('location');
            $table->double('area');
            $table->enum('direction', ['شمال', 'جنوب', 'غرب', 'شرق']);
            $table->double('rate')->default(0.0);
            $table->enum('buy_type', ['شراء', 'استأجار','رهن'])->default('استأجار');
            $table->double('budget');
            $table->string('img_url')->nullable();
            $table->string('img360_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('real_estates');
    }
};
