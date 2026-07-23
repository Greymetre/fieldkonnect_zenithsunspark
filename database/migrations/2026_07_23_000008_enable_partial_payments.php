<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::statement('ALTER TABLE sales_order_payments ADD INDEX sales_order_payments_sales_order_id_index (sales_order_id)');
        DB::statement('ALTER TABLE sales_order_payments DROP INDEX sales_order_payments_sales_order_id_unique');
        DB::statement("ALTER TABLE sales_orders MODIFY status ENUM('payment_pending','payment_partial','payment_received','confirmed','partially_dispatched','dispatched','cancelled') NOT NULL DEFAULT 'payment_pending'");

        DB::table('permissions')->updateOrInsert(
            ['name' => 'payment_ledger_access', 'guard_name' => 'users'],
            ['created_at' => now(), 'updated_at' => now()]
        );
        $role = DB::table('roles')->where('name', 'superadmin')->where('guard_name', 'users')->first();
        $permission = DB::table('permissions')->where('name', 'payment_ledger_access')->where('guard_name', 'users')->first();
        if ($role && $permission) {
            DB::table('role_has_permissions')->updateOrInsert(['permission_id' => $permission->id, 'role_id' => $role->id]);
        }
    }

    public function down()
    {
        $permissionIds = DB::table('permissions')->where('name', 'payment_ledger_access')->pluck('id');
        DB::table('role_has_permissions')->whereIn('permission_id', $permissionIds)->delete();
        DB::table('model_has_permissions')->whereIn('permission_id', $permissionIds)->delete();
        DB::table('permissions')->whereIn('id', $permissionIds)->delete();
        DB::statement("ALTER TABLE sales_orders MODIFY status ENUM('payment_pending','payment_received','confirmed','partially_dispatched','dispatched','cancelled') NOT NULL DEFAULT 'payment_pending'");
        DB::statement('ALTER TABLE sales_order_payments ADD UNIQUE sales_order_payments_sales_order_id_unique (sales_order_id)');
        DB::statement('ALTER TABLE sales_order_payments DROP INDEX sales_order_payments_sales_order_id_index');
    }
};
