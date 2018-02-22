<?php

include 'includes/config/constants.php';

class DB {

    protected $ssHost = "";
    protected $ssUsername = "";
    protected $ssPassword = "";
    protected $ssDBName = "";
    protected $ssDBPort = "";
    protected $obConn = "";

    public function __construct() {

        $this->ssHost = DB_HOST;
        $this->ssUsername = DB_USERNAME;
        $this->ssPassword = DB_PASSWORD;
        $this->ssDBName = DB_NAME;
        $this->ssDBPort = DB_PORT;
        $this->obConn = $this->dbConnection();
    }

    /**
     * Function to connect database
     * @return object
     */
    public function dbConnection() {
        $obCon = mysqli_connect($this->ssHost, $this->ssUsername, $this->ssPassword, $this->ssDBName);
        if (mysqli_connect_errno()) {
            return "Failed to connect to MySQL: " . mysqli_connect_error();
        } else {
            return $obCon;
        }
    }

    /**
     * Function to insert data
     * @param string $ssTableName
     * @param array $asData
     */
    public function insertData($ssTableName, $asData = array()) {
        if ($ssTableName != '' && count($asData) > 0) {
            $asFields = $this->getFields($asData);
            $ssQuery = 'INSERT INTO ' . $ssTableName . '(' . $asFields['fields'] . ') VALUES("' . $asFields['values'] . '")';
            echo $ssQuery;
            exit;
        }
    }

    /**
     * Function to get field name and values
     * @param array $asData
     * @return array
     */
    protected function getFields($asData = array()) {
        $asRsult = array();
        $asFieldName = array();
        $asFieldValues = array();
        if (count($asData) > 0) {
            $asFieldName = array_keys($asData);
            $asFieldValues = array_values($asData);
            $asRsult['fields'] = implode(',', $asFieldName);
            $asRsult['values'] = implode('","', $asFieldValues);
        }
        return $asRsult;
    }

    /**
     * Insert data into lead_capture table
     * @param array $asData
     * @return int
     */
    public function insertLeadCapture($asData = array()) {
        if (count($asData) > 0) {

            $ssQuery = 'INSERT INTO lead_capture(firstname,lastname,phone,email,state,city,description,created_at) VALUES("' . $asData['firstname'] . '","' . $asData['lastname'] . '","' . $asData['phone'] . '","' . $asData['email'] . '","' . $asData['state'] . '","' . $asData['city'] . '","' . $asData['description'] . '","' . date('Y-m-d H:i:s') . '")';
            if ($this->obConn) {
                mysqli_query($this->obConn, $ssQuery);
                return mysqli_insert_id($this->obConn);
            }
            return FALSE;
        }
        return FALSE;
    }

    public function validateData($asData = array()) {
        $asErrors = array();
        if (count($asData) > 0) {
            foreach ($asData as $ssField => $ssValue) {
                if ($ssValue == '') {

                    $asErrors["error_" . $ssField] = '<label id="' . $ssField . '-error" class="error" for="' . $ssField . '">This field is required.</label>';
                }
            }
            return $asErrors;
        }
        return $asErrors;
    }

}
