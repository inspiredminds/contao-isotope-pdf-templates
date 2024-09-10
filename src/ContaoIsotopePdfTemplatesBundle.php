<?php

declare(strict_types=1);

/*
 * This file is part of the ContaoIsotopePdfTemplatesBundle bundle.
 *
 * (c) inspiredminds
 *
 * @license LGPL-3.0-or-later
 */

namespace InspiredMinds\ContaoIsotopePdfTemplatesBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class ContaoIsotopePdfTemplatesBundle extends Bundle
{
    public function getPath()
    {
        return \dirname(__DIR__);
    }
}
