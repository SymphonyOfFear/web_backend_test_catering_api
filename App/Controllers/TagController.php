<?php

namespace App\Controllers;

use App\Models\Tag;
use Exception;

class TagController
{
    protected Tag $tagModel;

    public function __construct()
    {
        $this->tagModel = new Tag();
    }

    // Tag aanmaken
    public function create()
    {
        $name = $_POST['name'] ?? null;

        if (empty($name)) {
            $this->sendErrorResponse('Tagnaam is verplicht.');
            return;
        }

        try {
            $result = $this->tagModel->addTag($name);
            if ($result) {
                $this->sendSuccessResponse('Tag succesvol aangemaakt.');
            } else {
                $this->sendErrorResponse('Fout bij het aanmaken van tag.');
            }
        } catch (Exception $e) {
            $this->sendErrorResponse($e->getMessage());
        }
    }

    // Alle tags ophalen
    public function getAll()
    {
        try {
            $data = $this->tagModel->getAllTags();
            $this->sendSuccessResponse('Tags succesvol opgehaald.', $data);
        } catch (Exception $e) {
            $this->sendErrorResponse('Fout bij het ophalen van tags: ' . $e->getMessage());
        }
    }

    // Succesrespons versturen
    protected function sendSuccessResponse(string $message, array $data = [])
    {
        echo json_encode([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ]);
    }

    // Foutrespons versturen
    protected function sendErrorResponse(string $message)
    {
        echo json_encode([
            'status' => 'error',
            'message' => $message
        ]);
    }
}
