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
        Schema::create('occurences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->unsignedBigInteger('compartment_id')->nullable();
            $table->unsignedBigInteger('security_id')->nullable();
            $table->string('occurence_scene')->nullable();
            $table->string('first_aid_item')->nullable();
            $table->text('occurence');
            $table->string('department');
            $table->string('type');
            $table->json('evidence')->nullable();
            $table->string('status')->default('pending');
            $table->unsignedBigInteger('pushed_to')->nullable();
            $table->text('om_comment')->nullable();
            $table->text('hod_comment')->nullable();
            $table->text('md_comment')->nullable();
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
        Schema::dropIfExists('occurences');
    }
};
