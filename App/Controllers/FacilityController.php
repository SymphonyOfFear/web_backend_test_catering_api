<?php

namespace App\Controllers;

use App\Models\Facility;
use Exception;

class FacilityController
{
    protected Facility $facilityModel;

    public function __construct()
    {
        $this->facilityModel = new Facility();
    }

    // Methode om een faciliteit toe te voegen
    public function addFacility()
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

        if (empty($facilityData['name']) || empty($locationData['city'])) {
            $this->sendErrorResponse('Naam en stad van de faciliteit zijn verplicht.');
            return;
        }

        try {
            $result = $this->facilityModel->addFacility($facilityData, $locationData, $tagIds);
            $this->sendSuccessResponse('Faciliteit succesvol aangemaakt.');
        } catch (Exception $e) {
            $this->sendErrorResponse('Fout bij het aanmaken van de faciliteit: ' . $e->getMessage());
        }
    }

    // Methode om alle faciliteiten weer te geven
    public function listFacilities()
    {
        try {
            $data = $this->facilityModel->listFacilities();
            $this->sendSuccessResponse('Faciliteiten succesvol opgehaald.', $data);
        } catch (Exception $e) {
            $this->sendErrorResponse('Fout bij het ophalen van de faciliteiten: ' . $e->getMessage());
        }
    }

    // Methode om een specifieke faciliteit op ID op te halen
    public function getFacilityById($id)
    {
        // Controleer of de ID numeriek is en niet 'search'
        if (!is_numeric($id) || $id === 'search') {
            $this->sendErrorResponse('Ongeldige route: "search" kan niet als ID worden gebruikt.');
            return;
        }

        try {
            $data = $this->facilityModel->getFacilityById((int) $id);
            if ($data) {
                $this->sendSuccessResponse('Faciliteit succesvol opgehaald.', $data);
            } else {
                $this->sendErrorResponse('Faciliteit niet gevonden.');
            }
        } catch (Exception $e) {
            $this->sendErrorResponse('Fout bij het ophalen van de faciliteit: ' . $e->getMessage());
        }
    }

    // Methode om een faciliteit te bewerken
    public function editFacility($id)
    {
        if (!is_numeric($id)) {
            $this->sendErrorResponse('Ongeldige ID-indeling.');
            return;
        }

        parse_str(file_get_contents("php://input"), $postData);

        $facilityData = [
            'name' => $postData['name'] ?? null
        ];

        if (empty($facilityData['name'])) {
            $this->sendErrorResponse('Naam van de faciliteit is verplicht.');
            return;
        }

        try {
            $result = $this->facilityModel->editFacility((int) $id, $facilityData);
            if ($result) {
                $this->sendSuccessResponse('Faciliteit succesvol bijgewerkt.');
            } else {
                $this->sendErrorResponse('Fout bij het bijwerken van de faciliteit.');
            }
        } catch (Exception $e) {
            $this->sendErrorResponse('Fout bij het bijwerken van de faciliteit: ' . $e->getMessage());
        }
    }

    // Methode om een faciliteit te verwijderen
    public function removeFacility($id)
    {
        if (!is_numeric($id)) {
            $this->sendErrorResponse('Ongeldige ID-indeling.');
            return;
        }

        try {
            $result = $this->facilityModel->removeFacility((int) $id);
            if ($result) {
                $this->sendSuccessResponse('Faciliteit succesvol verwijderd.');
            } else {
                $this->sendErrorResponse('Fout bij het verwijderen van de faciliteit.');
            }
        } catch (Exception $e) {
            $this->sendErrorResponse('Fout bij het verwijderen van de faciliteit: ' . $e->getMessage());
        }
    }

    // Methode om faciliteiten te zoeken op naam
    public function searchFacilityByName()
    {
        $query = $_GET['query'] ?? null;

        if (empty($query)) {
            $this->sendErrorResponse('Zoekopdracht is verplicht.');
            return;
        }

        try {
            $data = $this->facilityModel->searchFacilityByName($query);
            if (!empty($data)) {
                $this->sendSuccessResponse('Faciliteiten gevonden.', $data);
            } else {
                $this->sendErrorResponse('Geen faciliteiten gevonden die overeenkomen met de zoekopdracht.');
            }
        } catch (Exception $e) {
            $this->sendErrorResponse('Zoekopdracht mislukt: ' . $e->getMessage());
        }
    }

    // Hulpmethode om een succesvolle respons te verzenden
    protected function sendSuccessResponse(string $message, array $data = [])
    {
        echo json_encode([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ]);
    }

    // Hulpmethode om een foutrespons te verzenden
    protected function sendErrorResponse(string $message)
    {
        echo json_encode([
            'status' => 'error',
            'message' => $message
        ]);
    }
}
