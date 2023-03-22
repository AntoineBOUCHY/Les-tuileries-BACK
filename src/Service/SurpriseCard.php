<?php

namespace App\Service;

use Spipu\Html2Pdf\Html2Pdf;

class SurpriseCard
{
    private $siteHost;
    private $publicFolderName;

    public function __construct(string $siteHost, string $publicFolderName)
    {
        $this->siteHost = $siteHost;
        $this->publicFolderName = $publicFolderName;
    }
    /**
     * Create a card from the template
     *
     * @param Html2Pdf $html2pdf The instance of Html2Pdf
     * @param string $gifter The gifter
     * @param string $receiver The receiver
     * @param integer $amount The amount
     * @param string $limiteDate The limite date
     * @param integer $cardId The id of the card
     * @return Html2Pdf
     */
    public function templateCard(Html2Pdf $html2pdf, string $gifter, string $receiver, int $amount, string $limiteDate, int $cardId): Html2Pdf
    {
        // Content of the PDF
        $html2pdf->writeHTML(
            '
            <style>
                .container-card {
                    position: relative;
                    width: 100vw;
                }
                .container-card {
                    border-bottom: dashed 2px black;
                }
                img {
                    width: 100%;
                }
                .informations {
                    position: absolute;
                    font-size: 17px;
                    top: 59%;
                    left: 28%;
                }
                p {
                    margin: 8px 0;
                }
                .id {
                    position: absolute;
                    color: #ebae77;
                    margin-left: 130px;
                    margin-top: 85px;
                    font-size: 15px;
                }
            
            </style>

            <div class="container-card">
                <img class="verso" src="' . $this->siteHost . 'assets/images/CarteGourmandeVerso.jpg" alt="">
                <div class="informations">
                    <p class="gifter">' . $gifter . '</p>
                    <p class="to">' . $receiver . '</p>
                    <p class="amount">' . $amount . '€</p>
                    <p class="date">' . $limiteDate . '</p>
                </div>
                <p class="id">n°' . $cardId . '</p>
            </div>

            <img src="' . $this->siteHost . 'assets/images/CarteGourmandeRecto.jpg" alt="">
            '
        );

        return $html2pdf;
    }

    /**
     * Generate the PDF of the surprise card
     *
     * @return string The path of the PDF created
     */
    public function createCard(string $cardId, string $gifter, string $receiver, string $amount, string $deadLine): string
    {
        $html2pdf = new Html2Pdf('P', 'A4', 'fr');

        // The path of the PDF file
        $pathFile = __DIR__ . '/../../' . $this->publicFolderName . '/assets/pdf/' . $gifter . '-' . $receiver . '-' . $cardId . '.pdf';
        // Create the content of the PDF
        $this->templateCard($html2pdf, $gifter, $receiver, $amount, $deadLine, $cardId);
        // Create and save the PDF
        $html2pdf->output($pathFile, 'F');

        return $pathFile;
    }
}