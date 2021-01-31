<?php

require_once('orbiter/Router.php');

$router = new Router();
$router->serve_current_path();
