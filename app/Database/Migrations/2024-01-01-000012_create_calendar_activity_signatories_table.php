<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCalendarActivitySignatoriesTable extends Migration
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
            'head_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'adviser_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
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
        $this->forge->addKey('organization_id');
        $this->forge->addKey('academic_year');
        $this->forge->addUniqueKey(['organization_id', 'academic_year'], 'org_year_unique');
        $this->forge->addForeignKey('organization_id', 'organizations', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('calendar_activity_signatories');
    }

    public function down()
    {
        $this->forge->dropTable('calendar_activity_signatories');
    }
}
