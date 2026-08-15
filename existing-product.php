<?php
if (!defined('ABSPATH')) {
    exit;
}

$iristick_product_file = iristick_existing_product_template_file();
if (!$iristick_product_file) {
    status_header(404);
    return;
}

status_header(200);
iristick_static_render($iristick_product_file);
