<?php

define('APP_NAME', $_ENV['APP_NAME'] ?? 'Mini ERP');
define('APP_ENV', $_ENV['APP_ENV'] ?? 'development');
define('BASE_URL', rtrim($_ENV['APP_URL'] ?? 'http://localhost:8000', '/'));
