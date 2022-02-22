<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Leyton\ClevExport\Models\Export;
use Leyton\ClevExport\Models\SubExport;

class CreateSubExportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sub_exports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('export_id');
            $table->string('file_path')->nullable();
            $table->integer('status')->default(SubExport::IN_PROGRESS);
            $table->text('pagination');
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
        Schema::dropIfExists('sub_exports');
    }
}
