<?php
require("../vendor/autoload.php");
$openapi = \OpenApi\scan('../application');
header('Content-Type: application/x-json');
echo $openapi->toJSON();
