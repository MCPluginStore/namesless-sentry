<?php
/*
 * NamelessSentry Module
 * Minimal initialization - just load the module class
 */

// Load the module class
require_once(__DIR__ . '/module.php');

// Initialize the module
$module = new NamelessSentry_Module($this, $pages);
?>
