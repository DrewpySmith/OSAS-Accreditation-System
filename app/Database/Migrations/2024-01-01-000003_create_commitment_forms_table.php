<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCommitmentFormsTable extends Migration
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
            'officer_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'position' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'organization_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'academic_year' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
            ],
            'signed_date' => [
                'type' => 'DATE',
            ],
            'signature' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
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
        $this->forge->createTable('commitment_forms');
    }

    public function down()
    {
        $this->forge->dropTable('commitment_forms');
    }
}