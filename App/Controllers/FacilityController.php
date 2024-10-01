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


    public function searchFacilityByName()
    {
        $query = $_GET['query'] ?? null;

        // Debug statement om te controleren of de query parameter correct wordt ontvangen.
        // var_dump("Gekregen zoekopdracht:", $query);

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

    protected function sendSuccessResponse(string $message, array $data = [])
    {
        echo json_encode([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ]);
    }

    protected function sendErrorResponse(string $message)
    {
        echo json_encode([
            'status' => 'error',
            'message' => $message
        ]);
    }
}
