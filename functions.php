<?php
if (!defined('ABSPATH')) {
    exit;
}

define('IRISTICK_STATIC_VERSION', '1.2.1');
define('IRISTICK_STATIC_DIR', get_template_directory());
define('IRISTICK_STATIC_URI', get_template_directory_uri());

function iristick_static_page_root() {
    return IRISTICK_STATIC_DIR . '/templates/pages';
}

function iristick_static_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array('style', 'script', 'gallery', 'caption'));
}
add_action('after_setup_theme', 'iristick_static_setup');

function iristick_static_assets() {
    wp_enqueue_style('iristick-static-admin-fixes', get_stylesheet_uri(), array(), IRISTICK_STATIC_VERSION);
    if (in_array(iristick_static_request_path(), array('book-demo', 'trial-order'), true)) {
        wp_enqueue_style(
            'iristick-demo-form',
            IRISTICK_STATIC_URI . '/assets/css/demo-form.css',
            array(),
            IRISTICK_STATIC_VERSION
        );
    }
    if (iristick_static_requested_file()) {
        wp_enqueue_script(
            'iristick-static-navigation',
            IRISTICK_STATIC_URI . '/assets/js/static-navigation.js',
            array(),
            IRISTICK_STATIC_VERSION,
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'iristick_static_assets');

/**
 * Static snapshots do not use the WooCommerce front-end runtime. Loading it on
 * these pages adds global button/form rules and several scripts that can alter
 * the captured layout (most noticeably on the pricing calculator).
 */
function iristick_static_remove_woocommerce_assets() {
    if (!iristick_static_requested_file()) {
        return;
    }

    foreach (array(
        'wc-blocks-style',
        'woocommerce-layout',
        'woocommerce-smallscreen',
        'woocommerce-general',
    ) as $handle) {
        wp_dequeue_style($handle);
    }

    foreach (array(
        'wc-jquery-blockui',
        'wc-add-to-cart',
        'wc-js-cookie',
        'woocommerce',
        'sourcebuster-js',
        'wc-order-attribution',
    ) as $handle) {
        wp_dequeue_script($handle);
    }
}
add_action('wp_enqueue_scripts', 'iristick_static_remove_woocommerce_assets', PHP_INT_MAX);
// WooCommerce Blocks enqueues its stylesheet after wp_enqueue_scripts; remove
// it immediately before WordPress prints styles in wp_head (priority 8).
add_action('wp_head', 'iristick_static_remove_woocommerce_assets', 7);

function iristick_static_should_load_woocommerce_blocks($should_load) {
    return iristick_static_requested_file() ? false : $should_load;
}
add_filter('should_load_woocommerce_block_assets', 'iristick_static_should_load_woocommerce_blocks');

function iristick_static_root() {
    return IRISTICK_STATIC_DIR . '/static';
}

function iristick_static_normalize_path($path) {
    $segments = array();
    foreach (explode('/', str_replace('\\', '/', $path)) as $segment) {
        if ($segment === '' || $segment === '.') {
            continue;
        }
        if ($segment === '..') {
            array_pop($segments);
            continue;
        }
        $segments[] = $segment;
    }
    return implode('/', $segments);
}

function iristick_static_request_path() {
    if (is_admin() || wp_doing_ajax() || (defined('REST_REQUEST') && REST_REQUEST)) {
        return false;
    }

    $request = isset($_SERVER['REQUEST_URI']) ? wp_unslash($_SERVER['REQUEST_URI']) : '/';
    $path = trim((string) wp_parse_url($request, PHP_URL_PATH), '/');
    $path = preg_replace('/\.(?:html?|php)$/i', '', $path);
    $path = preg_replace('#[^a-zA-Z0-9/_\-.]+#u', '', $path);

    if ($path === '') {
        return 'index';
    }
    if (strpos($path, '..') !== false) {
        return false;
    }
    return $path;
}

function iristick_static_requested_file() {
    $path = iristick_static_request_path();
    if ($path === false) {
        return false;
    }

    $relative = $path === 'index' ? 'page.php' : trailingslashit($path) . 'page.php';
    $file = iristick_static_page_root() . '/' . str_replace('/', DIRECTORY_SEPARATOR, $relative);
    $root = realpath(iristick_static_page_root());
    $real = is_file($file) ? realpath($file) : false;

    if ($root && $real && strpos($real, $root) === 0) {
        return $real;
    }
    return false;
}

function iristick_static_template_include($template) {
    return iristick_static_requested_file() ? IRISTICK_STATIC_DIR . '/static-page.php' : $template;
}
add_filter('template_include', 'iristick_static_template_include', 99);

function iristick_static_relative_directory($file) {
    $relative = str_replace('\\', '/', substr($file, strlen(iristick_static_page_root()) + 1));
    $directory = dirname($relative);
    return $directory === '.' ? '' : $directory;
}

function iristick_static_rewrite_url($url, $file) {
    $url = html_entity_decode($url, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    if ($url === '' || $url[0] === '#' || preg_match('#^(?:data:|mailto:|tel:|javascript:)#i', $url)) {
        return $url;
    }

    $parts = wp_parse_url($url);
    if ($parts === false) {
        return $url;
    }
    if (!empty($parts['host']) && !preg_match('/(^|\.)iristick\.com$/i', $parts['host'])) {
        return $url;
    }

    $path = isset($parts['path']) ? $parts['path'] : '';
    $query = isset($parts['query']) ? '?' . $parts['query'] : '';
    $fragment = isset($parts['fragment']) ? '#' . $parts['fragment'] : '';

    if (!empty($parts['host']) || substr($path, 0, 1) === '/') {
        $relative = ltrim($path, '/');
    } else {
        $relative = iristick_static_normalize_path(
            trailingslashit(iristick_static_relative_directory($file)) . $path
        );
    }

    $disk_relative = rawurldecode($relative);
    $disk_path = iristick_static_root() . '/' . str_replace('/', DIRECTORY_SEPARATOR, $disk_relative);
    if (is_file($disk_path)) {
        return IRISTICK_STATIC_URI . '/static/' . $relative . $query . $fragment;
    }

    $page_path = preg_replace('#(?:/)?index\.html?$#i', '', $relative);
    $page_path = preg_replace('/\.html?$/i', '', $page_path);
    return home_url('/' . trim($page_path, '/') . '/') . $query . $fragment;
}

function iristick_static_short_page_title($path, $original = '') {
    $titles = array(
        'index' => 'Trang chủ',
        'pricing' => 'Bảng giá',
        'book-demo' => 'Đặt lịch demo',
        'trial-program' => 'Chương trình dùng thử',
        'trial-order' => 'Đăng ký dùng thử',
        'enterprise' => 'Giải pháp doanh nghiệp',
        'developers' => 'SDK cho nhà phát triển',
        'contact' => 'Liên hệ',
        'sitemap' => 'Sơ đồ trang',
        'company/about-us' => 'Về chúng tôi',
        'company/careers' => 'Tuyển dụng',
        'industries/agriculture' => 'Nông nghiệp',
        'industries/field-service' => 'Dịch vụ hiện trường',
        'industries/healthcare' => 'Chăm sóc sức khỏe',
        'partners/Icona' => 'Đối tác Icona',
        'products/Iristick.Assist' => 'Iristick.Assist',
        'products/Iristick.Collector' => 'Iristick.Collector',
        'products/Iristick.Teams' => 'Iristick.Teams',
        'tools/Iristick.G2-PRO' => 'Iristick.G2 PRO',
        'tools/Iristick.G3' => 'Iristick.G3',
        'tools/Iristick.H1' => 'Iristick.H1',
        'tools/Iristick.H3' => 'Iristick.H3',
        'support/faqs' => 'Câu hỏi thường gặp',
        'policies/cookie-policy' => 'Chính sách cookie',
        'policies/privacy-policy' => 'Chính sách bảo mật',
        'policies/terms-conditions' => 'Điều khoản và điều kiện',
        'blog/news' => 'Tin tức',
        'blog/news/agrifood-professionals-benefit-from-smartglasses' => 'Kính thông minh trong ngành thực phẩm',
        'blog/news/also-and-iristick-smart-glasses-extend-partnership' => 'ALSO và Iristick mở rộng hợp tác',
        'blog/news/challenges-of-field-service-operations-during-summer-holidays-and-how-to-tackle-them' => 'Vận hành dịch vụ hiện trường mùa hè',
        'blog/news/iristick-announces-major-capital-increase' => 'Iristick công bố tăng vốn',
        'blog/news/iristick-distribution-agreement-capestone' => 'Iristick hợp tác cùng Capestone',
        'blog/news/join-webinar-hands-free-remote-assistance-smart-glasses-hazardous-areas' => 'Webinar hỗ trợ từ xa tại khu vực nguy hiểm',
        'blog/news/microsoft-teams-on-iristick-available-on-smart-glasses' => 'Microsoft Teams trên kính Iristick',
        'blog/news/second-generation-smart-glasses-iristick' => 'Kính thông minh Iristick thế hệ thứ hai',
        'blog/news/tackle-business-travel-emissions' => 'Giảm phát thải từ công tác',
        'blog/news/vr-ar-xr-difference' => 'Sự khác biệt giữa VR, AR và XR',
        'blog/news/webinar-hands-free-microsoft-teams-iristick' => 'Webinar Microsoft Teams rảnh tay',
        'blog/news/webinar-handtmann-icona' => 'Webinar cùng Handtmann và Icona',
    );

    return isset($titles[$path]) ? $titles[$path] : $original;
}

function iristick_static_render($file) {
    $html = file_get_contents($file);
    if ($html === false) {
        status_header(404);
        return;
    }

    // Correct awkward machine-translated calls to action in captured pages.
    $html = str_replace(
        'Nói với chúng tôi về một phi công',
        'Liên hệ với chúng tôi',
        $html
    );

    // Use one consistent browser title across every captured page.
    $title_suffix = ' | Iristick Việt Nam';
    $request_path = iristick_static_request_path();
    if (preg_match('#<title\b[^>]*>#i', $html)) {
        $html = preg_replace_callback(
            '#<title\b([^>]*)>(.*?)</title>#is',
            function ($matches) use ($title_suffix, $request_path) {
                $page_title = trim(wp_strip_all_tags(html_entity_decode($matches[2], ENT_QUOTES | ENT_HTML5, 'UTF-8')));
                $page_title = preg_replace('/\s*\|\s*Iristick(?:\s+(?:Việt Nam|VN))?\s*$/iu', '', $page_title);
                $page_title = iristick_static_short_page_title($request_path, $page_title);
                return '<title' . $matches[1] . '>' . esc_html($page_title . $title_suffix) . '</title>';
            },
            $html,
            1
        );
    } else {
        $page_title = iristick_static_short_page_title($request_path, 'Iristick');
        $html = '<title>' . esc_html($page_title . $title_suffix) . '</title>' . $html;
    }

    // Remove SvelteKit preload links and every Svelte hydration/boot script.
    $html = preg_replace('#<link\b[^>]*rel=["\']modulepreload["\'][^>]*>\s*#i', '', $html);
    $html = preg_replace('#<script\b[^>]*src=["\'][^"\']*(?:_app/immutable|script\.js)[^"\']*["\'][^>]*>\s*</script>#is', '', $html);
    $html = preg_replace('#<script\b[^>]*>(?:(?!</script>).)*(?:__sveltekit_|kit\.start\s*\(|client\.crisp\.chat)(?:(?!</script>).)*</script>#is', '', $html);

    // Remove captured analytics/chat attributes and SvelteKit-only body behavior.
    $html = preg_replace('/\sdata-umami-event(?:-[a-z-]+)?=["\'][^"\']*["\']/i', '', $html);
    $html = preg_replace('/\sdata-sveltekit-preload-data=["\'][^"\']*["\']/i', '', $html);

    // Dynamic values used by otherwise static internal forms.
    $html = str_replace(
        array(
            '{{IRISTICK_ADMIN_POST_URL}}',
            '{{IRISTICK_DEMO_FORM_NONCE}}',
            '{{IRISTICK_TRIAL_FORM_NONCE}}',
            '{{IRISTICK_THEME_URI}}',
        ),
        array(
            esc_url(admin_url('admin-post.php')),
            wp_nonce_field('iristick_demo_request', 'iristick_demo_nonce', true, false),
            wp_nonce_field('iristick_trial_request', 'iristick_trial_nonce', true, false),
            esc_url(IRISTICK_STATIC_URI),
        ),
        $html
    );

    $html = preg_replace_callback(
        '#\b(href|src|poster|action)=("|\')([^"\']*)\2#i',
        function ($matches) use ($file) {
            $rewritten = iristick_static_rewrite_url($matches[3], $file);
            // Snapshot logos and a few small illustrations are embedded WebP/SVG data URIs.
            // esc_url() intentionally removes the data protocol, so validate and escape those
            // image sources separately instead of turning them into broken images.
            if (($matches[1] === 'src' || $matches[1] === 'poster')
                && preg_match('#^data:image/(?:avif|gif|jpeg|png|svg\+xml|webp);base64,[a-zA-Z0-9+/=\s]+$#', $rewritten)) {
                $value = esc_attr($rewritten);
            } else {
                $value = esc_url($rewritten);
            }
            return $matches[1] . '=' . $matches[2]
                . $value
                . $matches[2];
        },
        $html
    );

    $html = preg_replace_callback(
        '#\bsrcset=("|\')([^"\']*)\1#i',
        function ($matches) use ($file) {
            $items = array_map(function ($item) use ($file) {
                $bits = preg_split('/\s+/', trim($item), 2);
                $bits[0] = iristick_static_rewrite_url($bits[0], $file);
                return implode(' ', $bits);
            }, explode(',', $matches[2]));
            return 'srcset=' . $matches[1] . esc_attr(implode(', ', $items)) . $matches[1];
        },
        $html
    );

    // Let WordPress/plugins add their required head and footer output.
    ob_start();
    wp_head();
    $wp_head = ob_get_clean();
    // The snapshot already owns the document title; remove WordPress' duplicate.
    $wp_head = preg_replace('#<title\b[^>]*>.*?</title>\s*#is', '', $wp_head);
    $wp_head = preg_replace('#<link\b[^>]*id=["\']wc-blocks-style-css["\'][^>]*>\s*#i', '', $wp_head);
    ob_start();
    wp_body_open();
    $wp_body_open = ob_get_clean();
    ob_start();
    wp_footer();
    $wp_footer = ob_get_clean();

    $html = preg_replace_callback('/<\/head>/i', function () use ($wp_head) {
        return $wp_head . '</head>';
    }, $html, 1);
    $html = preg_replace_callback('/<body\b[^>]*>/i', function ($matches) use ($wp_body_open) {
        return $matches[0] . $wp_body_open;
    }, $html, 1);
    $html = preg_replace_callback('/<\/body>/i', function () use ($wp_footer) {
        return $wp_footer . '</body>';
    }, $html, 1);

    // WooCommerce Blocks may print this after its enqueue phase; static pages
    // contain no Woo blocks, so keep its broad global CSS out of the snapshot.
    $html = preg_replace('#<link\b[^>]*wc-blocks\.css[^>]*>\s*#i', '', $html);

    echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted internal page templates.
}

function iristick_handle_demo_request() {
    if (!isset($_POST['iristick_demo_nonce'])
        || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['iristick_demo_nonce'])), 'iristick_demo_request')) {
        wp_safe_redirect(home_url('/book-demo/?status=invalid'));
        exit;
    }

    // Honeypot: bots commonly fill hidden fields, people do not.
    if (!empty($_POST['website'])) {
        wp_safe_redirect(home_url('/book-demo/?status=success'));
        exit;
    }

    $name = isset($_POST['name']) ? sanitize_text_field(wp_unslash($_POST['name'])) : '';
    $email = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
    $industry = isset($_POST['industry']) ? sanitize_text_field(wp_unslash($_POST['industry'])) : '';
    $company_size = isset($_POST['company_size']) ? sanitize_text_field(wp_unslash($_POST['company_size'])) : '';
    $solution = isset($_POST['solution']) ? sanitize_text_field(wp_unslash($_POST['solution'])) : '';
    $questions = isset($_POST['questions']) ? sanitize_textarea_field(wp_unslash($_POST['questions'])) : '';

    if ($name === '' || !is_email($email) || $solution === '') {
        wp_safe_redirect(home_url('/book-demo/?status=invalid'));
        exit;
    }

    $default_recipient = defined('IRISTICK_DEMO_EMAIL')
        ? sanitize_email(IRISTICK_DEMO_EMAIL)
        : sanitize_email(get_option('admin_email'));
    $recipient = apply_filters('iristick_demo_recipient_email', $default_recipient);
    $subject = sprintf('[Iristick] Yêu cầu đặt lịch demo từ %s', $name);
    $message = implode("\n", array(
        'Có một yêu cầu đặt lịch demo mới:',
        '',
        'Họ và tên: ' . $name,
        'Email: ' . $email,
        'Ngành nghề: ' . ($industry ?: 'Không cung cấp'),
        'Quy mô công ty: ' . ($company_size ?: 'Không cung cấp'),
        'Giải pháp quan tâm: ' . $solution,
        '',
        'Câu hỏi / nội dung muốn trao đổi:',
        $questions ?: 'Không cung cấp',
    ));
    $headers = array('Reply-To: ' . $name . ' <' . $email . '>');
    $sent = wp_mail($recipient, $subject, $message, $headers);

    wp_safe_redirect(home_url('/book-demo/?status=' . ($sent ? 'success' : 'error')));
    exit;
}
add_action('admin_post_nopriv_iristick_demo_request', 'iristick_handle_demo_request');
add_action('admin_post_iristick_demo_request', 'iristick_handle_demo_request');

