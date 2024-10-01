<?php

namespace App\Controllers;

class AuthController
{

    public function login()
    {

        echo json_encode([
            'status' => 'success',
            'message' => 'Login successful!'
        ]);
    }


    public function register()
    {
        echo json_encode([
            'status' => 'success',
            'message' => 'Registration successful!'
        ]);
    }


    public function logout()
    {

        echo json_encode([
            'status' => 'success',
            'message' => 'Logout successful!'
        ]);
    }
}
