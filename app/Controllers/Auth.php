<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class Auth extends Controller
{
    protected $helpers = ['form', 'url'];

    public function login()
    {
        if (session()->get('logged_in')) {
            return redirect()->to('/dashboard');
        }

        $data = [];
        
        if ($this->request->getMethod() === 'post') {
            $rules = [
                'email' => 'required|valid_email',
                'password' => 'required|min_length[8]'
            ];

            if ($this->validate($rules)) {
                $userModel = new UserModel();
                $email = $this->request->getPost('email');
                $password = $this->request->getPost('password');

                $user = $userModel->getUserByEmail($email);

                if ($user && password_verify($password, $user['password'])) {
                    if ($user['status'] !== 'active') {
                        $data['error'] = 'Your account is not active. Please verify your email.';
                    } elseif ($user['user_type'] !== 'ITSO') {
                        $data['error'] = 'Only ITSO personnel can access the system.';
                    } else {
                        $sessionData = [
                            'user_id' => $user['id'],
                            'email' => $user['email'],
                            'first_name' => $user['first_name'],
                            'last_name' => $user['last_name'],
                            'user_type' => $user['user_type'],
                            'logged_in' => true
                        ];
                        session()->set($sessionData);
                        return redirect()->to('/dashboard');
                    }
                } else {
                    $data['error'] = 'Invalid email or password.';
                }
            } else {
                $data['validation'] = $this->validator;
            }
        }

        return view('auth/login', $data);
    }

    public function register()
    {
        $data = [];

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
                $userModel = new UserModel();
                
                $verificationToken = bin2hex(random_bytes(32));
                
                $userData = [
                    'email' => $this->request->getPost('email'),
                    'password' => $this->request->getPost('password'),
                    'first_name' => $this->request->getPost('first_name'),
                    'last_name' => $this->request->getPost('last_name'),
                    'user_type' => $this->request->getPost('user_type'),
                    'status' => 'pending',
                    'verification_token' => $verificationToken
                ];

                if ($userModel->insert($userData)) {
                    // Send verification email
                    $this->sendVerificationEmail($userData['email'], $verificationToken);
                    
                    session()->setFlashdata('success', 'Registration successful! Please check your email to verify your account.');
                    return redirect()->to('/login');
                } else {
                    $data['error'] = 'Registration failed. Please try again.';
                }
            } else {
                $data['validation'] = $this->validator;
            }
        }

        return view('auth/register', $data);
    }

    private function sendVerificationEmail($email, $token)
    {
        $emailService = \Config\Services::email();
        
        $verificationLink = base_url("verify-email/{$token}");
        
        $message = "
            <h2>Email Verification</h2>
            <p>Thank you for registering with ITSO Equipment Management System.</p>
            <p>Please click the link below to verify your email address:</p>
            <p><a href='{$verificationLink}'>Verify Email</a></p>
            <p>If you didn't register for this account, please ignore this email.</p>
        ";
        
        $emailService->setTo($email);
        $emailService->setSubject('Verify Your Email - ITSO Equipment System');
        $emailService->setMessage($message);
        
        return $emailService->send();
    }

    public function verifyEmail($token)
    {
        $userModel = new UserModel();
        
        if ($userModel->verifyUser($token)) {
            session()->setFlashdata('success', 'Email verified successfully! You can now log in.');
        } else {
            session()->setFlashdata('error', 'Invalid or expired verification token.');
        }
        
        return redirect()->to('/login');
    }

    public function forgotPassword()
    {
        $data = [];

        if ($this->request->getMethod() === 'post') {
            $email = $this->request->getPost('email');
            $userModel = new UserModel();
            $user = $userModel->getUserByEmail($email);

            if ($user) {
                $resetToken = bin2hex(random_bytes(32));
                $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
                
                $userModel->update($user['id'], [
                    'reset_token' => $resetToken,
                    'reset_token_expires' => $expiry
                ]);

                $this->sendPasswordResetEmail($email, $resetToken);
                session()->setFlashdata('success', 'Password reset link has been sent to your email.');
            } else {
                $data['error'] = 'Email not found.';
            }
        }

        return view('auth/forgot_password', $data);
    }

    private function sendPasswordResetEmail($email, $token)
    {
        $emailService = \Config\Services::email();
        
        $resetLink = base_url("reset-password/{$token}");
        
        $message = "
            <h2>Password Reset Request</h2>
            <p>You requested to reset your password for ITSO Equipment Management System.</p>
            <p>Click the link below to reset your password (valid for 1 hour):</p>
            <p><a href='{$resetLink}'>Reset Password</a></p>
            <p>If you didn't request this, please ignore this email.</p>
        ";
        
        $emailService->setTo($email);
        $emailService->setSubject('Password Reset - ITSO Equipment System');
        $emailService->setMessage($message);
        
        return $emailService->send();
    }

    public function resetPassword($token = null)
    {
        $data = ['token' => $token];
        $userModel = new UserModel();

        if ($this->request->getMethod() === 'post') {
            $token = $this->request->getPost('token');
            $password = $this->request->getPost('password');
            $confirmPassword = $this->request->getPost('confirm_password');

            $user = $userModel->where('reset_token', $token)
                              ->where('reset_token_expires >', date('Y-m-d H:i:s'))
                              ->first();

            if ($user && $password === $confirmPassword) {
                $userModel->update($user['id'], [
                    'password' => $password,
                    'reset_token' => null,
                    'reset_token_expires' => null
                ]);

                session()->setFlashdata('success', 'Password reset successful! You can now log in.');
                return redirect()->to('/login');
            } else {
                $data['error'] = 'Invalid or expired token, or passwords do not match.';
            }
        }

        return view('auth/reset_password', $data);
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}