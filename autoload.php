<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// Inc
require __DIR__ . "/inc/mrm-debug/mrm-debug.php";
require __DIR__ . "/inc/mrm-helper/mrm-helper.php";

// Core
require __DIR__ . "/core/mrm-feature-loader.php";
require __DIR__ . "/core/mrm-feature.php";
require __DIR__ . "/core/mrm-feature-settings-renderer.php";
require __DIR__ . "/core/mrm-feature-settings-field-renderer.php";

// Admin View
require __DIR__ . "/views/admin.php";