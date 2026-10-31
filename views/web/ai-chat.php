<style>
    .ai-chat-shell {
        max-width: 600px;
        margin: 0 auto;
        background: #121212;
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 1rem;
        overflow: hidden;
    }
    .ai-chat-header {
        padding: 1rem 1.2rem;
        background: #181818;
        border-bottom: 1px solid rgba(255,255,255,0.08);
    }
    .ai-chat-header h1 {
        font-size: 1.35rem;
        margin-bottom: 0.25rem;
        color: #fff;
    }
    .ai-chat-header p {
        color: rgba(255,255,255,0.75);
        margin: 0;
        font-size: 0.92rem;
    }
    .ai-chat-body {
        padding: 1rem 1.2rem 0;
    }
    .ai-chat-history {
        max-height: 360px;
        overflow-y: auto;
        padding-right: 0.35rem;
        margin-bottom: 1rem;
    }
    .ai-chat-history::-webkit-scrollbar {
        width: 6px;
    }
    .ai-chat-history::-webkit-scrollbar-thumb {
        background: rgba(255,255,255,0.15);
        border-radius: 99px;
    }
    .ai-message-bubble {
        max-width: 78%;
        padding: 0.95rem 1rem;
        border-radius: 1.2rem;
        line-height: 1.5;
        font-size: 0.95rem;
        word-break: break-word;
    }
    .ai-message-bubble.assistant {
        background: rgba(255,255,255,0.08);
        border: 1px solid rgba(255,255,255,0.1);
        color: #f7f8fa;
    }
    .ai-message-bubble.user {
        background: rgba(255,122,26,0.18);
        border: 1px solid rgba(255,122,26,0.3);
        color: #fff;
    }
    .ai-chat-message {
        line-height: 1.6;
        color: inherit;
    }
    .ai-chat-message img {
        max-width: 100%;
        height: auto;
        display: block;
        margin: 0.75rem 0 0;
        border-radius: 0.7rem;
    }
    .ai-chat-message .product-card {
        background: rgba(255,255,255,0.05);
        border: 1px solid rgba(255,255,255,0.12);
        padding: 0.85rem;
        border-radius: 1rem;
        margin-top: 0.85rem;
    }
    .ai-chat-message .product-card h3 {
        margin-top: 0;
        margin-bottom: 0.5rem;
        font-size: 1rem;
        color: #fff;
    }
    .ai-chat-message .product-card p {
        margin: 0.3rem 0;
        color: rgba(255,255,255,0.82);
    }
    .ai-chat-message .product-card a {
        color: #8fd8ff;
        text-decoration: none;
    }
    .ai-chat-message .product-card a:hover {
        text-decoration: underline;
    }
    .ai-message-meta {
        display: block;
        margin-bottom: 0.35rem;
        font-size: 0.74rem;
        color: rgba(255,255,255,0.72);
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }
    .ai-chat-input-wrapper {
        padding: 0.85rem 1rem 1rem;
        background: #181818;
        border-top: 1px solid rgba(255,255,255,0.08);
    }
    .ai-chat-input {
        width: 100%;
        background: rgba(255,255,255,0.05);
        border: 1px solid rgba(255,255,255,0.12);
        color: #edf2f7;
        min-height: 72px;
        max-height: 140px;
        resize: vertical;
        padding: 0.85rem 0.95rem;
        border-radius: 0.9rem;
    }
    .ai-chat-input::placeholder {
        color: rgba(255,255,255,0.5);
    }
    .ai-chat-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        align-items: center;
        justify-content: space-between;
        margin-top: 0.75rem;
    }
    .ai-status {
        color: rgba(255,255,255,0.72);
        font-size: 0.9rem;
        min-height: 1.2rem;
    }
    .ai-clear-form {
        padding: 1rem 1.2rem 1.3rem;
        background: #181818;
        border-top: 1px solid rgba(255,255,255,0.08);
        text-align: right;
    }
