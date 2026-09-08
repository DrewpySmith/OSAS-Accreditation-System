<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RevampAnnouncements extends Migration
{
    public function up()
    {
        $fields = [
            'priority' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'normal', 'after' => 'target_value'],
            'status' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'published', 'after' => 'priority'],
            'is_pinned' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0, 'after' => 'status'],
            'publish_at' => ['type' => 'DATETIME', 'null' => true, 'after' => 'is_pinned'],
            'expires_at' => ['type' => 'DATETIME', 'null' => true, 'after' => 'publish_at'],
            'action_label' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'after' => 'expires_at'],
            'action_url' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'after' => 'action_label'],
        ];
        foreach ($fields as $field => $attr) {
            if (!$this->db->fieldExists($field, 'announcements')) {
                $this->forge->addColumn('announcements', [$field => $attr]);
            }
        }

        if (!$this->db->tableExists('announcement_targets')) {
            $this->forge->addField([
                'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'announcement_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'target_type' => ['type' => 'VARCHAR', 'constraint' => 20],
                'target_value' => ['type' => 'VARCHAR', 'constraint' => 100],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey('announcement_id');
            $this->forge->addForeignKey('announcement_id', 'announcements', 'id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('announcement_targets');
        }

        if (!$this->db->tableExists('announcement_attachments')) {
            $this->forge->addField([
                'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'announcement_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'file_name' => ['type' => 'VARCHAR', 'constraint' => 255],
                'file_path' => ['type' => 'VARCHAR', 'constraint' => 500],
                'mime_type' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'file_size' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey('announcement_id');
            $this->forge->addForeignKey('announcement_id', 'announcements', 'id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('announcement_attachments');
        }

        if (!$this->db->fieldExists('acknowledged_at', 'announcement_reads')) {
            $this->forge->addColumn('announcement_reads', [
                'acknowledged_at' => ['type' => 'DATETIME', 'null' => true, 'after' => 'read_at'],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('announcement_attachments')) {
            $this->forge->dropTable('announcement_attachments');
        }
        if ($this->db->tableExists('announcement_targets')) {
            $this->forge->dropTable('announcement_targets');
        }
        $drop = ['priority','status','is_pinned','publish_at','expires_at','action_label','action_url'];
        foreach ($drop as $col) {
            if ($this->db->fieldExists($col, 'announcements')) {
                $this->forge->dropColumn('announcements', $col);
            }
        }
        if ($this->db->fieldExists('acknowledged_at', 'announcement_reads')) {
            $this->forge->dropColumn('announcement_reads', 'acknowledged_at');
        }
    }
}
