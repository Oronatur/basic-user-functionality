<?php
require_once dirname( __DIR__ ) . '/private/db_data.php';
spl_autoload_register(function($className) {
    include_once dirname( __FILE__ ) . '/classes/' . $className . '.class.php';
});
?>

