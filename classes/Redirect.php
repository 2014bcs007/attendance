<?php

class Redirect {

    public static function to($location = NULL) {
        if (is_numeric($location)) {
            switch ($location) {
                case 404:
                    header("HTTP/1.0 404 Not Found");
                    include 'includes/errors/404.php';
                    exit();
                    break;
            }
        }
        ?>
        <script type="text/javascript">
            window.location = "<?php echo $location; ?>";
        </script>
        <?php
        exit();
    }

    public static function go_to($location = NULL) {
        ?>
        <script type="text/javascript">
            setTimeout('Redirect()', 2000);
            function Redirect() {
                window.location = "<?php echo $location; ?>";
            }
        </script>
        <?php
    }

    public static function to_print($parent_location = NULL, $print_location = NULL) {
        ?>
        <script type="text/javascript">
            window.open('<?php echo $print_location; ?>', '_blank');
            setTimeout('Redirect()', 2000);
            function Redirect() {
                window.location = "<?php echo $parent_location; ?>";
            }
        </script>
        <?php
    }

}
?>