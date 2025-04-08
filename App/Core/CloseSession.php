<?php

use Core\Session;

require_once dirname(__DIR__)."/Config/Autoload.php";

Session::sessionStart('HNPT');
Session::closeSession();