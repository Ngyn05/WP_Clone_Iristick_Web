<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Đăng ký gói trải nghiệm kính thông minh và phần mềm Iristick trong 6 tuần.">
    <title>Gói dùng thử Iristick 6 tuần</title>
    <link rel="icon" href="../pictures/favicon.ico">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="../css2-1?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000&amp;display=swap">
    <link rel="stylesheet" href="{{IRISTICK_THEME_URI}}/assets/css/demo-form.css">
    <link rel="stylesheet" href="{{IRISTICK_THEME_URI}}/assets/css/responsive.css">
    <style>
        .trial-shop { min-height: 100vh; background: #f7f7f8; color: #1e1e21; font-family: "DM Sans", Arial, sans-serif; overflow-x: hidden; }
        .trial-shop-header { display: flex; align-items: center; justify-content: space-between; gap: 16px; max-width: 1200px; margin: auto; padding: 1.5rem 2rem; box-sizing: border-box; }
        .trial-shop-header img { display: block; width: 180px; max-width: 100%; height: auto; object-fit: contain; }
        .trial-shop-header > a:last-child { color: #1e1e21; font-weight: 650; text-decoration: none; }
        .trial-product { display: grid; grid-template-columns: 1fr 1fr; gap: 48px; max-width: 1200px; margin: auto; padding: 2rem 2rem 5rem; box-sizing: border-box; align-items: start; }
        .trial-product-media { width: 100%; min-width: 0; max-width: 100%; box-sizing: border-box; }
        #trial-main-image { display: block; width: 100%; height: auto; max-height: 420px; aspect-ratio: 4 / 3; object-fit: contain; border-radius: 20px; background: #fff; box-shadow: 0 4px 20px rgba(0,0,0,.04); padding: 12px; box-sizing: border-box; }
        .trial-breadcrumb { margin: 0 0 1.25rem; color: #77777c; font-size: .85rem; line-height: 1.4; word-break: break-word; }
        .trial-thumbnails { display: grid !important; grid-template-columns: repeat(5, minmax(0, 1fr)) !important; gap: 8px !important; width: 100% !important; max-width: 100% !important; margin-top: 12px !important; box-sizing: border-box !important; }
        .trial-thumbnails button { display: flex !important; align-items: center !important; justify-content: center !important; width: 100% !important; min-width: 0 !important; max-width: 100% !important; aspect-ratio: 1 / 1 !important; padding: 0 !important; margin: 0 !important; overflow: hidden !important; border: 2px solid #e5e7eb !important; border-radius: 10px !important; background: #fff !important; cursor: pointer !important; box-sizing: border-box !important; }
        .trial-thumbnails button.active { border-color: #ff6c5c !important; }
        .trial-thumbnails img { display: block !important; width: 100% !important; height: 100% !important; object-fit: cover !important; }
        .trial-details { margin-top: 2.5rem; color: #333338; font-size: .95rem; line-height: 1.65; }
        .trial-product-info { width: 100%; min-width: 0; max-width: 100%; box-sizing: border-box; }
        .trial-eyebrow { margin: 0 0 .5rem; color: #ff6c5c; font-weight: 750; font-size: .85rem; text-transform: uppercase; }
        .trial-product-info h1 { margin: 0; font-size: clamp(1.6rem, 3.5vw, 2.3rem); line-height: 1.15; word-break: break-word; }
        .trial-price { margin: 1.25rem 0; font-size: clamp(1.6rem, 3vw, 2.2rem); font-weight: 750; }
        .trial-order-form { display: grid; gap: 1.3rem; margin-top: 1.5rem; width: 100%; box-sizing: border-box; }
        .trial-choice { display: flex; gap: .85rem; align-items: flex-start; padding: .85rem 1rem; border: 1px solid #d9d9df; border-radius: 12px; background: #fff; cursor: pointer; box-sizing: border-box; }
        .trial-choice:has(input:checked) { border-color: #ff6c5c; box-shadow: 0 0 0 2px rgba(255,108,92,.12); background: #fffdfd; }
        .trial-choice input[type="radio"] { margin-top: 3px; }
        .trial-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; width: 100%; }
        .trial-order-form > label, .trial-row label { display: grid; gap: .45rem; font-weight: 650; font-size: .9rem; }
        .trial-order-form input:not([type="radio"]):not([type="checkbox"]), .trial-order-form textarea { width: 100%; min-height: 44px; padding: .75rem .9rem; border: 1px solid #c9cad1; border-radius: 10px; background: #fff; font: inherit; box-sizing: border-box; }
        .trial-order-submit { width: 100%; min-height: 52px; padding: 0 2rem; border: 0; border-radius: 14px; background: #ff6c5c; color: #fff; font: inherit; font-size: 1.05rem; font-weight: 750; cursor: pointer; }

        @media (max-width: 900px) {
            .trial-product { display: flex !important; flex-direction: column !important; padding: 1.25rem 14px 4rem !important; gap: 24px !important; }
            .trial-product-media, .trial-product-info { width: 100% !important; max-width: 100% !important; }
            #trial-main-image { max-height: 280px !important; aspect-ratio: 4 / 3 !important; border-radius: 14px !important; padding: 8px !important; }
            .trial-thumbnails { gap: 6px !important; }
            .trial-thumbnails button { border-radius: 8px !important; }
            .trial-row { grid-template-columns: 1fr !important; gap: .85rem !important; }
            .trial-order-submit { min-height: 48px !important; font-size: 1rem !important; }
            .trial-shop-header { padding: 1rem 14px !important; }
            .trial-shop-header img { width: 130px !important; }
        }

        @media (max-width: 400px) {
            .trial-shop-header > a:last-child { display: none !important; }
            .trial-product-info h1 { font-size: 1.4rem !important; }
            .trial-price { font-size: 1.4rem !important; }
            .trial-thumbnails { gap: 4px !important; }
        }
    </style>
</head>
<body style="margin:0">
<main class="trial-shop">
    <header class="trial-shop-header">
        <a href="../" aria-label="Về trang chủ"><img src="{{IRISTICK_THEME_URI}}/assets/images/iristick-logo.webp" alt="Iristick"></a>
        <a href="../trial-program/">Thông tin chương trình dùng thử</a>
    </header>
    <section class="trial-product">
        <div class="trial-product-media">
            <nav class="trial-breadcrumb" aria-label="Breadcrumb"><a href="../">Trang chủ</a> / <a href="../trial-program/">Chương trình dùng thử</a> / Đăng ký</nav>
            <img id="trial-main-image" src="{{IRISTICK_THEME_URI}}/assets/images/trial-original-4.jpg" alt="Trọn bộ kính và phụ kiện Iristick dùng thử">
            <div class="trial-thumbnails" aria-label="Ảnh sản phẩm">
                <button type="button"><img src="{{IRISTICK_THEME_URI}}/assets/images/trial-original-1.jpg" alt="Kính thông minh Iristick"></button>
                <button type="button"><img src="{{IRISTICK_THEME_URI}}/assets/images/trial-original-2.jpg" alt="Kính Iristick cùng bộ kết nối"></button>
                <button type="button"><img src="{{IRISTICK_THEME_URI}}/assets/images/trial-original-3.jpg" alt="Kính Iristick nhìn từ phía trước"></button>
                <button class="active" type="button"><img src="{{IRISTICK_THEME_URI}}/assets/images/trial-original-4.jpg" alt="Trọn bộ kính và phụ kiện Iristick dùng thử"></button>
                <button type="button"><img src="{{IRISTICK_THEME_URI}}/assets/images/trial-original-5.jpg" alt="Thiết bị kính thông minh Iristick.H1"></button>
            </div>
            <div class="trial-details">
                <h2>Chi tiết sản phẩm</h2>
                <h3>Gói dùng thử bao gồm những gì?</h3>
                <p>Một hoặc nhiều bộ dùng thử (kính thông minh và phụ kiện) — bạn quyết định số lượng muốn bắt đầu.</p>
                <p>Quyền truy cập đầy đủ vào phần mềm bạn muốn trải nghiệm: Iristick.Collector để thu thập dữ liệu rảnh tay, Iristick.Assist để hỗ trợ từ xa và/hoặc Iristick.Teams để cộng tác từ xa. Gói dùng thử cũng bao gồm buổi hướng dẫn thiết lập và hỗ trợ xuyên suốt chương trình.</p>
                <p><em>Hãy cho chúng tôi biết ngày bạn muốn bắt đầu chương trình. Chúng tôi sẽ cố gắng giao bộ thiết bị tới địa chỉ của bạn trước ngày đó, nhưng không thể đảm bảo do thời gian vận chuyển quốc tế. Nếu dự kiến có vấn đề, chúng tôi sẽ chủ động liên hệ.</em></p>
                <h3>Bạn có thể mong đợi điều gì?</h3>
                <h4>Tuần 1 — Khởi động</h4>
                <p>Chúng tôi lên lịch một buổi hướng dẫn trực tuyến dựa trên phần mềm bạn muốn trải nghiệm.</p>
                <p>Nếu dùng thử Iristick.Collector, bạn có thể yêu cầu hỗ trợ thiết lập trực tiếp tại chỗ. Điều kiện áp dụng — vui lòng liên hệ để biết thêm thông tin.</p>
                <h4>Tuần 2–5 — Sử dụng trong môi trường của bạn</h4>
                <p>Bạn bắt đầu vận hành thực tế. Chúng tôi đề xuất trao đổi hằng tuần để giải đáp câu hỏi, thu thập phản hồi và giúp bạn khai thác tối đa gói dùng thử.</p>
                <h4>Tuần 6 — Đưa ra quyết định</h4>
                <p>Tiếp tục sử dụng hoặc hoàn trả bộ thiết bị. Nếu mua thiết bị, toàn bộ €1.000 chi phí dùng thử sẽ được khấu trừ khỏi giá mua.</p>
                <p><em>Nếu mua, đơn dùng thử sẽ được chuyển thành đơn quản trị. Các bước hậu cần, tài chính và dịch vụ liên quan sẽ được xử lý riêng.</em></p>
                <p>Mỗi công ty chỉ đủ điều kiện tham gia một chương trình dùng thử. Sau khi gửi đăng ký, bạn sẽ nhận được thỏa thuận dùng thử để ký. Chương trình chính thức bắt đầu khi thỏa thuận được ký.</p>
            </div>
        </div>
        <div class="trial-product-info">
            <p class="trial-eyebrow">Chương trình trải nghiệm</p>
            <h1>Kính thông minh Iristick + phần mềm — dùng thử 6 tuần</h1>
            <p class="trial-price">€1.000,00 <small>mỗi thiết bị · chưa gồm VAT</small></p>
            <p>Trải nghiệm giải pháp Iristick ngay trong môi trường làm việc của bạn. Toàn bộ chi phí dùng thử sẽ được khấu trừ nếu bạn quyết định mua thiết bị sau chương trình.</p>
            <div class="demo-notice success" data-status="success" hidden>Cảm ơn bạn! Đăng ký đã được gửi tới đội ngũ tư vấn.</div>
            <div class="demo-notice error" data-status="error" hidden>Chưa thể gửi đăng ký. Vui lòng kiểm tra thông tin hoặc thử lại.</div>
            <form class="trial-order-form" action="{{IRISTICK_ADMIN_POST_URL}}" method="post">
                <input type="hidden" name="action" value="iristick_trial_request">
                {{IRISTICK_TRIAL_FORM_NONCE}}
                <p class="demo-honeypot" aria-hidden="true"><label>Website <input name="website" tabindex="-1" autocomplete="off"></label></p>
                <fieldset><legend>1. Chọn kính thông minh *</legend>
                    <label class="trial-choice"><input type="radio" name="hardware" value="Iristick.G3" required><span><strong>Iristick.G3</strong><small>Mẫu mới nhất, kết nối USB-C trực tiếp</small></span></label>
                    <label class="trial-choice"><input type="radio" name="hardware" value="Iristick.H1"><span><strong>Iristick.H1</strong><small>Bền chắc, phù hợp mũ bảo hộ và PPE</small></span></label>
                    <label class="trial-choice"><input type="radio" name="hardware" value="Iristick.G2 PRO"><span><strong>Iristick.G2 PRO</strong><small>Kính bảo hộ thông minh được chứng nhận</small></span></label>
                </fieldset>
                <fieldset><legend>2. Chọn phần mềm muốn trải nghiệm *</legend>
                    <label class="trial-check"><input type="checkbox" name="software[]" value="Iristick.Collector"> Iristick.Collector</label>
                    <label class="trial-check"><input type="checkbox" name="software[]" value="Iristick.Teams"> Iristick.Teams</label>
                    <label class="trial-check"><input type="checkbox" name="software[]" value="Iristick.Assist"> Iristick.Assist</label>
                </fieldset>
                <div class="trial-row">
                    <label>Họ và tên *<input name="name" autocomplete="name" required></label>
                    <label>Email *<input type="email" name="email" autocomplete="email" required></label>
                    <label>Công ty<input name="company" autocomplete="organization"></label>
                    <label>Số điện thoại<input type="tel" name="phone" inputmode="numeric" autocomplete="tel" placeholder="Ví dụ: 0917834532" pattern="(?:\+84|0)(?:3|5|7|8|9)[0-9]{8}" minlength="10" maxlength="12" title="Nhập số di động Việt Nam, ví dụ 0917834532 hoặc +84917834532"></label>
                </div>
                <label>Số lượng<input type="number" name="quantity" value="1" min="1" max="10"></label>
                <label>Ghi chú<textarea name="notes" rows="4"></textarea></label>
                <button class="trial-order-submit" type="submit">Gửi đăng ký dùng thử</button>
                <p class="trial-fineprint">Đây là yêu cầu đăng ký, chưa phải thanh toán trực tuyến. Đội ngũ tư vấn sẽ xác nhận tình trạng thiết bị, vận chuyển và các bước tiếp theo qua email.</p>
            </form>
        </div>
    </section>
</main>
<script>
(function () {
    var status = new URLSearchParams(location.search).get('status');
    if (status) {
        var notice = document.querySelector('[data-status="' + (status === 'success' ? 'success' : 'error') + '"]');
        if (notice) notice.hidden = false;
    }
    var form = document.querySelector('.trial-order-form');
    var software = Array.from(form.querySelectorAll('input[name="software[]"]'));
    form.addEventListener('submit', function (event) {
        if (!software.some(function (item) { return item.checked; })) {
            event.preventDefault();
            software[0].setCustomValidity('Vui lòng chọn ít nhất một phần mềm.');
            software[0].reportValidity();
        }
    });
    software.forEach(function (item) {
        item.addEventListener('change', function () {
            software[0].setCustomValidity('');
        });
    });
    var mainImage = document.getElementById('trial-main-image');
    document.querySelectorAll('.trial-thumbnails button').forEach(function (button) {
        button.addEventListener('click', function () {
            var thumbnail = button.querySelector('img');
            mainImage.src = thumbnail.currentSrc || thumbnail.src;
            mainImage.alt = thumbnail.alt;
            document.querySelectorAll('.trial-thumbnails button').forEach(function (item) { item.classList.remove('active'); });
            button.classList.add('active');
        });
    });
}());
</script>
</body>
</html>
