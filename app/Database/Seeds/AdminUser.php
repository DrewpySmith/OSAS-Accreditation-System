<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminUser extends Seeder
{
    public function run()
    {
        $data = [
            'username'      => 'admin',
            'password'      => password_hash('password', PASSWORD_DEFAULT),
            'role'          => 'admin',
            'organization_id' => null,
            'is_active'     => 1,
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
        ];

        $this->db->table('users')->insert($data);
    }
}
