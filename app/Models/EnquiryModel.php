<?php

namespace App\Models;

use CodeIgniter\Model;

class EnquiryModel extends Model
{
    public const STATUSES = ['new', 'contacted', 'closed'];

    protected $table            = 'enquiries';
    protected $primaryKey       = 'id';
    protected $useTimestamps    = true;
    protected $allowedFields    = [
        'name', 'company', 'email', 'phone', 'service',
        'current_system', 'budget', 'timeline', 'preferred_contact', 'message', 'status',
    ];
}
