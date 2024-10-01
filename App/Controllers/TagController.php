<?php

namespace App\Controllers;

use App\Models\Tag;

class TagController
{
    protected Tag $tagModel;

    public function __construct()
    {
        $this->tagModel = new Tag();
    }

    public function create()
    {
        $tagData = [
            'name' => $_POST['name']
        ];

        $result = $this->tagModel->create($tagData);

        echo json_encode([
            'status' => $result ? 'success' : 'error',
            'message' => $result ? 'Tag created successfully.' : 'Failed to create tag.'
        ]);
    }

    public function getAll()
    {
        $data = $this->tagModel->getAll();

        echo json_encode([
            'status' => 'success',
            'data' => $data
        ]);
    }
}
