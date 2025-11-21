<?php

namespace App\Controllers;

class Pages extends BaseController
{
    public function about()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $data = [
            'title' => 'About Us',
            'group_name' => 'Your Group Name Here', // Change this
            'members' => [
                [
                    'name' => 'Member 1 Name',
                    'role' => 'Project Leader',
                    'student_id' => '2021-XXXXX'
                ],
                [
                    'name' => 'Member 2 Name',
                    'role' => 'Backend Developer',
                    'student_id' => '2021-XXXXX'
                ],
                [
                    'name' => 'Member 3 Name',
                    'role' => 'Frontend Developer',
                    'student_id' => '2021-XXXXX'
                ],
                [
                    'name' => 'Member 4 Name',
                    'role' => 'Database Administrator',
                    'student_id' => '2021-XXXXX'
                ]
                // Add more members as needed
            ]
        ];

        return view('pages/about', $data);
    }
}