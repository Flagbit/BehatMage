<?php

namespace MageTest\MagentoExtension;

use Behat\Behat\Extension\Extension as BaseExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder,
    Symfony\Component\DependencyInjection\Loader\XmlFileLoader,
    Symfony\Component\Config\FileLocator;

class Extension extends BaseExtension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $container->setParameter('magento.base_url', \Mage::getStoreConfig('web/unsecure/base_url'));
        $container->setParameter('magento.behat_mage_dir',  \Mage::getConfig()->getModuleDir('', 'Hackathon_MageBehat') . DS . 'Test');
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/services'));
        $loader->load('core.xml');
    }
}
