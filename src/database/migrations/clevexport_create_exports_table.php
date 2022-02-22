<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Leyton\ClevExport\Models\Export;

class CreateExportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exports', function (Blueprint $table) {
            $table->id();
            if(config('clevexport.with_owner')){
                $table->foreignId(config('clevexport.owner_id'))->nullable();
            }
            $table->text('criterias');
            $table->integer('status')->default(Export::CREATED);
            $table->string('file_path')->nullable();
            $table->string('reason')->nullable();
            $table->timestamp('exported_at')->nullable();
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
        Schema::dropIfExists('exports');
    }
}
