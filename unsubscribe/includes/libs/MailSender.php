<?php

//include_once 'includes/config/constants.php';
include('includes/libs/PHPMailer/PHPMailerAutoload.php');

/**
 * Description of MailSender
 *
 * @author Hardik Patel
 */
class MailSender {

    protected $obMailer;
    protected $Host;
    protected $SMTPAuth;
    protected $Username;
    protected $Password;
    protected $SMTPSecure;
    protected $CharSet;
    protected $setFrom;
    protected $isHTML;
    protected $Subject;
    protected $Port;

    public function __construct() {
        $this->obMailer = new PHPMailer;
        $this->obMailer->Host = MAILER_HOST;
        $this->obMailer->SMTPAuth = MAILER_SMTPAuth;
        $this->obMailer->Username = MAILER_Username;
        $this->obMailer->Password = MAILER_Password;
        $this->obMailer->SMTPSecure = MAILER_SMTPSecure;
        $this->obMailer->CharSet = MAILER_CharSet;
        $this->obMailer->setFrom(MAILER_setFromEmail, MAILER_setFromName);
        $this->obMailer->isHTML(MAILER_isHTML);
        $this->obMailer->Port = MAILER_Port;
    }

    /**
     * Function to send email 
     * @param string $ssTo
     * @param string $ssSubject
     * @param string $ssBody
     * @param string $ssAttachement
     */
    public function send_email($ssTo, $ssSubject, $ssBody, $ssAttachement = '') {

        if ($ssTo != '' && $ssSubject != '' && $ssBody != '') {
            $this->obMailer->addAddress($ssTo);
            $this->obMailer->Subject = $ssSubject;
            $this->obMailer->Body = $ssBody;

            if ($ssAttachement != '') {
                $this->obMailer->addAttachment($ssAttachement);
            }
            $this->obMailer->send();
            echo "<pre>";
            var_dump($this->obMailer);
//            $this->debugEmail();
        }
    }

    public function debugEmail() {
        $smDebug = $this->obMailer->Debugoutput;
        echo "<pre>";
        echo "PHPMailer Debug";
        echo "<hr>";
        print_r($smDebug);

        echo "<hr>";
        print_r($this->obMailer->ErrorInfo);
        echo "</pre>";
    }

    /**
     * Function to send email 
     * @param type $asUserData
     */
    public function sendTest1Email($asUserData = array()) {

        if (!empty($asUserData) && is_array($asUserData)) {
            $ssClientName = $asUserData['firstname'] . " " . $asUserData['lastname'];
            $ssClientEmail = $asUserData['email'];
            $ssPassword = $asUserData['password'];
            $asRealValue = array(
                $ssClientName,
                $ssClientEmail,
                $ssPassword,
            );
            $asPlaceHolder = array(
                '[NAME]',
                '[EMAIL]',
                '[PASSWORD]'
            );
            $ssEmailTemplate = dirname(__FILE__) . 'templatepath/filename.html';
            $ssBody = file_get_contents($ssEmailTemplate);
            $ssBody = str_replace($asPlaceHolder, $asRealValue, $ssBody);

            $this->obMailer->isHTML(TRUE);
            $this->obMailer->addAddress($ssClientEmail);
            $this->obMailer->addBCC("test@test.com");
            $this->obMailer->Subject = "Test Subject";
            $this->obMailer->Body = $ssBody;
            $this->obMailer->send();
            unset($this->obMailer);
        }
    }

    

}
