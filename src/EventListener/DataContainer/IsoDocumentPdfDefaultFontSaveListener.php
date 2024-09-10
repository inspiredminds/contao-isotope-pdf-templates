<?php

declare(strict_types=1);

/*
 * This file is part of the ContaoIsotopePdfTemplatesBundle bundle.
 *
 * (c) inspiredminds
 *
 * @license LGPL-3.0-or-later
 */

namespace InspiredMinds\ContaoIsotopePdfTemplatesBundle\EventListener\DataContainer;

use Contao\CoreBundle\ServiceAnnotation\Callback;

/**
 * @Callback(table="tl_iso_document", target="fields.pdfDefaultFont.save")
 */
class IsoDocumentPdfDefaultFontSaveListener
{
    public function __invoke($value): string
    {
        return strtolower((string) $value);
    }
}
