<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePasswordResetsAndRegistrations extends Migration
{
    public function up()
    {
        // 1. Password Resets Table
        if (!$this->db->tableExists('password_resets')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'email' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                ],
                'token' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                ],
                'expires_at' => [
                    'type' => 'DATETIME',
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey('email');
            $this->forge->createTable('password_resets');
        }

        // 2. Organization Registrations Table
        if (!$this->db->tableExists('organization_registrations')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'name' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                ],
                'acronym' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => true,
                ],
                'campus' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                ],
                'description' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'officer_email' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                ],
                'officer_password' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                ],
                'adviser_name' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                ],
                'adviser_email' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                ],
                'adviser_token' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                ],
                'signature_path' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                ],
                'signed_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'status' => [
                    'type'       => 'ENUM',
                    'constraint' => ['pending_adviser', 'pending_admin', 'rejected'],
                    'default'    => 'pending_adviser',
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'updated_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey('adviser_token');
            $this->forge->createTable('organization_registrations');
        }
    }

    public function down()
    {
        $this->forge->dropTable('password_resets', true);
        $this->forge->dropTable('organization_registrations', true);
    }
}
