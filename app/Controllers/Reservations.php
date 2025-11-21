<?php

namespace App\Controllers;

use App\Models\ReservationModel;
use App\Models\EquipmentModel;
use App\Models\UserModel;

class Reservations extends BaseController
{
    protected $reservationModel;
    protected $equipmentModel;
    protected $userModel;

    public function __construct()
    {
        $this->reservationModel = new ReservationModel();
        $this->equipmentModel = new EquipmentModel();
        $this->userModel = new UserModel();
    }

    public function index()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $data = [
            'title' => 'Reservation Management',
            'reservations' => $this->reservationModel->getReservationsWithDetails()
        ];

        return view('reservations/index', $data);
    }

    public function create()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $data = [
            'title' => 'New Reservation',
            'equipment' => $this->equipmentModel->getActiveEquipment(),
            'associates' => $this->userModel->where('user_type', 'Associate')
                                           ->where('status', 'active')
                                           ->findAll()
        ];

        if ($this->request->getMethod() === 'post') {
            $rules = [
                'user_id' => 'required|integer',
                'equipment_id' => 'required|integer',
                'quantity' => 'required|integer|greater_than[0]',
                'reservation_date' => 'required|valid_date',
                'start_time' => 'required',
                'end_time' => 'required',
                'purpose' => 'required|min_length[10]'
            ];

            if ($this->validate($rules)) {
                $reservationDate = $this->request->getPost('reservation_date');
                $today = date('Y-m-d');
                $tomorrow = date('Y-m-d', strtotime('+1 day'));
                
                // Check if reservation is at least 1 day in advance
                if ($reservationDate < $tomorrow) {
                    session()->setFlashdata('error', 'Reservations must be made at least one day in advance!');
                    return redirect()->back()->withInput();
                }

                $reservationData = [
                    'user_id' => $this->request->getPost('user_id'),
                    'equipment_id' => $this->request->getPost('equipment_id'),
                    'quantity' => $this->request->getPost('quantity'),
                    'reservation_date' => $reservationDate,
                    'start_time' => $this->request->getPost('start_time'),
                    'end_time' => $this->request->getPost('end_time'),
                    'purpose' => $this->request->getPost('purpose'),
                    'status' => 'pending'
                ];

                if ($this->reservationModel->insert($reservationData)) {
                    session()->setFlashdata('success', 'Reservation created successfully! Waiting for approval.');
                    return redirect()->to('/reservations');
                }
            } else {
                $data['validation'] = $this->validator;
            }
        }

        return view('reservations/create', $data);
    }

    public function view($id)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $reservation = $this->reservationModel->getReservationsWithDetails($id);
        
        if (!$reservation) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = [
            'title' => 'Reservation Details',
            'reservation' => $reservation
        ];

        return view('reservations/view', $data);
    }

    public function approve($id)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $reservation = $this->reservationModel->find($id);
        
        if (!$reservation) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if ($this->reservationModel->approveReservation($id, session()->get('user_id'))) {
            session()->setFlashdata('success', 'Reservation approved successfully!');
        } else {
            session()->setFlashdata('error', 'Failed to approve reservation.');
        }

        return redirect()->to('/reservations');
    }

    public function cancel($id)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $reservation = $this->reservationModel->find($id);
        
        if (!$reservation) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if ($this->reservationModel->cancelReservation($id)) {
            session()->setFlashdata('success', 'Reservation cancelled successfully!');
        } else {
            session()->setFlashdata('error', 'Failed to cancel reservation.');
        }

        return redirect()->to('/reservations');
    }
}