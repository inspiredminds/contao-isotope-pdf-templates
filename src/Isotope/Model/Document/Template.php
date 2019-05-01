<?php

declare(strict_types=1);

/*
 * This file is part of the ContaoIsotopePdfTemplatesBundle bundle.
 *
 * (c) inspiredminds
 *
 * @license LGPL-3.0-or-later
 */

namespace InspiredMinds\ContaoIsotopePdfTemplatesBundle\Isotope\Model\Document;

use Contao\Config;
use Contao\FilesModel;
use Contao\StringUtil;
use Isotope\Interfaces\IsotopeProductCollection;

class Template extends \Isotope\Model\Document\Standard
{
    protected function generatePDF(IsotopeProductCollection $objCollection, array $arrTokens)
    {
        // TCPDF configuration
        $l = [
            'a_meta_dir' => 'ltr',
            'a_meta_charset' => Config::get('characterSet'),
            'a_meta_language' => substr($GLOBALS['TL_LANGUAGE'], 0, 2),
            'w_page' => 'page',
        ];

        // Include TCPDF config
        if (file_exists(TL_ROOT.'/system/config/tcpdf.php')) {
            require_once TL_ROOT.'/system/config/tcpdf.php';
        } elseif (file_exists(TL_ROOT.'/vendor/contao/core-bundle/src/Resources/contao/config/tcpdf.php')) {
            require_once TL_ROOT.'/vendor/contao/core-bundle/src/Resources/contao/config/tcpdf.php';
        } elseif (file_exists(TL_ROOT.'/vendor/contao/tcpdf-bundle/src/Resources/contao/config/tcpdf.php')) {
            require_once TL_ROOT.'/vendor/contao/tcpdf-bundle/src/Resources/contao/config/tcpdf.php';
        }

        // Create new PDF document
        $pdf = new \setasign\Fpdi\Tcpdf\Fpdi(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true);

        // Set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor(PDF_AUTHOR);
        $pdf->SetTitle(StringUtil::parseSimpleTokens($this->documentTitle, $arrTokens));

        // Prevent font subsetting (huge speed improvement)
        $pdf->setFontSubsetting(false);

        // Remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // Set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

        // Set auto page breaks
        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);

        // Set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // Set some language-dependent strings
        $pdf->setLanguageArray($l);

        // Set font
        $pdf->SetFont(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN);

        // Initialize document and add a page
        $pdf->AddPage();

        // Check to use template
        if ($this->usePdfTemplate) {
            // Find file in database
            if (null !== ($file = FilesModel::findById($this->usePdfTemplateSRC))) {
                // Check if file exists
                if (file_exists(TL_ROOT.'/'.$file->path)) {
                    $pagecount = $pdf->setSourceFile(TL_ROOT.'/'.$file->path);

                    if ($pagecount > 0) {
                        $tpl = $pdf->importPage(1);
                        $pdf->useTemplate($tpl);
                    }
                }
            }
        }

        // Write the HTML content
        $pdf->writeHTML($this->generateTemplate($objCollection, $arrTokens), true, 0, true, 0);

        $pdf->lastPage();

        // Check to append PDF
        if ($this->appendPdfTemplate) {
            // Find file in database
            if (null !== ($file = FilesModel::findById($this->appendPdfTemplateSRC))) {
                // Check if file exists
                if (file_exists(TL_ROOT.'/'.$file->path)) {
                    $pagecount = $pdf->setSourceFile(TL_ROOT.'/'.$file->path);

                    for ($i = 1; $i <= $pagecount; ++$i) {
                        $tpl = $pdf->importPage($i);
                        $pdf->AddPage();
                        $pdf->useTemplate($tpl);
                    }
                }
            }
        }

        return $pdf;
    }
}
