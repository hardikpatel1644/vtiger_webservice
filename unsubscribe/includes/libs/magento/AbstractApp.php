<?php

/**
 * Description of AbstractApp
 *
 * @author Hardik Patel <hpca1644@gmail.com>
 */
use \Magento\Framework\AppInterface as AppInterface;
use \Magento\Framework\App\Http as Http;
use Magento\Framework\ObjectManager\ConfigLoaderInterface;
use Magento\Framework\App\Request\Http as RequestHttp;
use Magento\Framework\App\Response\Http as ResponseHttp;
use Magento\Framework\Event;
use Magento\Framework\Filesystem;
use Magento\Framework\App\AreaList as AreaList;
use Magento\Framework\App\State as State;
use Magento\Store\Model\StoreManagerInterface as StoreManager;


/**
 * Abstract class for magento custom app
 */
abstract class AbstractApp implements AppInterface {

    public function __construct(
    \Magento\Framework\ObjectManagerInterface $objectManager, Event\Manager $eventManager, AreaList $areaList, RequestHttp $request, ResponseHttp $response, ConfigLoaderInterface $configLoader, State $state, Filesystem $filesystem, \Magento\Framework\Registry $registry, StoreManager $storeManager, \Magento\Customer\Model\Customer $customer, \Magento\Customer\Model\Session $customerSession
    ) {
        $this->_objectManager = $objectManager;
        $this->_eventManager = $eventManager;
        $this->_areaList = $areaList;
        $this->_request = $request;
        $this->_response = $response;
        $this->_configLoader = $configLoader;
        $this->_state = $state;
        $this->_filesystem = $filesystem;
        $this->registry = $registry;
        $this->_storeManager = $storeManager;
        $this->_customer = $customer;
        $this->_customerSession = $customerSession;
    }

    /**
     * Function to execute launch
     * @return type
     */
    public function launch() {
        $this->run();
        return $this->_response;
    }

    abstract public function run();

    /**
     * Function to catch exception
     * @param \Magento\Framework\App\Bootstrap $bootstrap
     * @param \Exception $exception
     * @return boolean
     */
    public function catchException(\Magento\Framework\App\Bootstrap $bootstrap, \Exception $exception) {
        echo $exception->getMessage();
        return false;
    }

}
