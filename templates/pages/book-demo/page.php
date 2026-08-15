<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Đăng ký buổi tư vấn và trải nghiệm giải pháp kính thông minh Iristick.">
    <title>Đặt lịch demo | Iristick</title>
    <link rel="icon" href="../../pictures/favicon.ico">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="../../css2-1?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000&amp;display=swap">
</head>
<body style="margin:0">
<main class="demo-page">
    <section class="demo-card" aria-labelledby="demo-title">
        <div class="demo-intro">
            <a class="demo-logo" href="../" aria-label="Về trang chủ Iristick">
                <img src="{{IRISTICK_THEME_URI}}/assets/images/iristick-logo.webp" alt="Iristick">
            </a>
            <h1 id="demo-title">Đặt lịch demo</h1>
        </div>
        <div class="demo-form-wrap">
            <div class="demo-notice success" data-status="success" hidden>
                Cảm ơn bạn! Yêu cầu đã được gửi. Đội ngũ tư vấn sẽ sớm liên hệ lại.
            </div>
            <div class="demo-notice error" data-status="error" hidden>
                Chưa thể gửi yêu cầu. Vui lòng kiểm tra thông tin hoặc thử lại sau.
            </div>
            <form class="demo-form" action="{{IRISTICK_ADMIN_POST_URL}}" method="post">
                <input type="hidden" name="action" value="iristick_demo_request">
                {{IRISTICK_DEMO_FORM_NONCE}}
                <p class="demo-honeypot" aria-hidden="true">
                    <label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                </p>
                <div class="demo-field">
                    <label for="demo-name">Họ và tên *</label>
                    <input id="demo-name" name="name" type="text" autocomplete="name" required>
                </div>
                <div class="demo-field">
                    <label for="demo-email">Email *</label>
                    <input id="demo-email" name="email" type="email" autocomplete="email" required>
                </div>
                <div class="demo-field">
                    <label for="demo-industry">Công ty của bạn hoạt động trong lĩnh vực nào?</label>
                    <input id="demo-industry" name="industry" type="text" autocomplete="organization-title">
                </div>
                <div class="demo-field">
                    <label for="demo-size">Quy mô công ty của bạn?</label>
                    <select id="demo-size" name="company_size">
                        <option value="">Chọn...</option>
                        <option value="1–10 nhân viên">1–10 nhân viên</option>
                        <option value="11–50 nhân viên">11–50 nhân viên</option>
                        <option value="51–200 nhân viên">51–200 nhân viên</option>
                        <option value="201–1.000 nhân viên">201–1.000 nhân viên</option>
                        <option value="Trên 1.000 nhân viên">Trên 1.000 nhân viên</option>
                    </select>
                </div>
                <div class="demo-field">
                    <label for="demo-solution">Bạn quan tâm đến giải pháp Iristick nào? *</label>
                    <select id="demo-solution" name="solution" required>
                        <option value="">Chọn...</option>
                        <option value="Iristick.Collector">Iristick.Collector</option>
                        <option value="Iristick.Teams">Iristick.Teams</option>
                        <option value="Iristick.Assist">Iristick.Assist</option>
                        <option value="Kính thông minh Iristick">Kính thông minh Iristick</option>
                        <option value="Giải pháp khác">Giải pháp khác</option>
                    </select>
                </div>
                <div class="demo-field">
                    <label for="demo-questions">Bạn có câu hỏi hoặc chủ đề cụ thể nào muốn trao đổi trong cuộc gọi?</label>
                    <textarea id="demo-questions" name="questions"></textarea>
                </div>
                <button class="demo-submit" type="submit">Gửi yêu cầu</button>
            </form>
        </div>
    </section>
</main>
<script>
    (function () {
        var status = new URLSearchParams(window.location.search).get('status');
        var notice = document.querySelector('[data-status="' + (status === 'success' ? 'success' : 'error') + '"]');
        if (status && notice) notice.hidden = false;
    }());
</script>
</body>
</html>