function iristick_handle_trial_request() {
    if (!isset($_POST['iristick_trial_nonce'])
        || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['iristick_trial_nonce'])), 'iristick_trial_request')) {
        wp_safe_redirect(home_url('/trial-order/?status=invalid'));
        exit;
    }
    if (!empty($_POST['website'])) {
        wp_safe_redirect(home_url('/trial-order/?status=success'));
        exit;
    }

    $name = isset($_POST['name']) ? sanitize_text_field(wp_unslash($_POST['name'])) : '';
    $email = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
    $company = isset($_POST['company']) ? sanitize_text_field(wp_unslash($_POST['company'])) : '';
    $phone = isset($_POST['phone']) ? sanitize_text_field(wp_unslash($_POST['phone'])) : '';
    $hardware = isset($_POST['hardware']) ? sanitize_text_field(wp_unslash($_POST['hardware'])) : '';
    $software = isset($_POST['software']) ? array_map('sanitize_text_field', (array) wp_unslash($_POST['software'])) : array();
    $quantity = isset($_POST['quantity']) ? max(1, min(10, absint($_POST['quantity']))) : 1;
    $notes = isset($_POST['notes']) ? sanitize_textarea_field(wp_unslash($_POST['notes'])) : '';

    if ($name === '' || !is_email($email) || $hardware === '' || empty($software)) {
        wp_safe_redirect(home_url('/trial-order/?status=invalid'));
        exit;
    }

    $default_recipient = defined('IRISTICK_DEMO_EMAIL')
        ? sanitize_email(IRISTICK_DEMO_EMAIL)
        : sanitize_email(get_option('admin_email'));
    $recipient = apply_filters('iristick_trial_recipient_email', $default_recipient);
    $subject = sprintf('[Iristick] Đăng ký gói dùng thử 6 tuần từ %s', $name);
    $message = implode("\n", array(
        'Có một đăng ký gói dùng thử 6 tuần mới:', '',
        'Họ và tên: ' . $name,
        'Email: ' . $email,
        'Số điện thoại: ' . ($phone ?: 'Không cung cấp'),
        'Công ty: ' . ($company ?: 'Không cung cấp'),
        'Thiết bị: ' . $hardware,
        'Phần mềm: ' . implode(', ', $software),
        'Số lượng: ' . $quantity,
        'Tạm tính: €' . number_format(1000 * $quantity, 0, ',', '.') . ' (chưa gồm VAT)',
        '', 'Ghi chú:', $notes ?: 'Không cung cấp',
    ));
    $sent = wp_mail($recipient, $subject, $message, array('Reply-To: ' . $name . ' <' . $email . '>'));
    wp_safe_redirect(home_url('/trial-order/?status=' . ($sent ? 'success' : 'error')));
    exit;
}
add_action('admin_post_nopriv_iristick_trial_request', 'iristick_handle_trial_request');
add_action('admin_post_iristick_trial_request', 'iristick_handle_trial_request');
