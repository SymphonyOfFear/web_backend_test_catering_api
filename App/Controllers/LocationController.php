<?php

namespace App\Controllers;

use App\Models\Location;

class LocationController
{
    protected Location $locationModel;

    public function __construct()
    {
        $this->locationModel = new Location();
    }

    public function create()
    {
        $locationData = [
            'city' => $_POST['city'],
            'address' => $_POST['address'],
            'zip_code' => $_POST['zip_code'],
            'country_code' => $_POST['country_code'],
            'phone_number' => $_POST['phone_number']
        ];

        $result = $this->locationModel->create($locationData);

        echo json_encode([
            'status' => $result ? 'success' : 'error',
            'message' => $result ? 'Location created successfully.' : 'Failed to create location.'
        ]);
    }

    public function getAll()
    {
        $data = $this->locationModel->getAll();

        echo json_encode([
            'status' => 'success',
            'data' => $data
        ]);
    }
}
