<?php

namespace App\Service;

class File
{
    private $pathImgFolder;

    public function __construct(string  $publicFolderName)
    {
        $this->pathImgFolder = __DIR__ . '/../../' . $publicFolderName . "/assets/images/";
    }

    /**
     * Download the file
     * 
     * @return bool
     */
    public function isDownloadedFile($file):bool
    {
        // check if the name of the upload file exist in our folder
        if(is_file($this->pathImgFolder . $file->getClientOriginalName())){
            dump($this->pathImgFolder . $file->getClientOriginalName());
            return false;
        }
        // if it doesn't exist, we dowload him and save in our folder
        $file->move($this->pathImgFolder, $file->getClientOriginalName());

        return true;
    }

    /**
     * Get files by name
     * 
     * @return array
     */
    public function getFiles(string $fileName, bool $justFileName = false): array
    {
        // If it's a folder
        if(is_dir($this->pathImgFolder)){
             // if images are found             
            if (!empty(glob($this->pathImgFolder . $fileName))) {
                
                // Get all images whose name is the same than in the bdd.
                $allImg = glob($this->pathImgFolder . $fileName);

                if($justFileName){
                    // Get just the name of files
                    $allPictures = [];
                    foreach($allImg as $img){

                        $allPictures[] = basename($img);
                    }
                    return $allPictures;
                }

                return $allImg;
            }
        }

        return [];
    }

    /**
     * Delete files by name
     * 
     * @return bool
     */
    public function isDeleteFiles(array $allImg)
    {         
        foreach($allImg as $img) {
            if(!unlink($img)){

                return false;
            }
        }

        return true;
    }
}