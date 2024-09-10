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
use Contao\StringUtil;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Callback(table="tl_iso_document", target="fields.pdfFormatCustom.save")
 */
class TemplateCustomFormatSaveListener
{
    private RequestStack $requestStack;
    private TranslatorInterface $translator;

    public function __construct(RequestStack $requestStack, TranslatorInterface $translator)
    {
        $this->requestStack = $requestStack;
        $this->translator = $translator;
    }

    public function __invoke($value): string
    {
        $request = $this->requestStack->getCurrentRequest();
        $values = StringUtil::deserialize($value, true) + ['', ''];

        if (!array_filter($values) && !$request->request->get('pdfFormat')) {
            throw new \Exception($this->translator->trans('ERR.missingCustomPdfFormat', [], 'contao_default'));
        }

        return serialize(array_map('floatval', $values));
    }
}
