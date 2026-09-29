<?php

require_once __DIR__ . '/../includes/auth.php';

destroyCurrentSession();

header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');
header('Location: ../index.php');
exit();
