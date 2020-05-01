<?php


namespace App\Helper;


use Exception;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

trait FileHelper
{
    public function getFileName(UploadedFile $file){
        return $file->getClientOriginalName();
    }

    /**
     * @param UploadedFile $file
     * @return string
     * @throws Exception
     */
    public function getGeneratedName(UploadedFile $file){
        $random = bin2hex(random_bytes(8));
        return $random. '.' . $file->getClientOriginalExtension();
    }

    public function moveFile(UploadedFile $file, $path, $randomName){
        if ($file->move($path, $randomName)){
            return true;
        }
        return false;
    }

    public function removeFile($path){
        $fs = new Filesystem();
        $fs->remove($path);
    }
}