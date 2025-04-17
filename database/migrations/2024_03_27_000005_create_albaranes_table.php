<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('albaranes', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();
            $table->foreignId('cliente_id')->constrained()->onDelete('cascade');
            $table->date('fecha');
            $table->decimal('total', 10, 2)->default(0);
            $table->text('notas')->nullable();
            $table->enum('estado', ['pendiente', 'facturado', 'completado'])->default('pendiente');
            $table->foreignId('factura_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('albaran_producto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('albaran_id')->constrained('albaranes')->onDelete('cascade');
            $table->foreignId('producto_id')->constrained()->onDelete('cascade');
            $table->integer('cantidad');
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('albaran_producto');
        Schema::dropIfExists('albaranes');
    }
}; 