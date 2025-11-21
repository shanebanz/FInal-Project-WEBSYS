<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'email',
        'password',
        'first_name',
        'last_name',
        'user_type',
        'status',
        'verification_token',
        'reset_token',
        'reset_token_expires'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'email' => 'required|valid_email|is_unique[users.email,id,{id}]',
        'password' => 'required|min_length[8]',
        'first_name' => 'required|min_length[2]|max_length[100]',
        'last_name' => 'required|min_length[2]|max_length[100]',
        'user_type' => 'required|in_list[ITSO,Associate,Student]'
    ];
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = ['hashPassword'];
    protected $afterInsert = [];
    protected $beforeUpdate = ['hashPassword'];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }
        return $data;
    }

    public function getUserByEmail($email)
    {
        return $this->where('email', $email)->first();
    }

    public function verifyUser($token)
    {
        $user = $this->where('verification_token', $token)->first();
        if ($user) {
            return $this->update($user['id'], [
                'status' => 'active',
                'verification_token' => null
            ]);
        }
        return false;
    }

    public function getAllUsers($userType = null)
    {
        if ($userType) {
            return $this->where('user_type', $userType)->findAll();
        }
        return $this->findAll();
    }

    public function deactivateUser($userId)
    {
        return $this->update($userId, ['status' => 'inactive']);
    }

    public function activateUser($userId)
    {
        return $this->update($userId, ['status' => 'active']);
    }
}