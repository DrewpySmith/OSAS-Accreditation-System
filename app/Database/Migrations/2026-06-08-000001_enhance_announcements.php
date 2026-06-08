<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EnhanceAnnouncements extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        // Add new columns to announcements table
        $this->forge->addColumn('announcements', [
            'summary' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
            ],
            'content' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'priority' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'default' => 'medium',
            ],
            'category' => [
                'type' => 'VARCHAR',
                'constraint' => 30,
                'default' => 'general',
            ],
            'author' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'is_pinned' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'expires_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        // Create announcement_attachments table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'announcement_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'file_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'file_path' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
            ],
            'file_type' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'file_size' => [
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => true,
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
        $this->forge->addKey('announcement_id');
        $this->forge->addForeignKey('announcement_id', 'announcements', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('announcement_attachments');

        // Create announcement_comments table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'announcement_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'comment' => [
                'type' => 'TEXT',
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
        $this->forge->addKey('announcement_id');
        $this->forge->addForeignKey('announcement_id', 'announcements', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('announcement_comments');
    }

    public function down()
    {
        $this->forge->dropTable('announcement_comments');
        $this->forge->dropTable('announcement_attachments');
        $this->forge->dropColumn('announcements', [
            'summary', 'content', 'priority', 'category', 'author', 'is_pinned', 'expires_at'
        ]);
    }
}
