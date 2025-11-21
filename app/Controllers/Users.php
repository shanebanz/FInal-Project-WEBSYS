<?php

namespace App\Controllers;

use App\Models\UserModel;

class Users extends BaseController
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

        $perPage = 10;
        $data = [
            'title' => 'User Management',
            'users' => $this->userModel->paginate($perPage),
            'pager' => $this->userModel->pager
        ];

        return view('users/index', $data);
    }

    public function create()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $data = ['title' => 'Add User'];

        if ($this->request->getMethod() === 'post') {
            $rules = [
                'email' => 'required|valid_email|is_unique[users.email]',
                'password' => 'required|min_length[8]|regex_match[/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#])[A-Za-z\d@$!%*?&#]/]',
                'confirm_password' => 'required|matches[password]',
                'first_name' => 'required|min_length[2]|max_length[100]|alpha_space',
                'last_name' => 'required|min_length[2]|max_length[100]|alpha_space',
                'user_type' => 'required|in_list[ITSO,Associate,Student]'
            ];

            $errors = [
                'password' => [
                    'regex_match' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.'
                ]
            ];

            if ($this->validate($rules, $errors)) {
                $userData = [
                    'email' => $this->request->getPost('email'),
                    'password' => $this->request->getPost('password'),
                    'first_name' => $this->request->getPost('first_name'),
                    'last_name' => $this->request->getPost('last_name'),
                    'user_type' => $this->request->getPost('user_type'),
                    'status' => 'active',
                    'verification_token' => null
                ];

                if ($this->userModel->insert($userData)) {
                    session()->setFlashdata('success', 'User added successfully!');
                    return redirect()->to('/users');
                }
            } else {
                $data['validation'] = $this->validator;
            }
        }

        return view('users/create', $data);
    }

    public function edit($id)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $user = $this->userModel->find($id);
        
        if (!$user) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = [
            'title' => 'Edit User',
            'user' => $user
        ];

        if ($this->request->getMethod() === 'post') {
            $rules = [
                'email' => "required|valid_email|is_unique[users.email,id,{$id}]",
                'first_name' => 'required|min_length[2]|max_length[100]|alpha_space',
                'last_name' => 'required|min_length[2]|max_length[100]|alpha_space',
                'user_type' => 'required|in_list[ITSO,Associate,Student]'
            ];

            // Add password validation only if password is provided
            if ($this->request->getPost('password')) {
                $rules['password'] = 'min_length[8]|regex_match[/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#])[A-Za-z\d@$!%*?&#]/]';
                $rules['confirm_password'] = 'matches[password]';
            }

            if ($this->validate($rules)) {
                $userData = [
                    'email' => $this->request->getPost('email'),
                    'first_name' => $this->request->getPost('first_name'),
                    'last_name' => $this->request->getPost('last_name'),
                    'user_type' => $this->request->getPost('user_type')
                ];

                // Only update password if provided
                if ($this->request->getPost('password')) {
                    $userData['password'] = $this->request->getPost('password');
                }

                if ($this->userModel->update($id, $userData)) {
                    session()->setFlashdata('success', 'User updated successfully!');
                    return redirect()->to('/users');
                }
            } else {
                $data['validation'] = $this->validator;
            }
        }

        return view('users/edit', $data);
    }

    public function view($id)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $user = $this->userModel->find($id);
        
        if (!$user) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = [
            'title' => 'User Details',
            'user' => $user
        ];

        return view('users/view', $data);
    }

    public function deactivate($id)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        // Prevent deactivating self
        if ($id == session()->get('user_id')) {
            session()->setFlashdata('error', 'You cannot deactivate your own account!');
            return redirect()->to('/users');
        }

        if ($this->userModel->deactivateUser($id)) {
            session()->setFlashdata('success', 'User deactivated successfully!');
        } else {
            session()->setFlashdata('error', 'Failed to deactivate user.');
        }

        return redirect()->to('/users');
    }

    public function activate($id)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        if ($this->userModel->activateUser($id)) {
            session()->setFlashdata('success', 'User activated successfully!');
        } else {
            session()->setFlashdata('error', 'Failed to activate user.');
        }

        return redirect()->to('/users');
    }
}