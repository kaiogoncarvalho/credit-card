<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\Brand;

class CreateCreditCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('credit_cards', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 80)->unique();
            $table->string('slug', 255)->unique();
            $table->string('image', 255);
            $table->enum('brand', [Brand::ELO, Brand::MASTERCARD, Brand::VISA]);
            $table->integer('category_id')->unsigned();
            $table->double('credit_limit', 15,2)->nullable();
            $table->double('annual_fee', 15, 2)->nullable();
            $table->softDeletes();
            $table->timestamps();
            
            $table->foreign('category_id')->references('id')->on('categories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('credit_cards');
    }
}
