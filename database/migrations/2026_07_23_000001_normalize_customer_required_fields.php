<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::table('customers')->whereNull('customertype')->update(['customertype' => 1]);

        foreach ([
            'first_name', 'last_name', 'password', 'notification_id',
            'device_type', 'gender', 'customer_code', 'manager_name', 'manager_phone',
        ] as $column) {
            DB::table('customers')->where($column, '')->update([$column => null]);
        }

        foreach (['address2', 'landmark', 'locality'] as $column) {
            DB::table('addresses')->where($column, '')->update([$column => null]);
        }

        foreach (['address1', 'address2', 'landmark', 'locality'] as $column) {
            DB::table('shipping_addresses')->where($column, '')->update([$column => null]);
        }

        foreach (['shop_image', 'grade', 'visit_status'] as $column) {
            DB::table('customer_details')->where($column, '')->update([$column => null]);
        }

        DB::statement('ALTER TABLE customers
            MODIFY name VARCHAR(200) NOT NULL,
            MODIFY mobile VARCHAR(15) NOT NULL,
            MODIFY customertype BIGINT UNSIGNED NOT NULL DEFAULT 1,
            MODIFY firmtype BIGINT UNSIGNED NOT NULL,
            MODIFY first_name VARCHAR(250) NULL DEFAULT NULL,
            MODIFY last_name VARCHAR(250) NULL DEFAULT NULL,
            MODIFY password VARCHAR(255) NULL DEFAULT NULL,
            MODIFY notification_id VARCHAR(450) NULL DEFAULT NULL,
            MODIFY device_type VARCHAR(50) NULL DEFAULT NULL,
            MODIFY gender VARCHAR(20) NULL DEFAULT NULL,
            MODIFY customer_code VARCHAR(250) NULL DEFAULT NULL,
            MODIFY manager_name VARCHAR(250) NULL DEFAULT NULL,
            MODIFY manager_phone VARCHAR(50) NULL DEFAULT NULL');

        DB::statement('ALTER TABLE addresses
            MODIFY address1 VARCHAR(250) NOT NULL,
            MODIFY address2 VARCHAR(250) NULL DEFAULT NULL,
            MODIFY landmark VARCHAR(250) NULL DEFAULT NULL,
            MODIFY locality VARCHAR(250) NULL DEFAULT NULL');

        DB::statement('ALTER TABLE shipping_addresses
            MODIFY address1 VARCHAR(250) NULL DEFAULT NULL,
            MODIFY address2 VARCHAR(250) NULL DEFAULT NULL,
            MODIFY landmark VARCHAR(250) NULL DEFAULT NULL,
            MODIFY locality VARCHAR(250) NULL DEFAULT NULL');

        DB::statement('ALTER TABLE customer_details
            MODIFY shop_image VARCHAR(250) NULL DEFAULT NULL,
            MODIFY grade VARCHAR(250) NULL DEFAULT NULL,
            MODIFY visit_status VARCHAR(250) NULL DEFAULT NULL');
    }

    public function down()
    {
        foreach ([
            'first_name', 'last_name', 'password', 'notification_id',
            'device_type', 'gender', 'customer_code', 'manager_name', 'manager_phone',
        ] as $column) {
            DB::table('customers')->whereNull($column)->update([$column => '']);
        }

        foreach (['address2', 'landmark', 'locality'] as $column) {
            DB::table('addresses')->whereNull($column)->update([$column => '']);
        }

        foreach (['address1', 'address2', 'landmark', 'locality'] as $column) {
            DB::table('shipping_addresses')->whereNull($column)->update([$column => '']);
        }

        foreach (['shop_image', 'grade', 'visit_status'] as $column) {
            DB::table('customer_details')->whereNull($column)->update([$column => '']);
        }

        DB::statement("ALTER TABLE customers
            MODIFY customertype BIGINT UNSIGNED NULL DEFAULT NULL,
            MODIFY first_name VARCHAR(250) NOT NULL DEFAULT '',
            MODIFY last_name VARCHAR(250) NOT NULL DEFAULT '',
            MODIFY password VARCHAR(255) NOT NULL DEFAULT '',
            MODIFY notification_id VARCHAR(450) NOT NULL DEFAULT '',
            MODIFY device_type VARCHAR(50) NOT NULL DEFAULT '',
            MODIFY gender VARCHAR(20) NOT NULL DEFAULT '',
            MODIFY customer_code VARCHAR(250) NOT NULL DEFAULT '',
            MODIFY manager_name VARCHAR(250) NOT NULL DEFAULT '',
            MODIFY manager_phone VARCHAR(50) NOT NULL DEFAULT ''");

        DB::statement("ALTER TABLE addresses
            MODIFY address1 VARCHAR(250) NOT NULL DEFAULT '',
            MODIFY address2 VARCHAR(250) NOT NULL DEFAULT '',
            MODIFY landmark VARCHAR(250) NOT NULL DEFAULT '',
            MODIFY locality VARCHAR(250) NOT NULL DEFAULT ''");

        DB::statement("ALTER TABLE shipping_addresses
            MODIFY address1 VARCHAR(250) NOT NULL DEFAULT '',
            MODIFY address2 VARCHAR(250) NOT NULL DEFAULT '',
            MODIFY landmark VARCHAR(250) NOT NULL DEFAULT '',
            MODIFY locality VARCHAR(250) NOT NULL DEFAULT ''");

        DB::statement("ALTER TABLE customer_details
            MODIFY shop_image VARCHAR(250) NOT NULL DEFAULT '',
            MODIFY grade VARCHAR(250) NOT NULL DEFAULT '',
            MODIFY visit_status VARCHAR(250) NOT NULL DEFAULT ''");
    }
};
