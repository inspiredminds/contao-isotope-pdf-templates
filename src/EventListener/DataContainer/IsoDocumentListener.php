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

use Contao\DataContainer;
use Contao\FilesModel;
use Contao\StringUtil;
use Isotope\Model\Document;
use Symfony\Component\Finder\Finder;

class IsoDocumentListener
{
    protected $projectDir;

    public function __construct(string $projectDir)
    {
        $this->projectDir = $projectDir;
    }

    public function onLoadCallback(DataContainer $dc): void
    {
        if ($dc->id && null !== ($document = Document::findById($dc->id))) {
            $dc->activeRecord = new \stdClass();
            $dc->activeRecord->customFontsDirectory = $document->customFontsDirectory;
            $config = $this->loadDefaultFontsConfig($dc);
            $GLOBALS['TL_DCA'][$dc->table]['fields']['customFontsConfig']['eval']['minCount'] = \count($config);
            $GLOBALS['TL_DCA'][$dc->table]['fields']['customFontsConfig']['eval']['maxCount'] = \count($config);
        }
    }

    public function onCustomFontsConfigLoad($value, DataContainer $dc)
    {
        $defaultConfig = $this->loadDefaultFontsConfig($dc);
        $userConfig = StringUtil::deserialize($value, true);

        for ($i = 0; $i < \count($defaultConfig); ++$i) {
            foreach ($userConfig as $font) {
                if ($font['filename'] === $defaultConfig[$i]['filename']) {
                    $defaultConfig[$i]['enabled'] = $font['enabled'];
                    $defaultConfig[$i]['variant'] = $font['variant'];
                    $defaultConfig[$i]['fontname'] = strtolower($font['fontname']);
                }
            }
        }

        return serialize($defaultConfig);
    }

    public function onCustomFontsConfigSave($value, DataContainer $dc)
    {
        $defaultConfig = $this->loadDefaultFontsConfig($dc);
        $userConfig = StringUtil::deserialize($value, true);

        for ($i = 0; $i < \count($defaultConfig); ++$i) {
            $defaultConfig[$i]['enabled'] = $userConfig[$i]['enabled'];
            $defaultConfig[$i]['variant'] = $userConfig[$i]['variant'];
            $defaultConfig[$i]['fontname'] = strtolower($userConfig[$i]['fontname']);
        }

        return serialize($defaultConfig);
    }

    protected function loadDefaultFontsConfig(DataContainer $dc)
    {
        $config = [];

        if ($dc->activeRecord && $dc->activeRecord->customFontsDirectory) {
            if (null !== ($folder = FilesModel::findByUuid($dc->activeRecord->customFontsDirectory))) {
                $finder = new Finder();
                $finder->files()->name('/\.ttf$/i')->in($this->projectDir.'/'.$folder->path);
                if ($finder->hasResults()) {
                    foreach ($finder as $file) {
                        $config[] = [
                            'enabled' => '',
                            'variant' => 'R',
                            'filename' => $file->getRelativePathname(),
                            'fontname' => '',
                        ];
                    }
                }
            }
        }

        return $config;
    }
}
