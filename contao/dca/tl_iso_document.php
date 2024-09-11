<?php

declare(strict_types=1);

/*
 * This file is part of the ContaoIsotopePdfTemplatesBundle bundle.
 *
 * (c) inspiredminds
 *
 * @license LGPL-3.0-or-later
 */

use Contao\CoreBundle\DataContainer\PaletteManipulator;
use InspiredMinds\ContaoIsotopePdfTemplatesBundle\EventListener\DataContainer\IsoDocumentListener;

$GLOBALS['TL_DCA']['tl_iso_document']['config']['onload_callback'][] = [IsoDocumentListener::class, 'onLoadCallback'];

$GLOBALS['TL_DCA']['tl_iso_document']['palettes']['template'] = $GLOBALS['TL_DCA']['tl_iso_document']['palettes']['standard'];

$GLOBALS['TL_DCA']['tl_iso_document']['fields']['usePdfHeader'] = [
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => ['submitOnChange' => true],
    'sql' => "char(1) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_iso_document']['fields']['pdfHeader'] = [
    'exclude' => true,
    'inputType' => 'textarea',
    'eval' => ['rte' => 'ace|html', 'mandatory' => true, 'allowHtml' => true],
    'sql' => ['type' => 'text', 'default' => '<div style="text-align: right">My document</div>'],
];

