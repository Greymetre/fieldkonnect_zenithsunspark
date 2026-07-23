<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        DB::table('firm_types')->updateOrInsert(
            ['firmtype_name' => 'Vendor'],
            ['active' => 'Y', 'updated_at' => now(), 'created_at' => now()]
        );

        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('po_number', 30)->unique()->nullable();
            $table->unsignedBigInteger('supplier_id')->index();
            $table->date('po_date')->index();
            $table->enum('status', ['draft', 'approved', 'partially_received', 'received', 'cancelled'])->default('draft')->index();
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('total_gst', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->unsignedBigInteger('updated_by')->nullable()->index();
            $table->unsignedBigInteger('approved_by')->nullable()->index();
            $table->timestamp('approved_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('supplier_id')->references('id')->on('customers');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            $table->foreign('approved_by')->references('id')->on('users');
        });

        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_order_id')->index();
            $table->unsignedBigInteger('product_id')->index();
            $table->unsignedBigInteger('warehouse_id')->index();
            $table->decimal('quantity', 15, 3);
            $table->decimal('received_quantity', 15, 3)->default(0);
            $table->decimal('rate', 15, 2);
            $table->decimal('gst_percent', 8, 2)->default(0);
            $table->decimal('taxable_amount', 15, 2);
            $table->decimal('gst_amount', 15, 2);
            $table->decimal('total_amount', 15, 2);
            $table->timestamps();

            $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('warehouse_id')->references('id')->on('ware_houses');
        });

        $permissions = [
            'purchase_order_access',
            'purchase_order_create',
            'purchase_order_edit',
            'purchase_order_show',
            'purchase_order_delete',
            'purchase_order_approve',
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $permission, 'guard_name' => 'users'],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }

        $superAdminRole = DB::table('roles')->where('name', 'superadmin')->where('guard_name', 'users')->first();
        if ($superAdminRole) {
            $permissionIds = DB::table('permissions')->whereIn('name', $permissions)->pluck('id');
            foreach ($permissionIds as $permissionId) {
                DB::table('role_has_permissions')->updateOrInsert([
                    'permission_id' => $permissionId,
                    'role_id' => $superAdminRole->id,
                ]);
            }
        }
    }

    public function down()
    {
        $permissions = [
            'purchase_order_access',
            'purchase_order_create',
            'purchase_order_edit',
            'purchase_order_show',
            'purchase_order_delete',
            'purchase_order_approve',
        ];
        $permissionIds = DB::table('permissions')->whereIn('name', $permissions)->pluck('id');
        DB::table('role_has_permissions')->whereIn('permission_id', $permissionIds)->delete();
        DB::table('model_has_permissions')->whereIn('permission_id', $permissionIds)->delete();
        DB::table('permissions')->whereIn('id', $permissionIds)->delete();

        Schema::dropIfExists('purchase_order_items');
        Schema::dropIfExists('purchase_orders');
    }
};
