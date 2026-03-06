<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCalendarActivitiesTable extends Migration
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
            'activity_date' => [
                'type' => 'DATE',
            ],
            'activity_title' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'responsible_person' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'remarks' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['planned', 'ongoing', 'completed', 'cancelled'],
                'default' => 'planned',
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
        $this->forge->createTable('calendar_activities');
    }

    public function down()
    {
        $this->forge->dropTable('calendar_activities');
    }
}