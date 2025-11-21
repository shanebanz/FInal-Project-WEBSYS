<?php

namespace App\Controllers;

use App\Models\UserModel;

class Profile extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);

        $data = [
            'title' => 'My Profile',
            'user' => $user
        ];

        return view('profile/index', $data);
    }

    public function update()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $userId = session()->get('user_id');

        $rules = [
            'first_name' => 'required|min_length[2]|max_length[100]|alpha_space',
            'last_name' => 'required|min_length[2]|max_length[100]|alpha_space',
            'email' => "required|valid_email|is_unique[users.email,id,{$userId}]"
        ];

        if ($this->validate($rules)) {
            $userData = [
                'first_name' => $this->request->getPost('first_name'),
                'last_name' => $this->request->getPost('last_name'),
                'email' => $this->request->getPost('email')
            ];

            if ($this->userModel->update($userId, $userData)) {
                // Update session data
                session()->set([
                    'first_name' => $userData['first_name'],
                    'last_name' => $userData['last_name'],
                    'email' => $userData['email']
                ]);

                session()->setFlashdata('success', 'Profile updated successfully!');
            } else {
                session()->setFlashdata('error', 'Failed to update profile.');
            }
        } else {
            session()->setFlashdata('error', 'Validation failed. Please check your inputs.');
        }

        return redirect()->to('/profile');
    }

    public function changePassword()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $userId = session()->get('user_id');

        $rules = [
            'current_password' => 'required',
            'new_password' => 'required|min_length[8]|regex_match[/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#])[A-Za-z\d@$!%*?&#]/]',
            'confirm_password' => 'required|matches[new_password]'
        ];

        $errors = [
            'new_password' => [
                'regex_match' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.'
            ]
        ];

        if ($this->validate($rules, $errors)) {
            $user = $this->userModel->find($userId);
            
            // Verify current password
            if (password_verify($this->request->getPost('current_password'), $user['password'])) {
                $newPassword = $this->request->getPost('new_password');
                
                if ($this->userModel->update($userId, ['password' => $newPassword])) {
                    session()->setFlashdata('success', 'Password changed successfully!');
                } else {
                    session()->setFlashdata('error', 'Failed to change password.');
                }
            } else {
                session()->setFlashdata('error', 'Current password is incorrect.');
            }
        } else {
            session()->setFlashdata('error', 'Validation failed. Please check your inputs.');
        }

        return redirect()->to('/profile');
    }
}