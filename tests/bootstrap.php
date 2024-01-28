<?php
/** @noinspection AutoloadingIssuesInspection */
/** @noinspection PhpIllegalPsrClassPathInspection */

define('ROOT', dirname(__DIR__).'/');
const PRIVATE_DIR = ROOT.'tests/private/';
const TMP_DIR = ROOT.'tests/tmp/';
const LOG_DIR = ROOT.'tests/logs/';
const LANGUAGE_DIR = ROOT.'languages/';
const TEMPLATE_DIR = ROOT.'templates/';
const LANGUAGE_FILE_NAME = 'translations';
const DEFAULT_LANGUAGE = 'cs_CZ';
const CHECK_TRANSLATIONS = true;
const TRANSLATIONS_COMMENTS = false;
const PRODUCTION = true;
const ASSETS_DIR = ROOT.'assets/';

require_once ROOT.'vendor/autoload.php';

require_once ROOT.'includes/functions.php';
