<?php
if (!defined('ABSPATH')) {
    exit;
}

$iristick_file = iristick_static_requested_file();
if (!$iristick_file) {
    status_header(404);
    get_template_part('index');
    return;
}

status_header(200);
iristick_static_render($iristick_file);

