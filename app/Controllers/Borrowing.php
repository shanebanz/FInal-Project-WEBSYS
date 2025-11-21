<?php

namespace App\Controllers;

use App\Models\BorrowingModel;
use App\Models\EquipmentModel;
use App\Models\UserModel;

class Borrowings extends BaseController
{
    protected $borrowingModel;
    protected $equipmentModel;
    protected $userModel;

    public function __construct()
    {
        $this->borrowingModel = new BorrowingModel();
        $this->equipmentModel = new EquipmentModel();
        $this->userModel = new UserModel();
    }

    public function index()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $perPage = 10;
        $data = [
            'title' => 'Borrowing Management',
            'borrowings' => $this->borrowingModel->getBorrowingWithDetails(),
            'pager' => $this->borrowingModel->pager
        ];

        return view('borrowings/index', $data);
    }

    public function create()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $data = [
            'title' => 'New Borrowing',
            'equipment' => $this->equipmentModel->getActiveEquipment(),
            'borrowers' => $this->userModel->where('user_type !=', 'ITSO')
                                          ->where('status', 'active')
                                          ->findAll()
        ];

        if ($this->request->getMethod() === 'post') {
            $rules = [
                'borrower_id' => 'required|integer',
                'equipment_id' => 'required|integer',
                'quantity' => 'required|integer|greater_than[0]',
                'borrow_date' => 'required|valid_date',
                'expected_return_date' => 'required|valid_date'
            ];

            if ($this->validate($rules)) {
                $equipmentId = $this->request->getPost('equipment_id');
                $quantity = $this->request->getPost('quantity');
                
                // Check if equipment is available
                $equipment = $this->equipmentModel->find($equipmentId);
                
                if ($equipment['available_quantity'] < $quantity) {
                    session()->setFlashdata('error', 'Insufficient equipment available!');
                    return redirect()->back()->withInput();
                }

                $borrowingData = [
                    'borrower_id' => $this->request->getPost('borrower_id'),
                    'equipment_id' => $equipmentId,
                    'quantity' => $quantity,
                    'borrow_date' => $this->request->getPost('borrow_date'),
                    'expected_return_date' => $this->request->getPost('expected_return_date'),
                    'status' => 'borrowed',
                    'notes' => $this->request->getPost('notes'),
                    'issued_by' => session()->get('user_id')
                ];

                if ($this->borrowingModel->insert($borrowingData)) {
                    // Update equipment availability
                    $this->equipmentModel->updateAvailability($equipmentId, $quantity, 'decrease');
                    
                    // Send email notification
                    $borrower = $this->userModel->find($this->request->getPost('borrower_id'));
                    $this->sendBorrowingEmail($borrower, $equipment, 'borrowed');
                    
                    session()->setFlashdata('success', 'Equipment borrowed successfully!');
                    return redirect()->to('/borrowings');
                }
            } else {
                $data['validation'] = $this->validator;
            }
        }

        return view('borrowings/create', $data);
    }

    public function return($id)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $borrowing = $this->borrowingModel->getBorrowingWithDetails($id);
        
        if (!$borrowing) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = [
            'title' => 'Return Equipment',
            'borrowing' => $borrowing
        ];

        if ($this->request->getMethod() === 'post') {
            if ($this->borrowingModel->returnEquipment($id, session()->get('user_id'))) {
                // Update equipment availability
                $this->equipmentModel->updateAvailability(
                    $borrowing['equipment_id'], 
                    $borrowing['quantity'], 
                    'increase'
                );
                
                // Send email notification
                $borrower = $this->userModel->find($borrowing['borrower_id']);
                $equipment = $this->equipmentModel->find($borrowing['equipment_id']);
                $this->sendBorrowingEmail($borrower, $equipment, 'returned');
                
                session()->setFlashdata('success', 'Equipment returned successfully!');
                return redirect()->to('/borrowings');
            }
        }

        return view('borrowings/return', $data);
    }

    public function view($id)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $borrowing = $this->borrowingModel->getBorrowingWithDetails($id);
        
        if (!$borrowing) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = [
            'title' => 'Borrowing Details',
            'borrowing' => $borrowing
        ];

        return view('borrowings/view', $data);
    }

    private function sendBorrowingEmail($borrower, $equipment, $action)
    {
        $emailService = \Config\Services::email();
        
        $subject = ($action === 'borrowed') ? 'Equipment Borrowed' : 'Equipment Returned';
        
        $message = "
            <h2>{$subject}</h2>
            <p>Dear {$borrower['first_name']} {$borrower['last_name']},</p>
            <p>This is to confirm that the following equipment has been {$action}:</p>
            <ul>
                <li><strong>Equipment:</strong> {$equipment['name']}</li>
                <li><strong>Equipment ID:</strong> {$equipment['equipment_id']}</li>
                <li><strong>Date:</strong> " . date('F d, Y') . "</li>
            </ul>
            <p>Thank you for using the ITSO Equipment Management System.</p>
        ";
        
        $emailService->setTo($borrower['email']);
        $emailService->setSubject($subject . ' - ITSO Equipment System');
        $emailService->setMessage($message);
        
        return $emailService->send();
    }
}