<?php

declare(strict_types=1);

/*
 * This file is part of the ContaoIsotopePdfTemplatesBundle bundle.
 *
 * (c) inspiredminds
 *
 * @license LGPL-3.0-or-later
 */

namespace InspiredMinds\ContaoIsotopePdfTemplatesBundle\Event;

use InspiredMinds\ContaoIsotopePdfTemplatesBundle\Isotope\Model\Document\Template;
use Symfony\Contracts\EventDispatcher\Event;

class ModifyPdfEvent extends Event
{
    /**
     * The contao_isotope_pdf_templates.modify_pdf event is triggered before pages are added to the pdf.
     *
     * @var string
     */
    public const EVENT_NAME = 'contao_isotope_pdf_templates.modify_pdf';

    /** @var \Mpdf\Mpdf */
    private $pdf;

    /** @var Template */
    private $document;

    public function __construct(\Mpdf\Mpdf $pdf, Template $document)
    {
        $this->pdf = $pdf;
        $this->document = $document;
    }

    public function getPdf(): \Mpdf\Mpdf
    {
        return $this->pdf;
    }

    public function getDocument(): Template
    {
        return $this->document;
    }
}
