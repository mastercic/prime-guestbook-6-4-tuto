<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FileController extends AbstractController
{
    #[Route('/api/upload', name: 'api_file_upload', methods: ['POST'])]
    public function upload(Request $request): Response
    {
        $file = $request->files->get('file');

        if (!$file) {
            return $this->json([
                'status' => 'error',
                'message' => 'Aucun fichier reçu'
            ], Response::HTTP_BAD_REQUEST);
        }

        // Récupération infos du fichier
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $mimeType = $file->getMimeType();
        $size = $file->getSize();

        // Dossier de stockage
        $uploadDir = $this->getParameter('kernel.project_dir') . '/public/uploads';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Nouveau nom unique
        $newFilename = uniqid() . '.' . $extension;
        $file->move($uploadDir, $newFilename);

        return $this->json([
            'status' => 'success',
            'originalName' => $originalName,
            'storedName' => $newFilename,
            'extension' => $extension,
            'mimeType' => $mimeType,
            'size' => $size,
            'path' => '/uploads/' . $newFilename
        ]);
    }
}
