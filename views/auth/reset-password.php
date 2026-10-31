<div class="auth-shell">
    <div class="auth-panel">
        <h2>Đặt lại mật khẩu</h2>
        <form id="resetPasswordForm" class="auth-form" action="<?= url('?action=reset-password-submit') ?>" method="POST">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '', ENT_QUOTES, 'UTF-8') ?>">

            <?php if (isset($message)): ?>
                <div class="auth-alert" role="alert">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <div class="auth-row">
                <label for="password" class="auth-label">Mật khẩu mới</label>
                <input type="password" class="auth-field" id="password" name="password" placeholder="Mật khẩu mới" required autocomplete="new-password">
            </div>

            <div class="auth-row">
                <label for="confirm_password" class="auth-label">Xác nhận mật khẩu</label>
                <input type="password" class="auth-field" id="confirm_password" name="confirm_password" placeholder="Xác nhận mật khẩu" required autocomplete="new-password">
            </div>

            <button id="resetSubmitButton" type="submit" class="auth-submit">Cập nhật mật khẩu</button>
            <div class="text-center mt-3">
                <a id="resetBackLink" href="<?= url('?action=login') ?>" class="auth-link">Quay lại đăng nhập</a>
            </div>
        </form>
        <script>
            document.getElementById('resetPasswordForm').addEventListener('submit', function () {
                var submitButton = document.getElementById('resetSubmitButton');
                var backLink = document.getElementById('resetBackLink');
                submitButton.disabled = true;
                submitButton.textContent = 'Đang cập nhật...';
                backLink.classList.add('disabled');
                backLink.style.pointerEvents = 'none';
            });
        </script>
    </div>
</div>
