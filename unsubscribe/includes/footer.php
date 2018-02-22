<footer class="footer section">
    
</footer>
<!--   Core JS Files   -->

<!-- Address search -->
<!-- END Scripts -->
<script >
    $(document).ready(function () {

        $('#contact-form').validate({
            rules: {
                email: {
                    required: true,
                    email: true
                }
            },
            highlight: function (element) {
                $(element).closest('.control-group').removeClass('success').addClass('error');
            },
            success: function (element) {
                element.closest('.control-group').removeClass('error').addClass('success');
            }
        });

    });
</script>
<style >
    .valid  {color: green;}
    .success  {color: green;}
</style>