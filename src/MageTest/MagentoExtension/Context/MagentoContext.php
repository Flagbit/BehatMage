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

    /**
     * @Given /^I log in as admin user "([^"]*)" identified by "([^"]*)"$/
     */
    public function iLoginAsAdmin($username, $password)
    {
        $sid = $this->sessionService->adminLogin($username, $password);
        $this->getSession()->setCookie('adminhtml', $sid);
    }

    /**
     * @Given /^I am logged in as admin user "([^"]*)" identified by "([^"]*)"$/
     */
    public function iAmLoggedInAsAdminUserIdentifiedBy($username, $password)
    {
        $this->getSession()->visit($this->locatePath('/admin'));

        if (false === strpos(parse_url($this->getSession()->getCurrentUrl(), PHP_URL_PATH), '/admin/dashboard/')) {
            $this->getSession()->getPage()->fillField('login[username]', $username);
            $this->getSession()->getPage()->fillField('login[password]', $password);
            $this->getSession()->getPage()->pressButton('Login');
        }

        $this->assertSession()->addressMatches('#^/admin/dashboard/.+$#');
    }


    /**
     * @When /^I open admin URI "([^"]*)"$/
     */
    public function iOpenAdminUri($uri)
    {
        $urlModel = new \Mage_Adminhtml_Model_Url();
        if (preg_match('@^/admin/(.*?)/(.*?)((/.*)?)$@', $uri, $m)) {
            $processedUri = "/admin/{$m[1]}/{$m[2]}/key/".$urlModel->getSecretKey($m[1], $m[2])."/{$m[3]}";
            $this->getSession()->visit($this->locatePath($processedUri));
        } else {
            throw new \InvalidArgumentException('$uri parameter should start with /admin/ and contain controller and action elements');
        }
    }

    /**
    * @Then /^I should see text "([^"]*)"$/
    */
    public function iShouldSeeText($text)
    {
        $this->assertPageContainsText($text);
    }

    /**
    * @Then /^I should not see text "([^"]*)"$/
    */
    public function iShouldNotSeeText($text)
    {
        $this->assertPageNotContainsText($text);
    }

    /**
     * @Given /^I set config value for "([^"]*)" to "([^"]*)" in "([^"]*)" scope$/
     */
    public function iSetConfigValueForScope($path, $value, $scope)
    {
        $this->configManager->setCoreConfig($path, $value, $scope);
    }


    /**
     * @Given /^the cache is clean$/
     */
    public function theCacheIsClean()
    {
        $this->cacheManager->clear();
    }

    /**
     * @Given /the following products exist:/
     */
    public function theProductsExist(TableNode $table)
    {
        $hash = $table->getHash();
        $fixtureGenerator = $this->factory->create('product');
        foreach ($hash as $row) {
            $row['stock_data'] = array();
            if (isset($row['is_in_stock'])) {
                $row['stock_data']['is_in_stock'] = $row['is_in_stock'];
            }
            if (isset($row['is_in_stock'])) {
                $row['stock_data']['qty'] = $row['qty'];
            }
            $row['website_ids'] = array(1);
            $fixtureGenerator->create($row);
        }
    }

    public function setApp(MageApp $app)
    {
        $this->app = $app;
    }

    public function setConfigManager(ConfigManager $config)
    {
        $this->configManager = $config;
    }

    public function setCacheManager(CacheManager $cache)
    {
        $this->cacheManager = $cache;
    }

    public function setFixtureFactory(FixtureFactory $factory)
    {
        $this->factory = $factory;
    }

    public function setSessionService(Session $session)
    {
        $this->sessionService = $session;
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
