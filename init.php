<?php
/*
 * NamelessSentry Module Init - Working version
 */

try {
    require_once(__DIR__ . '/module.php');
    $sentry_module = new NamelessSentry_Module($module, $pages);
} catch (Exception $e) {
    error_log('NamelessSentry Exception: ' . $e->getMessage());
} catch (Error $e) {
    error_log('NamelessSentry Error: ' . $e->getMessage());
}
?>
