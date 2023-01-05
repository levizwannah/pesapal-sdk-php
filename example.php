<?php
require(__DIR__. "/vendor/autoload.php");

use LeviZwannah\PesapalSdk\Pesapal;

$pp = Pesapal::new(["env" => "development"]);
$pp->token();
var_dump($pp->response());
?>