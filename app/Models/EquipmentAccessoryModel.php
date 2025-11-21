<?php

namespace App\Models;

use CodeIgniter\Model;

class EquipmentAccessoryModel extends Model
{
    protected $table = 'equipment_accessories';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'equipment_id',
        'accessory_name',
        'quantity_per_equipment'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected $validationRules = [
        'equipment_id' => 'required|integer',
        'accessory_name' => 'required|min_length[3]|max_length[255]',
        'quantity_per_equipment' => 'required|integer|greater_than[0]'
    ];

    public function getAccessoriesByEquipment($equipmentId)
    {
        return $this->where('equipment_id', $equipmentId)->findAll();
    }

    public function deleteByEquipment($equipmentId)
    {
        return $this->where('equipment_id', $equipmentId)->delete();
    }
}