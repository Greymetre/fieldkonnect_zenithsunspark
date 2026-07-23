<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        DB::statement("ALTER TABLE sales_orders MODIFY status ENUM('payment_pending','payment_received','confirmed','partially_dispatched','dispatched','cancelled') NOT NULL DEFAULT 'payment_pending'");

        Schema::table('sales_order_items', function (Blueprint $table) {
            $table->decimal('dispatched_quantity', 15, 3)->default(0)->after('quantity');
        });

        Schema::create('sales_order_dispatches', function (Blueprint $table) {
            $table->id();
            $table->string('dispatch_number', 30)->unique()->nullable();
            $table->unsignedBigInteger('sales_order_id')->index();
            $table->date('dispatch_date')->index();
            $table->text('remark')->nullable();
            $table->unsignedBigInteger('dispatched_by')->nullable()->index();
            $table->timestamps();

            $table->foreign('sales_order_id')->references('id')->on('sales_orders');
            $table->foreign('dispatched_by')->references('id')->on('users');
        });

        Schema::create('sales_order_dispatch_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sales_order_dispatch_id')->index();
            $table->unsignedBigInteger('sales_order_item_id')->index();
            $table->unsignedBigInteger('product_id')->index();
            $table->unsignedBigInteger('warehouse_id')->index();
            $table->decimal('quantity', 15, 3);
            $table->timestamps();

            $table->foreign('sales_order_dispatch_id')->references('id')->on('sales_order_dispatches')->onDelete('cascade');
            $table->foreign('sales_order_item_id')->references('id')->on('sales_order_items');
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('warehouse_id')->references('id')->on('ware_houses');
        });

        DB::table('permissions')->updateOrInsert(
            ['name' => 'dispatch_create', 'guard_name' => 'users'],
            ['created_at' => now(), 'updated_at' => now()]
        );
        $role = DB::table('roles')->where('name', 'superadmin')->where('guard_name', 'users')->first();
        $permission = DB::table('permissions')->where('name', 'dispatch_create')->where('guard_name', 'users')->first();
        if ($role && $permission) {
            DB::table('role_has_permissions')->updateOrInsert(['permission_id' => $permission->id, 'role_id' => $role->id]);
        }
    }

    public function down()
    {
        $permissionIds = DB::table('permissions')->where('name', 'dispatch_create')->pluck('id');
        DB::table('role_has_permissions')->whereIn('permission_id', $permissionIds)->delete();
        DB::table('model_has_permissions')->whereIn('permission_id', $permissionIds)->delete();
        DB::table('permissions')->whereIn('id', $permissionIds)->delete();
        Schema::dropIfExists('sales_order_dispatch_items');
        Schema::dropIfExists('sales_order_dispatches');
        Schema::table('sales_order_items', fn (Blueprint $table) => $table->dropColumn('dispatched_quantity'));
        DB::statement("ALTER TABLE sales_orders MODIFY status ENUM('payment_pending','payment_received','confirmed','dispatched','cancelled') NOT NULL DEFAULT 'payment_pending'");
    }
};
