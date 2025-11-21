<?php

namespace App\Models;

use CodeIgniter\Model;

class EquipmentCategoryModel extends Model
{
    protected $table = 'equipment_categories';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'name',
        'description'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected $validationRules = [
        'name' => 'required|min_length[3]|max_length[100]'
    ];

    protected $validationMessages = [];
    protected $skipValidation = false;
}