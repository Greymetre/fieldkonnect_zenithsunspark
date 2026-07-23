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
            $table->text('approval_remark')->nullable()->after('notes');
        });

        DB::table('permissions')->updateOrInsert(
            ['name' => 'receive_stock_access', 'guard_name' => 'users'],
            ['created_at' => now(), 'updated_at' => now()]
        );

        $role = DB::table('roles')->where('name', 'superadmin')->where('guard_name', 'users')->first();
        $permission = DB::table('permissions')->where('name', 'receive_stock_access')->where('guard_name', 'users')->first();
        if ($role && $permission) {
            DB::table('role_has_permissions')->updateOrInsert([
                'permission_id' => $permission->id,
                'role_id' => $role->id,
            ]);
        }
    }

    public function down()
    {
        $permissionIds = DB::table('permissions')->where('name', 'receive_stock_access')->pluck('id');
        DB::table('role_has_permissions')->whereIn('permission_id', $permissionIds)->delete();
        DB::table('model_has_permissions')->whereIn('permission_id', $permissionIds)->delete();
        DB::table('permissions')->whereIn('id', $permissionIds)->delete();

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn('approval_remark');
        });
    }
};
