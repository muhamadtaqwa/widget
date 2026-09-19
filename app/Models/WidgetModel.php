<?php

namespace App\Models;

use CodeIgniter\Model;

class WidgetModel extends Model
{
    protected $table            = 'widgets';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'journal_name',
        'widget_id',
        'settings',
        'is_whitelabel',
        'expired_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
