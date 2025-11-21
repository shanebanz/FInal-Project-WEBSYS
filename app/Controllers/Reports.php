<?php

namespace App\Controllers;

use App\Models\EquipmentModel;
use App\Models\BorrowingModel;
use App\Models\UserModel;

class Reports extends BaseController
{
    protected $equipmentModel;
    protected $borrowingModel;
    protected $userModel;

    public function __construct()
    {
        $this->equipmentModel = new EquipmentModel();
        $this->borrowingModel = new BorrowingModel();
        $this->userModel = new UserModel();
    }

    public function index()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $data = [
            'title' => 'Reports'
        ];

        return view('reports/index', $data);
    }

    public function activeEquipment()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $data = [
            'title' => 'Active Equipment Report',
            'equipment' => $this->equipmentModel->getEquipmentWithCategory()
        ];

        // Filter only active equipment
        $data['equipment'] = array_filter($data['equipment'], function($item) {
            return $item['status'] === 'active';
        });

        return view('reports/active_equipment', $data);
    }

    public function unusableEquipment()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $data = [
            'title' => 'Unusable Equipment Report',
            'equipment' => $this->equipmentModel->getEquipmentWithCategory()
        ];

        // Filter only inactive or maintenance equipment
        $data['equipment'] = array_filter($data['equipment'], function($item) {
            return $item['status'] === 'inactive' || $item['status'] === 'maintenance';
        });

        return view('reports/unusable_equipment', $data);
    }

    public function borrowingHistory()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $data = [
            'title' => 'User Borrowing History',
            'users' => $this->userModel->where('user_type !=', 'ITSO')
                                      ->where('status', 'active')
                                      ->findAll(),
            'borrowings' => []
        ];

        if ($this->request->getMethod() === 'post') {
            $userId = $this->request->getPost('user_id');
            
            if ($userId) {
                $data['borrowings'] = $this->borrowingModel->getUserBorrowingHistory($userId);
                $data['selected_user'] = $this->userModel->find($userId);
            }
        }

        return view('reports/borrowing_history', $data);
    }
}