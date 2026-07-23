<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sales_order_payments', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_number', 30)->unique()->nullable();
            $table->unsignedBigInteger('sales_order_id')->unique();
            $table->date('payment_date')->index();
            $table->decimal('amount_received', 15, 2);
            $table->string('payment_mode', 30);
            $table->string('reference_number', 100)->nullable();
            $table->unsignedBigInteger('received_by')->nullable()->index();
            $table->timestamps();

            $table->foreign('sales_order_id')->references('id')->on('sales_orders');
            $table->foreign('received_by')->references('id')->on('users');
        });

        $permissions = ['sales_order_payment', 'sales_order_confirm', 'dispatch_access'];
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
        $permissionIds = DB::table('permissions')->whereIn('name', ['sales_order_payment', 'sales_order_confirm', 'dispatch_access'])->pluck('id');
        DB::table('role_has_permissions')->whereIn('permission_id', $permissionIds)->delete();
        DB::table('model_has_permissions')->whereIn('permission_id', $permissionIds)->delete();
        DB::table('permissions')->whereIn('id', $permissionIds)->delete();
        Schema::dropIfExists('sales_order_payments');
    }
};
