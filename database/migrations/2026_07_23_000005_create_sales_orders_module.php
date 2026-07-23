<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sales_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 30)->unique()->nullable();
            $table->enum('order_type', ['b2b', 'b2c'])->index();
            $table->unsignedBigInteger('customer_id')->index();
            $table->unsignedBigInteger('warehouse_id')->index();
            $table->date('order_date')->index();
            $table->enum('status', ['payment_pending', 'payment_received', 'confirmed', 'dispatched', 'cancelled'])->default('payment_pending')->index();
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('total_gst', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->unsignedBigInteger('updated_by')->nullable()->index();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreign('warehouse_id')->references('id')->on('ware_houses');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
        });

        Schema::create('sales_order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sales_order_id')->index();
            $table->unsignedBigInteger('product_id')->index();
            $table->decimal('quantity', 15, 3);
            $table->decimal('rate', 15, 2);
            $table->decimal('gst_percent', 8, 2)->default(0);
            $table->decimal('taxable_amount', 15, 2);
            $table->decimal('gst_amount', 15, 2);
            $table->decimal('total_amount', 15, 2);
            $table->timestamps();

            $table->foreign('sales_order_id')->references('id')->on('sales_orders')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products');
        });

        $permissions = ['sales_order_access', 'sales_order_create', 'sales_order_show', 'sales_order_delete'];
        foreach ($permissions as $permission) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $permission, 'guard_name' => 'users'],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }

        $role = DB::table('roles')->where('name', 'superadmin')->where('guard_name', 'users')->first();
        if ($role) {
            foreach (DB::table('permissions')->whereIn('name', $permissions)->pluck('id') as $permissionId) {
                DB::table('role_has_permissions')->updateOrInsert(['permission_id' => $permissionId, 'role_id' => $role->id]);
            }
        }
    }

    public function down()
    {
        $permissionIds = DB::table('permissions')->whereIn('name', ['sales_order_access', 'sales_order_create', 'sales_order_show', 'sales_order_delete'])->pluck('id');
        DB::table('role_has_permissions')->whereIn('permission_id', $permissionIds)->delete();
        DB::table('model_has_permissions')->whereIn('permission_id', $permissionIds)->delete();
        DB::table('permissions')->whereIn('id', $permissionIds)->delete();
        Schema::dropIfExists('sales_order_items');
        Schema::dropIfExists('sales_orders');
    }
};
