<?php

namespace Ivo\ProtectPage\ContaoManager;

use Contao\ManagerPlugin\BundlePluginInterface;
use Contao\ManagerPlugin\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;

class Plugin implements BundlePluginInterface
{
    public function getBundles(ParserInterface $parser)
    {
        return [
            BundleConfig::create('Ivo\ProtectPage\ProtectPage')
                ->setLoadAfter(['Contao\CoreBundle\ContaoCoreBundle'])
        ];
    }
}