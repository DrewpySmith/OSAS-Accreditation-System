<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOrganizationChecklistsTable extends Migration
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
                'constraint' => '20',
            ],
            'application_letter' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'officer_list' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'commitment_forms' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'constitution_bylaws' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'org_structure' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'calendar_activities' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'financial_report' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'program_expenditures' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'accomplishment_report' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
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
        $this->forge->addUniqueKey(['organization_id', 'academic_year']);
        $this->forge->addForeignKey('organization_id', 'organizations', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('organization_checklists');
    }

    public function down()
    {
        $this->forge->dropTable('organization_checklists');
    }
}
