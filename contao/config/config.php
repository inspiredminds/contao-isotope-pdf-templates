<?php

declare(strict_types=1);

/*
 * This file is part of the ContaoIsotopePdfTemplatesBundle bundle.
 *
 * (c) inspiredminds
 *
 * @license LGPL-3.0-or-later
 */

Isotope\Model\Document::registerModelType('template', InspiredMinds\ContaoIsotopePdfTemplatesBundle\Isotope\Model\Document\Template::class);
