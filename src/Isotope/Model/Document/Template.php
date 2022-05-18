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

use Contao\Environment;
use Contao\FilesModel;
use Contao\StringUtil;
use Contao\System;
use InspiredMinds\ContaoIsotopePdfTemplatesBundle\Event\ModifyPdfEvent;
use Isotope\Interfaces\IsotopeProductCollection;

class Template extends \Isotope\Model\Document\Standard
{
    protected function generatePDF(IsotopeProductCollection $objCollection, array $arrTokens)
    {
        // Get the project directory
        $projectDir = System::getContainer()->getParameter('kernel.project_dir');

        // Include TCPDF config
        if (file_exists($projectDir.'/system/config/tcpdf.php')) {
            require_once $projectDir.'/system/config/tcpdf.php';
        } elseif (file_exists($projectDir.'/vendor/contao/core-bundle/src/Resources/contao/config/tcpdf.php')) {
            require_once $projectDir.'/vendor/contao/core-bundle/src/Resources/contao/config/tcpdf.php';
        } elseif (file_exists($projectDir.'/vendor/contao/tcpdf-bundle/src/Resources/contao/config/tcpdf.php')) {
            require_once $projectDir.'/vendor/contao/tcpdf-bundle/src/Resources/contao/config/tcpdf.php';
        }

        $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        // Add custom fonts
        if ($this->useCustomFonts) {
            if (null !== ($folder = FilesModel::findByUuid($this->customFontsDirectory))) {
                $fontDirs[] = $projectDir.'/'.$folder->path;

                $config = StringUtil::deserialize($this->customFontsConfig, true);
                if (!empty($config)) {
                    foreach ($config as $font) {
                        if (!empty($font['fontname']) && $font['enabled']) {
                            $fontData[$font['fontname']][$font['variant']] = $font['filename'];
                        }
                    }
                }
            }
        }

        $margin = StringUtil::deserialize($this->pdfMargin, true);

        // Create new PDF document
        $pdf = new \Mpdf\Mpdf([
            'fontDir' => $fontDirs,
            'fontdata' => $fontData,
            'format' => $this->pdfFormat,
            'orientation' => $this->pdfOrientation,
            'margin_left' => (int) $margin['left'] ?? 15,
            'margin_right' => (int) $margin['right'] ?? 15,
            'margin_top' => (int) $margin['top'] ?? 10,
            'margin_bottom' => (int) $margin['bottom'] ?? 10,
            'default_font_size' => (int) $this->pdfDefaultFontSize,
            'default_font' => $this->pdfDefaultFont,
        ]);

        // Set document information
        $pdf->SetCreator($this->pdfCreator);
        $pdf->SetAuthor($this->pdfAuthor ?: Environment::get('url'));
        $pdf->SetTitle(StringUtil::parseSimpleTokens($this->documentTitle, $arrTokens));

        // Dispatch modify pdf event
        System::getContainer()->get('event_dispatcher')->dispatch(
            new ModifyPdfEvent($pdf, $this),
            ModifyPdfEvent::EVENT_NAME
        );

        // Check to use template
        if ($this->usePdfTemplate) {
            // Find file in database
            if (null !== ($file = FilesModel::findById($this->usePdfTemplateSRC))) {
                // Check if file exists
                if (file_exists($projectDir.'/'.$file->path)) {
                    $pdf->SetDocTemplate($projectDir.'/'.$file->path, true);
                }
            }
        }

        // Initialize document and add a page
        $pdf->AddPage();

        // Write the HTML content
        $pdf->WriteHTML($this->generateTemplate($objCollection, $arrTokens));

        // Reset template
        $pdf->SetDocTemplate();

        // Check to append PDF
        if ($this->appendPdfTemplate) {
            // Find file in database
            if (null !== ($file = FilesModel::findById($this->appendPdfTemplateSRC))) {
                // Check if file exists
                if (file_exists($projectDir.'/'.$file->path)) {
                    $pagecount = $pdf->SetSourceFile($projectDir.'/'.$file->path);

                    for ($i = 1; $i <= $pagecount; ++$i) {
                        $pdf->AddPage();
                        $tpl = $pdf->ImportPage($i);
                        $pdf->UseTemplate($tpl);
                    }
                }
            }
        }

        return $pdf;
    }
}
