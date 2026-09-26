<?php

namespace App\Models;

use CodeIgniter\Model;

class EnquiryModel extends Model
{
    protected $table            = 'enquiries';
    protected $primaryKey       = 'id';
    protected $useTimestamps    = true;
    protected $allowedFields    = [
        'name', 'company', 'email', 'phone', 'service',
        'current_system', 'budget', 'timeline', 'preferred_contact', 'message',
    ];
}
