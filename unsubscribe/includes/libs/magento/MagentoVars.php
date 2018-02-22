<?php

ini_set('display_errors', 0);
error_reporting(E_ALL);

date_default_timezone_set('America/Denver');
ob_start();
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

header('Access-Control-Allow-Methods: POST');
header('Access-Control-Max-Age: 3000');


include_once dirname(__FILE__) . '/../../config/constants.php';
require dirname(__FILE__) . '/../../../../app/bootstrap.php';

$bootstrap = \Magento\Framework\App\Bootstrap::create(BP, $_SERVER);

include dirname(__FILE__) . '/AbstractApp.php';

/**
 * Description of MagentoVars
 *
 * @author Hardik Patel <hpca1644@gmail.com>
 */
class MagentoVars extends AbstractApp {

    public $ssSalt = '';

    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager, \Magento\Framework\Event\Manager $eventManager, \Magento\Framework\App\AreaList $areaList, \Magento\Framework\App\Request\Http $request, \Magento\Framework\App\Response\Http $response, \Magento\Framework\ObjectManager\ConfigLoaderInterface $configLoader, \Magento\Framework\App\State $state, \Magento\Framework\Filesystem $filesystem, \Magento\Framework\Registry $registry, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Customer\Model\Customer $customer, \Magento\Customer\Model\Session $customerSession) {
        $this->_customer = $customer;
        $this->_customerSession = $customerSession;

        parent::__construct($objectManager, $eventManager, $areaList, $request, $response, $configLoader, $state, $filesystem, $registry, $storeManager, $customer, $customerSession);
    }

    //put your code here
    public function run() {
        $this->_state->setAreaCode('frontend');
        $this->_objectManager->get('Magento\Framework\Registry')->register('isSecureArea', true);
    }

    /**
     * Function to add user info in magento database
     * @param array $asUserData
     * @param array $asAddress
     * @return  array
     */
    public function addUser($asUserData = array(), $asAddress = array()) {
        if (is_array($asUserData) && !empty($asUserData) && is_array($asAddress) && !empty($asAddress)) {
            $obMagento = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
            $connection = $obMagento->getConnection();

            $blIseUser = $this->isUserExist($asUserData['email']);
            echo $blIseUser;
            if ($blIseUser == false) {
                $snUserId = $this->saveUser($asUserData);
                if ($snUserId) {
                    $asAddress['parent_id'] = $snUserId;
                    $snAddressId = $this->saveAddress($asAddress);
                    $this->updateDefaultAddressSettings($snAddressId, $snUserId);
                    $this->signinInMagento($asUserData['email']);
                    return array('customer_id' => $snUserId, 'customer_address_id' => $snAddressId);
                }
            } else {
                return array("error" => "User already exist. Please try with another email address");
            }
        }
        return array("error" => "Something went wrong, Please try agian.");
    }

    /**
     * Function to save User
     * @param array $asUserData
     * @return int
     */
    public function saveUser($asUserData = array()) {

        if (is_array($asUserData) && !empty($asUserData)) {
            $obCustomer = $this->_objectManager->create('Magento\Customer\Model\Customer');

            $obCustomer->setWebsiteId(1);
            $obCustomer->setEmail($asUserData['email']);
            $obCustomer->setFirstname($asUserData['firstname']);
            $obCustomer->setLastname($asUserData['lastname']);
            $obCustomer->setGroupId(1);
            $obCustomer->setStoreId(1);
            $obCustomer->setCreatedIn('Furniture7 Store View');
            $obCustomer->setPasswordHash($this->passwordHas($asUserData['password']));
            $obCustomer->save();
            return $obCustomer->getId();
        }
    }

    /**
     * Function to add address of customer
     * @param type $asAddress
     * @return type
     */
    public function saveAddress($asAddress = array()) {
        if (is_array($asAddress) && !empty($asAddress)) {
            $obAddress = $this->_objectManager->create('Magento\Customer\Model\Address');
            $obAddress->setFirstname($asAddress['firstname']);
            $obAddress->setLastname($asAddress['lastname']);
            $obAddress->setTelephone($asAddress['telephone']);
            $obAddress->setCity($asAddress['city']);
            $obAddress->setPostcode($asAddress['postcode']);
            $obAddress->setRegionId($asAddress['state']);
            $obAddress->setRegion($this->getRegionName($asAddress['state']));
            $obAddress->setStreet($asAddress['address']);
            $obAddress->setCountryId('US');
            $obAddress->setParentId($asAddress['parent_id']);
            $obAddress->save();
            return $obAddress->getId();
        }
    }

    /**
     * Generate Magento password has
     * @param string $ssPassword
     */
    public function passwordHas($ssPassword) {
        if ($ssPassword != '') {
            return hash("sha256", $this->ssSalt . $ssPassword) . ":$this->ssSalt:1\n";
        }
    }

    /**
     * Check user exist or not 
     * @param string $ssEmail
     * @return boolean
     */
    protected function isUserExist($ssEmail = '') {
        if ($ssEmail != '') {
            $obManager = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
            $obConnection = $obManager->getConnection();
            $ssQuery = 'SELECT * FROM `customer_entity` WHERE `email` = "' . $ssEmail . '" LIMIT 1';
            $asCustomer = $obConnection->fetchRow($ssQuery);
            if (!empty($asCustomer) && count($asCustomer) > 0)
                return true;
            else
                return false;
        }
        return false;
    }

    /* code for getting customer detail */

    public function get_cust_detail($custid) {
        $resource = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();
        $sql = "SELECT email,firstname,lastname,gender,dob FROM `customer_entity` WHERE `entity_id` = " . $custid . " LIMIT 1";
        $custDetail = $connection->fetchRow($sql);

        return $custDetail;
    }

    /**
     * Update setting of default address
     * @param type $snAddressId
     */
    public function updateDefaultAddressSettings($snAddressId = '', $snCustomerId = '') {
        if ($snAddressId != '' && $snCustomerId != '') {
            $obManager = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
            $obConnection = $obManager->getConnection();
            $ssQuery = 'UPDATE `customer_entity` SET `default_billing` = "' . $snAddressId . '" ,`default_shipping` = "' . $snAddressId . '" WHERE entity_id = "' . $snCustomerId . '"';
            //echo $ssQuery;
            $obConnection->exec($ssQuery);
        }
    }

    /**
     * Function to get Regions
     * @return array
     */
    public function getRegions() {
        $obManager = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
        $obConnection = $obManager->getConnection();
        $ssQuery = 'SELECT * FROM `directory_country_region` WHERE country_id = "US"';
        $asRegions = $obConnection->fetchAll($ssQuery);
        return $asRegions;
    }

    /**
     * Function to get Regions Name
     * @return array
     */
    public function getRegionName($snRegionId = '') {
        $obManager = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
        $obConnection = $obManager->getConnection();
        $ssQuery = 'SELECT default_name FROM `directory_country_region` WHERE country_id = "US" AND region_id="' . $snRegionId . '"';
        $ssRegionName = $obConnection->fetchOne($ssQuery);
        return $ssRegionName;
    }

    /**
     * Function to signin In magento
     * @param type $ssEmail
     */
    public function signinInMagento($ssEmail = '') {
        if ($ssEmail != '') {
            $asCustomer = $this->_customer->setWebsiteId(1)->loadByEmail($ssEmail);
            $this->_customerSession->setCustomerAsLoggedIn($asCustomer);
            return true;
        }
        return false;
    }

}
