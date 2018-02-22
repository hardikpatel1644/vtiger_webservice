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
?>

<div class="container">
    <div class="row">
        <div id="rootwizard">
            <div class="content">
                <?php
                if (isset($_GET['message']) && $_GET['message'] == "success") {
                    echo "<h3>Your email id has been unsubscribed successfully from our mailing list.</h3>";
                } else {
                    header("Location:" . MAIN_SITE_URL);
                }
                ?>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>
<?php include('includes/footer_end.php'); ?>