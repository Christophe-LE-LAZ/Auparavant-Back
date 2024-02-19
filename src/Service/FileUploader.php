<?php

// src/Service/FileUploader.php
namespace App\Service;

use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class FileUploader
{
    private $params;
    private $targetDirectory;
    private $slugger;

    public function __construct(ParameterBagInterface $params, string $targetDirectory, SluggerInterface $slugger)
    {
        $this->params = $params;
        $this->targetDirectory = $targetDirectory;
        $this->slugger = $slugger;
    }

    // public function upload(UploadedFile $file): string
    // {
    //     $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
    //     $safeFilename = $this->slugger->slug($originalFilename);
    //     $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

    //     try {
    //         $file->move($this->getTargetDirectory(), $fileName);
    //     } catch (FileException $e) {
    //         // ... handle exception if something happens during file upload
    //     }

    //     return $fileName;
    // }

    // public function getTargetDirectory(): string
    // {
    //     return $this->targetDirectory;
    // }

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


}