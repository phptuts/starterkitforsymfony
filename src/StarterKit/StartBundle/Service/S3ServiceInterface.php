<?php

namespace StarterKit\StartBundle\Service;


use Symfony\Component\HttpFoundation\File\UploadedFile;

interface S3ServiceInterface
{
    /**
     * Uploads a file to amazon s3 using
     *
     * @param UploadedFile $file
     * @param string $folderPath
     * @param string $fileName
     * @return string the url to the file
     */
    public function uploadFile(UploadedFile $file, $folderPath, $fileName);

}