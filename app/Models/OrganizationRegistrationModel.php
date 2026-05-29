<?php

namespace App\Models;

use CodeIgniter\Model;

class OrganizationRegistrationModel extends Model
{
    protected $table            = 'organization_registrations';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'acronym',
        'campus',
        'description',
        'officer_email',
        'officer_password',
        'adviser_name',
        'adviser_email',
        'adviser_token',
        'signature_path',
        'signed_at',
        'status',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Generate secure token for adviser verification
     */
    public function generateToken(): string
    {
        return bin2hex(random_bytes(16));
    }
}
