<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->text('receive_remark')->nullable()->after('approval_remark');
            $table->unsignedBigInteger('received_by')->nullable()->index()->after('approved_by');
            $table->timestamp('received_at')->nullable()->after('approved_at');
            $table->foreign('received_by')->references('id')->on('users');
        });

        Schema::create('warehouse_stocks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('warehouse_id');
            $table->unsignedBigInteger('product_id');
            $table->decimal('quantity', 15, 3)->default(0);
            $table->timestamps();

            $table->unique(['warehouse_id', 'product_id']);
            $table->foreign('warehouse_id')->references('id')->on('ware_houses');
            $table->foreign('product_id')->references('id')->on('products');
        });

        Schema::create('inventory_ledgers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('warehouse_id')->index();
            $table->unsignedBigInteger('product_id')->index();
            $table->string('transaction_type', 30)->index();
            $table->string('reference_type', 50)->index();
            $table->unsignedBigInteger('reference_id')->index();
            $table->decimal('quantity_in', 15, 3)->default(0);
            $table->decimal('quantity_out', 15, 3)->default(0);
            $table->decimal('balance_quantity', 15, 3)->default(0);
            $table->text('remark')->nullable();
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->timestamps();

            $table->foreign('warehouse_id')->references('id')->on('ware_houses');
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('created_by')->references('id')->on('users');
        });

        DB::table('permissions')->updateOrInsert(
            ['name' => 'receive_stock_create', 'guard_name' => 'users'],
            ['created_at' => now(), 'updated_at' => now()]
        );

        $role = DB::table('roles')->where('name', 'superadmin')->where('guard_name', 'users')->first();
        $permission = DB::table('permissions')->where('name', 'receive_stock_create')->where('guard_name', 'users')->first();
        if ($role && $permission) {
            DB::table('role_has_permissions')->updateOrInsert([
                'permission_id' => $permission->id,
                'role_id' => $role->id,
            ]);
        }
    }

    public function down()
    {
        $permissionIds = DB::table('permissions')->where('name', 'receive_stock_create')->pluck('id');
        DB::table('role_has_permissions')->whereIn('permission_id', $permissionIds)->delete();
        DB::table('model_has_permissions')->whereIn('permission_id', $permissionIds)->delete();
        DB::table('permissions')->whereIn('id', $permissionIds)->delete();

        Schema::dropIfExists('inventory_ledgers');
        Schema::dropIfExists('warehouse_stocks');

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropForeign(['received_by']);
            $table->dropColumn(['receive_remark', 'received_by', 'received_at']);
        });
    }
};
