<?php

include_once 'includes/config/constants.php';

/**
 * Description of Vtiger
 *
 * @author Hardik Patel <hpca1644@gmail.com>
 */
class Vtiger {

    protected $ssWebServiceUrl = "";
    protected $ssUrl = "";
    protected $ssPublicID = "";
    protected $ssUserName = "";

    public function __construct() {
        $this->ssWebServiceUrl = VTIGER_WEBSERVICE_URL;
        $this->ssUrl = VTIGER_URL;
        $this->ssPublicID = VTIGER_PUBLIC_ID;
        $this->ssUserName = VTIGER_USERNAME;
        $this->asLogin = $this->getLogin();
        // var_dump($this->asLogin);
    }

    /**
     * Function to add Lead in CRM
     * @param array $asData
     * @return string
     */
    public function addLead($asData = array()) {
        $asResult = "";
        if (count($asData) > 0) {

            $asVtigerData = $this->generateVtigerData($asData);
            $asVtigerOptions = $this->generateVtigerOptions($asVtigerData);
            $asVtigerContext = stream_context_create($asVtigerOptions);
            $asVtigerResult = file_get_contents($this->ssUrl, false, $asVtigerContext);
            $asResult = $asVtigerResult;
        }
        return $asResult;
    }

    /**
     * Function to generate Vtiger data
     * @param array $asData
     * @return array
     */
    protected function generateVtigerData($asData = array()) {

        if (count($asData) > 0) {
            $asData['publicid'] = $this->ssPublicID;
        }
        return $asData;
    }

    /**
     * Function to generate Vtiger Options values
     * @param array $asData
     * @return array
     */
    protected function generateVtigerOptions($asData = array()) {
        $asOptions = array();
        if (count($asData) > 0) {
            $asOptions = array(
                'http' => array(
                    'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method' => 'POST',
                    'content' => http_build_query($asData),
            ));
        }
        return $asOptions;
    }

    /**
     * Function to get Access token
     * @return string
     */
    protected function getAccessToken() {
        $ssParams = "?operation=getchallenge&username=" . $this->ssUserName;

        $asResult = array();
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $this->ssWebServiceUrl . $ssParams,
        ));
        $asResult = curl_exec($curl);
        curl_close($curl);
        $asResult = json_decode($asResult);
        return $asResult->result->token;
    }

    /**
     * Function to get Login in CRM
     * @return array object
     */
    protected function getLogin() {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $this->ssWebServiceUrl,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => array(
                'operation' => 'login',
                'username' => $this->ssUserName,
                'accessKey' => md5($this->getAccessToken() . VTIGER_ACCESSKEY)
            )
        ));
        $asResult = curl_exec($curl);
        curl_close($curl);
        $asResult = json_decode($asResult);
        if ($asResult->success == 1) {
            return $asResult->result;
        } else {
            echo "Invalid Login details";
        }
    }

    /**
     * FUnction to get data from CRM by email id using query method
     * @param string $ssQuery
     * @return array object
     */
    public function query($ssQuery = '') {
        if ($ssQuery != '') {
            $ssParams = "?operation=query&sessionName=" . urlencode($this->asLogin->sessionName) . "&query=" . urlencode($ssQuery);

            $asResult = array();
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => $this->ssWebServiceUrl . $ssParams,
            ));
            $asResult = curl_exec($curl);
            curl_close($curl);
            $asResult = json_decode($asResult);
            return $asResult->result[0];
        } else {
            return "error";
        }
    }

    /**
     * Function to update crm data
     * @param object $obData

     * @return json
     */
    public function update($obData = '') {

        if ($obData != "") {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => $this->ssWebServiceUrl,
                CURLOPT_POST => 1,
                CURLOPT_POSTFIELDS => array(
                    'operation' => 'update',
                    'sessionName' => $this->asLogin->sessionName,
                    'element' => ($obData),
                )
            ));
            $asResult = curl_exec($curl);
            curl_close($curl);
            $asResult = json_decode($asResult);
            return $asResult->success;
        }
    }

}
