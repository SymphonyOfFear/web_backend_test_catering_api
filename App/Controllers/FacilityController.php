<?php

namespace App\Controllers;

use App\Models\Facility;

class FacilityController
{
    protected Facility $facilityModel;

    public function __construct()
    {
        $this->facilityModel = new Facility();
    }

    public function create()
    {
        $facilityData = [
            'name' => $_POST['name'] ?? null
        ];

        $locationData = [
            'city' => $_POST['city'] ?? null,
            'address' => $_POST['address'] ?? null,
            'zip_code' => $_POST['zip_code'] ?? null,
            'country_code' => $_POST['country_code'] ?? null,
            'phone_number' => $_POST['phone_number'] ?? null
        ];

        $tagIds = isset($_POST['tag_ids']) ? explode(',', $_POST['tag_ids']) : [];

        // Validate input data
        if (empty($facilityData['name']) || empty($locationData['city'])) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Facility name and city are required.'
            ]);
            return;
        }

        try {
            $result = $this->facilityModel->createFacilityWithDetails($facilityData, $locationData, $tagIds);
            echo json_encode([
                'status' => $result ? 'success' : 'error',
                'message' => $result ? 'Facility created successfully.' : 'Failed to create facility.'
            ]);
        } catch (\Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function read($id = null)
    {
        $data = $id ? $this->facilityModel->getFacilityWithDetails($id) : $this->facilityModel->getAllFacilities();

        echo json_encode([
            'status' => 'success',
            'data' => $data
        ]);
    }

    public function update($id)
    {
        parse_str(file_get_contents("php://input"), $postData);

        $facilityData = [
            'name' => $postData['name'] ?? null
        ];

        if (empty($facilityData['name'])) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Facility name is required.'
            ]);
            return;
        }

        $result = $this->facilityModel->updateFacility($id, $facilityData);

        echo json_encode([
            'status' => $result ? 'success' : 'error',
            'message' => $result ? 'Facility updated successfully.' : 'Failed to update facility.'
        ]);
    }

    public function delete($id)
    {
        $result = $this->facilityModel->deleteFacility($id);

        echo json_encode([
            'status' => $result ? 'success' : 'error',
            'message' => $result ? 'Facility deleted successfully.' : 'Failed to delete facility.'
        ]);
    }

    public function search($query)
    {
        $data = $this->facilityModel->searchFacilities($query);

        echo json_encode([
            'status' => 'success',
            'data' => $data
        ]);
    }
}
