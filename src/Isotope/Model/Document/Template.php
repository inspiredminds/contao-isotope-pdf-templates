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

        // Create new PDF document
        $pdf = new \Mpdf\Mpdf([
            'fontDir' => $fontDirs,
            'fontdata' => $fontData,
            'format' => \defined('PDF_PAGE_FORMAT') ? PDF_PAGE_FORMAT : 'A4',
            'orientation' => \defined('PDF_PAGE_ORIENTATION') ? PDF_PAGE_ORIENTATION : 'P',
            'margin_left' => \defined('PDF_MARGIN_LEFT') ? PDF_MARGIN_LEFT : 15,
            'margin_right' => \defined('PDF_MARGIN_RIGHT') ? PDF_MARGIN_RIGHT : 15,
            'margin_top' => \defined('PDF_MARGIN_TOP') ? PDF_MARGIN_TOP : 10,
            'margin_bottom' => \defined('PDF_MARGIN_BOTTOM') ? PDF_MARGIN_BOTTOM : 10,
            'default_font_size' => \defined('PDF_FONT_SIZE_MAIN') ? PDF_FONT_SIZE_MAIN : 12,
            'default_font' => \defined('PDF_FONT_NAME_MAIN') ? PDF_FONT_NAME_MAIN : 'freeserif',
        ]);

        // Set document information
        $pdf->SetCreator(\defined('PDF_CREATOR') ? PDF_CREATOR : 'Contao Open Source CMS');
        $pdf->SetAuthor(\defined('PDF_AUTHOR') ? PDF_AUTHOR : Environment::get('url'));
        $pdf->SetTitle(StringUtil::parseSimpleTokens($this->documentTitle, $arrTokens));

        // Dispatch modify pdf event
        System::getContainer()->get('event_dispatcher')->dispatch(
            ModifyPdfEvent::EVENT_NAME,
            new ModifyPdfEvent($pdf, $this)
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
