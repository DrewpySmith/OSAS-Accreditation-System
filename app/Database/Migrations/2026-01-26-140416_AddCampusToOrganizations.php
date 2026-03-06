<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCampusToOrganizations extends Migration
{
    public function up()
    {
        $fields = [
            'campus' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'acronym'
            ],
        ];
        $this->forge->addColumn('organizations', $fields);

        $docFields = [
            'campus' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'organization_id'
            ],
        ];
        $this->forge->addColumn('document_submissions', $docFields);
    }

    public function down()
    {
        $this->forge->dropColumn('organizations', 'campus');
        $this->forge->dropColumn('document_submissions', 'campus');
    }
}
