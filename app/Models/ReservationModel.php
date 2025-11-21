<?php

namespace App\Models;

use CodeIgniter\Model;

class BorrowingModel extends Model
{
    protected $table = 'borrowings';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $allowedFields = [
        'borrower_id',
        'equipment_id',
        'quantity',
        'borrow_date',
        'expected_return_date',
        'actual_return_date',
        'status',
        'notes',
        'issued_by',
        'returned_to'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getBorrowingWithDetails($id = null)
    {
        $builder = $this->db->table($this->table);
        $builder->select('borrowings.*, 
                         equipment.name as equipment_name, 
                         equipment.equipment_id,
                         users.first_name, 
                         users.last_name, 
                         users.email,
                         issuer.first_name as issued_by_name,
                         returner.first_name as returned_by_name');
        $builder->join('equipment', 'equipment.id = borrowings.equipment_id');
        $builder->join('users', 'users.id = borrowings.borrower_id');
        $builder->join('users as issuer', 'issuer.id = borrowings.issued_by', 'left');
        $builder->join('users as returner', 'returner.id = borrowings.returned_to', 'left');
        
        if ($id) {
            $builder->where('borrowings.id', $id);
            return $builder->get()->getRowArray();
        }
        
        $builder->orderBy('borrowings.created_at', 'DESC');
        return $builder->get()->getResultArray();
    }

    public function getActiveBorrowings()
    {
        return $this->getBorrowingWithDetails();
    }

    public function getUserBorrowingHistory($userId)
    {
        $builder = $this->db->table($this->table);
        $builder->select('borrowings.*, equipment.name as equipment_name, equipment.equipment_id');
        $builder->join('equipment', 'equipment.id = borrowings.equipment_id');
        $builder->where('borrowings.borrower_id', $userId);
        $builder->orderBy('borrowings.created_at', 'DESC');
        return $builder->get()->getResultArray();
    }

    public function returnEquipment($borrowingId, $returnedBy)
    {
        return $this->update($borrowingId, [
            'actual_return_date' => date('Y-m-d H:i:s'),
            'status' => 'returned',
            'returned_to' => $returnedBy
        ]);
    }
}