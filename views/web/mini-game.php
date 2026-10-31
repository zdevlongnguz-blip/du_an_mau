<style>
    .game-shell { max-width: 820px; margin: 0 auto; }
    .game-hero { border: 1px solid rgba(255, 122, 26, .3); background: radial-gradient(circle at 80% 10%, rgba(255, 122, 26, .18), transparent 38%), #151515; }
    .quiz-panel { border: 1px solid rgba(255,255,255,.1); background: #17181a; }
    .question-card { border-left: 3px solid var(--accent); background: rgba(255,255,255,.04); }
    .voucher-banner { border: 1px solid rgba(255, 122, 26, .35); background: radial-gradient(circle at 85% 15%, rgba(255, 122, 26, .22), transparent 36%), #17181a; }
    .voucher-step { display: inline-flex; min-width: 58px; justify-content: center; padding: .55rem .7rem; border: 1px solid rgba(255, 193, 7, .55); border-radius: 999px; color: #ffc107; font-weight: 700; }
    .voucher-step.is-owned { color: #8c9298; border-color: rgba(140, 146, 152, .5); background: rgba(140, 146, 152, .12); text-decoration: line-through; }
    .award-log { border-top: 1px solid rgba(255,255,255,.1); }
    .award-log-item { display: flex; align-items: center; gap: .65rem; padding: .8rem 0; border-bottom: 1px solid rgba(255,255,255,.08); }
    @media (max-width: 575.98px) { .award-log-item { align-items: flex-start; flex-wrap: wrap; } .award-log-item time { width: 100%; margin-left: 1.55rem !important; } }
</style>

<div class="container py-4 py-lg-5">
    <div class="game-shell">
        <section class="game-hero rounded-3 p-4 p-lg-5 mb-4">
            <div class="d-flex justify-content-between align-items-end gap-3">
                <div>
                    <p class="text-uppercase text-warning small fw-bold mb-2">Mini game mỗi ngày</p>
                    <h1 class="display-5 fw-bold mb-2">Thử thách 5 câu</h1>
                    <p class="text-secondary mb-0">Trả lời đúng các câu PHP, voucher càng lớn.</p>
                </div>
                <i class="bi bi-lightbulb text-warning fs-1" aria-hidden="true"></i>
            </div>
        </section>

    </div>
</div>

<?php $ownedDiscounts = array_map('intval', array_column($vouchers ?? [], 'discount_percent')); ?>
<section class="container pb-4">
    <div class="voucher-banner rounded-3 p-4 p-lg-5">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <p class="text-warning text-uppercase small fw-bold mb-2">Thử thách mỗi 5 phút</p>
                <h2 class="display-6 fw-bold mb-2">Chinh phục 5 câu, mở khóa ưu đãi</h2>
                <p class="text-secondary mb-0">Mỗi câu đúng nâng cấp phần thưởng. Đạt điểm tuyệt đối để săn voucher giảm 90%.</p>
            </div>
            <div class="col-lg-5">
                <div class="d-flex flex-wrap gap-2 justify-content-lg-end">
                    <?php foreach ([10, 30, 50, 70, 90] as $percent): ?>
                        <?php $isOwned = in_array($percent, $ownedDiscounts, true); ?>
                        <span class="voucher-step <?= $isOwned ? 'is-owned' : '' ?>">+<?= $percent ?>%<?= $isOwned ? ' <i class="bi bi-check2"></i>' : '' ?></span>
                    <?php endforeach; ?>
                </div>
                <a href="<?= url('?action=my-vouchers') ?>" class="btn btn-warning fw-bold mt-3 float-lg-end"><i class="bi bi-wallet2 me-1"></i>Xem túi voucher</a>
            </div>
        </div>
    </div>
</section>

<section class="container pb-5">
    <div class="d-flex align-items-center gap-2 mb-3"><i class="bi bi-trophy text-warning"></i><h2 class="h5 mb-0">Bảng tin chúc mừng</h2></div>
    <?php if (empty($recentAwards)): ?>
        <div class="text-secondary small">Chưa có lượt nhận voucher nào.</div>
    <?php else: ?>
        <div class="award-log">
            <?php foreach ($recentAwards as $award): ?>
                <div class="award-log-item">
                    <i class="bi bi-stars text-warning"></i>
                    <span>Chúc mừng <strong><?= htmlspecialchars($award['fullname'] ?: $award['username']) ?></strong> đã sở hữu voucher <strong><?= (int)$award['discount_percent'] ?>%</strong> với mã <code><?= htmlspecialchars($award['code']) ?></code>.</span>
                    <time class="text-secondary small ms-auto" datetime="<?= htmlspecialchars($award['created_at']) ?>"><?= date('d/m H:i', strtotime($award['created_at'])) ?></time>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<div class="container pb-5">
    <section class="quiz-panel rounded-3 p-4">
        <div id="quiz-start" class="text-center py-4 <?= $hasCooldown ? 'd-none' : '' ?>">
            <i class="bi bi-stars text-warning fs-1"></i>
            <h2 class="h4 mt-3">Sẵn sàng chưa?</h2>
            <p class="text-secondary">AI sẽ tạo 5 câu hỏi PHP, gồm cơ bản và nâng cao nhẹ.</p>
            <button type="button" id="start-quiz" class="btn btn-warning fw-bold px-4">
                <i class="bi bi-play-fill me-1"></i>Bắt đầu
            </button>
        </div>

        <form id="quiz-form" class="d-none">
            <div id="question-list" class="d-grid gap-3"></div>
            <button type="submit" id="submit-quiz" class="btn btn-primary mt-4">
                <i class="bi bi-check2-circle me-1"></i>Trả lời
            </button>
        </form>

        <div id="game-message" class="mt-3" aria-live="polite">
            <?php if ($hasCooldown && $todayAward): ?>
                <div class="alert alert-success mb-0">Bạn đã nhận mã <code><?= htmlspecialchars($todayAward['code']) ?></code> giảm <?= (int)$todayAward['discount_percent'] ?>%.</div>
            <?php elseif ($hasCooldown): ?>
                <div class="alert alert-secondary mb-0">Bạn vừa chơi xong. Có thể chơi lại sau <?= max(1, (int)ceil($cooldownRemaining / 60)) ?> phút.</div>
            <?php else: ?>
                <p class="text-secondary text-center mb-0">Mỗi tài khoản được chơi một lần sau mỗi 5 phút.</p>
            <?php endif; ?>
        </div>
    </section>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const startButton = document.getElementById('start-quiz');
        const startPanel = document.getElementById('quiz-start');
        const form = document.getElementById('quiz-form');
        const questionList = document.getElementById('question-list');
        const submitButton = document.getElementById('submit-quiz');
        const message = document.getElementById('game-message');
        const escapeHtml = (value) => String(value).replace(/[&<>"']/g, (character) => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
        }[character]));

        const renderQuestion = (item) => {
            questionList.innerHTML = `
                <div class="question-card rounded-2 p-3">
                    <label class="form-label fw-bold" for="answer">Câu ${item.index + 1}: ${escapeHtml(item.question)}</label>
                    <input id="answer" name="answer" class="form-control" required autocomplete="off" placeholder="Nhập câu trả lời">
                </div>
            `;
            submitButton.disabled = false;
            submitButton.innerHTML = '<i class="bi bi-check2-circle me-1"></i>Trả lời';
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
                startButton.innerHTML = '<i class="bi bi-play-fill me-1"></i>Bắt đầu';
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
                    const voucherText = voucher ? ` Mã <code>${voucher.code}</code> giảm <strong>${voucher.discount_percent}%</strong>.` : '';
                    message.innerHTML += `<div class="alert alert-success mb-0"><strong>${data.message}</strong>${voucherText}</div>`;
                    form.classList.add('d-none');
                } else {
                    renderQuestion(data.next_question);
                }
            } catch (error) {
                message.innerHTML = `<div class="alert alert-warning">${error.message}</div>`;
                submitButton.disabled = false;
                submitButton.innerHTML = '<i class="bi bi-check2-circle me-1"></i>Chấm điểm';
            }
        });
    });
</script>
