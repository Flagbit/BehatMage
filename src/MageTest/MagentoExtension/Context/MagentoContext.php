<?php

namespace MageTest\MagentoExtension\Context;

use Mage_Core_Model_App as MageApp;
use MageTest\MagentoExtension\Context\MagentoAwareContext,
    MageTest\MagentoExtension\Service\ConfigManager,
    MageTest\MagentoExtension\Service\CacheManager,
    MageTest\MagentoExtension\Service,
    MageTest\MagentoExtension\Fixture\FixtureFactory,
    MageTest\MagentoExtension\Service\Session;

use Behat\MinkExtension\Context\MinkAwareInterface,
    Behat\MinkExtension\Context\MinkContext,
    Behat\Gherkin\Node\TableNode;

//require_once 'PHPUnit/Autoload.php';
// require_once 'PHPUnit/Framework/Assert/Functions.php';


class MagentoContext extends MinkContext implements MinkAwareInterface, MagentoAwareInterface
{
    private $app;
    private $configManager;
    private $cacheManager;
    private $factory;
    private $sessionService;

    public function setApp(MageApp $app)
    {
        $this->app = $app;
    }

    public function getApp()
    {
        return $this->app;
    }

    public function setConfigManager(ConfigManager $config)
    {
        $this->configManager = $config;
    }

    public function getConfigManager()
    {
        return $this->configManager;
    }

    public function setCacheManager(CacheManager $cache)
    {
        $this->cacheManager = $cache;
    }

    public function getCacheManager()
    {
        return $this->cacheManager;
    }

    public function setFixtureFactory(FixtureFactory $factory)
    {
        $this->factory = $factory;
    }

    public function getFixtureFactory()
    {
        return $this->factory;
    }

    public function setSessionService(Session $session)
    {
        $this->sessionService = $session;
    }

    public function getSessionService()
    {
        return $this->sessionService;
    }

    public function getFixture($identifier)
    {
        return $this->factory->create($identifier);
    }

    public function locatePath($path)
    {
        if(strpos($path, 'http') !== false){
            return $path;
        }

        $startUrl = rtrim($this->getMinkParameter('base_url'), '/') . '/';
        if(strpos($startUrl, 'http') === false){
            $startUrl = 'http://'.$startUrl;
        }
        return $startUrl . ltrim($path, '/');
    }
}