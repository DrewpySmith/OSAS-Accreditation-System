<?php

use CodeIgniter\Database\Migration;

class CreateMissingTables extends Migration
{
    public function up()
    {
        // 1. Academic Years Table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'year' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
            ],
            'start_date' => [
                'type' => 'DATE',
            ],
            'end_date' => [
                'type' => 'DATE',
            ],
            'is_current' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'is_active' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
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
        $this->forge->addUniqueKey('year');
        $this->forge->createTable('academic_years');

        // 2. System Settings Table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'setting_key' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'setting_value' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'setting_type' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'default' => 'text',
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
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
        $this->forge->addUniqueKey('setting_key');
        $this->forge->createTable('system_settings');

        // Initial Data Seeding
        $db = \Config\Database::connect();

        // Seed Academic Years
        $db->table('academic_years')->insertBatch([
            [
                'year' => '2023-2024',
                'start_date' => '2023-08-01',
                'end_date' => '2024-07-31',
                'is_current' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'year' => '2024-2025',
                'start_date' => '2024-08-01',
                'end_date' => '2025-07-31',
                'is_current' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'year' => '2025-2026',
                'start_date' => '2025-08-01',
                'end_date' => '2026-07-31',
                'is_current' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ]);

        // Seed System Settings
        $db->table('system_settings')->insertBatch([
            ['setting_key' => 'system_name', 'setting_value' => 'USG Accreditation Management System', 'setting_type' => 'text', 'description' => 'System name displayed in header', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['setting_key' => 'system_email', 'setting_value' => 'admin@usg-accreditation.com', 'setting_type' => 'email', 'description' => 'System email for notifications', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['setting_key' => 'max_file_size', 'setting_value' => '50', 'setting_type' => 'number', 'description' => 'Maximum file upload size in MB', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['setting_key' => 'allowed_file_types', 'setting_value' => 'pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png', 'setting_type' => 'text', 'description' => 'Allowed file extensions', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['setting_key' => 'auto_approve_documents', 'setting_value' => '0', 'setting_type' => 'boolean', 'description' => 'Automatically approve documents (0=No, 1=Yes)', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['setting_key' => 'notification_enabled', 'setting_value' => '1', 'setting_type' => 'boolean', 'description' => 'Enable email notifications (0=No, 1=Yes)', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['setting_key' => 'maintenance_mode', 'setting_value' => '0', 'setting_type' => 'boolean', 'description' => 'Put system in maintenance mode (0=No, 1=Yes)', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('system_settings');
        $this->forge->dropTable('academic_years');
    }
}
