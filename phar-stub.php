<?php
Phar::mapPhar();
define('PHAL_DIR', 'phar://phal.phar/phal/');
define('SANDBOX_DIR', 'phar://phal.phar/app/');
include 'phar://phal.phar/phal/phal.php';
__HALT_COMPILER();