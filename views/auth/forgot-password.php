<div class="auth-shell">
    <div class="auth-panel">
        <h2>Quên mật khẩu</h2>
        <form id="forgotPasswordForm" class="auth-form" action="<?= url('?action=forgot-password-submit') ?>" method="POST">

            <?php if (isset($message)): ?>
                <div class="auth-alert" role="alert">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <div class="auth-row">
                <label for="email" class="auth-label">Email đăng ký</label>
                <input type="email" class="auth-field" id="email" name="email" placeholder="Nhập email của bạn" required autocomplete="email">
            </div>

            <button id="forgotSubmitButton" type="submit" class="auth-submit">Gửi liên kết đặt lại</button>
            <div class="text-center mt-3">
                <a id="forgotBackLink" href="<?= url('?action=login') ?>" class="auth-link">Quay lại đăng nhập</a>
            </div>
        </form>
        <script>
            document.getElementById('forgotPasswordForm').addEventListener('submit', function () {
                var submitButton = document.getElementById('forgotSubmitButton');
                var backLink = document.getElementById('forgotBackLink');
                submitButton.disabled = true;
                submitButton.textContent = 'Đang gửi...';
                backLink.classList.add('disabled');
                backLink.style.pointerEvents = 'none';
            });
        </script>
    </div>
</div>