$GLOBALS['TL_DCA']['tl_iso_document']['fields']['usePdfFooter'] = [
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => ['submitOnChange' => true],
    'sql' => "char(1) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_iso_document']['fields']['pdfFooter'] = [
    'exclude' => true,
    'inputType' => 'textarea',
    'eval' => ['rte' => 'ace|html', 'mandatory' => true, 'allowHtml' => true],
    'sql' => ['type' => 'text', 'default' => '<table width="100%">
    <tr>
        <td width="33%">{DATE j-m-Y}</td>
        <td width="33%" align="center">{PAGENO}/{nbpg}</td>
        <td width="33%" style="text-align: right; ">My document</td>
    </tr>
</table>'],
];

$GLOBALS['TL_DCA']['tl_iso_document']['fields']['usePdfTemplate'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_iso_document']['usePdfTemplate'],
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => ['submitOnChange' => true],
    'sql' => "char(1) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_iso_document']['fields']['appendPdfTemplate'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_iso_document']['appendPdfTemplate'],
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => ['submitOnChange' => true],
    'sql' => "char(1) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_iso_document']['fields']['useCustomFonts'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_iso_document']['useCustomFonts'],
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => ['submitOnChange' => true],
    'sql' => "char(1) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_iso_document']['fields']['usePdfTemplateSRC'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_iso_document']['usePdfTemplateSRC'],
    'exclude' => true,
    'inputType' => 'fileTree',
    'eval' => ['fieldType' => 'radio', 'files' => true, 'mandatory' => true, 'extensions' => 'pdf'],
    'sql' => 'binary(16) NULL',
];

$GLOBALS['TL_DCA']['tl_iso_document']['fields']['appendPdfTemplateSRC'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_iso_document']['appendPdfTemplateSRC'],
    'exclude' => true,
    'inputType' => 'fileTree',
    'eval' => ['fieldType' => 'radio', 'files' => true, 'mandatory' => true, 'extensions' => 'pdf'],
    'sql' => 'binary(16) NULL',
];

$GLOBALS['TL_DCA']['tl_iso_document']['fields']['customFontsDirectory'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_iso_document']['customFontsDirectory'],
    'exclude' => true,
    'inputType' => 'fileTree',
    'eval' => ['fieldType' => 'radio', 'mandatory' => true],
    'sql' => 'binary(16) NULL',
];

$GLOBALS['TL_DCA']['tl_iso_document']['fields']['customFontsConfig'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_iso_document']['customFontsConfig'],
    'exclude' => true,
    'inputType' => 'multiColumnWizard',
    'load_callback' => [[IsoDocumentListener::class, 'onCustomFontsConfigLoad']],
    'save_callback' => [[IsoDocumentListener::class, 'onCustomFontsConfigSave']],
    'eval' => [
        'disableSorting' => true,
        'columnFields' => [
            'enabled' => [
                'label' => &$GLOBALS['TL_LANG']['tl_iso_document']['customFontsConfigEnabled'],
                'exclude' => true,
                'inputType' => 'checkbox',
            ],
            'variant' => [
                'label' => &$GLOBALS['TL_LANG']['tl_iso_document']['customFontsConfigVariant'],
                'exclude' => true,
                'inputType' => 'select',
                'options' => ['R' => 'Regular', 'B' => 'Bold', 'I' => 'Italic', 'BI' => 'BoldItalic'],
                'reference' => &$GLOBALS['TL_LANG']['tl_iso_document']['customFontsConfigVariantOptions'],
            ],
            'filename' => [
                'label' => &$GLOBALS['TL_LANG']['tl_iso_document']['customFontsConfigFilename'],
                'inputType' => 'text',
                'eval' => ['disabled' => true],
            ],
            'fontname' => [
                'label' => &$GLOBALS['TL_LANG']['tl_iso_document']['customFontsConfigFontname'],
                'inputType' => 'text',
            ],
        ],
    ],
    'sql' => 'blob NULL',
];

$GLOBALS['TL_DCA']['tl_iso_document']['fields']['pdfFormat'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_iso_document']['pdfFormat'],
    'exclude' => true,
    'inputType' => 'select',
    'options' => ['A0', 'A1', 'A2', 'A3', 'A4', 'A5', 'A6', 'A7', 'A8', 'A9', 'A10'],
    'eval' => ['tl_class' => 'w50', 'includeBlankOption' => true, 'blankOptionLabel' => &$GLOBALS['TL_LANG']['tl_iso_document']['pdfFormatBlank']],
    'sql' => ['type' => 'string', 'length' => 3, 'default' => 'A4'],
];

$GLOBALS['TL_DCA']['tl_iso_document']['fields']['pdfOrientation'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_iso_document']['pdfOrientation'],
    'exclude' => true,
    'inputType' => 'select',
    'options' => ['P', 'L'],
    'reference' => &$GLOBALS['TL_LANG']['tl_iso_document']['pdfOrientationOptions'],
    'eval' => ['tl_class' => 'w50', 'mandatory' => true],
    'sql' => ['type' => 'string', 'length' => 1, 'default' => 'P'],
];

$GLOBALS['TL_DCA']['tl_iso_document']['fields']['pdfFormatCustom'] = [
    'exclude' => true,
    'inputType' => 'text',
    'eval' => ['tl_class' => 'w50', 'maxlength' => 32, 'multiple' => true, 'size' => 2],
    'sql' => ['type' => 'blob', 'notnull' => false],
];

$GLOBALS['TL_DCA']['tl_iso_document']['fields']['pdfMargin'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_iso_document']['pdfMargin'],
    'exclude' => true,
    'inputType' => 'trbl',
    'options' => ['mm' => 'mm'],
    'eval' => ['tl_class' => 'w50', 'mandatory' => true],
    'sql' => ['type' => 'string', 'length' => 128, 'default' => serialize(['bottom' => '10', 'left' => '15', 'right' => '15', 'top' => '10', 'unit' => 'mm'])],
];

$GLOBALS['TL_DCA']['tl_iso_document']['fields']['pdfDefaultFont'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_iso_document']['pdfDefaultFont'],
    'exclude' => true,
    'inputType' => 'text',
    'eval' => ['tl_class' => 'w50 clr', 'maxlength' => 128, 'mandatory' => true],
    'sql' => ['type' => 'string', 'length' => 128, 'default' => 'freeserif'],
];

$GLOBALS['TL_DCA']['tl_iso_document']['fields']['pdfDefaultFontSize'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_iso_document']['pdfDefaultFontSize'],
    'exclude' => true,
    'inputType' => 'text',
    'eval' => ['tl_class' => 'w50', 'maxlength' => 4, 'mandatory' => true],
    'sql' => ['type' => 'string', 'length' => 4, 'default' => '12'],
];

$GLOBALS['TL_DCA']['tl_iso_document']['fields']['pdfCreator'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_iso_document']['pdfCreator'],
    'exclude' => true,
    'inputType' => 'text',
    'eval' => ['tl_class' => 'w50', 'maxlength' => 128, 'mandatory' => true],
    'sql' => ['type' => 'string', 'length' => 128, 'default' => 'Contao Open Source CMS'],
];

$GLOBALS['TL_DCA']['tl_iso_document']['fields']['pdfAuthor'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_iso_document']['pdfAuthor'],
    'exclude' => true,
    'inputType' => 'text',
    'eval' => ['tl_class' => 'w50', 'maxlength' => 128],
    'sql' => ['type' => 'string', 'length' => 128, 'default' => ''],
];

$GLOBALS['TL_DCA']['tl_iso_document']['palettes']['__selector__'][] = 'usePdfHeader';
$GLOBALS['TL_DCA']['tl_iso_document']['palettes']['__selector__'][] = 'usePdfFooter';
$GLOBALS['TL_DCA']['tl_iso_document']['palettes']['__selector__'][] = 'usePdfTemplate';
$GLOBALS['TL_DCA']['tl_iso_document']['palettes']['__selector__'][] = 'appendPdfTemplate';
$GLOBALS['TL_DCA']['tl_iso_document']['palettes']['__selector__'][] = 'useCustomFonts';

$GLOBALS['TL_DCA']['tl_iso_document']['subpalettes']['usePdfHeader'] = 'pdfHeader';
$GLOBALS['TL_DCA']['tl_iso_document']['subpalettes']['usePdfFooter'] = 'pdfFooter';
$GLOBALS['TL_DCA']['tl_iso_document']['subpalettes']['usePdfTemplate'] = 'usePdfTemplateSRC';
$GLOBALS['TL_DCA']['tl_iso_document']['subpalettes']['appendPdfTemplate'] = 'appendPdfTemplateSRC';
$GLOBALS['TL_DCA']['tl_iso_document']['subpalettes']['useCustomFonts'] = 'customFontsDirectory,customFontsConfig';

PaletteManipulator::create()
    ->addLegend('pdftemplate_legend', 'template_legend')
    ->addField('usePdfTemplate', 'pdftemplate_legend', PaletteManipulator::POSITION_APPEND)
    ->addField('appendPdfTemplate', 'pdftemplate_legend', PaletteManipulator::POSITION_APPEND)
    ->addLegend('font_legend', 'pdftemplate_legend')
    ->addField('useCustomFonts', 'font_legend', PaletteManipulator::POSITION_APPEND)
    ->addLegend('pdfconfig_legend', 'pdftemplate_legend', PaletteManipulator::POSITION_AFTER, true)
    ->addField('usePdfHeader', 'pdfconfig_legend', PaletteManipulator::POSITION_APPEND)
    ->addField('usePdfFooter', 'pdfconfig_legend', PaletteManipulator::POSITION_APPEND)
    ->addField('pdfFormat', 'pdfconfig_legend', PaletteManipulator::POSITION_APPEND)
    ->addField('pdfOrientation', 'pdfconfig_legend', PaletteManipulator::POSITION_APPEND)
    ->addField('pdfFormatCustom', 'pdfconfig_legend', PaletteManipulator::POSITION_APPEND)
    ->addField('pdfMargin', 'pdfconfig_legend', PaletteManipulator::POSITION_APPEND)
    ->addField('pdfDefaultFont', 'pdfconfig_legend', PaletteManipulator::POSITION_APPEND)
    ->addField('pdfDefaultFontSize', 'pdfconfig_legend', PaletteManipulator::POSITION_APPEND)
    ->addField('pdfCreator', 'pdfconfig_legend', PaletteManipulator::POSITION_APPEND)
    ->addField('pdfAuthor', 'pdfconfig_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('template', 'tl_iso_document')
;
