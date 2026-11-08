<?php $ownedDiscounts = array_map('intval', array_column($vouchers ?? [], 'discount_percent')); ?>

<div class="hs-container hs-game-page">
    <div class="hs-game-wrap">
        <header class="hs-game-head">
            <p class="hs-eyebrow">Thử thách mỗi 5 phút</p>
            <h1 class="hs-page-title">Thử thách 5 câu</h1>
            <p class="hs-hint">Trả lời đúng các câu hỏi, voucher càng lớn. Đạt điểm tuyệt đối để săn voucher giảm 90%.</p>
        </header>

        <div class="hs-game-steps">
            <?php foreach ([10, 30, 50, 70, 90] as $percent): ?>
                <?php $isOwned = in_array($percent, $ownedDiscounts, true); ?>
                <span class="hs-reward-chip<?= $isOwned ? ' is-owned' : '' ?>">
                    <?= $isOwned ? '<i class="bi bi-check2"></i>' : '' ?>
                    +<?= $percent ?>%
                </span>
            <?php endforeach; ?>
            <a class="hs-link" href="<?= url('?action=my-vouchers') ?>">Xem túi voucher →</a>
        </div>

        <section class="hs-game-panel">
            <div id="quiz-start" class="hs-game-start<?= $hasCooldown ? ' d-none' : '' ?>">
                <button type="button" id="start-quiz" class="hs-btn hs-btn--primary hs-btn--lg">
                    <i class="bi bi-play-fill" aria-hidden="true"></i>Bắt đầu
                </button>
            </div>

            <form id="quiz-form" class="d-none">
                <div id="question-list" class="d-grid gap-3"></div>
                <button type="submit" id="submit-quiz" class="hs-btn hs-btn--primary">
                    <i class="bi bi-check2-circle" aria-hidden="true"></i>Trả lời
                </button>
            </form>

            <div id="game-message" aria-live="polite">
                <?php if ($hasCooldown && $todayAward): ?>
                    <div class="alert alert-success mb-0">Bạn đã nhận mã <code><?= htmlspecialchars($todayAward['code']) ?></code> giảm <?= (int)$todayAward['discount_percent'] ?>%. Có thể chơi lại sau <span id="cooldown-timer" data-seconds="<?= max(0, (int)$cooldownRemaining) ?>"></span>.</div>
                <?php elseif ($hasCooldown): ?>
                    <div class="alert alert-secondary mb-0">Bạn vừa chơi xong. Có thể chơi lại sau <span id="cooldown-timer" data-seconds="<?= max(0, (int)$cooldownRemaining) ?>"></span>.</div>
                <?php else: ?>
                    <p class="hs-hint">Mỗi tài khoản được chơi một lần sau mỗi 5 phút.</p>
                <?php endif; ?>
            </div>
        </section>

        <section class="hs-game-feed">
            <h2 class="hs-section-title">Bảng tin chúc mừng</h2>
            <?php if (empty($recentAwards)): ?>
                <p class="hs-hint">Chưa có lượt nhận voucher nào.</p>
            <?php else: ?>
                <div class="hs-feed">
                    <?php foreach ($recentAwards as $award): ?>
                        <div class="hs-feed-item">
                            <i class="bi bi-stars" aria-hidden="true"></i>
                            <span>Chúc mừng <strong><?= htmlspecialchars($award['fullname'] ?: $award['username']) ?></strong> đã sở hữu voucher <strong><?= (int)$award['discount_percent'] ?>%</strong> với mã <code><?= htmlspecialchars($award['code']) ?></code>.</span>
                            <time class="hs-hint" datetime="<?= htmlspecialchars($award['created_at']) ?>"><?= date('d/m H:i', strtotime($award['created_at'])) ?></time>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const startButton = document.getElementById('start-quiz');
        const startPanel = document.getElementById('quiz-start');
        const form = document.getElementById('quiz-form');
        const questionList = document.getElementById('question-list');
        const submitButton = document.getElementById('submit-quiz');
        const message = document.getElementById('game-message');
        const timer = document.getElementById('cooldown-timer');
        const escapeHtml = (value) => String(value).replace(/[&<>"']/g, (character) => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
        }[character]));

        const formatCountdown = (seconds) => {
            const totalSeconds = Math.max(0, Number(seconds) || 0);
            const minutes = String(Math.floor(totalSeconds / 60)).padStart(2, '0');
            const remainingSeconds = String(totalSeconds % 60).padStart(2, '0');
            return `${minutes}:${remainingSeconds}`;
        };

        const startCooldownTimer = () => {
            if (!timer) {
                return;
            }

            let remaining = Math.max(0, Number(timer.dataset.seconds) || 0);
            const apply = () => {
                timer.textContent = formatCountdown(remaining);
                remaining = Math.max(0, remaining - 1);
                if (remaining < 0) {
                    remaining = 0;
                }
                if (remaining === 0) {
                    if (message) {
                        message.innerHTML = '<button type="button" class="hs-btn hs-btn--primary" id="replay">Chơi lại</button>';
                        const replayButton = document.getElementById('replay');
                        replayButton?.addEventListener('click', () => {
                            location.reload();
                        });
                    }
                    return true;
                }
                return false;
            };

            if (apply()) {
                return;
            }

            const interval = setInterval(() => {
                if (apply()) {
                    clearInterval(interval);
                }
            }, 1000);
        };

        const renderQuestion = (item) => {
            questionList.innerHTML = `
                <div class="hs-question">
                    <label class="hs-label hs-question__text" for="answer">Câu ${item.index + 1}: ${escapeHtml(item.question)}</label>
                    <input id="answer" name="answer" class="form-control hs-input" required autocomplete="off" placeholder="Nhập câu trả lời">
                </div>
            `;
            submitButton.disabled = false;
            submitButton.innerHTML = '<i class="bi bi-check2-circle" aria-hidden="true"></i>Trả lời';
        };

        startButton?.addEventListener('click', async () => {
            startButton.disabled = true;
            startButton.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Đang tạo câu hỏi...';
            try {
                const response = await fetch('<?= url('?action=mini-game-start') ?>', {
                    method: 'POST',
                    headers: {'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json'}
                });
                const data = await response.json();
                if (data.status !== 'success') throw new Error(data.message || 'Không thể tạo câu hỏi.');

                renderQuestion(data.question);
                startPanel.classList.add('d-none');
                form.classList.remove('d-none');
                if (data.fallback && data.message) {
                    message.innerHTML = `<div class="alert alert-info">${data.message}</div>`;
                }
            } catch (error) {
                message.innerHTML = `<div class="alert alert-warning">${error.message}</div>`;
                startButton.disabled = false;
                startButton.innerHTML = '<i class="bi bi-play-fill" aria-hidden="true"></i>Bắt đầu';
            }
        });

        form?.addEventListener('submit', async (event) => {
            event.preventDefault();
            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Đang chấm...';
            try {
                const response = await fetch('<?= url('?action=mini-game-answer') ?>', {
                    method: 'POST',
                    headers: {'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json'},
                    body: new FormData(form)
                });
                const data = await response.json();
                if (data.status !== 'success') throw new Error(data.message || 'Không thể chấm điểm.');

                message.innerHTML = `<div class="alert alert-${data.correct ? 'success' : 'warning'} mb-3">Câu ${data.question_number}: ${data.correct ? 'Đúng!' : 'Chưa đúng.'} Đáp án: <strong>${escapeHtml(data.correct_answer)}</strong></div>`;
                if (data.finished) {
                    const voucher = data.voucher;
                    const voucherMarkup = voucher ? `
                        <div class="hs-ticket hs-ticket--lg">
                            <div class="hs-ticket__pct">GIẢM <strong>${voucher.discount_percent}%</strong></div>
                            <div class="hs-ticket__body">
                                <div class="hs-ticket__code">
                                    <code>${voucher.code}</code>
                                    <button type="button" class="hs-btn hs-btn--ghost" data-copy="${voucher.code}">Sao chép</button>
                                </div>
                                <a class="hs-btn hs-btn--primary" href="<?= url('?action=cart') ?>">Dùng khi thanh toán →</a>
                            </div>
                        </div>
                    ` : '';
                    message.innerHTML = `<div class="alert alert-success mb-0"><strong>${data.message}</strong>${voucherMarkup}</div>`;
                    form.classList.add('d-none');
                } else {
                    renderQuestion(data.next_question);
                }
            } catch (error) {
                message.innerHTML = `<div class="alert alert-warning">${error.message}</div>`;
                submitButton.disabled = false;
                submitButton.innerHTML = '<i class="bi bi-check2-circle" aria-hidden="true"></i>Chấm điểm';
            }
        });

        document.addEventListener('click', async (event) => {
            const copyButton = event.target.closest('[data-copy]');
            if (!copyButton) {
                return;
            }

            const originalText = copyButton.textContent;
            await navigator.clipboard.writeText(copyButton.dataset.copy);
            copyButton.textContent = 'Đã chép';
            setTimeout(() => {
                copyButton.textContent = originalText;
            }, 1200);
        });

        startCooldownTimer();
    });
</script>
