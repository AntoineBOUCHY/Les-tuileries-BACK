<?php

namespace App\Service;

use CURLFile;

class ZamzarApi
{
    private $apiKey;
    private $pathImgFolder;
    private $fileService;
    private $siteHost;
    
    public function __construct(string $apiKey, string $siteHost, string $publicFolderName, File $fileService)
    {
        $this->apiKey = $apiKey;
        $this->pathImgFolder = __DIR__ . '/../../' . $publicFolderName . "/assets/images/";
        $this->fileService = $fileService;
        $this->siteHost = $siteHost;
    }

    /**
     * Send a request to Zamzar API on the "jobs" endpoint
     *
     * @return void
     */
    public function jobRequest()
    {
        $endpoint = "https://sandbox.zamzar.com/v1/jobs";
        // Path of the file to convert
        $sourceFilePath = $this->siteHost . "assets/pdf/cartes.pdf";
        // Desired extension
        $targetFormat = "jpg";

        // CURLFile instance allowing to use CURLOPT_POSTFIELDS to download the file.
        $postData = [
        "source_file" => new CURLFile($sourceFilePath),
        "target_format" => $targetFormat
        ];

        // Initialize cURL session
        $ch = curl_init();
        // Set up the URL to call
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        // Set up the method of the request
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        // Download the file to convert
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        // curl_setopt($ch, CURLOPT_SAFE_UPLOAD, true); // Enable the @ prefix for uploading files
        // Return a response in string format
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // Set up the API authentification
        curl_setopt($ch, CURLOPT_USERPWD, $this->apiKey . ":test");
        // Execute the request
        $body = curl_exec($ch);
        // Close the cURL session
        curl_close($ch);

        // Convert the response into an associative array
        $response = json_decode($body, true);
        // Wait 2 secondes before sending a conversion request
        sleep(2);

        if($this->isConversionFile($response['id'])){

             return true;
        } else {

            return false;
        }
    }

    /**
     * Send a conversion request to the Zamzar API as long as the status was not equal to "successful" or the request was sent 5 times
     *
     * @param integer $responseId The id of the response of the job request
     * @return bool
     */
    public function isConversionFile(int $responseId): bool
    {
        // Send a conversion request and save the response in $job
        $job = $this->checkConversionRequest($responseId);

        // Count the number of conversion request
        $count = 1;
        while($job['status'] !== 'successful' && $count < 10){
            // Wait 2 secondes before sending a conversion request
            sleep(3);
            $job = $this->checkConversionRequest($responseId);

            $count++;
        }

        // If the file was converted
        if($job['status'] === 'successful'){
            // Get all files to download
            $allFiles = $job['target_files'];
            // Send a download request
            $this->downloadFileRequest($allFiles);

            return true;

        //The file was not converted 
        } else {
            // Renvoyer sur la page d'import d'un PDF
            return false;
        }
        dump('erreur de conversion dans ZAMZAR');
    }

    /**
     * Send a request to check if the file has been converted
     *
     * @param integer $responseId
     * @return void
     */
    public function checkConversionRequest(int $responseId)
    {
        $jobId = $responseId;
        $endpoint = 'https://sandbox.zamzar.com/v1/jobs/' . $jobId;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $this->apiKey . ":test");
        $body = curl_exec($ch);
        curl_close($ch);
        
        return json_decode($body, true);
    }

    /**
     * Send a download request
     *
     * @param array $allFiles The files to download
     * @return void
     */
    public function downloadFileRequest(array $allFiles=[])
    {
        $this->deleteFiles();
        $count = 1;
        foreach($allFiles as $file){
            // Check the extension to avoid downloading a file with another extension (.zip,...)
            if(str_ends_with($file['name'], ".jpg")){
                $fileID = $file['id'];
                // Path where we will download the file
                $localFilename = $this->pathImgFolder . 'menu_page_' . $count . '.jpg';
                $endpoint = 'https://sandbox.zamzar.com/v1/files/' . $fileID . '/content';
                
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $endpoint);
                curl_setopt($ch, CURLOPT_USERPWD, $this->apiKey . ":test");
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
                // Open or create the file (w =  create the file if it does not exist and open it in write mode, b = force writing in binary without translating the data)
                $fh = fopen($localFilename, "wb");
                // Write in the file
                curl_setopt($ch, CURLOPT_FILE, $fh);
                curl_exec($ch);
                curl_close($ch);

                $count++;
            }
        }
        sleep(2);
        // Delete the files in the Zamzar server
        $this->deleteFileRequest($allFiles);
    }

    /**
     * Delete all images whose name starts with 'menu_page_'
     *
     * @return array
     */
    public function deleteFiles()
    {
        // If it's a folder
        if(is_dir($this->pathImgFolder)){
            dump(is_dir($this->pathImgFolder));
             // if images are found
            $allFiles = $this->fileService->getFiles("menu_page_*.jpg");
            $this->fileService->isDeleteFiles($allFiles);
        }
    }

    /**
     * Send a request to delete a file in the Zamzar server
     *
     * @param array $allFiles The files to delete
     * @return void
     */
    public function deleteFileRequest(array $allFiles)
    {
        foreach ($allFiles as $file) {
            $fileId = $file['id'];
            $endpoint = 'https://api.zamzar.com/v1/files/' . $fileId;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $endpoint);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERPWD, $this->apiKey . ":test");
            $body = curl_exec($ch);
            curl_close($ch);
        }
    }
}
