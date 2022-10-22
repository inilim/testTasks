<?php
error_reporting(E_ALL);
set_time_limit(0);
date_default_timezone_set('Etc/GMT-3');
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/vendor/autoload.php';
ini_set('memory_limit', '5024M');
timeRun();

use Symfony\Component\DomCrawler\Crawler;


