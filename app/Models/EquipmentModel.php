<?php

namespace App\Models;

use CodeIgniter\Model;

class EquipmentModel extends Model
{
    protected $table = 'equipment';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'equipment_id',
        'category_id',
        'name',
        'description',
        'total_quantity',
        'available_quantity',
        'status',
        'image'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'equipment_id' => 'required|is_unique[equipment.equipment_id,id,{id}]',
        'category_id' => 'required|integer',
        'name' => 'required|min_length[3]|max_length[255]',
        'total_quantity' => 'required|integer|greater_than_equal_to[0]',
        'available_quantity' => 'required|integer|greater_than_equal_to[0]'
    ];

    public function getEquipmentWithCategory($id = null)
    {
        $builder = $this->db->table($this->table);
        $builder->select('equipment.*, equipment_categories.name as category_name');
        $builder->join('equipment_categories', 'equipment_categories.id = equipment.category_id');
        
        if ($id) {
            $builder->where('equipment.id', $id);
            return $builder->get()->getRowArray();
        }
        
        return $builder->get()->getResultArray();
    }

    public function getActiveEquipment()
    {
        return $this->where('status', 'active')
                    ->where('available_quantity >', 0)
                    ->findAll();
    }

    public function updateAvailability($equipmentId, $quantity, $operation = 'decrease')
    {
        $equipment = $this->find($equipmentId);
        if (!$equipment) {
            return false;
        }

        if ($operation === 'decrease') {
            $newQuantity = $equipment['available_quantity'] - $quantity;
        } else {
            $newQuantity = $equipment['available_quantity'] + $quantity;
        }

        if ($newQuantity < 0 || $newQuantity > $equipment['total_quantity']) {
            return false;
        }

        return $this->update($equipmentId, ['available_quantity' => $newQuantity]);
    }

    public function getEquipmentByCategory($categoryId)
    {
        return $this->where('category_id', $categoryId)
                    ->where('status', 'active')
                    ->findAll();
    }
}