<?php

namespace App\Controllers;

use App\Models\EquipmentModel;
use App\Models\BorrowingModel;
use App\Models\ReservationModel;
use App\Models\UserModel;

class Dashboard extends BaseController
{
    protected $equipmentModel;
    protected $borrowingModel;
    protected $reservationModel;
    protected $userModel;

    public function __construct()
    {
        $this->equipmentModel = new EquipmentModel();
        $this->borrowingModel = new BorrowingModel();
        $this->reservationModel = new ReservationModel();
        $this->userModel = new UserModel();
    }

    public function index()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $data = [
            'title' => 'Dashboard',
            'total_equipment' => $this->equipmentModel->where('status', 'active')->countAllResults(),
            'active_borrowings' => $this->borrowingModel->where('status', 'borrowed')->countAllResults(),
            'pending_reservations' => $this->reservationModel->where('status', 'pending')->countAllResults(),
            'total_users' => $this->userModel->where('status', 'active')->countAllResults(),
            'recent_borrowings' => $this->borrowingModel->getBorrowingWithDetails(),
            'recent_reservations' => $this->reservationModel->getReservationsWithDetails()
        ];

        // Limit to recent 5 items
        $data['recent_borrowings'] = array_slice($data['recent_borrowings'], 0, 5);
        $data['recent_reservations'] = array_slice($data['recent_reservations'], 0, 5);

        return view('dashboard/index', $data);
    }
}