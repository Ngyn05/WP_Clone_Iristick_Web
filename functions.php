<?php
if (!defined('ABSPATH')) {
    exit;
}

define('IRISTICK_STATIC_VERSION', '1.8.1');
define('IRISTICK_STATIC_DIR', get_template_directory());
define('IRISTICK_STATIC_URI', get_template_directory_uri());
define('IRISTICK_EUR_TO_VND_RATE', 35000);

function iristick_migrate_contact_email_v1() {
    if (get_option('iristick_contact_email_migrated_v1')) {
        return;
    }

    global $wpdb;
    $old_email = 'info@iristick.com';
    $new_email = 'contact@iristick.vn';
    $wpdb->query($wpdb->prepare(
        "UPDATE {$wpdb->posts} SET post_content = REPLACE(post_content, %s, %s), post_excerpt = REPLACE(post_excerpt, %s, %s) WHERE post_content LIKE %s OR post_excerpt LIKE %s",
        $old_email,
        $new_email,
        $old_email,
        $new_email,
        '%' . $wpdb->esc_like($old_email) . '%',
        '%' . $wpdb->esc_like($old_email) . '%'
    ));
    $wpdb->query($wpdb->prepare(
        "UPDATE {$wpdb->postmeta} SET meta_value = REPLACE(meta_value, %s, %s) WHERE meta_value LIKE %s",
        $old_email,
        $new_email,
        '%' . $wpdb->esc_like($old_email) . '%'
    ));
    update_option('iristick_contact_email_migrated_v1', current_time('mysql'), false);
}
add_action('init', 'iristick_migrate_contact_email_v1', 34);

add_filter('wp_mail_from_name', function () {
    return 'Iristick Việt Nam';
});

function iristick_static_page_root() {
    return IRISTICK_STATIC_DIR . '/templates/pages';
}

function iristick_static_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array('style', 'script', 'gallery', 'caption'));
    add_theme_support('woocommerce');
    add_theme_support('editor-styles');
    add_editor_style('assets/css/editor-blog.css');
}
add_action('after_setup_theme', 'iristick_static_setup');

function iristick_enqueue_site_footer_styles() {
    wp_enqueue_style('iristick-site-footer', IRISTICK_STATIC_URI . '/assets/css/site-footer.css', array(), IRISTICK_STATIC_VERSION);
    wp_enqueue_style('iristick-responsive', IRISTICK_STATIC_URI . '/assets/css/responsive.css', array(), IRISTICK_STATIC_VERSION);
    wp_enqueue_style('iristick-contact-widget', IRISTICK_STATIC_URI . '/assets/css/contact-widget.css', array(), IRISTICK_STATIC_VERSION);
    wp_enqueue_script('iristick-contact-widget', IRISTICK_STATIC_URI . '/assets/js/contact-widget.js', array(), IRISTICK_STATIC_VERSION, true);
}
add_action('wp_enqueue_scripts', 'iristick_enqueue_site_footer_styles', 30);

function iristick_contact_widget_html() {
    return '<aside class="iristick-contact-widget" aria-label="Liên hệ Iristick Việt Nam">'
        . '<div id="iristick-contact-panel" class="iristick-contact-panel" aria-hidden="true">'
        . '<header><div><span>HỖ TRỢ KHÁCH HÀNG</span><strong>Liên hệ Iristick Việt Nam</strong></div><button type="button" class="iristick-contact-close" aria-label="Đóng cửa sổ liên hệ">×</button></header>'
        . '<div class="iristick-contact-options">'
        . '<a href="tel:0917834532"><i aria-hidden="true">☎</i><span><strong>Hotline tư vấn</strong><small>0917 834 532</small></span><b>Gọi ngay</b></a>'
        . '<button type="button" class="iristick-contact-office-toggle"><i aria-hidden="true">⌂</i><span><strong>Hệ thống văn phòng</strong><small>Hà Nội và TP. Hồ Chí Minh</small></span><b>Xem địa chỉ</b></button>'
        . '<a href="https://zalo.me/0917834532" target="_blank" rel="noopener noreferrer"><i aria-hidden="true">Z</i><span><strong>Chat qua Zalo</strong><small>Nhắn tin tư vấn ngay</small></span><b>Mở Zalo</b></a>'
        . '</div><div class="iristick-contact-offices" aria-hidden="true"><button type="button" class="iristick-contact-office-back">← Quay lại</button>'
        . '<article><span>CHI NHÁNH MIỀN BẮC</span><strong>Văn phòng Hà Nội</strong><p>226 Đường Láng, Phường Thịnh Quang, Quận Đống Đa, Hà Nội.</p><a href="tel:02473048700">☎ 024 7304 8700</a></article>'
        . '<article><span>CHI NHÁNH MIỀN NAM</span><strong>Văn phòng TP.HCM</strong><p>137 Đường Hòa Hưng, Phường Hòa Hưng, TP. Hồ Chí Minh.</p><a href="tel:02873048700">☎ 028 7304 8700</a></article>'
        . '</div><p class="iristick-contact-note">Đội ngũ Iristick Việt Nam sẵn sàng hỗ trợ nhu cầu của doanh nghiệp bạn.</p></div>'
        . '<button type="button" class="iristick-contact-toggle" aria-expanded="false" aria-controls="iristick-contact-panel" aria-label="Mở cửa sổ liên hệ"><span class="iristick-contact-phone" aria-hidden="true">☎</span><span class="iristick-contact-x" aria-hidden="true">×</span><em>Liên hệ</em></button>'
        . '</aside>';
}

