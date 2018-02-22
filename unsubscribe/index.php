<?php

error_reporting(0); //comment this line IN when live, to turn of the warning an notices
/**
 * Description of test
 *
 * @author Hardik Patel <hpca1644@gmail.com>
 */
include("includes/libs/db.php");
include("includes/libs/Vtiger.php");
//include("includes/libs/MailSender.php");
include("includes/libs/functions.php");
include("includes/head.php");
include("includes/nav.php");


$ssEmail = $_GET['email'];

if (isset($_GET['action']) && $_GET['action'] == "unsubscribe" && $ssEmail != '') {

    $obVtiger = new Vtiger();
    $asData = $obVtiger->query("select * from Leads where  email='" . $ssEmail . "' LIMIT 1;");
    $asData->leadstatus = "Do Not Contact / Call";
    $ssUpdateStatus = $obVtiger->update(json_encode($asData));
    if ($ssUpdateStatus == 1) {
        header("Location:" . BASE_URL . "/success.php?message=success");
        echo "Your email id has been unsubscribed successfully from our mailing list.";
    } else {
        echo "Something went wrong, Please try again later.";
    }
} else {
    header("Location:" . MAIN_SITE_URL);
}
?>

<?php include('includes/footer.php'); ?>
<?php include('includes/footer_end.php'); ?>