</style>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8">
            <div class="ai-chat-shell shadow-sm">
                <div class="ai-chat-header">
                    <h1>Hustler420 Chat</h1>
                    <p>Chat đơn giản. Gõ yêu cầu, gửi, AI trả lời ngay phía dưới.</p>
                </div>

                <div class="ai-chat-body">
                    <?php if (!empty($chatError)): ?>
                        <div class="alert alert-danger mb-3"><?= htmlspecialchars($chatError) ?></div>
                    <?php endif; ?>
                    <?php if (!empty($chatSuccess)): ?>
                        <div class="alert alert-success mb-3"><?= htmlspecialchars($chatSuccess) ?></div>
                    <?php endif; ?>

                    <div id="aiChatHistory" class="ai-chat-history">
                        <?php if (empty($chatHistory)): ?>
                            <div class="d-flex justify-content-start mb-3">
                                <div class="ai-message-bubble assistant">
                                    <span class="ai-message-meta">Hustler420 AI</span>
                                    <div>Chào mừng đến với Hustler420! Tôi sẽ giúp bạn tìm sản phẩm phù hợp và tư vấn nhanh.</div>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php foreach ($chatHistory as $item): ?>
                                <?php $isAssistant = ($item['role'] ?? '') === 'assistant'; ?>
                                <div class="d-flex mb-3 <?= $isAssistant ? 'justify-content-start' : 'justify-content-end' ?>">
                                    <div class="ai-message-bubble <?= $isAssistant ? 'assistant' : 'user' ?>">
                                        <span class="ai-message-meta"><?= $isAssistant ? 'Hustler420 AI' : 'Bạn' ?></span>
                                        <div class="ai-chat-message"><?= $isAssistant ? ($item['text'] ?? '') : nl2br(htmlspecialchars($item['text'] ?? '')) ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <form id="aiChatForm" action="<?= url('?action=ai-chat-send') ?>" method="POST" class="ai-chat-input-wrapper">
                        <textarea id="aiMessage" name="message" class="form-control ai-chat-input" placeholder="Nhập yêu cầu của bạn..." required></textarea>
                        <div class="ai-chat-actions">
                            <button type="submit" id="aiChatSendButton" class="btn btn-primary">Gửi</button>
                            <div class="ai-status" id="aiChatStatus">&nbsp;</div>
                        </div>
                    </form>

                    <form id="aiChatClearForm" action="<?= url('?action=ai-chat-clear') ?>" method="POST" class="ai-clear-form">
                        <button type="submit" class="btn btn-outline-light btn-sm">Xóa lịch sử chat</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var chatForm = document.getElementById('aiChatForm');
        var chatHistory = document.getElementById('aiChatHistory');
        var status = document.getElementById('aiChatStatus');
        var messageInput = document.getElementById('aiMessage');
        var sendButton = document.getElementById('aiChatSendButton');
        var clearForm = document.getElementById('aiChatClearForm');

        function setChatPending(pending) {
            messageInput.disabled = pending;
            sendButton.disabled = pending;
            clearForm.querySelector('button').disabled = pending;
            sendButton.textContent = pending ? 'Đang gửi...' : 'Gửi';
        }

        chatForm.addEventListener('submit', function (event) {
            event.preventDefault();
            status.textContent = 'Đang gửi...';
            var formData = new FormData(chatForm);
            setChatPending(true);
            fetch(chatForm.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            }).then(function (response) {
                return response.json();
            }).then(function (data) {
                if (data.status === 'success') {
                    chatHistory.innerHTML = data.historyHtml;
                    messageInput.value = '';
                    chatHistory.scrollTop = chatHistory.scrollHeight;
                    status.textContent = '';
                } else {
                    status.textContent = data.message || 'Lỗi khi gửi chat.';
                }
                setChatPending(false);
            }).catch(function () {
                status.textContent = 'Lỗi kết nối. Vui lòng thử lại.';
                setChatPending(false);
            });
        });

        clearForm.addEventListener('submit', function (event) {
            event.preventDefault();
            status.textContent = 'Đang xóa...';
            fetch(clearForm.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).then(function (response) {
                return response.json();
            }).then(function (data) {
                if (data.status === 'success') {
                    chatHistory.innerHTML = '<div class="d-flex justify-content-start mb-3"><div class="ai-message-bubble assistant"><span class="ai-message-meta">Hustler420 AI</span><div>Chào mừng đến với Hustler420! Tôi sẽ giúp bạn tìm sản phẩm phù hợp và tư vấn nhanh.</div></div></div>';
                    status.textContent = '';
                } else {
                    status.textContent = data.message || 'Lỗi khi xóa lịch sử.';
                }
                setChatPending(false);
            }).catch(function () {
                status.textContent = 'Lỗi kết nối. Vui lòng thử lại.';
                setChatPending(false);
            });
        });
    });
</script>
