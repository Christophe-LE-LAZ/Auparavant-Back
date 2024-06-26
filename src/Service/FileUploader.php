<?php

// src/Service/FileUploader.php
namespace App\Service;


use Symfony\Component\Validator\Constraints\File as AssertFile;  // Ajout de l'importation
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class FileUploader
{
    private $params;
    private $targetDirectory;
    private $validator;


    public function __construct(ParameterBagInterface $params, ValidatorInterface $validator, string $targetDirectory)
    {
        $this->params = $params;
        $this->targetDirectory = $targetDirectory;
        $this->validator = $validator;
    }

    public function uploadImage(UploadedFile $file): string
    {
        // on ajoute uniqid() afin de ne pas avoir 2 fichiers avec le même nom
        $newFilename = uniqid() . '.' . $file->getClientOriginalExtension();
        // enregistrement de l'image dans le dossier public du serveur
        $file->move($this->params->get('images_directory'), $newFilename);

        return $newFilename;
    }
    public function deletePictureFile(string $directory, string $filename)
{
    $filePath = $directory . '/' . $filename;

    // Vérifier si le fichier existe avant de le supprimer
    if (file_exists($filePath)) {
        unlink($filePath);
        return true;
    }
    return false;
}


public function validateImage($file)
{
    $constraints = [
        new AssertFile([
            'maxSize' => '5M',
            'mimeTypes' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp', ],
            'mimeTypesMessage' => 'Le fichier doit être une image au format JPEG, PNG, WEBP ou GIF.',
        ]),
    ];

    $violations = $this->validator->validate($file, $constraints);

    // management violations
    if (count($violations) > 0) {
        $errorMessages = [];
        foreach ($violations as $violation) {
            $errorMessages[] = $violation->getMessage();
        }

        return $errorMessages;
    }
    return null;
}
}