function iristick_render_contact_widget() {
    echo iristick_contact_widget_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
add_action('wp_footer', 'iristick_render_contact_widget', 20);

function iristick_site_header_html() {
    $logo = IRISTICK_STATIC_URI . '/assets/images/iristick-logo.webp';
    $home = esc_url(home_url('/'));
    $demo = esc_url(home_url('/book-demo/'));
    $pricing = esc_url(home_url('/pricing/'));
    $trial = esc_url(home_url('/trial-program/'));

    return '<div class="navigation-wrapper svelte-1wxfnil">'
        . '<nav class="laptop svelte-1wxfnil">'
        . '<div class="nav-container svelte-1wxfnil">'
        . '<div class="nav-logo svelte-1wxfnil"><a href="' . $home . '"><picture><img src="' . esc_url($logo) . '" alt="logo Iristick" class="svelte-1wxfnil"></picture></a></div>'
        . '<div class="nav-pages svelte-1wxfnil">'
        . '<button class="nav-cat svelte-1wxfnil"><span class="svelte-1wxfnil">Sản phẩm</span><span class="material-symbols-outlined svelte-1wxfnil">keyboard_arrow_down</span></button>'
        . '<button class="nav-cat svelte-1wxfnil"><span class="svelte-1wxfnil">Tài nguyên</span><span class="material-symbols-outlined svelte-1wxfnil">keyboard_arrow_down</span></button>'
        . '<button class="nav-cat svelte-1wxfnil"><span class="svelte-1wxfnil">Ngành nghề</span><span class="material-symbols-outlined svelte-1wxfnil">keyboard_arrow_down</span></button>'
        . '<div class="nav-cat svelte-1wxfnil"><a href="' . $trial . '">Chương trình thử nghiệm</a></div>'
        . '<div class="nav-cat svelte-1wxfnil"><a href="' . $pricing . '">Bảng giá</a></div>'
        . '</div>'
        . '<a href="' . $demo . '"><button class="svelte-1v3bb8g">Đặt lịch demo</button></a>'
        . '</div>'
        . '<div class="subnav svelte-1wxfnil">'
        . '<div class="dropdown-content svelte-1wxfnil">'
        . '<div class="dropdown-cat svelte-1wxfnil">'
        . '<h4><span class="material-symbols-outlined svelte-1wxfnil">robot</span>Sản phẩm</h4>'
        . '<a href="' . esc_url(home_url('/tools/Iristick.G3/')) . '"><div class="dropdown-topic svelte-1wxfnil"><div>Iristick.G3<span class="beta svelte-1wxfnil">MỚI</span></div><span>Kính thông minh thế hệ mới đa năng.</span></div></a>'
        . '<a href="' . esc_url(home_url('/tools/Iristick.G2-PRO/')) . '"><div class="dropdown-topic svelte-1wxfnil"><div>Iristick.G2 PRO</div><span>Kính thông minh công nghiệp bền bỉ.</span></div></a>'
        . '<a href="' . esc_url(home_url('/tools/Iristick.H1/')) . '"><div class="dropdown-topic svelte-1wxfnil"><div>Iristick.H1</div><span>Kính thông minh gắn mũ bảo hộ.</span></div></a>'
        . '<a href="' . esc_url(home_url('/tools/Iristick.H3/')) . '"><div class="dropdown-topic svelte-1wxfnil"><div>Iristick.H3<span class="comingsoon svelte-1wxfnil">Sắp ra mắt</span></div><span>Thế hệ kính thông minh hạng nặng tiếp theo.</span></div></a>'
        . '</div>'
        . '<div class="dropdown-cat svelte-1wxfnil">'
        . '<h4><span class="material-symbols-outlined svelte-1wxfnil">devices</span>Công cụ</h4>'
        . '<a href="' . esc_url(home_url('/products/Iristick.Collector/')) . '"><div class="dropdown-topic svelte-1wxfnil"><div>Iristick.Collector</div><span>Thu thập dữ liệu tốc độ cao, khối lượng lớn.</span></div></a>'
        . '<a href="' . esc_url(home_url('/products/Iristick.Teams/')) . '"><div class="dropdown-topic svelte-1wxfnil"><div>Iristick.Teams</div><span>Gọi video call qua Microsoft Teams.</span></div></a>'
        . '<a href="' . esc_url(home_url('/products/Iristick.Assist/')) . '"><div class="dropdown-topic svelte-1wxfnil"><div>Iristick.Assist</div><span>Gọi video nhanh, hỗ trợ từ xa tức thì.</span></div></a>'
        . '</div>'
        . '<div class="dropdown-cat svelte-1wxfnil">'
        . '<h4><span class="material-symbols-outlined svelte-1wxfnil">code</span>Nhà phát triển</h4>'
        . '<a href="' . esc_url(home_url('/developers/')) . '"><div class="dropdown-topic svelte-1wxfnil"><div>Iristick.SDK</div><span>Tự phát triển ứng dụng kính thông minh.</span></div></a>'
        . '</div>'
        . '<div class="dropdown-cat svelte-1wxfnil">'
        . '<h4><span class="material-symbols-outlined svelte-1wxfnil">handshake</span>Đối tác</h4>'
        . '<a href="' . esc_url(home_url('/partners/Icona/')) . '"><div class="dropdown-topic svelte-1wxfnil"><div>Acty by Icona</div><span>Nền tảng hỗ trợ từ xa chuyên gia.</span></div></a>'
        . '</div>'
        . '</div>'
        . '<div class="dropdown-content svelte-1wxfnil">'
        . '<div class="dropdown-cat svelte-1wxfnil">'
        . '<h4><span class="material-symbols-outlined svelte-1wxfnil">support_agent</span>Hỗ trợ</h4>'
        . '<a target="_blank" rel="noopener noreferrer" href="https://docs.iristick.com"><div class="dropdown-topic svelte-1wxfnil"><div>Trung tâm kiến thức</div><span>Tài liệu kỹ thuật và hướng dẫn sử dụng.</span></div></a>'
        . '<a href="' . esc_url(home_url('/support/faqs/')) . '"><div class="dropdown-topic svelte-1wxfnil"><div>Câu hỏi thường gặp</div><span>Giải đáp các thắc mắc phổ biến.</span></div></a>'
        . '</div>'
        . '<div class="dropdown-cat svelte-1wxfnil">'
        . '<h4><span class="material-symbols-outlined svelte-1wxfnil">meeting_room</span>Công ty</h4>'
        . '<a href="' . esc_url(home_url('/company/about-us/')) . '"><div class="dropdown-topic svelte-1wxfnil"><div>Về chúng tôi</div><span>Tìm hiểu về Iristick Việt Nam.</span></div></a>'
        . '<a href="' . esc_url(home_url('/company/careers/')) . '"><div class="dropdown-topic svelte-1wxfnil"><div>Tuyển dụng</div><span>Gia nhập đội ngũ Iristick.</span></div></a>'
        . '<a href="' . esc_url(home_url('/enterprise/')) . '"><div class="dropdown-topic svelte-1wxfnil"><div>Doanh nghiệp</div><span>Giải pháp quy mô lớn cho doanh nghiệp.</span></div></a>'
        . '</div>'
        . '<div class="dropdown-cat svelte-1wxfnil">'
        . '<h4><span class="material-symbols-outlined svelte-1wxfnil">article</span>Blog</h4>'
        . '<a href="' . esc_url(home_url('/blog/news/')) . '"><div class="dropdown-topic svelte-1wxfnil"><div>Tin tức</div><span>Cập nhật bài viết và sự kiện mới nhất.</span></div></a>'
        . '</div>'
        . '</div>'
        . '<div class="dropdown-content svelte-1wxfnil">'
        . '<div class="dropdown-cat svelte-1wxfnil">'
        . '<h4><span class="material-symbols-outlined svelte-1wxfnil">factory</span>Ngành nghề</h4>'
        . '<a href="' . esc_url(home_url('/industries/agriculture/')) . '"><div class="dropdown-topic svelte-1wxfnil"><div>Nông nghiệp</div><span>Kiểm tra thực địa và nông nghiệp chính xác.</span></div></a>'
        . '<a href="' . esc_url(home_url('/industries/healthcare/')) . '"><div class="dropdown-topic svelte-1wxfnil"><div>Chăm sóc sức khỏe</div><span>Hỗ trợ khám bệnh và phẫu thuật từ xa.</span></div></a>'
        . '<a href="' . esc_url(home_url('/industries/field-service/')) . '"><div class="dropdown-topic svelte-1wxfnil"><div>Dịch vụ hiện trường</div><span>Chẩn đoán và bảo trì kỹ thuật từ xa.</span></div></a>'
        . '</div>'
        . '</div>'
        . '</div>'
        . '</nav>'
        . '<nav class="mobile svelte-1wxfnil">'
        . '<div class="mobile-nav-header svelte-1wxfnil">'
        . '<div class="nav-logo svelte-1wxfnil"><a href="' . $home . '"><picture><img src="' . esc_url($logo) . '" alt="logo Iristick" class="svelte-1wxfnil"></picture></a></div>'
        . '<button class="mobile-menu-button svelte-1wxfnil" type="button" aria-label="Mở menu"><span class="material-symbols-outlined svelte-1wxfnil">menu</span></button>'
        . '</div>'
        . '<div class="mobile-menu svelte-1wxfnil">'
        . '<div class="menu-topic">'
        . '<div class="nav-cat products svelte-1wxfnil">Sản phẩm <span class="material-symbols-outlined svelte-1wxfnil">keyboard_arrow_down</span></div>'
        . '<div class="nav-cat-subnav svelte-1wxfnil">'
        . '<div class="nav-cat-subnav-topic svelte-1wxfnil">'
        . '<span class="topic-title svelte-1wxfnil">Sản phẩm</span>'
        . '<div class="topic-content svelte-1wxfnil">'
        . '<a href="' . esc_url(home_url('/tools/Iristick.G3/')) . '"><div class="dropdown-topic svelte-1wxfnil"><div>Iristick.G3<span class="beta svelte-1wxfnil">MỚI</span></div><span>Kính thông minh thế hệ mới</span></div></a>'
        . '<a href="' . esc_url(home_url('/tools/Iristick.G2-PRO/')) . '"><div class="dropdown-topic svelte-1wxfnil"><div>Iristick.G2 PRO</div><span>Kính thông minh công nghiệp bền bỉ</span></div></a>'
        . '<a href="' . esc_url(home_url('/tools/Iristick.H1/')) . '"><div class="dropdown-topic svelte-1wxfnil"><div>Iristick.H1</div><span>Kính gắn mũ bảo hộ</span></div></a>'
        . '<a href="' . esc_url(home_url('/tools/Iristick.H3/')) . '"><div class="dropdown-topic svelte-1wxfnil"><div>Iristick.H3<span class="comingsoon svelte-1wxfnil">Sắp ra mắt</span></div><span>Kính hạng nặng thế hệ mới</span></div></a>'
        . '</div>'
        . '</div>'
        . '<div class="nav-cat-subnav-topic svelte-1wxfnil">'
        . '<span class="topic-title svelte-1wxfnil">Công cụ</span>'
        . '<div class="topic-content svelte-1wxfnil">'
        . '<a href="' . esc_url(home_url('/products/Iristick.Collector/')) . '"><div class="dropdown-topic svelte-1wxfnil"><div>Iristick.Collector</div><span>Thu thập dữ liệu tốc độ cao</span></div></a>'
        . '<a href="' . esc_url(home_url('/products/Iristick.Teams/')) . '"><div class="dropdown-topic svelte-1wxfnil"><div>Iristick.Teams</div><span>Video call qua Microsoft Teams</span></div></a>'
        . '<a href="' . esc_url(home_url('/products/Iristick.Assist/')) . '"><div class="dropdown-topic svelte-1wxfnil"><div>Iristick.Assist</div><span>Hỗ trợ chuyên gia tức thì</span></div></a>'
        . '</div>'
        . '</div>'
        . '<div class="nav-cat-subnav-topic svelte-1wxfnil">'
        . '<span class="topic-title svelte-1wxfnil">Nhà phát triển</span>'
        . '<div class="topic-content svelte-1wxfnil">'
        . '<a href="' . esc_url(home_url('/developers/')) . '"><div class="dropdown-topic svelte-1wxfnil"><div>Iristick.SDK</div><span>Bộ phát triển ứng dụng SDK</span></div></a>'
        . '</div>'
        . '</div>'
        . '<div class="nav-cat-subnav-topic svelte-1wxfnil">'
        . '<span class="topic-title svelte-1wxfnil">Đối tác</span>'
        . '<div class="topic-content svelte-1wxfnil">'
        . '<a href="' . esc_url(home_url('/partners/Icona/')) . '"><div class="dropdown-topic svelte-1wxfnil"><div>Acty by Icona</div><span>Hỗ trợ từ xa chuyên gia</span></div></a>'
        . '</div>'
        . '</div>'
        . '</div>'
        . '</div>'
        . '<div class="menu-topic">'
        . '<div class="nav-cat resources svelte-1wxfnil">Tài nguyên <span class="material-symbols-outlined svelte-1wxfnil">keyboard_arrow_down</span></div>'
        . '<div class="nav-cat-subnav svelte-1wxfnil">'
        . '<div class="nav-cat-subnav-topic svelte-1wxfnil">'
        . '<span class="topic-title svelte-1wxfnil">Hỗ trợ</span>'
        . '<div class="topic-content svelte-1wxfnil">'
        . '<a target="_blank" rel="noopener noreferrer" href="https://docs.iristick.com"><div class="dropdown-topic svelte-1wxfnil"><div>Trung tâm kiến thức</div><span>Tài liệu kỹ thuật</span></div></a>'
        . '<a href="' . esc_url(home_url('/support/faqs/')) . '"><div class="dropdown-topic svelte-1wxfnil"><div>Câu hỏi thường gặp</div><span>Hỏi đáp phổ biến</span></div></a>'
        . '</div>'
        . '</div>'
        . '<div class="nav-cat-subnav-topic svelte-1wxfnil">'
        . '<span class="topic-title svelte-1wxfnil">Công ty</span>'
        . '<div class="topic-content svelte-1wxfnil">'
        . '<a href="' . esc_url(home_url('/company/about-us/')) . '"><div class="dropdown-topic svelte-1wxfnil"><div>Về chúng tôi</div><span>Tầm nhìn & sứ mệnh</span></div></a>'
        . '<a href="' . esc_url(home_url('/company/careers/')) . '"><div class="dropdown-topic svelte-1wxfnil"><div>Tuyển dụng</div><span>Cơ hội nghề nghiệp</span></div></a>'
        . '<a href="' . esc_url(home_url('/enterprise/')) . '"><div class="dropdown-topic svelte-1wxfnil"><div>Doanh nghiệp</div><span>Giải pháp quy mô lớn</span></div></a>'
        . '</div>'
        . '</div>'
        . '<div class="nav-cat-subnav-topic svelte-1wxfnil">'
        . '<span class="topic-title svelte-1wxfnil">Blog</span>'
        . '<div class="topic-content svelte-1wxfnil">'
        . '<a href="' . esc_url(home_url('/blog/news/')) . '"><div class="dropdown-topic svelte-1wxfnil"><div>Tin tức</div><span>Bài viết & cập nhật mới</span></div></a>'
        . '</div>'
        . '</div>'
        . '</div>'
        . '</div>'
        . '<div class="menu-topic">'
        . '<div class="nav-cat industries svelte-1wxfnil">Ngành nghề <span class="material-symbols-outlined svelte-1wxfnil">keyboard_arrow_down</span></div>'
        . '<div class="nav-cat-subnav svelte-1wxfnil">'
        . '<div class="nav-cat-subnav-topic svelte-1wxfnil">'
        . '<span class="topic-title svelte-1wxfnil">Lĩnh vực</span>'
        . '<div class="topic-content svelte-1wxfnil">'
        . '<a href="' . esc_url(home_url('/industries/agriculture/')) . '"><div class="dropdown-topic svelte-1wxfnil"><div>Nông nghiệp</div><span>Giải pháp thực địa</span></div></a>'
        . '<a href="' . esc_url(home_url('/industries/healthcare/')) . '"><div class="dropdown-topic svelte-1wxfnil"><div>Chăm sóc sức khỏe</div><span>Y tế & khám từ xa</span></div></a>'
        . '<a href="' . esc_url(home_url('/industries/field-service/')) . '"><div class="dropdown-topic svelte-1wxfnil"><div>Dịch vụ hiện trường</div><span>Bảo trì kỹ thuật</span></div></a>'
        . '</div>'
        . '</div>'
        . '</div>'
        . '</div>'
        . '<div class="nav-cat svelte-1wxfnil"><a href="' . $trial . '">Chương trình thử nghiệm</a></div>'
        . '<div class="nav-cat svelte-1wxfnil"><a href="' . $pricing . '">Bảng giá</a></div>'
        . '<div class="mobile-demo-cta" style="width:100%;max-width:100%;box-sizing:border-box;padding:14px 14px 20px;margin:0;border:none;"><a href="' . $demo . '" style="display:flex;align-items:center;justify-content:center;width:100%;max-width:100%;box-sizing:border-box;min-height:48px;padding:12px 16px;border-radius:12px;background:#17171c;color:#ffffff;font-size:16px;font-weight:700;text-align:center;text-decoration:none;box-shadow:0 4px 12px rgba(23,23,28,0.15);white-space:nowrap;margin:0;">Đặt lịch demo</a></div>'
        . '</div>'
        . '</nav>'
        . '</div>';
}

function iristick_site_footer_html() {
    $logo = IRISTICK_STATIC_URI . '/assets/images/iristick-logo.webp';
    return '<footer id="site-footer" class="iristick-vn-footer">'
        . '<div class="iristick-vn-footer__top"><a class="iristick-vn-footer__brand" href="' . esc_url(home_url('/')) . '"><img src="' . esc_url($logo) . '" alt="Iristick Việt Nam"><span>VIỆT NAM</span></a><p>Giải pháp kính thông minh cho doanh nghiệp Việt</p></div>'
        . '<div class="iristick-vn-footer__line"></div><h2>HỖ TRỢ KHÁCH HÀNG</h2><div class="iristick-vn-footer__cards">'
        . '<article><div class="iristick-vn-footer__card-head"><span class="iristick-vn-footer__icon">⌂</span><b>CHI NHÁNH MIỀN BẮC</b></div><h3>VĂN PHÒNG HÀ NỘI</h3><a href="tel:02473048700">024 7304 8700</a><p>226 Đường Láng, Phường Thịnh Quang, Quận Đống Đa, Hà Nội.</p></article>'
        . '<article><div class="iristick-vn-footer__card-head"><span class="iristick-vn-footer__icon">⌂</span><b>CHI NHÁNH MIỀN NAM</b></div><h3>VĂN PHÒNG TP.HCM</h3><a href="tel:02873048700">028 7304 8700</a><p>137 Đường Hòa Hưng, Phường Hòa Hưng, TP. Hồ Chí Minh.</p></article>'
        . '<article class="iristick-vn-footer__card--primary"><div class="iristick-vn-footer__card-head"><span class="iristick-vn-footer__icon">☎</span><b>TƯ VẤN 24/7</b></div><h3>HOTLINE TỔNG ĐÀI</h3><a class="iristick-vn-footer__cta" href="tel:0917834532">0917 834 532</a><p>Hỗ trợ và tư vấn khách hàng mọi lúc, mọi nơi.</p></article>'
        . '</div>'
        . '<div class="iristick-vn-footer__promises"><span>Sản phẩm chính hãng</span><i></i><span>Tư vấn đúng nhu cầu</span><i></i><span>Hỗ trợ tận tâm</span></div>'
        . '<div class="iristick-vn-footer__line iristick-vn-footer__line--links"></div>'
        . '<div class="iristick-vn-footer__links">'
        . '<section><h3>Khách hàng</h3><a href="' . esc_url(home_url('/book-demo/')) . '">Đặt lịch demo</a><a href="' . esc_url(home_url('/trial-program/')) . '">Chương trình dùng thử</a><a href="' . esc_url(home_url('/support/faqs/')) . '">Câu hỏi thường gặp</a><a href="mailto:contact@iristick.vn">Liên hệ hỗ trợ</a><a href="https://zalo.me/0917834532" target="_blank" rel="noopener noreferrer">Tư vấn qua Zalo</a></section>'
        . '<section><h3>Sản phẩm</h3><a href="' . esc_url(home_url('/tools/Iristick.G3/')) . '">Iristick.G3</a><a href="' . esc_url(home_url('/tools/Iristick.G2-PRO/')) . '">Iristick.G2 PRO</a><a href="' . esc_url(home_url('/tools/Iristick.H1/')) . '">Iristick.H1</a><a href="' . esc_url(home_url('/tools/Iristick.H3/')) . '">Iristick.H3</a><a href="' . esc_url(home_url('/shop/')) . '">Tất cả sản phẩm</a></section>'
        . '<section><h3>Giải pháp</h3><a href="' . esc_url(home_url('/products/Iristick.Collector/')) . '">Iristick.Collector</a><a href="' . esc_url(home_url('/products/Iristick.Teams/')) . '">Iristick.Teams</a><a href="' . esc_url(home_url('/products/Iristick.Assist/')) . '">Iristick.Assist</a><a href="' . esc_url(home_url('/developers/')) . '">Iristick.SDK</a><a href="https://docs.iristick.com" target="_blank" rel="noopener noreferrer">Trung tâm kiến thức</a></section>'
        . '<section><h3>Thông tin</h3><a href="' . esc_url(home_url('/company/about-us/')) . '">Về chúng tôi</a><a href="' . esc_url(home_url('/company/careers/')) . '">Tuyển dụng</a><a href="' . esc_url(home_url('/blog/news/')) . '">Tin tức</a><a href="' . esc_url(home_url('/partners/Icona/')) . '">Đối tác</a><a href="' . esc_url(home_url('/sitemap/')) . '">Sơ đồ trang web</a></section>'
        . '</div>'
        . '<div class="iristick-vn-footer__bottom"><span>© ' . esc_html(wp_date('Y')) . ' Iristick Việt Nam</span><nav><a href="' . esc_url(home_url('/policies/privacy-policy/')) . '">Chính sách bảo mật</a><a href="' . esc_url(home_url('/policies/cookie-policy/')) . '">Chính sách cookie</a><a href="' . esc_url(home_url('/policies/terms-conditions/')) . '">Điều khoản sử dụng</a></nav></div></footer>';
}

function iristick_custom_favicon() {
    $version = rawurlencode(IRISTICK_STATIC_VERSION);
    $favicon_48 = IRISTICK_STATIC_URI . '/assets/images/favicon-iristick-large-48.png?v=' . $version;
    $favicon_192 = IRISTICK_STATIC_URI . '/assets/images/favicon-iristick-large-192.png?v=' . $version;
    echo '<link rel="icon" type="image/png" sizes="48x48" href="' . esc_url($favicon_48) . '">';
    echo '<link rel="shortcut icon" type="image/png" sizes="48x48" href="' . esc_url($favicon_48) . '">';
    echo '<link rel="apple-touch-icon" sizes="192x192" href="' . esc_url($favicon_192) . '">';
}
add_action('wp_head', 'iristick_custom_favicon', PHP_INT_MAX);

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
    if (function_exists('is_product') && is_product()) {
        wp_enqueue_style(
            'iristick-woocommerce-product',
            IRISTICK_STATIC_URI . '/assets/css/woocommerce-product.css',
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

function iristick_seed_woocommerce_products() {
    if (!class_exists('WC_Product_Simple') || get_option('iristick_seeded_products_v1')) {
        return;
    }

    // Set the flag before inserts so simultaneous front-end/API requests cannot
    // run the seed twice. It is removed again if product creation throws.
    update_option('iristick_seeded_products_v1', 'running', false);

    $products = array(
        array('Iristick.G3', 'IR-G3', '2275', 'Kính thông minh công nghiệp thế hệ mới.'),
        array('Iristick.G2 PRO', 'IR-G2-PRO', '1975', 'Kính thông minh có chứng nhận an toàn.'),
        array('Iristick.H1', 'IR-H1', '', 'Thiết bị kính thông minh chuyên dụng hạng nặng.'),
        array('Iristick.H3', 'IR-H3', '', 'Kính thông minh Iristick thế hệ tiếp theo.'),
    );

    foreach ($products as $data) {
        if (wc_get_product_id_by_sku($data[1])) {
            continue;
        }
        $product = new WC_Product_Simple();
        $product->set_name($data[0]);
        $product->set_sku($data[1]);
        $product->set_status('publish');
        $product->set_catalog_visibility('visible');
        $product->set_short_description($data[3]);
        $product->set_description('N/A');
        if ($data[2] !== '') {
            $product->set_regular_price($data[2]);
        }
        $product->set_manage_stock(false);
        $product->set_stock_status('instock');
        $product->save();
    }

    update_option('iristick_seeded_products_v1', 1, false);
}
add_action('init', 'iristick_seed_woocommerce_products', 20);

function iristick_cleanup_seed_duplicate() {
    if (!class_exists('WooCommerce') || get_option('iristick_seed_cleanup_v1')) {
        return;
    }
    $duplicates = get_posts(array(
        'post_type' => 'product',
        'post_status' => array('publish', 'draft', 'private'),
        'posts_per_page' => -1,
        'fields' => 'ids',
        'meta_key' => '_sku',
        'meta_value' => 'IR-H1',
        'orderby' => 'ID',
        'order' => 'ASC',
    ));
    foreach (array_slice($duplicates, 1) as $duplicate_id) {
        wp_delete_post($duplicate_id, true);
    }
    update_option('iristick_seed_cleanup_v1', 1, false);
}
add_action('init', 'iristick_cleanup_seed_duplicate', 21);

function iristick_sync_full_product_content() {
    if (!function_exists('wc_get_product_id_by_sku') || get_option('iristick_product_content_v3')) {
        return;
    }

    $asset = IRISTICK_STATIC_URI . '/static/_app/immutable/assets/';
    $catalog = array(
        'IR-G3' => array(
            'short' => 'Kính thông minh được chứng nhận an toàn, kết nối USB-C, camera kép và thiết kế công thái học cho công việc hiện trường.',
            'description' => 'Iristick.G3 là kính thông minh công nghiệp thế hệ mới, hỗ trợ gọi video rảnh tay, quét mã vạch và chia sẻ góc nhìn trực tiếp với chuyên gia từ xa.',
            'media' => $asset . 'website-header-g3.ChWKG3uj.mp4', 'media_type' => 'video',
            'features' => array('Kết nối USB-C', 'Camera kép', 'Quét mã vạch', 'Điều khiển giọng nói', 'Tương thích kính thuốc'),
            'specs' => array('Nguồn điện' => 'Điện thoại thông minh', 'Trọng lượng' => 'N/A', 'Hệ điều hành' => 'Android'),
            'documents' => array('Thông số kỹ thuật G3' => $asset . 'spec-sheet-iristick-g3.d01GFml7.pdf'),
        ),
        'IR-G2-PRO' => array(
            'short' => 'Kính thông minh công nghiệp bền bỉ với camera, quét mã vạch và hỗ trợ từ xa rảnh tay.',
            'description' => 'Iristick.G2 PRO hỗ trợ kỹ thuật viên làm việc rảnh tay, kết nối với chuyên gia và truy cập hướng dẫn ngay tại hiện trường.',
            'media' => $asset . 'website-header-g2-h264.Blab_mEs.mp4', 'media_type' => 'video',
            'features' => array('Camera trung tâm', 'Quét mã vạch', 'Điều khiển giọng nói', 'Kết nối điện thoại'),
            'specs' => array('Nguồn điện' => 'Điện thoại thông minh', 'Hệ điều hành' => 'Android', 'Chứng nhận' => 'N/A'),
            'documents' => array('Thông số kỹ thuật G2' => $asset . 'spec-sheet-iristick-g2.DRFRI4Ef.pdf'),
        ),
        'IR-H1' => array(
            'short' => 'Kính thông minh hạng nặng dành cho môi trường công nghiệp và thiết bị bảo hộ.',
            'description' => 'Iristick.H1 là thiết bị đeo đầu chuyên dụng với camera trung tâm, hỗ trợ quét mã vạch và làm việc rảnh tay.',
            'media' => $asset . 'website-header-h1-h264.B_hsy1Mk.mp4', 'media_type' => 'video',
            'features' => array('Thiết kế hạng nặng', 'Camera trung tâm', 'Quét mã vạch', 'Hỗ trợ thiết bị bảo hộ'),
            'specs' => array('Tình trạng' => 'Ngừng bán', 'Nguồn điện' => 'N/A', 'Trọng lượng' => 'N/A'),
            'documents' => array('Thông số kỹ thuật H1' => $asset . 'spec-sheet-iristick-h1.CgDSlgGY.pdf'),
        ),
        'IR-H3' => array(
            'short' => 'Thế hệ kính thông minh hạng nặng tiếp theo của Iristick.',
            'description' => 'Iristick.H3 đang được phát triển cho các môi trường làm việc công nghiệp đòi hỏi độ bền cao.',
            'media' => '', 'media_type' => 'N/A',
            'features' => array('Thế hệ tiếp theo', 'Thiết kế công nghiệp'),
            'specs' => array('Ngày phát hành' => 'N/A', 'Thông số kỹ thuật' => 'N/A'),
            'documents' => array(),
        ),
        'IR-COLLECTOR' => array(
            'short' => 'Thu thập dữ liệu rảnh tay bằng giọng nói, mã vạch và biểu mẫu có cấu trúc.',
            'description' => 'Iristick.Collector giúp nhân viên ghi nhận dữ liệu tại hiện trường mà không cần cầm giấy, điện thoại hoặc máy quét riêng.',
            'media' => $asset . 'agro-video-header-h264.CDGfQvC9.mp4', 'media_type' => 'video',
            'features' => array('Nhập liệu bằng giọng nói', 'Quét mã vạch', 'Biểu mẫu không giới hạn', 'Đồng bộ dữ liệu'),
            'specs' => array('Loại sản phẩm' => 'Phần mềm', 'Giấy phép' => 'Theo người dùng'),
            'documents' => array(),
        ),
        'IR-TEAMS' => array(
            'short' => 'Gọi Microsoft Teams rảnh tay trực tiếp từ kính thông minh Iristick.',
            'description' => 'Iristick.Teams kết nối kỹ thuật viên hiện trường với đồng nghiệp và chuyên gia bằng cuộc gọi Microsoft Teams rảnh tay.',
            'media' => $asset . 'iristick-teams-header-h264.C8ZelPPr.mp4', 'media_type' => 'video',
            'features' => array('Microsoft Teams', 'Cuộc gọi rảnh tay', 'Đăng nhập một lần', 'Chia sẻ góc nhìn trực tiếp'),
            'specs' => array('Loại sản phẩm' => 'Phần mềm', 'Giấy phép' => 'Theo cặp kính'),
            'documents' => array(),
        ),
        'IR-ASSIST' => array(
            'short' => 'Hỗ trợ từ xa nhanh chóng, đáng tin cậy và không yêu cầu người tham gia cài ứng dụng.',
            'description' => 'Iristick.Assist cho phép chuyên gia nhìn thấy góc nhìn của nhân viên hiện trường và hướng dẫn công việc theo thời gian thực.',
            'media' => $asset . 'assist.SHeORdFk.webp', 'media_type' => 'image',
            'features' => array('Hỗ trợ từ xa', 'Chia sẻ màn hình', 'Giấy phép linh hoạt', 'Không cần cài ứng dụng'),
            'specs' => array('Loại sản phẩm' => 'Phần mềm', 'Giấy phép' => 'Theo cặp kính'),
            'documents' => array(),
        ),
    );

    foreach ($catalog as $sku => $data) {
        $product_id = wc_get_product_id_by_sku($sku);
        $product = $product_id ? wc_get_product($product_id) : false;
        if (!$product) {
            continue;
        }
        $product->set_short_description($data['short']);
        $product->set_description($data['description']);
        $product->update_meta_data('_iristick_media_url', $data['media'] !== '' ? $data['media'] : 'N/A');
        $product->update_meta_data('_iristick_media_type', $data['media_type']);
        $product->update_meta_data('_iristick_features', $data['features']);
        $product->update_meta_data('_iristick_specs', $data['specs']);
        $product->update_meta_data('_iristick_documents', $data['documents'] ?: array('N/A' => 'N/A'));
        $product->update_meta_data('_iristick_faq', array(
            array('question' => 'Sản phẩm này phù hợp với ai?', 'answer' => $data['short']),
            array('question' => 'Tôi có thể yêu cầu tư vấn không?', 'answer' => 'Có. Vui lòng sử dụng biểu mẫu đặt lịch demo để liên hệ đội ngũ Iristick Việt Nam.'),
        ));
        $product->set_stock_status('instock');
        $product->save();
        wc_delete_product_transients($product->get_id());
    }

    update_option('iristick_product_content_v3', current_time('mysql'), false);
}
add_action('init', 'iristick_sync_full_product_content', 22);

function iristick_sync_product_categories() {
    if (!taxonomy_exists('product_cat')) {
        return;
    }
    $categories = array('san-pham' => 'Sản phẩm');
    foreach ($categories as $slug => $name) {
        if (!term_exists($slug, 'product_cat')) {
            wp_insert_term($name, 'product_cat', array('slug' => $slug));
        }
    }
    $assignments = array(
        'IR-G3' => 'san-pham', 'IR-G2-PRO' => 'san-pham', 'IR-H1' => 'san-pham', 'IR-H3' => 'san-pham',
    );
    foreach ($assignments as $sku => $category_slug) {
        $product_id = wc_get_product_id_by_sku($sku);
        if ($product_id) {
            wp_set_object_terms($product_id, $category_slug, 'product_cat', false);
            wc_delete_product_transients($product_id);
        }
    }
}
add_action('init', 'iristick_sync_product_categories', 24);

/**
 * Collector, Teams and Assist are static tools, not WooCommerce products.
 * Move the records previously seeded by this theme to Trash once so they stay
 * recoverable from the WordPress admin.
 */
function iristick_remove_tools_from_woocommerce() {
    if (!function_exists('wc_get_product_id_by_sku') || get_option('iristick_tools_removed_from_wc_v1')) {
        return;
    }

    foreach (array('IR-COLLECTOR', 'IR-TEAMS', 'IR-ASSIST') as $sku) {
        $product_id = wc_get_product_id_by_sku($sku);
        if ($product_id) {
            wp_trash_post($product_id);
        }
    }

    update_option('iristick_tools_removed_from_wc_v1', 1, false);
}
add_action('init', 'iristick_remove_tools_from_woocommerce', 26);

function iristick_extract_static_product_faqs($file) {
    $html = is_file($file) ? file_get_contents($file) : '';
    if ($html === '' || !preg_match('/>FAQ<\/p>/i', $html, $start, PREG_OFFSET_CAPTURE)) {
        return array();
    }
    $section = substr($html, $start[0][1]);
    $footer_position = strpos($section, 'footer-wrapper');
    if ($footer_position !== false) {
        $section = substr($section, 0, $footer_position);
    }
    preg_match_all(
        '#<div class="add-content-topic[^"]*"><div[^>]*><strong>(.*?)</strong></div>\s*(.*?)</div>#is',
        $section,
        $matches,
        PREG_SET_ORDER
    );
    $faqs = array();
    foreach ($matches as $match) {
        $question = trim(wp_strip_all_tags(html_entity_decode($match[1], ENT_QUOTES | ENT_HTML5, 'UTF-8')));
        $answer = trim(wp_strip_all_tags(html_entity_decode($match[2], ENT_QUOTES | ENT_HTML5, 'UTF-8')));
        if ($question !== '' && $answer !== '') {
            $faqs[] = array('question' => $question, 'answer' => $answer);
        }
    }
    return $faqs;
}

function iristick_extract_static_product_sections($file) {
    $html = is_file($file) ? file_get_contents($file) : '';
    $result = array('features' => array(), 'specs' => array(), 'documents' => array());
    if ($html === '') {
        return $result;
    }
    $headers = array();
    $offset = 0;
    while (($header_position = strpos($html, '<div class="additional-header', $offset)) !== false) {
        $paragraph_position = strpos($html, '<p', $header_position);
        $paragraph_start = $paragraph_position !== false ? strpos($html, '>', $paragraph_position) : false;
        $paragraph_end = $paragraph_start !== false ? strpos($html, '</p>', $paragraph_start) : false;
        if ($paragraph_start === false || $paragraph_end === false) {
            break;
        }
        $headers[] = array(
            'position' => $header_position,
            'name' => substr($html, $paragraph_start + 1, $paragraph_end - $paragraph_start - 1),
        );
        $offset = $paragraph_end + 4;
    }
    $count = count($headers);
    for ($index = 0; $index < $count; $index++) {
        $start = $headers[$index]['position'];
        $end = $index + 1 < $count ? $headers[$index + 1]['position'] : strlen($html);
        $section = substr($html, $start, $end - $start);
        $section_name = trim(wp_strip_all_tags(html_entity_decode($headers[$index]['name'], ENT_QUOTES | ENT_HTML5, 'UTF-8')));
        $normalized = strtolower(remove_accents($section_name));
        if (strpos($normalized, 'faq') !== false) {
            continue;
        }
        $topics = array();
        $topic_offset = 0;
        $topic_marker = '<div class="add-content-topic';
        while (($topic_position = strpos($section, $topic_marker, $topic_offset)) !== false) {
            $next_topic = strpos($section, $topic_marker, $topic_position + strlen($topic_marker));
            $topic_html = substr($section, $topic_position, ($next_topic !== false ? $next_topic : strlen($section)) - $topic_position);
            $strong_start = strpos($topic_html, '<strong>');
            $strong_end = $strong_start !== false ? strpos($topic_html, '</strong>', $strong_start) : false;
            if ($strong_start !== false && $strong_end !== false) {
                $answer_start = strpos($topic_html, '</div>', $strong_end);
                $topics[] = array(
                    'title' => substr($topic_html, $strong_start + 8, $strong_end - $strong_start - 8),
                    'answer' => $answer_start !== false ? substr($topic_html, $answer_start + 6) : '',
                    'html' => $topic_html,
                );
            }
            $topic_offset = $next_topic !== false ? $next_topic : strlen($section);
        }
        foreach ($topics as $topic) {
            $title = trim(wp_strip_all_tags(html_entity_decode($topic['title'], ENT_QUOTES | ENT_HTML5, 'UTF-8')));
            $description = trim(wp_strip_all_tags(html_entity_decode($topic['answer'], ENT_QUOTES | ENT_HTML5, 'UTF-8')));
            if ($title === '') {
                continue;
            }
            $unique_title = $title;
            if (strpos($normalized, 'document') !== false || strpos($normalized, 'tai lieu') !== false) {
                $suffix = 2;
                while (isset($result['documents'][$unique_title])) {
                    $unique_title = $title . ' (' . $suffix++ . ')';
                }
                if (preg_match('#href=("|\')([^"\']+)\1#i', $topic['html'], $link)) {
                    $result['documents'][$unique_title] = $link[2];
                } else {
                    $result['documents'][$unique_title] = $description !== '' ? $description : 'N/A';
                }
            } elseif (strpos($normalized, 'feature') !== false || strpos($normalized, 'tinh nang') !== false) {
                $suffix = 2;
                while (isset($result['features'][$unique_title])) {
                    $unique_title = $title . ' (' . $suffix++ . ')';
                }
                $result['features'][$unique_title] = $description !== '' ? $description : 'N/A';
            } else {
                $suffix = 2;
                while (isset($result['specs'][$unique_title])) {
                    $unique_title = $title . ' (' . $suffix++ . ')';
                }
                $result['specs'][$unique_title] = $description !== '' ? $description : 'N/A';
            }
        }
    }
    return $result;
}

function iristick_sync_original_product_faqs() {
    if (!function_exists('wc_get_product_id_by_sku') || get_option('iristick_original_faq_sync_v1')) {
        return;
    }
    $sources = array(
        'IR-G3' => '/tools/Iristick.G3/page.php',
        'IR-G2-PRO' => '/tools/Iristick.G2-PRO/page.php',
        'IR-H1' => '/tools/Iristick.H1/page.php',
        'IR-H3' => '/tools/Iristick.H3/page.php',
        'IR-COLLECTOR' => '/products/Iristick.Collector/page.php',
        'IR-TEAMS' => '/products/Iristick.Teams/page.php',
        'IR-ASSIST' => '/products/Iristick.Assist/page.php',
    );
    foreach ($sources as $sku => $relative_file) {
        $product_id = wc_get_product_id_by_sku($sku);
        $product = $product_id ? wc_get_product($product_id) : false;
        if (!$product) {
            continue;
        }
        $faqs = iristick_extract_static_product_faqs(iristick_static_page_root() . $relative_file);
        $product->update_meta_data('_iristick_faq', $faqs ?: array(
            array('question' => 'N/A', 'answer' => 'N/A'),
        ));
        $product->save_meta_data();
        wc_delete_product_transients($product_id);
    }
    update_option('iristick_original_faq_sync_v1', current_time('mysql'), false);
}
add_action('init', 'iristick_sync_original_product_faqs', 23);

function iristick_sync_original_product_sections() {
    if (!function_exists('wc_get_product_id_by_sku') || get_option('iristick_original_sections_sync_v5')) {
        return;
    }
    $sources = array(
        'IR-G3' => '/tools/Iristick.G3/page.php', 'IR-G2-PRO' => '/tools/Iristick.G2-PRO/page.php',
        'IR-H1' => '/tools/Iristick.H1/page.php', 'IR-H3' => '/tools/Iristick.H3/page.php',
        'IR-COLLECTOR' => '/products/Iristick.Collector/page.php', 'IR-TEAMS' => '/products/Iristick.Teams/page.php',
        'IR-ASSIST' => '/products/Iristick.Assist/page.php',
    );
    foreach ($sources as $sku => $relative_file) {
        $product_id = wc_get_product_id_by_sku($sku);
        $product = $product_id ? wc_get_product($product_id) : false;
        if (!$product) {
            continue;
        }
        $sections = iristick_extract_static_product_sections(iristick_static_page_root() . $relative_file);
        if ($sections['features']) {
            $product->update_meta_data('_iristick_features', $sections['features']);
        }
        if ($sections['specs']) {
            $product->update_meta_data('_iristick_specs', $sections['specs']);
        }
        if ($sections['documents']) {
            $product->update_meta_data('_iristick_documents', $sections['documents']);
        }
        $product->save_meta_data();
        wc_delete_product_transients($product_id);
    }
    update_option('iristick_original_sections_sync_v5', current_time('mysql'), false);
}
add_action('init', 'iristick_sync_original_product_sections', 25);

function iristick_clean_imported_section_label($label, $description, $fallback_number) {
    $plain_label = strtolower(remove_accents(trim((string) $label)));
    $plain_description = strtolower(remove_accents(wp_strip_all_tags((string) $description)));
    if ($plain_label === 'bo & phat') {
        return 'Cắm và sử dụng';
    }
    if (!preg_match('/^(?:name|comment)(?:\s*\(\d+\))?$/', $plain_label)) {
        return $label;
    }
    if (strpos($plain_description, 'toa') !== false || strpos($plain_description, 'don thuoc') !== false || strpos($plain_description, 'prescription') !== false) {
        return 'Tròng kính theo toa';
    }
    if (strpos($plain_description, 'oled') !== false || strpos($plain_description, 'man hinh') !== false) {
        return 'Màn hình tối ưu';
    }
    if (strpos($plain_description, 'ac quy') !== false || strpos($plain_description, 'pin') !== false || strpos($plain_description, 'battery') !== false) {
        return 'Pin dùng cả ca';
    }
    if (strpos($plain_description, 'android') !== false || strpos($plain_description, 'usb-c') !== false || strpos($plain_description, 'dien thoai thong minh') !== false) {
        return 'Hoạt động bằng điện thoại thông minh';
    }
    return 'Thông tin bổ sung ' . $fallback_number;
}

function iristick_cleanup_imported_product_labels() {
    if (!function_exists('wc_get_products') || get_option('iristick_clean_product_labels_v1')) {
        return;
    }
    foreach (wc_get_products(array('status' => 'publish', 'limit' => -1)) as $product) {
        foreach (array('_iristick_features', '_iristick_specs') as $meta_key) {
            $items = (array) $product->get_meta($meta_key);
            $clean = array();
            $number = 1;
            foreach ($items as $label => $description) {
                $new_label = iristick_clean_imported_section_label($label, $description, $number++);
                $unique_label = $new_label;
                $suffix = 2;
                while (isset($clean[$unique_label])) {
                    $unique_label = $new_label . ' (' . $suffix++ . ')';
                }
                $clean[$unique_label] = $description;
            }
            $product->update_meta_data($meta_key, $clean);
        }
        $product->save_meta_data();
        wc_delete_product_transients($product->get_id());
    }
    update_option('iristick_clean_product_labels_v1', current_time('mysql'), false);
}
add_action('init', 'iristick_cleanup_imported_product_labels', 26);

function iristick_buy_now_button_text($text) {
    return 'Mua ngay';
}
add_filter('woocommerce_product_single_add_to_cart_text', 'iristick_buy_now_button_text');

/**
 * One-time database migration: existing prices were entered in EUR. Persist
 * their VND values in WooCommerce so no run-time conversion is needed.
 */
function iristick_migrate_product_prices_to_vnd() {
    if (!function_exists('wc_get_products') || get_option('iristick_prices_stored_as_vnd_v1')) {
        return;
    }

    $product_ids = wc_get_products(array(
        'status' => array('publish', 'private', 'draft', 'pending'),
        'limit' => -1,
        'return' => 'ids',
    ));

    foreach ($product_ids as $product_id) {
        $product = wc_get_product($product_id);
        if (!$product) {
            continue;
        }

        $regular_price = $product->get_regular_price('edit');
        $sale_price = $product->get_sale_price('edit');
        if ($regular_price !== '') {
            $product->set_regular_price((float) $regular_price * IRISTICK_EUR_TO_VND_RATE);
        }
        if ($sale_price !== '') {
            $product->set_sale_price((float) $sale_price * IRISTICK_EUR_TO_VND_RATE);
        }
        $product->save();
        wc_delete_product_transients($product_id);
    }

    update_option('iristick_prices_stored_as_vnd_v1', current_time('mysql'), false);
}
add_action('init', 'iristick_migrate_product_prices_to_vnd', 27);

function iristick_import_static_blog_posts() {
    if (get_option('iristick_blog_posts_imported_v1')) {
        return;
    }

    $news_root = iristick_static_page_root() . '/blog/news';
    if (!is_dir($news_root)) {
        return;
    }
    $category = term_exists('tin-tuc', 'category');
    if (!$category) {
        $category = wp_insert_term('Tin tức', 'category', array('slug' => 'tin-tuc'));
    }
    $category_id = is_array($category) ? (int) $category['term_id'] : (int) $category;

    foreach (glob($news_root . '/*/page.php') as $file) {
        $slug = basename(dirname($file));
        if (get_page_by_path($slug, OBJECT, 'post')) {
            continue;
        }
        $html = file_get_contents($file);
        if (!preg_match('#<h1\b[^>]*>(.*?)</h1>#is', $html, $title_match)) {
            continue;
        }
        $title = html_entity_decode(wp_strip_all_tags($title_match[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $excerpt = '';
        if (preg_match('#<p\b[^>]*class="[^"]*intro[^"]*"[^>]*>(.*?)</p>#is', $html, $excerpt_match)) {
            $excerpt = trim(wp_strip_all_tags($excerpt_match[1]));
        }
        $content = '';
        if (preg_match_all('#<div class="block">(.*?)</div>#is', $html, $blocks)) {
            foreach ($blocks[1] as $block) {
                $block = preg_replace('#<!--.*?-->#s', '', $block);
                $block = preg_replace('#<span\b[^>]*class=["\'][^"\']*bold[^"\']*["\'][^>]*>(.*?)</span>#is', '<strong>$1</strong>', $block);
                $content .= '<p>' . trim($block) . '</p>';
            }
        }
        if ($content === '') {
            $content = '<p>' . ($excerpt ?: 'N/A') . '</p>';
        }
        $image_url = '';
        $before_title = substr($html, 0, strpos($html, $title_match[0]));
        if (preg_match_all('#<img\b[^>]*src=["\']([^"\']+)["\'][^>]*>#i', $before_title, $images) && !empty($images[1])) {
            $image_url = iristick_static_rewrite_url(end($images[1]), $file);
        }
        $post_date = current_time('mysql');
        if (preg_match('/(\d{1,2})\s+Tháng\s+(\d{1,2})\s+năm\s+(\d{4})/u', $html, $date_match)) {
            $post_date = sprintf('%04d-%02d-%02d 09:00:00', $date_match[3], $date_match[2], $date_match[1]);
        }
        $post_id = wp_insert_post(array(
            'post_type' => 'post', 'post_status' => 'publish', 'post_name' => $slug,
            'post_title' => $title, 'post_excerpt' => $excerpt, 'post_content' => wp_kses_post($content),
            'post_date' => $post_date, 'post_category' => $category_id ? array($category_id) : array(),
        ));
        if (!is_wp_error($post_id) && $image_url) {
            update_post_meta($post_id, '_iristick_blog_image_url', esc_url_raw($image_url));
        }
    }
    update_option('iristick_blog_posts_imported_v1', current_time('mysql'), false);
}
add_action('init', 'iristick_import_static_blog_posts', 30);

// Keep blog editing simple: classic title/content editor with a large featured
// image panel above the content, matching the requested editorial workflow.
add_filter('use_block_editor_for_post_type', function ($use_block_editor, $post_type) {
    return $post_type === 'post' ? false : $use_block_editor;
}, 20, 2);
add_filter('use_block_editor_for_post', function ($use_block_editor, $post) {
    return $post instanceof WP_Post && $post->post_type === 'post' ? false : $use_block_editor;
}, 20, 2);

// Standardize Yoast SEO titles to always end with '| Iristick Việt Nam'
add_filter('wpseo_title', function ($title) {
    if (trim($title) === '') {
        return '';
    }
    // Remove existing suffix to avoid double suffixing
    $clean_title = preg_replace('/\s*\|\s*Iristick(?:\s+(?:Việt Nam|VN))?\s*$/iu', '', $title);
    return trim($clean_title) . ' | Iristick Việt Nam';
}, 30);

// Exclude non-content WooCommerce utility pages (cart, checkout, my-account) from XML Sitemap
add_filter('wpseo_exclude_from_sitemap_by_post_ids', function ($excluded_ids) {
    $cart_id = get_option('woocommerce_cart_page_id');
    $checkout_id = get_option('woocommerce_checkout_page_id');
    $myaccount_id = get_option('woocommerce_myaccount_page_id');
    
    if ($cart_id) {
        $excluded_ids[] = (int) $cart_id;
    }
    if ($checkout_id) {
        $excluded_ids[] = (int) $checkout_id;
    }
    if ($myaccount_id) {
        $excluded_ids[] = (int) $myaccount_id;
    }
    return array_unique(array_filter($excluded_ids));
});

// Set noindex meta tag on WooCommerce utility pages (cart, checkout, my-account)
add_filter('wpseo_robots', function ($robots) {
    if (function_exists('is_cart') && (is_cart() || is_checkout() || is_account_page())) {
        return 'noindex,follow';
    }
    return $robots;
});

add_action('add_meta_boxes_post', function () {
    remove_meta_box('postimagediv', 'post', 'side');
    add_meta_box('postimagediv', 'Ảnh đại diện', 'post_thumbnail_meta_box', 'post', 'normal', 'high');
});

function iristick_import_blog_featured_images() {
    if (get_option('iristick_blog_featured_images_v1')) {
        return;
    }
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';

    $posts = get_posts(array('post_type' => 'post', 'post_status' => 'any', 'numberposts' => -1, 'category_name' => 'tin-tuc'));
    foreach ($posts as $post) {
        if (has_post_thumbnail($post)) {
            continue;
        }
        $image_url = get_post_meta($post->ID, '_iristick_blog_image_url', true);
        $marker = '/wp-content/themes/iristick-static-theme/static/';
        $position = strpos($image_url, $marker);
        if (!$image_url || $position === false) {
            continue;
        }
        $relative = rawurldecode(substr($image_url, $position + strlen($marker)));
        $source = IRISTICK_STATIC_DIR . '/static/' . str_replace('/', DIRECTORY_SEPARATOR, $relative);
        if (!is_file($source)) {
            continue;
        }
        $temp = wp_tempnam(basename($source));
        if (!$temp || !copy($source, $temp)) {
            continue;
        }
        $file_array = array('name' => sanitize_file_name(basename($source)), 'tmp_name' => $temp);
        $attachment_id = media_handle_sideload($file_array, $post->ID, $post->post_title);
        if (is_wp_error($attachment_id)) {
            @unlink($temp);
            continue;
        }
        set_post_thumbnail($post->ID, $attachment_id);
    }
    update_option('iristick_blog_featured_images_v1', current_time('mysql'), false);
}
add_action('init', 'iristick_import_blog_featured_images', 31);

function iristick_static_editable_page_paths() {
    return array(
        'index', 'book-demo', 'contact', 'developers', 'enterprise', 'pricing', 'sitemap',
        'trial-order', 'trial-program', 'company/about-us', 'company/careers',
        'industries/agriculture', 'industries/field-service', 'industries/healthcare',
        'partners/Icona', 'policies/cookie-policy', 'policies/privacy-policy',
        'policies/terms-conditions', 'support/faqs',
    );
}

function iristick_extract_static_page_body($html) {
    if (preg_match('#</nav></div>(.*?)(?=<div class="footer-wrapper)#is', $html, $match)) {
        $content = preg_replace('#<!--.*?-->#s', '', $match[1]);
        return trim($content);
    }
    return '';
}

function iristick_import_static_pages_to_database() {
    if (get_option('iristick_static_pages_imported_v1')) {
        return;
    }
    // Prevent two simultaneous first requests from importing the same pages twice.
    if (!add_option('iristick_static_pages_import_lock_v1', time(), '', false)) {
        return;
    }
    $parents = array();
    $parent_titles = array('company' => 'Công ty', 'industries' => 'Ngành nghề', 'partners' => 'Đối tác', 'policies' => 'Chính sách', 'support' => 'Hỗ trợ');
    foreach ($parent_titles as $slug => $title) {
        $existing = get_page_by_path($slug, OBJECT, 'page');
        $parents[$slug] = $existing ? $existing->ID : wp_insert_post(array(
            'post_type' => 'page', 'post_status' => 'publish', 'post_name' => $slug,
            'post_title' => $title . ' | Iristick Việt Nam', 'post_content' => '',
        ));
    }
    foreach (iristick_static_editable_page_paths() as $path) {
        $existing_ids = get_posts(array('post_type' => 'page', 'post_status' => 'any', 'numberposts' => 1, 'meta_key' => '_iristick_static_path', 'meta_value' => $path, 'fields' => 'ids'));
        if ($existing_ids) {
            continue;
        }
        $file = $path === 'index'
            ? iristick_static_page_root() . '/page.php'
            : iristick_static_page_root() . '/' . $path . '/page.php';
        if (!is_file($file)) {
            continue;
        }
        $html = file_get_contents($file);
        $content = iristick_extract_static_page_body($html);
        $segments = explode('/', $path);
        $slug = $path === 'index' ? 'trang-chu' : end($segments);
        $parent = count($segments) > 1 && isset($parents[$segments[0]]) ? (int) $parents[$segments[0]] : 0;
        $title = iristick_static_short_page_title($path, ucfirst(str_replace('-', ' ', $slug)));
        $post_id = wp_insert_post(array(
            'post_type' => 'page', 'post_status' => 'publish', 'post_parent' => $parent,
            'post_name' => sanitize_title($slug), 'post_title' => $title . ' | Iristick Việt Nam',
            'post_content' => $content,
        ));
        if (!is_wp_error($post_id)) {
            update_post_meta($post_id, '_iristick_static_path', $path);
            // Pre-seed Yoast SEO titles and meta descriptions into the database
            update_post_meta($post_id, '_yoast_wpseo_title', $title);
            
            // Extract a clean snippet for description or fall back to a standard default
            $desc = '';
            if ($excerpt !== '') {
                $desc = $excerpt;
            } elseif (preg_match('#<p\b[^>]*>(.*?)</p>#is', $content, $p_match)) {
                $desc = trim(wp_strip_all_tags($p_match[1]));
            }
            if ($desc !== '') {
                $desc = wp_html_excerpt($desc, 155, '…');
                update_post_meta($post_id, '_yoast_wpseo_metadesc', $desc);
            }
        }
    }
    update_option('iristick_static_pages_imported_v1', current_time('mysql'), false);
    delete_option('iristick_static_pages_import_lock_v1');
}
add_action('init', 'iristick_import_static_pages_to_database', 32);

// Upgrade migration: write Yoast SEO default values directly to database for already imported pages, posts, and products
function iristick_migrate_yoast_seo_db_values() {
    if (get_option('iristick_yoast_seo_db_seeded_v14')) {
        return;
    }

    // Comprehensive dictionary of high-ranking, concise Vietnamese SEO titles, focus keyphrases & optimal descriptions (120-145 chars)
    $seo_data = array(
        // News Posts
        'webinar-hands-free-microsoft-teams-iristick' => array(
            'kw' => 'Microsoft Teams trên kính thông minh',
            'title' => 'Họp Microsoft Teams trên kính Iristick',
            'desc' => 'Giải pháp họp Microsoft Teams rảnh tay bằng giọng nói trên kính thông minh Iristick giúp chuyên gia hỗ trợ kỹ thuật viên từ xa.',
        ),
        'vr-ar-xr-difference' => array(
            'kw' => 'phân biệt VR AR XR',
            'title' => 'Phân biệt thực tế ảo VR AR và XR',
            'desc' => 'Tìm hiểu sự khác biệt giữa công nghệ thực tế ảo VR, AR và XR cùng các ứng dụng thực tế của kính thông minh trong công nghiệp.',
        ),
        'agrifood-professionals-benefit-from-smartglasses' => array(
            'kw' => 'kính thông minh nông nghiệp',
            'title' => 'Kính thông minh trong nông nghiệp',
            'desc' => 'Ứng dụng kính thông minh Iristick giúp chuyên gia nông nghiệp và thực phẩm thu thập dữ liệu hiện trường chính xác và nhanh chóng.',
        ),
        'also-and-iristick-smart-glasses-extend-partnership' => array(
            'kw' => 'ALSO và Iristick',
            'title' => 'ALSO và Iristick mở rộng hợp tác',
            'desc' => 'ALSO và Iristick hợp tác chiến lược mở rộng mạng lưới phân phối kính thông minh công nghiệp và giải pháp hỗ trợ từ xa.',
        ),
        'challenges-of-field-service-operations-during-summer-holidays-and-how-to-tackle-them' => array(
            'kw' => 'dịch vụ hiện trường mùa hè',
            'title' => 'Vận hành dịch vụ hiện trường mùa hè',
            'desc' => 'Giải pháp tối ưu hóa hoạt động dịch vụ hiện trường và khắc phục sự cố từ xa trong kỳ nghỉ với kính thông minh Iristick.',
        ),
        'iristick-announces-major-capital-increase' => array(
            'kw' => 'Iristick tăng vốn',
            'title' => 'Iristick công bố tăng vốn phát triển',
            'desc' => 'Iristick tăng vốn đầu tư nhằm mở rộng phát triển kính thông minh công nghiệp và các ứng dụng hỗ trợ rảnh tay doanh nghiệp.',
        ),
        'iristick-distribution-agreement-capestone' => array(
            'kw' => 'Iristick Capestone',
            'title' => 'Iristick hợp tác cùng Capestone',
            'desc' => 'Iristick ký thỏa thuận phân phối cùng Capestone nhằm mở rộng thị trường kính thông minh chuyên dụng tại Châu Âu và toàn cầu.',
        ),
        'join-webinar-hands-free-remote-assistance-smart-glasses-hazardous-areas' => array(
            'kw' => 'hỗ trợ từ xa khu vực nguy hiểm',
            'title' => 'Hỗ trợ từ xa tại khu vực nguy hiểm',
            'desc' => 'Webinar chia sẻ giải pháp hỗ trợ từ xa rảnh tay bằng kính thông minh Iristick tại các môi trường công nghiệp nguy hiểm.',
        ),
        'microsoft-teams-on-iristick-available-on-smart-glasses' => array(
            'kw' => 'Microsoft Teams kính Iristick',
            'title' => 'Microsoft Teams trên kính Iristick',
            'desc' => 'Trải nghiệm ứng dụng Microsoft Teams trên kính thông minh Iristick giúp gọi video và trao đổi công việc rảnh tay hiệu quả.',
        ),
        'second-generation-smart-glasses-iristick' => array(
            'kw' => 'kính thông minh Iristick thế hệ 2',
            'title' => 'Kính thông minh Iristick thế hệ 2',
            'desc' => 'Khám phá thế hệ kính thông minh Iristick thứ hai với camera zoom quang học vượt trội và khả năng tương thích môi trường khắt khe.',
        ),
        'tackle-business-travel-emissions' => array(
            'kw' => 'giảm phát thải công tác',
            'title' => 'Giảm phát thải từ các chuyến công tác',
            'desc' => 'Cắt giảm khí thải carbon và chi phí đi lại nhờ áp dụng kính thông minh Iristick cho hoạt động kiểm tra và bảo trì từ xa.',
        ),
        'webinar-handtmann-icona' => array(
            'kw' => 'Webinar Handtmann Icona',
            'title' => 'Webinar cùng Handtmann và Icona',
            'desc' => 'Hội thảo trực tuyến chia sẻ kinh nghiệm ứng dụng kính thông minh Iristick nâng cao năng suất và bảo trì thiết bị tối ưu.',
        ),

        // Static Pages
        'index' => array(
            'kw' => 'kính thông minh công nghiệp',
            'title' => 'Kính thông minh công nghiệp',
            'desc' => 'Iristick cung cấp kính thông minh công nghiệp và giải pháp hỗ trợ từ xa rảnh tay hàng đầu cho doanh nghiệp tại Việt Nam.',
        ),
        'pricing' => array(
            'kw' => 'bảng giá kính thông minh',
            'title' => 'Bảng giá kính thông minh Iristick',
            'desc' => 'Xem bảng giá chi tiết các dòng kính thông minh Iristick G2 PRO, G3, H1 và các gói phần mềm hỗ trợ từ xa doanh nghiệp.',
        ),
        'book-demo' => array(
            'kw' => 'đặt lịch demo Iristick',
            'title' => 'Đặt lịch trải nghiệm kính Iristick',
            'desc' => 'Đăng ký đặt lịch demo trực tiếp để trải nghiệm giải pháp kính thông minh và phần mềm hỗ trợ từ xa Iristick Việt Nam.',
        ),
        'trial-program' => array(
            'kw' => 'dùng thử kính thông minh',
            'title' => 'Chương trình dùng thử kính Iristick',
            'desc' => 'Tìm hiểu chương trình trải nghiệm dùng thử kính thông minh Iristick trong 6 tuần để đánh giá hiệu quả cho doanh nghiệp.',
        ),
        'trial-order' => array(
            'kw' => 'đăng ký dùng thử Iristick',
            'title' => 'Đăng ký dùng thử kính Iristick',
            'desc' => 'Điền thông tin đăng ký chương trình dùng thử 6 tuần kính thông minh Iristick và nhận thiết bị trải nghiệm thực tế.',
        ),
        'enterprise' => array(
            'kw' => 'giải pháp doanh nghiệp Iristick',
            'title' => 'Giải pháp kính thông minh doanh nghiệp',
            'desc' => 'Giải pháp kính thông minh toàn diện cho doanh nghiệp giúp nâng cao hiệu suất làm việc hiện trường và đào tạo nhân sự.',
        ),
        'developers' => array(
            'kw' => 'SDK kính thông minh Iristick',
            'title' => 'SDK cho nhà phát triển Iristick',
            'desc' => 'Bộ công cụ SDK và tài liệu kỹ thuật dành cho nhà phát triển ứng dụng Android trên nền tảng kính thông minh Iristick.',
        ),
        'contact' => array(
            'kw' => 'liên hệ Iristick Việt Nam',
            'title' => 'Liên hệ Iristick Việt Nam',
            'desc' => 'Liên hệ đội ngũ Iristick Việt Nam để được tư vấn thiết bị kính thông minh, giải pháp phần mềm và báo giá doanh nghiệp.',
        ),
        'sitemap' => array(
            'kw' => 'sơ đồ trang Iristick',
            'title' => 'Sơ đồ trang Iristick Việt Nam',
            'desc' => 'Tổng hợp toàn bộ liên kết trang giới thiệu, sản phẩm, ngành nghề và tài liệu hỗ trợ trên website Iristick Việt Nam.',
        ),
        'company/about-us' => array(
            'kw' => 'về Iristick',
            'title' => 'Về chúng tôi Iristick Việt Nam',
            'desc' => 'Tìm hiểu về sứ mệnh, tầm nhìn và công nghệ kính thông minh hàng đầu thế giới của Iristick dành cho công nghiệp.',
        ),
        'company/careers' => array(
            'kw' => 'tuyển dụng Iristick',
            'title' => 'Cơ hội nghề nghiệp tại Iristick',
            'desc' => 'Khám phá các cơ hội nghề nghiệp hấp dẫn và gia nhập đội ngũ phát triển công nghệ kính thông minh Iristick tại Việt Nam.',
        ),
        'industries/agriculture' => array(
            'kw' => 'kính thông minh nông nghiệp',
            'title' => 'Kính thông minh ngành nông nghiệp',
            'desc' => 'Giải pháp kính thông minh rảnh tay giúp đánh giá kiểu hình cây trồng và số hóa quy trình nghiên cứu nông nghiệp.',
        ),
        'industries/field-service' => array(
            'kw' => 'dịch vụ hiện trường rảnh tay',
            'title' => 'Kính thông minh dịch vụ hiện trường',
            'desc' => 'Hỗ trợ kỹ thuật viên hiện trường kết nối trực tiếp với chuyên gia văn phòng qua video call rảnh tay độ nét cao.',
        ),
        'industries/healthcare' => array(
            'kw' => 'kính thông minh y tế',
            'title' => 'Kính thông minh chăm sóc y tế',
            'desc' => 'Hỗ trợ y bác sĩ khám chữa bệnh từ xa, hội chẩn chuyên môn và đào tạo y khoa trực quan với kính thông minh Iristick.',
        ),
        'partners/Icona' => array(
            'kw' => 'đối tác Icona Iristick',
            'title' => 'Đối tác phần mềm Icona Acty',
            'desc' => 'Giải pháp phần mềm hỗ trợ từ xa Acty của Icona tích hợp hoàn hảo trên kính thông minh Iristick cho doanh nghiệp.',
        ),
        'products/Iristick.Assist' => array(
            'kw' => 'phần mềm Iristick Assist',
            'title' => 'Phần mềm Iristick Assist',
            'desc' => 'Phần mềm hỗ trợ từ xa chuyên dụng giúp truyền hình ảnh trực tiếp, đánh dấu màn hình và tương tác 2 chiều liền mạch.',
        ),
        'products/Iristick.Collector' => array(
            'kw' => 'phần mềm Iristick Collector',
            'title' => 'Phần mềm Iristick Collector',
            'desc' => 'Ứng dụng thu thập dữ liệu hiện trường và nhận diện mã vạch tốc độ cao hoàn toàn rảnh tay trên kính Iristick.',
        ),
        'products/Iristick.Teams' => array(
            'kw' => 'Microsoft Teams Iristick',
            'title' => 'Iristick cho Microsoft Teams',
            'desc' => 'Tích hợp Microsoft Teams trực tiếp lên kính thông minh Iristick cho phép gọi video và chia sẻ góc nhìn rảnh tay.',
        ),
        'tools/Iristick.G2-PRO' => array(
            'kw' => 'kính thông minh Iristick G2 PRO',
            'title' => 'Kính thông minh Iristick G2 PRO',
            'desc' => 'Kính thông minh công nghiệp cao cấp với camera kép 16MP, zoom quang học 6x và màn hình hiển thị trước mắt sắc nét.',
        ),
        'tools/Iristick.G3' => array(
            'kw' => 'kính thông minh Iristick G3',
            'title' => 'Kính thông minh Iristick G3',
            'desc' => 'Dòng kính thông minh thế hệ mới siêu nhẹ, kết nối USB-C trực tiếp với điện thoại Android và camera góc rộng 16MP.',
        ),
        'tools/Iristick.H1' => array(
            'kw' => 'kính Iristick H1 gắn mũ bảo hiểm',
            'title' => 'Kính thông minh Iristick H1',
            'desc' => 'Thiết bị kính thông minh gắn trực tiếp lên mũ bảo hộ lao động, đạt chuẩn an toàn cao cho môi trường xây dựng và dầu khí.',
        ),
        'tools/Iristick.H3' => array(
            'kw' => 'kính Iristick H3',
            'title' => 'Kính thông minh Iristick H3',
            'desc' => 'Kính thông minh gắn mũ bảo hộ thế hệ mới tối ưu trọng lượng và tích hợp camera zoom quang học cho công trường.',
        ),
        'support/faqs' => array(
            'kw' => 'câu hỏi thường gặp Iristick',
            'title' => 'Câu hỏi thường gặp về Iristick',
            'desc' => 'Giải đáp mọi thắc mắc về phần cứng, phần mềm, cách kết nối và chính sách bảo hành kính thông minh Iristick.',
        ),
        'policies/cookie-policy' => array(
            'kw' => 'chính sách cookie Iristick',
            'title' => 'Chính sách Cookie Iristick Việt Nam',
            'desc' => 'Thông tin chi tiết về việc sử dụng cookie và công nghệ theo dõi nhằm cải thiện trải nghiệm người dùng trên website.',
        ),
        'policies/privacy-policy' => array(
            'kw' => 'chính sách bảo mật Iristick',
            'title' => 'Chính sách bảo mật thông tin',
            'desc' => 'Cam kết bảo mật dữ liệu cá nhân, thông tin liên hệ và quyền riêng tư của khách hàng khi sử dụng dịch vụ Iristick.',
        ),
        'policies/terms-conditions' => array(
            'kw' => 'điều khoản điều kiện Iristick',
            'title' => 'Điều khoản và điều kiện sử dụng',
            'desc' => 'Các quy định, điều khoản sử dụng website, quyền sở hữu trí tuệ và chính sách giao dịch của Iristick Việt Nam.',
        ),
        'blog/news' => array(
            'kw' => 'tin tức kính thông minh',
            'title' => 'Tin tức và sự kiện Iristick',
            'desc' => 'Cập nhật các tin tức công nghệ mới nhất, sự kiện hội thảo và ứng dụng thực tế của kính thông minh Iristick.',
        ),
        'shop' => array(
            'kw' => 'cửa hàng kính thông minh',
            'title' => 'Cửa hàng thiết bị và phần mềm',
            'desc' => 'Mua sắm kính thông minh Iristick, phụ kiện chính hãng và các gói phần mềm hỗ trợ từ xa doanh nghiệp uy tín.',
        ),
        'cart' => array(
            'kw' => 'giỏ hàng Iristick',
            'title' => 'Giỏ hàng của bạn',
            'desc' => 'Kiểm tra danh sách thiết bị kính thông minh và phần mềm Iristick trong giỏ hàng trước khi tiến hành thanh toán.',
        ),
        'checkout' => array(
            'kw' => 'thanh toán Iristick',
            'title' => 'Thanh toán đơn hàng',
            'desc' => 'Hoàn tất thông tin giao hàng và thanh toán an toàn các sản phẩm kính thông minh Iristick chính hãng tại Việt Nam.',
        ),
        'my-account' => array(
            'kw' => 'tài khoản Iristick',
            'title' => 'Tài khoản khách hàng',
            'desc' => 'Quản lý thông tin tài khoản cá nhân, lịch sử đơn hàng và trạng thái giấy phép phần mềm kính thông minh Iristick.',
        ),
        'company' => array(
            'kw' => 'công ty Iristick',
            'title' => 'Thông tin công ty Iristick',
            'desc' => 'Tìm hiểu tổng quan về công ty Iristick, đội ngũ nhân sự, giá trị cốt lõi và các cơ hội hợp tác kinh doanh.',
        ),
        'industries' => array(
            'kw' => 'ngành nghề ứng dụng Iristick',
            'title' => 'Giải pháp theo ngành nghề',
            'desc' => 'Khám phá các giải pháp kính thông minh được tùy biến tối ưu cho từng ngành sản xuất, nông nghiệp và y tế.',
        ),
        'partners' => array(
            'kw' => 'đối tác Iristick',
            'title' => 'Mạng lưới đối tác toàn cầu',
            'desc' => 'Hệ sinh thái đối tác phần mềm và nhà phân phối chiến lược của Iristick trên toàn cầu và tại Việt Nam.',
        ),
        'policies' => array(
            'kw' => 'chính sách Iristick',
            'title' => 'Chính sách và quy định',
            'desc' => 'Tổng hợp các điều khoản dịch vụ, chính sách bảo mật thông tin và bảo hành sản phẩm của Iristick Việt Nam.',
        ),
        'support' => array(
            'kw' => 'hỗ trợ Iristick',
            'title' => 'Trung tâm hỗ trợ khách hàng',
            'desc' => 'Tài liệu hướng dẫn sử dụng, giải đáp thắc mắc và hỗ trợ kỹ thuật chuyên sâu cho kính thông minh Iristick.',
        ),
    );

    $suffix = ' | Iristick Việt Nam';

    // 1. Static Pages & System Pages
    $pages = get_posts(array(
        'post_type' => 'page',
        'post_status' => 'any',
        'numberposts' => -1,
    ));
    foreach ($pages as $page) {
        $static_path = get_post_meta($page->ID, '_iristick_static_path', true);
        $slug = $page->post_name;
        if ($static_path && isset($seo_data[$static_path])) {
            update_post_meta($page->ID, '_yoast_wpseo_title', $seo_data[$static_path]['title'] . $suffix);
            update_post_meta($page->ID, '_yoast_wpseo_focuskw', $seo_data[$static_path]['kw']);
            update_post_meta($page->ID, '_yoast_wpseo_metadesc', $seo_data[$static_path]['desc']);
        } elseif (isset($seo_data[$slug])) {
            update_post_meta($page->ID, '_yoast_wpseo_title', $seo_data[$slug]['title'] . $suffix);
            update_post_meta($page->ID, '_yoast_wpseo_focuskw', $seo_data[$slug]['kw']);
            update_post_meta($page->ID, '_yoast_wpseo_metadesc', $seo_data[$slug]['desc']);
        } else {
            $raw_title = preg_replace('/\s*\|\s*Iristick(?:\s+(?:Việt Nam|VN))?\s*$/iu', '', $page->post_title);
            $clean_title = wp_html_excerpt($raw_title, 35, '');
            update_post_meta($page->ID, '_yoast_wpseo_title', $clean_title . $suffix);
            update_post_meta($page->ID, '_yoast_wpseo_focuskw', $clean_title);
            $desc = $page->post_excerpt ?: wp_strip_all_tags($page->post_content);
            $clean_desc = wp_html_excerpt($desc, 135, '');
            if ($clean_desc === '') {
                $clean_desc = sprintf('Thông tin chi tiết về %s trên hệ thống chính thức của Iristick Việt Nam.', $clean_title);
            }
            update_post_meta($page->ID, '_yoast_wpseo_metadesc', $clean_desc);
        }
    }

    // 2. Posts (Tin tức)
    $posts = get_posts(array(
        'post_type' => 'post',
        'post_status' => 'any',
        'numberposts' => -1,
    ));
    foreach ($posts as $post) {
        $slug = $post->post_name;
        if (isset($seo_data[$slug])) {
            update_post_meta($post->ID, '_yoast_wpseo_title', $seo_data[$slug]['title'] . $suffix);
            update_post_meta($post->ID, '_yoast_wpseo_focuskw', $seo_data[$slug]['kw']);
            update_post_meta($post->ID, '_yoast_wpseo_metadesc', $seo_data[$slug]['desc']);
        } else {
            // Fallback: build clean, concise title and description
            $title = preg_replace('/\s*\|\s*Iristick(?:\s+(?:Việt Nam|VN))?\s*$/iu', '', $post->post_title);
            $clean_title = wp_html_excerpt($title, 38, '');
            update_post_meta($post->ID, '_yoast_wpseo_title', $clean_title . $suffix);
            update_post_meta($post->ID, '_yoast_wpseo_focuskw', $clean_title);
            $desc = $post->post_excerpt ?: wp_strip_all_tags($post->post_content);
            $clean_desc = wp_html_excerpt($desc, 135, '');
            update_post_meta($post->ID, '_yoast_wpseo_metadesc', $clean_desc);
        }
    }

    // 3. Products
    $products = get_posts(array(
        'post_type' => 'product',
        'post_status' => 'any',
        'numberposts' => -1,
    ));
    foreach ($products as $product) {
        $title = preg_replace('/\s*\|\s*Iristick(?:\s+(?:Việt Nam|VN))?\s*$/iu', '', $product->post_title);
        $clean_title = wp_html_excerpt($title, 35, '');
        update_post_meta($product->ID, '_yoast_wpseo_title', $clean_title . $suffix);
        update_post_meta($product->ID, '_yoast_wpseo_focuskw', $clean_title);
        $desc = $product->post_excerpt ?: wp_strip_all_tags($product->post_content);
        $clean_desc = wp_html_excerpt($desc, 135, '');
        update_post_meta($product->ID, '_yoast_wpseo_metadesc', $clean_desc);
    }

    update_option('iristick_yoast_seo_db_seeded_v14', current_time('mysql'), false);
}
add_action('init', 'iristick_migrate_yoast_seo_db_values', 35);

function iristick_remove_duplicate_imported_pages_v1() {
    if (get_option('iristick_duplicate_imported_pages_cleaned_v1')) {
        return;
    }

    foreach (iristick_static_editable_page_paths() as $path) {
        $ids = get_posts(array(
            'post_type' => 'page', 'post_status' => 'any', 'numberposts' => -1,
            'meta_key' => '_iristick_static_path', 'meta_value' => $path,
            'fields' => 'ids', 'orderby' => 'ID', 'order' => 'ASC',
        ));
        if (count($ids) < 2) {
            continue;
        }
        array_shift($ids);
        foreach ($ids as $duplicate_id) {
            wp_delete_post($duplicate_id, true);
        }
    }

    update_option('iristick_duplicate_imported_pages_cleaned_v1', current_time('mysql'), false);
}
add_action('init', 'iristick_remove_duplicate_imported_pages_v1', 33);

function iristick_rebuild_all_wordpress_pages_v2() {
    if (get_option('iristick_all_pages_rebuilt_v2')) {
        return;
    }

    $page_ids = get_posts(array(
        'post_type' => 'page', 'post_status' => 'any', 'numberposts' => -1,
        'fields' => 'ids', 'orderby' => 'ID', 'order' => 'ASC',
    ));
    foreach ($page_ids as $page_id) {
        wp_delete_post($page_id, true);
    }

    delete_option('iristick_static_pages_imported_v1');

    $system_pages = array(
        'shop' => array('Cửa hàng | Iristick Việt Nam', '', 'woocommerce_shop_page_id'),
        'cart' => array('Giỏ hàng | Iristick Việt Nam', '[woocommerce_cart]', 'woocommerce_cart_page_id'),
        'checkout' => array('Thanh toán | Iristick Việt Nam', '[woocommerce_checkout]', 'woocommerce_checkout_page_id'),
        'my-account' => array('Tài khoản | Iristick Việt Nam', '[woocommerce_my_account]', 'woocommerce_myaccount_page_id'),
    );
    foreach ($system_pages as $slug => $data) {
        $page_id = wp_insert_post(array(
            'post_type' => 'page', 'post_status' => 'publish', 'post_name' => $slug,
            'post_title' => $data[0], 'post_content' => $data[1],
        ));
        if (!is_wp_error($page_id)) {
            update_option($data[2], (int) $page_id);
        }
    }

    update_option('iristick_all_pages_rebuilt_v2', current_time('mysql'), false);
}
add_action('init', 'iristick_rebuild_all_wordpress_pages_v2', 31);

function iristick_get_database_page_for_static_path($path) {
    $ids = get_posts(array('post_type' => 'page', 'post_status' => 'publish', 'numberposts' => 1, 'meta_key' => '_iristick_static_path', 'meta_value' => $path, 'fields' => 'ids'));
    return $ids ? get_post($ids[0]) : null;
}

function iristick_inject_database_page_content($html) {
    $path = iristick_static_request_path();
    if (!in_array($path, iristick_static_editable_page_paths(), true)) {
        return $html;
    }
    $page = iristick_get_database_page_for_static_path($path);
    if (!$page || trim($page->post_content) === '') {
        return $html;
    }
    $content = do_shortcode($page->post_content);
    $file = $path === 'index'
        ? iristick_static_page_root() . '/page.php'
        : iristick_static_page_root() . '/' . $path . '/page.php';
    $content = str_replace(
        array('{{IRISTICK_ADMIN_POST_URL}}', '{{IRISTICK_DEMO_FORM_NONCE}}', '{{IRISTICK_TRIAL_FORM_NONCE}}', '{{IRISTICK_THEME_URI}}'),
        array(esc_url(admin_url('admin-post.php')), wp_nonce_field('iristick_demo_request', 'iristick_demo_nonce', true, false), wp_nonce_field('iristick_trial_request', 'iristick_trial_nonce', true, false), esc_url(IRISTICK_STATIC_URI)),
        $content
    );
    $content = preg_replace_callback('#\b(href|src|poster|action)=("|\')([^"\']*)\2#i', function ($matches) use ($file) {
        $url = iristick_static_rewrite_url($matches[3], $file);
        return $matches[1] . '=' . $matches[2] . esc_url($url) . $matches[2];
    }, $content);
    // Keep the complete editable heading markup from post_content. Several captured
    // pages use nested elements/classes inside H1 for their hero typography.
    return preg_replace('#(</nav></div>).*?(?=<div class="footer-wrapper)#is', '$1' . $content, $html, 1) ?: $html;
}

// Direct checkout always purchases one unit, so no quantity selector is shown.
add_filter('woocommerce_is_sold_individually', '__return_true', 10, 2);

function iristick_buy_now_empty_previous_cart($passed, $product_id, $quantity) {
    if ($passed && function_exists('WC') && WC()->cart && !WC()->cart->is_empty()) {
        WC()->cart->empty_cart();
    }
    return $passed;
}
add_filter('woocommerce_add_to_cart_validation', 'iristick_buy_now_empty_previous_cart', 10, 3);

function iristick_buy_now_checkout_redirect($url) {
    return function_exists('wc_get_checkout_url') ? wc_get_checkout_url() : $url;
}
add_filter('woocommerce_add_to_cart_redirect', 'iristick_buy_now_checkout_redirect');

function iristick_disable_cart_page() {
    if (!function_exists('is_cart') || !is_cart()) {
        return;
    }

    $destination = function_exists('WC') && WC()->cart && !WC()->cart->is_empty()
        ? wc_get_checkout_url()
        : home_url('/');
    wp_safe_redirect($destination);
    exit;
}
add_action('template_redirect', 'iristick_disable_cart_page', 1);

// Remove cart-only UI and background refreshes; checkout still uses the
// WooCommerce session populated by the direct "Mua ngay" action.
add_filter('woocommerce_add_to_cart_message_html', '__return_empty_string');
add_filter('wc_add_to_cart_message_html', '__return_empty_string');
add_filter('woocommerce_widget_cart_is_hidden', '__return_true');
add_action('wp_enqueue_scripts', function () {
    wp_dequeue_script('wc-cart-fragments');
    wp_deregister_script('wc-cart-fragments');
}, 100);

function iristick_handle_phone_consultation() {
    $product_id = isset($_POST['product_id']) ? absint($_POST['product_id']) : 0;
    $fallback_url = $product_id ? get_permalink($product_id) : home_url('/');

    if (!isset($_POST['iristick_phone_nonce'])
        || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['iristick_phone_nonce'])), 'iristick_phone_consultation')) {
        wp_safe_redirect(add_query_arg('phone_status', 'error', $fallback_url));
        exit;
    }

    $phone = isset($_POST['phone']) ? iristick_normalize_vietnam_phone(sanitize_text_field(wp_unslash($_POST['phone']))) : '';
    if (!preg_match('/^0(?:3|5|7|8|9)[0-9]{8}$/', $phone)) {
        wp_safe_redirect(add_query_arg('phone_status', 'error', $fallback_url));
        exit;
    }

    $product = function_exists('wc_get_product') ? wc_get_product($product_id) : false;
    $product_name = $product ? $product->get_name() : 'N/A';
    $recipient = get_option('admin_email');
    $subject = '[Iristick Việt Nam] Yêu cầu tư vấn qua điện thoại';
    $message = implode("\n", array(
        'Số điện thoại: ' . $phone,
        'Sản phẩm quan tâm: ' . $product_name,
        'Trang sản phẩm: ' . $fallback_url,
        'Thời gian: ' . current_time('d/m/Y H:i:s'),
    ));
    $sent = wp_mail($recipient, $subject, $message);

    wp_safe_redirect(add_query_arg('phone_status', $sent ? 'success' : 'error', $fallback_url));
    exit;
}
add_action('admin_post_nopriv_iristick_phone_consultation', 'iristick_handle_phone_consultation');
add_action('admin_post_iristick_phone_consultation', 'iristick_handle_phone_consultation');

/**
 * Use the controllable classic checkout instead of the Checkout Block so the
 * Vietnam form stays compact and has only one required field: phone number.
 */
function iristick_use_simple_checkout($content) {
    if (function_exists('is_checkout') && is_checkout()
        && !is_wc_endpoint_url('order-received') && is_main_query() && in_the_loop()) {
        return do_shortcode('[woocommerce_checkout]');
    }
    return $content;
}
add_filter('the_content', 'iristick_use_simple_checkout', 1);

function iristick_simple_checkout_fields($fields) {
    if (!isset($fields['billing'])) {
        return $fields;
    }

    foreach ($fields['billing'] as &$field) {
        $field['required'] = false;
    }
    unset($field);

    $allowed_billing_fields = array('billing_phone', 'billing_email', 'billing_first_name', 'billing_address_1');
    foreach (array_keys($fields['billing']) as $field_key) {
        if (!in_array($field_key, $allowed_billing_fields, true)) {
            unset($fields['billing'][$field_key]);
        }
    }

    $labels = array(
        'billing_first_name' => array('Họ và tên', 'Nhập họ và tên'),
        'billing_phone' => array('Số điện thoại', 'Nhập số điện thoại'),
        'billing_email' => array('Email', 'Nhập email (không bắt buộc)'),
        'billing_address_1' => array('Địa chỉ', 'Nhập địa chỉ (không bắt buộc)'),
    );
    foreach ($labels as $field_key => $copy) {
        if (isset($fields['billing'][$field_key])) {
            $fields['billing'][$field_key]['label'] = $copy[0];
            $fields['billing'][$field_key]['placeholder'] = $copy[1];
        }
    }

    if (isset($fields['billing']['billing_phone'])) {
        $fields['billing']['billing_phone']['required'] = true;
        $fields['billing']['billing_phone']['priority'] = 10;
        $fields['billing']['billing_phone']['class'] = array('form-row-wide');
        $fields['billing']['billing_phone']['type'] = 'tel';
        $fields['billing']['billing_phone']['placeholder'] = 'Ví dụ: 0917834532';
        $fields['billing']['billing_phone']['custom_attributes'] = array(
            'inputmode' => 'numeric',
            'autocomplete' => 'tel',
            'pattern' => '(?:\\+84|0)(?:3|5|7|8|9)[0-9]{8}',
            'minlength' => '10',
            'maxlength' => '12',
            'title' => 'Nhập số di động Việt Nam, ví dụ 0917834532 hoặc +84917834532',
        );
    }
    if (isset($fields['billing']['billing_first_name'])) {
        $fields['billing']['billing_first_name']['priority'] = 20;
        $fields['billing']['billing_first_name']['class'] = array('form-row-wide');
    }
    if (isset($fields['billing']['billing_email'])) {
        $fields['billing']['billing_email']['priority'] = 30;
        $fields['billing']['billing_email']['class'] = array('form-row-wide');
    }

    $fields['shipping'] = array();
    if (isset($fields['order']['order_comments'])) {
        $fields['order']['order_comments']['label'] = 'Ghi chú';
        $fields['order']['order_comments']['placeholder'] = 'Ghi chú thêm (không bắt buộc)';
        $fields['order']['order_comments']['required'] = false;
    }

    return $fields;
}
add_filter('woocommerce_checkout_fields', 'iristick_simple_checkout_fields', 20);
add_filter('woocommerce_order_button_text', function () {
    return 'Đặt hàng';
});
add_filter('woocommerce_thankyou_order_received_text', function ($text, $order) {
    return '<strong>Đặt hàng thành công!</strong><br>Cảm ơn bạn. Iristick Việt Nam đã nhận đơn hàng và sẽ sớm liên hệ để xác nhận.';
}, 20, 2);
add_filter('woocommerce_enable_order_notes_field', '__return_true');
add_filter('woocommerce_cart_needs_shipping_address', '__return_false');

function iristick_normalize_vietnam_phone($phone) {
    $phone = preg_replace('/[\s.()-]+/', '', (string) $phone);
    if (strpos($phone, '+84') === 0) {
        $phone = '0' . substr($phone, 3);
    } elseif (strpos($phone, '84') === 0 && strlen($phone) === 11) {
        $phone = '0' . substr($phone, 2);
    }
    return $phone;
}

add_filter('woocommerce_checkout_posted_data', function ($data) {
    if (isset($data['billing_phone'])) {
        $data['billing_phone'] = iristick_normalize_vietnam_phone($data['billing_phone']);
    }
    return $data;
});

add_action('woocommerce_checkout_process', function () {
    $phone = isset($_POST['billing_phone'])
        ? iristick_normalize_vietnam_phone(sanitize_text_field(wp_unslash($_POST['billing_phone'])))
        : '';
    if (!preg_match('/^0(?:3|5|7|8|9)[0-9]{8}$/', $phone)) {
        wc_add_notice('Vui lòng nhập đúng số di động Việt Nam (ví dụ: 0917834532).', 'error');
    }
});

/**
 * Offline confirmation gateway: creates a real WooCommerce order without
 * requiring an online payment provider. The consultant confirms it by phone.
 */
function iristick_register_phone_confirmation_gateway() {
    if (!class_exists('WC_Payment_Gateway') || class_exists('WC_Gateway_Iristick_Phone')) {
        return;
    }

    class WC_Gateway_Iristick_Phone extends WC_Payment_Gateway {
        public function __construct() {
            $this->id = 'iristick_phone_confirmation';
            $this->method_title = 'Thanh toán khi nhận hàng';
            $this->method_description = 'Khách hàng thanh toán khi nhận hàng.';
            $this->has_fields = false;
            $this->enabled = 'yes';
            $this->title = 'Thanh toán khi nhận hàng';
            $this->description = '';
            $this->supports = array('products');
        }

        public function process_payment($order_id) {
            $order = wc_get_order($order_id);
            if (!$order) {
                return array('result' => 'failure');
            }

            $order->update_status('on-hold', 'Đơn hàng thanh toán khi nhận hàng.');
            wc_reduce_stock_levels($order_id);
            if (WC()->cart) {
                WC()->cart->empty_cart();
            }

            return array(
                'result' => 'success',
                'redirect' => $this->get_return_url($order),
            );
        }
    }
}
add_action('init', 'iristick_register_phone_confirmation_gateway', 5);
add_filter('woocommerce_payment_gateways', function ($gateways) {
    $gateways[] = 'WC_Gateway_Iristick_Phone';
    return $gateways;
});

/* Iristick Vietnam branding for WooCommerce order emails. */
add_filter('woocommerce_email_from_name', function () {
    return 'Iristick Việt Nam';
});
add_filter('woocommerce_email_header_image', '__return_empty_string');

add_filter('woocommerce_email_order_items_args', function ($args) {
    $args['show_image'] = false;
    return $args;
});

function iristick_branded_email_heading($title) {
    return 'Iristick Việt Nam — ' . wp_strip_all_tags($title);
}
add_filter('woocommerce_email_heading_new_order', function () {
    return iristick_branded_email_heading('Đơn hàng mới');
});
add_filter('woocommerce_email_heading_customer_on_hold_order', function () {
    return iristick_branded_email_heading('Chúng tôi đã nhận đơn hàng');
});
add_filter('woocommerce_email_heading_customer_processing_order', function () {
    return iristick_branded_email_heading('Đơn hàng đang được xử lý');
});
add_filter('woocommerce_email_heading_customer_completed_order', function () {
    return iristick_branded_email_heading('Đơn hàng đã hoàn tất');
});
add_filter('woocommerce_email_heading_customer_invoice', function () {
    return iristick_branded_email_heading('Thông tin đơn hàng');
});

add_filter('woocommerce_email_subject_new_order', function ($subject, $order) {
    return '[Iristick Việt Nam] Đơn hàng mới #' . $order->get_order_number();
}, 20, 2);
add_filter('woocommerce_email_subject_customer_on_hold_order', function ($subject, $order) {
    return 'Iristick Việt Nam đã nhận đơn hàng #' . $order->get_order_number();
}, 20, 2);
add_filter('woocommerce_email_subject_customer_processing_order', function ($subject, $order) {
    return 'Đơn hàng #' . $order->get_order_number() . ' đang được xử lý | Iristick Việt Nam';
}, 20, 2);
add_filter('woocommerce_email_subject_customer_completed_order', function ($subject, $order) {
    return 'Đơn hàng #' . $order->get_order_number() . ' đã hoàn tất | Iristick Việt Nam';
}, 20, 2);

add_filter('woocommerce_email_footer_text', function () {
    return '<strong>Iristick Việt Nam</strong><br>Cảm ơn bạn đã tin tưởng và lựa chọn sản phẩm của chúng tôi.';
});

add_filter('woocommerce_email_styles', function ($css) {
    $css .= '\n
        body { background-color:#f4f3f8 !important; color:#19191c !important; }
        #wrapper { background-color:#f4f3f8 !important; padding:34px 12px !important; }
        #template_container { border:0 !important; border-radius:22px !important; overflow:hidden !important; box-shadow:0 16px 45px rgba(32,27,55,.10) !important; }
        #template_header { background:#19191c !important; border:0 !important; }
        #template_header h1 { padding:30px 34px !important; color:#fff !important; font-size:25px !important; line-height:1.35 !important; }
        .iristick-email-brand { color:#fff !important; font-size:28px !important; font-weight:700 !important; letter-spacing:-.02em !important; }
        .iristick-email-title { color:#c9c2ff !important; font-size:15px !important; font-weight:500 !important; }
        #template_body td, #template_body th { color:#2b2930 !important; font-size:15px !important; line-height:1.65 !important; }
        #body_content { padding:0 !important; }
        #body_content_inner { padding:34px !important; }
        h2, h3 { color:#19191c !important; }
        table.td { border:1px solid #e2dfec !important; border-radius:12px !important; overflow:hidden !important; }
        table.td th { background:#f2f0fa !important; color:#19191c !important; font-weight:700 !important; }
        table.td th, table.td td { border-color:#e2dfec !important; padding:13px !important; }
        .product-image, img.attachment-thumbnail { display:none !important; }
        a { color:#6557df !important; }
        #template_footer { background:#faf9fc !important; }
        #template_footer td { padding:22px 34px !important; color:#74717c !important; font-size:13px !important; line-height:1.6 !important; }
        @media only screen and (max-width:620px) {
            #wrapper { padding:10px 4px !important; }
            #body_content_inner, #template_header h1 { padding:22px !important; }
            .iristick-email-brand { font-size:24px !important; }
            table.td th, table.td td { padding:9px !important; font-size:13px !important; }
        }
    ';
    return $css;
});

add_action('woocommerce_before_checkout_form', function () {
    if (!wp_doing_ajax() && function_exists('wc_clear_notices')) {
        wc_clear_notices();
    }
}, 1);

function iristick_checkout_vietnamese_text($translated, $original, $domain) {
    $copy = array(
        'Checkout' => 'Thanh toán',
        'Billing details' => 'Thông tin liên hệ',
        'Additional information' => 'Ghi chú',
        'Your order' => 'Đơn hàng của bạn',
        'Product' => 'Sản phẩm',
        'Subtotal' => 'Tạm tính',
        'Total' => 'Tổng cộng',
        'Place order' => 'Đặt hàng',
        'optional' => 'không bắt buộc',
        'Order' => 'Đơn hàng',
        'Order received' => 'Đặt hàng thành công',
        'Thank you. Your order has been received.' => 'Cảm ơn bạn. Iristick Việt Nam đã nhận đơn hàng.',
        'New order' => 'Đơn hàng mới',
        'New order: #%s' => 'Đơn hàng mới: #%s',
        'Order summary' => 'Chi tiết đơn hàng',
        'Order details' => 'Chi tiết đơn hàng',
        'Order #%s' => 'Đơn hàng #%s',
        'Order #%1$s (%2$s)' => 'Đơn hàng #%1$s (%2$s)',
        'Order number:' => 'Mã đơn hàng:',
        'Order number' => 'Mã đơn hàng',
        'Date:' => 'Ngày đặt:',
        'Date' => 'Ngày đặt',
        'Email:' => 'Email:',
        'Total:' => 'Tổng cộng:',
        'Payment method:' => 'Phương thức thanh toán:',
        'Payment method:' => 'Phương thức thanh toán:',
        'Quantity' => 'Số lượng',
        'Price' => 'Thành tiền',
        'Subtotal:' => 'Tạm tính:',
        'Total:' => 'Tổng cộng:',
        'Payment method:' => 'Phương thức thanh toán:',
        'Billing address' => 'Thông tin khách hàng',
        'Shipping address' => 'Địa chỉ giao hàng',
        'Customer note' => 'Ghi chú của khách hàng',
        'Email' => 'Email',
        'Phone' => 'Số điện thoại',
        'Download' => 'Tải xuống',
        'Expires' => 'Hết hạn',
        'Thanks for reading.' => 'Cảm ơn bạn đã đọc email.',
        'Thanks for using %s!' => 'Cảm ơn bạn đã lựa chọn %s!',
        'We look forward to fulfilling your order soon.' => 'Chúng tôi sẽ sớm liên hệ và xử lý đơn hàng của bạn.',
        'Your order is on-hold until we confirm payment has been received. In the meantime, here\'s a reminder of what you ordered:' => 'Đơn hàng của bạn đã được tiếp nhận và đang chờ xử lý. Dưới đây là thông tin đơn hàng:',
        'Hi %s,' => 'Xin chào %s,',
        'You\'ve received the following order from %s:' => 'Bạn vừa nhận đơn hàng mới từ %s:',
        'You\'ve received a new order from %s. Their order is as follows:' => 'Bạn vừa nhận đơn hàng mới từ %s. Chi tiết đơn hàng như sau:',
        'You’ve received the following order from %s:' => 'Bạn vừa nhận đơn hàng mới từ %s:',
        'You’ve received a new order from %s. Their order is as follows:' => 'Bạn vừa nhận đơn hàng mới từ %s. Chi tiết đơn hàng như sau:',
        'Just to let you know — we\'ve received your order #%s, and it is now being processed:' => 'Chúng tôi đã nhận đơn hàng #%s và đang tiến hành xử lý:',
        'Thanks for your order. It’s on-hold until we confirm that payment has been received.' => 'Cảm ơn bạn đã đặt hàng. Đơn hàng hiện đang chờ được xử lý.',
        'We have finished processing your order.' => 'Đơn hàng của bạn đã được xử lý hoàn tất.',
        'Congratulations on the sale!' => 'Chúc mừng bạn có đơn hàng mới!',
        'Process your orders on the go.' => 'Quản lý và xử lý đơn hàng mọi lúc, mọi nơi.',
        'Get the app.' => 'Tải ứng dụng.',
        'Manage your orders on the go. Get the app.' => 'Quản lý đơn hàng mọi lúc, mọi nơi. Tải ứng dụng.',
        'Process your orders on the go. Get the app.' => 'Xử lý đơn hàng mọi lúc, mọi nơi. Tải ứng dụng.',
    );
    return isset($copy[$original]) ? $copy[$original] : $translated;
}
add_filter('gettext', 'iristick_checkout_vietnamese_text', 20, 3);

add_filter('the_title', function ($title, $post_id) {
    if (function_exists('is_checkout') && is_checkout() && in_the_loop() && is_main_query()) {
        return function_exists('is_order_received_page') && is_order_received_page()
            ? 'Đặt hàng thành công'
            : 'Thanh toán';
    }
    return $title;
}, 20, 2);

function iristick_product_admin_columns($columns) {
    $columns['iristick_sync'] = 'Dữ liệu Iristick';
    return $columns;
}
add_filter('manage_edit-product_columns', 'iristick_product_admin_columns', 30);

function iristick_product_admin_column_content($column, $post_id) {
    if ($column !== 'iristick_sync') {
        return;
    }
    $product = wc_get_product($post_id);
    if (!$product) {
        echo 'Chưa đồng bộ';
        return;
    }
    $checks = array(
        $product->get_sku(),
        $product->get_short_description(),
        $product->get_description(),
        $product->get_meta('_iristick_media_type'),
        $product->get_meta('_iristick_media_url'),
        $product->get_meta('_iristick_features'),
        $product->get_meta('_iristick_specs'),
        $product->get_meta('_iristick_documents'),
        $product->get_meta('_iristick_faq'),
    );
    $complete = count(array_filter($checks, function ($value) {
        return $value !== '' && $value !== array();
    }));
    echo $complete === count($checks)
        ? '<strong style="color:#16803c">Đã đồng bộ</strong><br><small>' . esc_html($complete . '/' . count($checks)) . ' nhóm dữ liệu</small>'
        : '<strong style="color:#b32d2e">Thiếu dữ liệu</strong><br><small>' . esc_html($complete . '/' . count($checks)) . ' nhóm dữ liệu</small>';
}
add_action('manage_product_posts_custom_column', 'iristick_product_admin_column_content', 10, 2);


function iristick_add_product_data_meta_box() {
    add_meta_box(
        'iristick-product-data',
        'Thông tin sản phẩm Iristick',
        'iristick_render_product_data_meta_box',
        'product',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes_product', 'iristick_add_product_data_meta_box');

function iristick_meta_lines($value, $separator = ' | ') {
    if (!is_array($value)) {
        return '';
    }
    $lines = array();
    foreach ($value as $key => $item) {
        if (is_array($item)) {
            $left = isset($item['question']) ? $item['question'] : '';
            $right = isset($item['answer']) ? $item['answer'] : '';
            $lines[] = $left . $separator . $right;
        } elseif (is_string($key)) {
            $lines[] = $key . $separator . $item;
        } else {
            $lines[] = $item;
        }
    }
    return implode("\n", $lines);
}

function iristick_render_product_data_meta_box($post) {
    $product = wc_get_product($post->ID);
    if (!$product) {
        return;
    }
    wp_nonce_field('iristick_save_product_data', 'iristick_product_data_nonce');
    $media_type = $product->get_meta('_iristick_media_type');
    ?>
    <style>
        .iristick-admin-fields{display:grid;grid-template-columns:180px 1fr;gap:14px 18px;align-items:start}.iristick-admin-fields label{font-weight:600;padding-top:8px}.iristick-admin-fields input,.iristick-admin-fields select,.iristick-admin-fields textarea{width:100%;max-width:none}.iristick-admin-fields small{display:block;margin-top:5px;color:#646970}@media(max-width:782px){.iristick-admin-fields{grid-template-columns:1fr;gap:7px}.iristick-admin-fields label{padding-top:6px}}
    </style>
    <div class="iristick-admin-fields">
        <label for="iristick_media_type">Loại media</label>
        <div><select id="iristick_media_type" name="iristick_media_type"><option value="N/A" <?php selected($media_type, 'N/A'); ?>>N/A</option><option value="image" <?php selected($media_type, 'image'); ?>>Ảnh</option><option value="video" <?php selected($media_type, 'video'); ?>>Video</option></select></div>

        <label for="iristick_media_url">Media chính</label>
        <div><input id="iristick_media_url" name="iristick_media_url" type="text" value="<?php echo esc_attr($product->get_meta('_iristick_media_url')); ?>" placeholder="URL ảnh hoặc video"><small>Dán URL ảnh/video hoặc chọn ảnh đại diện trong khối “Ảnh sản phẩm” của WooCommerce.</small></div>

        <label for="iristick_features">Tính năng chính</label>
        <div><textarea id="iristick_features" name="iristick_features" rows="6"><?php echo esc_textarea(iristick_meta_lines($product->get_meta('_iristick_features'))); ?></textarea><small>Mỗi dòng: Tên tính năng | Mô tả.</small></div>

        <label for="iristick_specs">Thông số và phụ kiện</label>
        <div><textarea id="iristick_specs" name="iristick_specs" rows="7"><?php echo esc_textarea(iristick_meta_lines($product->get_meta('_iristick_specs'))); ?></textarea><small>Mỗi dòng: Tên thông số | Giá trị.</small></div>

        <label for="iristick_documents">Tài liệu PDF</label>
        <div><textarea id="iristick_documents" name="iristick_documents" rows="5"><?php echo esc_textarea(iristick_meta_lines($product->get_meta('_iristick_documents'))); ?></textarea><small>Mỗi dòng: Tên tài liệu | URL.</small></div>

        <label for="iristick_faq">FAQ</label>
        <div><textarea id="iristick_faq" name="iristick_faq" rows="7"><?php echo esc_textarea(iristick_meta_lines($product->get_meta('_iristick_faq'))); ?></textarea><small>Mỗi dòng: Câu hỏi | Câu trả lời.</small></div>
    </div>
    <?php
}

function iristick_parse_product_meta_lines($raw, $mode) {
    $result = array();
    foreach (preg_split('/\r\n|\r|\n/', (string) $raw) as $line) {
        $line = trim($line);
        if ($line === '') {
            continue;
        }
        if ($mode === 'list') {
            $result[] = sanitize_text_field($line);
            continue;
        }
        $parts = array_map('trim', explode('|', $line, 2));
        $left = sanitize_text_field($parts[0]);
        $right = isset($parts[1]) && $parts[1] !== '' ? $parts[1] : 'N/A';
        if ($mode === 'faq') {
            $result[] = array('question' => $left, 'answer' => sanitize_textarea_field($right));
        } elseif ($mode === 'documents') {
            $result[$left] = $right === 'N/A' ? 'N/A' : esc_url_raw($right);
        } else {
            $result[$left] = sanitize_text_field($right);
        }
    }
    return $result;
}

function iristick_save_product_data_meta_box($post_id) {
    if (!isset($_POST['iristick_product_data_nonce'])
        || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['iristick_product_data_nonce'])), 'iristick_save_product_data')
        || (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        || !current_user_can('edit_post', $post_id)) {
        return;
    }
    $product = wc_get_product($post_id);
    if (!$product) {
        return;
    }
    $media_type = isset($_POST['iristick_media_type']) ? sanitize_key(wp_unslash($_POST['iristick_media_type'])) : 'N/A';
    $product->update_meta_data('_iristick_media_type', in_array($media_type, array('image', 'video'), true) ? $media_type : 'N/A');
    $product->update_meta_data('_iristick_media_url', isset($_POST['iristick_media_url']) ? esc_url_raw(wp_unslash($_POST['iristick_media_url'])) : 'N/A');
    $product->update_meta_data('_iristick_features', iristick_parse_product_meta_lines(isset($_POST['iristick_features']) ? wp_unslash($_POST['iristick_features']) : '', 'specs'));
    $product->update_meta_data('_iristick_specs', iristick_parse_product_meta_lines(isset($_POST['iristick_specs']) ? wp_unslash($_POST['iristick_specs']) : '', 'specs'));
    $product->update_meta_data('_iristick_documents', iristick_parse_product_meta_lines(isset($_POST['iristick_documents']) ? wp_unslash($_POST['iristick_documents']) : '', 'documents'));
    $product->update_meta_data('_iristick_faq', iristick_parse_product_meta_lines(isset($_POST['iristick_faq']) ? wp_unslash($_POST['iristick_faq']) : '', 'faq'));
    $product->save_meta_data();
}
add_action('save_post_product', 'iristick_save_product_data_meta_box', 20);

function iristick_product_document_title_parts($parts) {
    if (function_exists('is_product') && is_product()) {
        $parts['title'] = single_post_title('', false);
        $parts['site'] = 'Iristick Việt Nam';
        unset($parts['tagline'], $parts['page']);
    }
    return $parts;
}
add_filter('document_title_parts', 'iristick_product_document_title_parts');
add_filter('pre_get_document_title', function ($title) {
    if (function_exists('is_product') && is_product()) {
        return single_post_title('', false) . ' | Iristick Việt Nam';
    }
    if (function_exists('is_shop') && is_shop()) {
        $shop_page_id = (int) get_option('woocommerce_shop_page_id');
        return $shop_page_id ? get_the_title($shop_page_id) : 'Cửa hàng | Iristick Việt Nam';
    }
    if (is_page()) {
        $page = get_queried_object();
        if ($page instanceof WP_Post) {
            return get_the_title($page);
        }
    }
    return $title;
}, 20);
add_filter('document_title_separator', function ($separator) {
    return function_exists('is_product') && is_product() ? '|' : $separator;
});

function iristick_woocommerce_header_items($category_slug) {
    if (!function_exists('wc_get_products')) {
        return '';
    }
    $products = wc_get_products(array('status' => 'publish', 'limit' => -1, 'category' => array($category_slug), 'orderby' => 'menu_order', 'order' => 'ASC'));
    $items = '';
    foreach ($products as $product) {
        $summary = trim(wp_strip_all_tags($product->get_short_description()));
        $full_summary = $summary !== '' ? $summary : 'N/A';
        $menu_summary = wp_trim_words($full_summary, 10, '…');
        $items .= '<a class="iristick-wc-menu-product" href="' . esc_url($product->get_permalink()) . '"><div class="dropdown-topic svelte-1wxfnil"><div class="svelte-1wxfnil">' . esc_html($product->get_name()) . '</div><span class="svelte-1wxfnil" title="' . esc_attr($full_summary) . '">' . esc_html($menu_summary) . '</span></div></a>';
    }
    return $items;
}

function iristick_existing_product_page_path($product) {
    if (!$product instanceof WC_Product) {
        return '';
    }
    $paths = array(
        'IR-G3' => '/tools/Iristick.G3/',
        'IR-G2-PRO' => '/tools/Iristick.G2-PRO/',
        'IR-H1' => '/tools/Iristick.H1/',
        'IR-H3' => '/tools/Iristick.H3/',
        'IR-COLLECTOR' => '/products/Iristick.Collector/',
        'IR-TEAMS' => '/products/Iristick.Teams/',
        'IR-ASSIST' => '/products/Iristick.Assist/',
    );
    $sku = $product->get_sku();
    return isset($paths[$sku]) ? $paths[$sku] : '';
}

function iristick_existing_product_template_file() {
    if (!function_exists('is_product') || !is_product()) {
        return false;
    }
    $product = wc_get_product(get_queried_object_id());
    $path = iristick_existing_product_page_path($product);
    if ($path === '') {
        return false;
    }
    $file = iristick_static_page_root() . str_replace('/', DIRECTORY_SEPARATOR, $path) . 'page.php';
    return is_file($file) ? $file : false;
}

function iristick_existing_product_template_include($template) {
    return iristick_existing_product_template_file()
        ? IRISTICK_STATIC_DIR . '/existing-product.php'
        : $template;
}

function iristick_inject_woocommerce_header($html) {
    // Correct machine-translated copy in the Teams industry cards.
    $html = strtr($html, array(
        'Hợp tác quan sát thực địa với đồng nghiệp <span class="bold">Nông nghiệp</span>.' => 'Phối hợp quan sát thực địa cùng đồng nghiệp trong lĩnh vực <span class="bold">Nông nghiệp</span>.',
        'Chẩn đoán và giải quyết vấn đề tài sản với trợ giúp chuyên gia <span class="bold">Ngành công nghiệp tiện ích</span>.' => 'Chẩn đoán và xử lý sự cố thiết bị với sự hỗ trợ của chuyên gia trong <span class="bold">Ngành tiện ích</span>.',
        'Giải quyết vấn đề môi trường, sức khỏe và an toàn trong <span class="bold">quá trình công nghiệp</span>.' => 'Giải quyết các vấn đề về môi trường, sức khỏe và an toàn trong <span class="bold">Sản xuất công nghiệp</span>.',
        'Name <span class="bold">Y tế</span>.' => 'Hỗ trợ chăm sóc và tư vấn từ xa trong lĩnh vực <span class="bold">Y tế</span>.',
        'Tọa độ các vấn đề với các nhóm từ xa ở yên tại <span class="bold">Công trình xây dựng và kỹ thuật</span>.' => 'Phối hợp xử lý sự cố với các nhóm từ xa ngay tại <span class="bold">Công trường xây dựng và kỹ thuật</span>.',
        'Thi hành ngay lập tức, đánh giá thiệt hại chính xác với các chuyên gia không chính thức bởi <span class="bold">Công ty bảo hiểm</span>.' => 'Thực hiện đánh giá thiệt hại nhanh chóng, chính xác cùng chuyên gia trong <span class="bold">Ngành bảo hiểm</span>.',
        'Giải quyết vấn đề trong <span class="bold">Công trình xây dựng và kỹ thuật</span>.' => 'Giải quyết sự cố tại <span class="bold">Công trình xây dựng và kỹ thuật</span>.',
        'Hỗ trợ chuyên gia ngay lập tức cho thất bại tài sản <span class="bold">Tiện ích và năng lượng</span>.' => 'Hỗ trợ chuyên gia tức thời khi xảy ra sự cố tài sản trong ngành <span class="bold">Tiện ích và năng lượng</span>.',
        'Giám sát từ xa trong khi thực hiện kiểm tra <span class="bold">quá trình công nghiệp</span>.' => 'Giám sát từ xa trong quá trình kiểm tra <span class="bold">Sản xuất công nghiệp</span>.',
        'Sống theo hướng dẫn để phân phát <span class="bold">Y tế</span> Các đội.' => 'Hướng dẫn trực tiếp cho các đội ngũ trong lĩnh vực <span class="bold">Y tế</span>.',
        'Name <span class="bold">Sản xuất</span>.' => 'Hỗ trợ xử lý sự cố từ xa trong hoạt động <span class="bold">Sản xuất</span>.',
        'Hỗ trợ sau quảng cáo mà không cần thăm nơi Mạng <span class="bold">Nhà cung cấp thiết bị</span>.' => 'Hỗ trợ hậu mãi từ xa, không cần đến tận nơi, dành cho <span class="bold">Nhà cung cấp thiết bị</span>.',
    ));

    // Hardware glasses are products; Collector, Teams and Assist are tools.
    $html = preg_replace(
        '#(<h4\b[^>]*>\s*<span\b[^>]*>robot</span>\s*)CÔNG CỤ(\s*</h4>)#iu',
        '$1SẢN PHẨM$2',
        $html
    );
    $html = preg_replace(
        '#(<h4\b[^>]*>\s*<span\b[^>]*>devices</span>\s*)SẢN PHẨM(\s*</h4>)#iu',
        '$1CÔNG CỤ$2',
        $html
    );

    $product_items = iristick_woocommerce_header_items('san-pham');
    if ($product_items !== '') {
        $html = preg_replace(
            '#(<h4\b[^>]*>\s*<span\b[^>]*>robot</span>\s*SẢN PHẨM\s*</h4>)#iu',
            '$1' . $product_items,
            $html
        );
    }
    // This storefront uses a direct "Mua ngay" checkout flow and has no cart UI.
    $html = preg_replace('#<div class="shoplink\b[^>]*>.*?</div>#is', '', $html);
    return $html;
}

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

    // Newly created news posts do not have a captured static directory. Render
    // them through the news shell so the database content can still be injected.
    if (!is_file($file) && preg_match('#^blog/news/([^/]+)$#', $path, $matches)) {
        $news_post = get_page_by_path($matches[1], OBJECT, 'post');
        if ($news_post instanceof WP_Post && $news_post->post_status === 'publish') {
            $file = iristick_static_page_root() . '/blog/news/page.php';
        }
    }

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
    // The knowledge centre is a separate Iristick service and must remain external.
    if (!empty($parts['host']) && preg_match('/^docs\.iristick\.com$/i', $parts['host'])) {
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

    $page_path = preg_replace('#(?:/)?index\.html?$#i', '', $relative);
    $page_path = preg_replace('/\.html?$/i', '', $page_path);

    // Any HTML page route must link directly to its clean WordPress URL
    if (preg_match('/\.html?$/i', $relative) || preg_match('#(?:/)?index\.html?$#i', $relative)) {
        return home_url('/' . trim($page_path, '/') . '/') . $query . $fragment;
    }

    $disk_relative = rawurldecode($relative);
    $disk_path = iristick_static_root() . '/' . str_replace('/', DIRECTORY_SEPARATOR, $disk_relative);
    if (is_file($disk_path)) {
        return IRISTICK_STATIC_URI . '/static/' . $relative . $query . $fragment;
    }

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

function iristick_convert_static_euro_prices($html) {
    $html = preg_replace_callback('/€\s*([0-9][0-9.,]*)/u', function ($matches) {
        $raw = rtrim($matches[1], '.,');
        if (preg_match('/^([0-9.]+),([0-9]{2})$/', $raw, $parts)) {
            $euros = (float) str_replace('.', '', $parts[1]) + ((int) $parts[2] / 100);
        } elseif (preg_match('/^([0-9,]+)\.([0-9]{2})$/', $raw, $parts)) {
            $euros = (float) str_replace(',', '', $parts[1]) + ((int) $parts[2] / 100);
        } else {
            $euros = (float) preg_replace('/[^0-9]/', '', $raw);
        }
        return number_format($euros * IRISTICK_EUR_TO_VND_RATE, 0, ',', '.') . '&nbsp;₫';
    }, $html);

    return strtr($html, array(
        'Tất cả giá đều nằm ở EUR' => 'Tất cả giá đều tính bằng VND',
        'Thay vì 2.575.00' => 'thay vì 90.125.000 ₫',
        'tiết kiệm 75,00' => 'tiết kiệm 2.625.000 ₫',
        'Lưu $75,00' => 'Tiết kiệm 2.625.000 ₫',
    ));
}

function iristick_blog_database_content($request_path) {
    $is_index = $request_path === 'blog/news';
    $slug = $is_index ? '' : basename($request_path);
    if (!$is_index && strpos($request_path, 'blog/news/') !== 0) {
        return '';
    }

    if ($is_index) {
        $posts = get_posts(array('post_type' => 'post', 'post_status' => 'publish', 'numberposts' => -1, 'category_name' => 'tin-tuc', 'orderby' => 'date', 'order' => 'DESC'));
        $cards = '';
        foreach ($posts as $post) {
            $image = get_the_post_thumbnail_url($post, 'large') ?: get_post_meta($post->ID, '_iristick_blog_image_url', true);
            $excerpt = $post->post_excerpt ?: wp_trim_words(wp_strip_all_tags($post->post_content), 24, '…');
            $cards .= '<article class="iristick-news-card">'
                . ($image ? '<a href="' . esc_url(home_url('/blog/news/' . $post->post_name . '/')) . '"><img src="' . esc_url($image) . '" alt="' . esc_attr($post->post_title) . '"></a>' : '')
                . '<div><time>' . esc_html(get_the_date('d/m/Y', $post)) . '</time><h2><a href="' . esc_url(home_url('/blog/news/' . $post->post_name . '/')) . '">' . esc_html($post->post_title) . '</a></h2>'
                . '<p>' . esc_html($excerpt) . '</p><a class="iristick-news-more" href="' . esc_url(home_url('/blog/news/' . $post->post_name . '/')) . '">Đọc bài viết</a></div></article>';
        }
        return '<main class="iristick-news-db"><header><span>TIN TỨC</span><h1>Tin tức mới nhất</h1><p>Cập nhật những tin tức, sự kiện và câu chuyện mới nhất từ Iristick Việt Nam.</p></header><section class="iristick-news-grid">' . ($cards ?: '<p>Chưa có bài viết.</p>') . '</section></main>';
    }

    $post = get_page_by_path($slug, OBJECT, 'post');
    if (!$post || $post->post_status !== 'publish') {
        return '';
    }
    $image = get_the_post_thumbnail_url($post, 'full') ?: get_post_meta($post->ID, '_iristick_blog_image_url', true);
    return '<main class="iristick-news-single"><a class="iristick-news-back" href="' . esc_url(home_url('/blog/news/')) . '">← Tin tức</a><article>'
        . '<header><time>' . esc_html(get_the_date('d/m/Y', $post)) . '</time><h1>' . esc_html($post->post_title) . '</h1>'
        . ($post->post_excerpt ? '<p class="iristick-news-intro">' . esc_html($post->post_excerpt) . '</p>' : '') . '</header>'
        . ($image ? '<img class="iristick-news-hero" src="' . esc_url($image) . '" alt="' . esc_attr($post->post_title) . '">' : '')
        . '<div class="iristick-news-content">' . apply_filters('the_content', $post->post_content) . '</div></article></main>';
}

function iristick_inject_blog_database_content($html) {
    $request_path = iristick_static_request_path();
    $content = iristick_blog_database_content($request_path);
    if ($content === '') {
        return $html;
    }

    $content_start = strpos($html, '<div class="app-container"');
    $footer_start = strpos($html, '<footer id="site-footer"');
    if ($content_start === false || $footer_start === false || $footer_start <= $content_start) {
        return $html;
    }

    return substr($html, 0, $content_start) . $content . substr($html, $footer_start);
}

function iristick_static_render($file) {
    $html = file_get_contents($file);
    if ($html === false) {
        status_header(404);
        return;
    }

    // Replace the captured navigation header with the unified Iristick Việt Nam header.
    $html = preg_replace('#<div class="navigation-wrapper\b.*?(?:<nav class="mobile\b.*?<\/nav>)\s*<\/div>#is', iristick_site_header_html(), $html);

    // Replace the captured footer with the unified Iristick Việt Nam footer.
    $html = preg_replace('#<div class="footer-wrapper\b.*?</footer></div>#is', iristick_site_footer_html(), $html);

    // Correct awkward machine-translated text in captured pages.
    $translations = array(
        'Nói với chúng tôi về một phi công' => 'Liên hệ với chúng tôi',
        '<div class="pc-promo-title svelte-yaxynm">Name <span' => '<div class="pc-promo-title svelte-yaxynm">Ưu đãi đặc biệt <span',
        '<strong>Name</strong>' => '<strong>Pin dùng cả ca</strong>',
        '<strong>Comment</strong>' => '<strong>Tối ưu qua điện thoại thông minh</strong>',
    );
    $html = str_replace(array_keys($translations), array_values($translations), $html);

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
    // Snapshot pages contain their own legacy favicon.ico. Remove every saved
    // icon declaration so the single WordPress favicon below applies globally.
    $html = preg_replace('#<link\b[^>]*rel=["\'][^"\']*(?:icon|apple-touch-icon)[^"\']*["\'][^>]*>\s*#i', '', $html);
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
    $html = iristick_inject_woocommerce_header($html);

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
    $html = iristick_inject_database_page_content($html);
    $html = iristick_inject_blog_database_content($html);
    $html = iristick_convert_static_euro_prices($html);

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
    $subject = sprintf('[Iristick Việt Nam] Yêu cầu đặt lịch demo từ %s', $name);
    $rows = array(
        'Họ và tên' => $name,
        'Email' => $email,
        'Ngành nghề' => $industry ?: 'Không cung cấp',
        'Quy mô công ty' => $company_size ?: 'Không cung cấp',
        'Giải pháp quan tâm' => $solution,
    );
    $table_rows = '';
    foreach ($rows as $label => $value) {
        $table_rows .= '<tr><th style="width:38%;padding:12px 14px;text-align:left;border-bottom:1px solid #e6e3ef;background:#f5f3fb;color:#27242d;font-size:14px;">'
            . esc_html($label)
            . '</th><td style="padding:12px 14px;border-bottom:1px solid #e6e3ef;color:#3d3944;font-size:14px;">'
            . esc_html($value)
            . '</td></tr>';
    }
    $message = '<!doctype html><html lang="vi"><body style="margin:0;padding:0;background:#f4f3f8;font-family:Arial,sans-serif;color:#19191c;">'
        . '<div style="padding:32px 12px;"><div style="max-width:640px;margin:0 auto;overflow:hidden;border-radius:20px;background:#fff;box-shadow:0 14px 38px rgba(30,25,48,.1);">'
        . '<div style="padding:28px 32px;background:#19191c;color:#fff;"><div style="font-size:26px;font-weight:700;">Iristick Việt Nam</div><div style="margin-top:6px;color:#c9c2ff;font-size:15px;">Yêu cầu đặt lịch demo mới</div></div>'
        . '<div style="padding:30px 32px;"><p style="margin:0 0 20px;font-size:16px;line-height:1.6;">Website vừa nhận được một yêu cầu tư vấn và đặt lịch demo.</p>'
        . '<table role="presentation" style="width:100%;border-collapse:separate;border-spacing:0;overflow:hidden;border:1px solid #e6e3ef;border-radius:12px;">' . $table_rows . '</table>'
        . '<div style="margin-top:22px;padding:18px;border-radius:12px;background:#f5f3fb;"><strong style="display:block;margin-bottom:8px;color:#27242d;">Câu hỏi hoặc nội dung muốn trao đổi</strong><div style="font-size:14px;line-height:1.65;color:#4d4855;">'
        . nl2br(esc_html($questions ?: 'Không cung cấp'))
        . '</div></div><p style="margin:24px 0 0;color:#77727e;font-size:13px;">Bạn có thể trả lời trực tiếp email này để liên hệ với khách hàng.</p></div>'
        . '<div style="padding:18px 32px;background:#faf9fc;color:#817c87;text-align:center;font-size:12px;">Iristick Việt Nam</div>'
        . '</div></div></body></html>';
    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'Reply-To: ' . $name . ' <' . $email . '>',
    );
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
    $phone = isset($_POST['phone']) ? iristick_normalize_vietnam_phone(sanitize_text_field(wp_unslash($_POST['phone']))) : '';
    $hardware = isset($_POST['hardware']) ? sanitize_text_field(wp_unslash($_POST['hardware'])) : '';
    $software = isset($_POST['software']) ? array_map('sanitize_text_field', (array) wp_unslash($_POST['software'])) : array();
    $quantity = isset($_POST['quantity']) ? max(1, min(10, absint($_POST['quantity']))) : 1;
    $notes = isset($_POST['notes']) ? sanitize_textarea_field(wp_unslash($_POST['notes'])) : '';

    if ($name === '' || !is_email($email) || $hardware === '' || empty($software)
        || ($phone !== '' && !preg_match('/^0(?:3|5|7|8|9)[0-9]{8}$/', $phone))) {
        wp_safe_redirect(home_url('/trial-order/?status=invalid'));
        exit;
    }

    $default_recipient = defined('IRISTICK_DEMO_EMAIL')
        ? sanitize_email(IRISTICK_DEMO_EMAIL)
        : sanitize_email(get_option('admin_email'));
    $recipient = apply_filters('iristick_trial_recipient_email', $default_recipient);
    $subject = sprintf('[Iristick Việt Nam] Đăng ký dùng thử 6 tuần từ %s', $name);
    $rows = array(
        'Họ và tên' => $name,
        'Email' => $email,
        'Số điện thoại' => $phone ?: 'Không cung cấp',
        'Công ty' => $company ?: 'Không cung cấp',
        'Thiết bị dùng thử' => $hardware,
        'Phần mềm dùng thử' => implode(', ', $software),
        'Số lượng' => $quantity,
        'Tạm tính' => number_format(1000 * IRISTICK_EUR_TO_VND_RATE * $quantity, 0, ',', '.') . ' ₫ (chưa gồm VAT)',
    );
    $table_rows = '';
    foreach ($rows as $label => $value) {
        $table_rows .= '<tr><th style="width:38%;padding:12px 14px;text-align:left;border-bottom:1px solid #e6e3ef;background:#f5f3fb;color:#27242d;font-size:14px;">'
            . esc_html($label)
            . '</th><td style="padding:12px 14px;border-bottom:1px solid #e6e3ef;color:#3d3944;font-size:14px;">'
            . esc_html($value)
            . '</td></tr>';
    }
    $message = '<!doctype html><html lang="vi"><body style="margin:0;padding:0;background:#f4f3f8;font-family:Arial,sans-serif;color:#19191c;">'
        . '<div style="padding:32px 12px;"><div style="max-width:640px;margin:0 auto;overflow:hidden;border-radius:20px;background:#fff;box-shadow:0 14px 38px rgba(30,25,48,.1);">'
        . '<div style="padding:28px 32px;background:#19191c;color:#fff;"><div style="font-size:26px;font-weight:700;">Iristick Việt Nam</div><div style="margin-top:6px;color:#c9c2ff;font-size:15px;">Đăng ký chương trình dùng thử 6 tuần</div></div>'
        . '<div style="padding:30px 32px;"><p style="margin:0 0 20px;font-size:16px;line-height:1.6;">Website vừa nhận được một yêu cầu đăng ký chương trình dùng thử mới.</p>'
        . '<table role="presentation" style="width:100%;border-collapse:separate;border-spacing:0;overflow:hidden;border:1px solid #e6e3ef;border-radius:12px;">' . $table_rows . '</table>'
        . '<div style="margin-top:22px;padding:18px;border-radius:12px;background:#f5f3fb;"><strong style="display:block;margin-bottom:8px;color:#27242d;">Ghi chú</strong><div style="font-size:14px;line-height:1.65;color:#4d4855;">'
        . nl2br(esc_html($notes ?: 'Không cung cấp'))
        . '</div></div><p style="margin:24px 0 0;color:#77727e;font-size:13px;">Bạn có thể trả lời trực tiếp email này để liên hệ với khách hàng.</p></div>'
        . '<div style="padding:18px 32px;background:#faf9fc;color:#817c87;text-align:center;font-size:12px;">Iristick Việt Nam</div>'
        . '</div></div></body></html>';
    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'Reply-To: ' . $name . ' <' . $email . '>',
    );
    $sent = wp_mail($recipient, $subject, $message, $headers);
    wp_safe_redirect(home_url('/trial-order/?status=' . ($sent ? 'success' : 'error')));
    exit;
}
add_action('admin_post_nopriv_iristick_trial_request', 'iristick_handle_trial_request');
add_action('admin_post_iristick_trial_request', 'iristick_handle_trial_request');
