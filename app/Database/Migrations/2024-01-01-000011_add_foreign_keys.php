<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddForeignKeys extends Migration
{
    public function up()
    {
        // Add foreign key to users table
        $this->db->query('ALTER TABLE `users` ADD CONSTRAINT `users_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations`(`id`) ON DELETE SET NULL ON UPDATE CASCADE');
    }

    public function down()
    {
        // Drop foreign key from users table
        $this->db->query('ALTER TABLE `users` DROP FOREIGN KEY `users_organization_id_foreign`');
    }
}
