<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAccomplishmentReportsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'organization_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'academic_year' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
            ],
            'activity_title' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'narrative_report' => [
                'type' => 'TEXT',
            ],
            'pictorials' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'JSON array of image paths',
            ],
            'activity_designs' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'JSON array of design file paths',
            ],
            'evaluation_sheets' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'JSON array of evaluation sheet paths',
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['draft', 'submitted', 'approved', 'rejected'],
                'default' => 'draft',
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
        $this->forge->addKey('organization_id');
        $this->forge->addForeignKey('organization_id', 'organizations', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('accomplishment_reports');
    }

    public function down()
    {
        $this->forge->dropTable('accomplishment_reports');
    }
}