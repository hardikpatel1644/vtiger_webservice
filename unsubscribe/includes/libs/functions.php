<?php

/**
 * Generate Random password
 * @return string
 */
function randomPassword() {
    $alphabet = "abcdefghijkmnpqrstuwxyzABCDEFGHJKLMNPQRSTUWXYZ23456789";
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 8; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}

function cleanInput($input) {

    $search = array(
        '@<script[^>]*?>.*?</script>@si', // Strip out javascript
        '@<[\/\!]*?[^<>]*?>@si', // Strip out HTML tags
        '@<style[^>]*?>.*?</style>@siU', // Strip style tags properly
        '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments
    );

    $output = preg_replace($search, '', $input);
    return $output;
}

//sanitize function
function sanitize($input) {
    if (is_array($input)) {
        foreach ($input as $var => $val) {
            $output[$var] = sanitize($val);
        }
    } else {
        if (get_magic_quotes_gpc()) {
            $input = stripslashes($input);
        }
        $input = cleanInput($input);
        $output = $input;
    }
    return $output;
}

/**
 * Function to set field value by post or edit data
 * @param string $ssField
 * @param array $asEdit
 * @return string
 */
function fieldValue($ssField = '', $asEdit = array()) {
    $ssFieldVal = "";
    if ($ssField != "") {
        if (is_array($asEdit) && count($asEdit) > 0) {
            $ssFieldVal = (isset($_POST[$ssField]) ? $_POST[$ssField] : $asEdit[$ssField]);
        } else {
            $ssFieldVal = (isset($_POST[$ssField]) ? $_POST[$ssField] : "");
        }
    }
    return $ssFieldVal;
}
