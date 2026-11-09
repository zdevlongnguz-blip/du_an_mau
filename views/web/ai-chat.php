<div class="hs-container hs-chat-page">
    <div class="hs-chat">
        <div class="hs-chat__head">
            <div>
                <h1 class="hs-page-title">Trợ lý mua sắm AI</h1>
                <p class="hs-hint">Hỏi về điếu cày, thuốc lào, giá và gợi ý phù hợp.</p>
            </div>
            <form id="aiChatClearForm" action="<?= url('?action=ai-chat-clear') ?>" method="POST">
                <button type="submit" class="hs-btn hs-btn--ghost">Xóa cuộc trò chuyện</button>
            </form>
        </div>

        <?php if (!empty($chatError)): ?>
            <div class="alert alert-danger hs-form-alert mb-3"><?= htmlspecialchars($chatError) ?></div>
        <?php endif; ?>
        <?php if (!empty($chatSuccess)): ?>
            <div class="alert alert-success mb-3"><?= htmlspecialchars($chatSuccess) ?></div>
        <?php endif; ?>

        <div id="aiChatHistory" class="hs-chat__history">
            <?php if (empty($chatHistory)): ?>
                <div class="d-flex justify-content-start mb-3">
                    <div class="ai-message-bubble assistant">
                        <span class="ai-message-meta">Hustler420 AI</span>
                        <div class="ai-chat-message">Chào mừng đến với Hustler420! Tôi sẽ giúp bạn tìm sản phẩm phù hợp và tư vấn nhanh.</div>
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

        <div class="hs-suggest" id="aiSuggest">
            <button type="button" class="hs-suggest__btn" data-prompt="Điếu cày dưới 1 triệu">Điếu cày dưới 1 triệu</button>
            <button type="button" class="hs-suggest__btn" data-prompt="Thuốc lào cho người mới">Thuốc lào cho người mới</button>
            <button type="button" class="hs-suggest__btn" data-prompt="Món nào đang hot?">Món nào đang hot?</button>
        </div>

        <form id="aiChatForm" action="<?= url('?action=ai-chat-send') ?>" method="POST" class="hs-chat__input">
            <textarea id="aiMessage" name="message" class="hs-input hs-chat__textarea" rows="1" placeholder="Nhập yêu cầu của bạn…" required></textarea>
            <button type="submit" id="aiChatSendButton" class="hs-btn hs-btn--primary">Gửi</button>
        </form>
        <div class="hs-chat__status" id="aiChatStatus" role="status">&nbsp;</div>
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
        var suggestWrap = document.getElementById('aiSuggest');
        var baseUrl = '<?= rtrim((string)BASE_URL, '/') ?>';

        function esc(value) {
            return String(value ?? '').replace(/[&<>"']/g, function (character) {
                return ({
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                })[character];
            });
        }

        function isAllowedUrl(value) {
            if (typeof value !== 'string') {
                return '';
            }

            var trimmed = value.trim();
            if (!trimmed) {
                return '';
            }

            if (/^javascript:/i.test(trimmed) || /^data:/i.test(trimmed)) {
                return '';
            }

            if (trimmed.indexOf('/' + '?action=') === 0 || trimmed.indexOf('?action=') === 0 || trimmed.indexOf('/') === 0) {
                return trimmed;
            }

            if (trimmed.indexOf(baseUrl) === 0) {
                return trimmed;
            }

            if (/^https?:\/\//i.test(trimmed)) {
                try {
                    var parsed = new URL(trimmed);
                    var origin = baseUrl || window.location.origin;
                    return parsed.origin === new URL(origin).origin ? trimmed : '';
                } catch (error) {
                    return '';
                }
            }

            return '';
        }

        function setChatPending(pending) {
            messageInput.readOnly = pending;
            sendButton.disabled = pending;
            clearForm.querySelector('button').disabled = pending;
            sendButton.textContent = pending ? 'Đang gửi...' : 'Gửi';
        }

        function removeTypingIndicator() {
            var typing = document.getElementById('aiTyping');
            if (typing) {
                typing.remove();
            }
        }

        function updateSuggestVisibility() {
            if (!suggestWrap) {
                return;
            }
            suggestWrap.hidden = chatHistory && chatHistory.querySelector('.ai-message-bubble') ? true : false;
        }

        function autosize(el) {
            el.style.height = 'auto';
            const border = el.offsetHeight - el.clientHeight;
            const h = Math.min(Math.max(el.scrollHeight + border, 44), 140);
            el.style.height = h + 'px';
            el.style.overflowY = (el.scrollHeight + border) > 140 ? 'auto' : 'hidden';
        }

        autosize(messageInput);
        messageInput.addEventListener('input', function () {
            autosize(messageInput);
        });
        messageInput.addEventListener('keydown', function (event) {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();
                chatForm.requestSubmit();
            }
        });

        document.querySelectorAll('.hs-suggest__btn').forEach(function (button) {
            button.addEventListener('click', function () {
                if (!messageInput) {
                    return;
                }
                messageInput.value = button.dataset.prompt || '';
                autosize(messageInput);
                chatForm.requestSubmit();
                if (!suggestWrap) {
                    return;
                }
                suggestWrap.hidden = true;
            });
        });

        updateSuggestVisibility();

        chatForm.addEventListener('submit', function (event) {
            event.preventDefault();
            var formData = new FormData(chatForm);
            var messageValue = (formData.get('message') || '').toString().trim();

            if (!messageValue) {
                status.textContent = 'Vui lòng nhập nội dung chat.';
                return;
            }

            status.textContent = 'Đang gửi...';
            removeTypingIndicator();
            chatHistory.insertAdjacentHTML('beforeend', '<div class="d-flex mb-3 justify-content-start" id="aiTyping"><div class="ai-message-bubble assistant"><span class="hs-typing"><i></i><i></i><i></i></span></div></div>');
            chatHistory.scrollTop = chatHistory.scrollHeight;
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
                removeTypingIndicator();
                if (data.status === 'success') {
                    chatHistory.innerHTML = data.historyHtml || '';
                    messageInput.value = '';
                    autosize(messageInput);
                    suggestWrap.hidden = true;
                    status.textContent = '';

                    if (Array.isArray(data.products) && data.products.length > 0) {
                        var productHtml = '<div class="hs-chat-cards">';
                        data.products.forEach(function (product) {
                            var productUrl = isAllowedUrl(product.url || '');
                            var imageUrl = esc(product.image || '');
                            var nameText = esc(product.name || '');
                            var categoryText = esc(product.category || '');
                            var priceText = Number(product.price_vnd || 0).toLocaleString('vi-VN') + 'đ';

                            productHtml += '<a class="hs-chat-card" href="' + esc(productUrl || '#') + '"><img src="' + imageUrl + '" alt="" /><span class="hs-chat-card__info"><span class="hs-chat-card__cat">' + categoryText + '</span><span class="hs-chat-card__name">' + nameText + '</span><span class="hs-chat-card__price">' + esc(priceText) + '</span></span><i class="bi bi-chevron-right"></i></a>';
                        });
                        productHtml += '</div>';
                        chatHistory.insertAdjacentHTML('beforeend', productHtml);
                    }

                    if (data.redirect && data.redirect.url) {
                        var redirectUrl = isAllowedUrl(data.redirect.url);
                        if (redirectUrl) {
                            var redirectLabel = esc(data.redirect.label || 'Đi tới');
                            chatHistory.insertAdjacentHTML('beforeend', '<div class="hs-chat__cta"><a class="hs-btn hs-btn--primary" href="' + esc(redirectUrl) + '">' + redirectLabel + ' →</a></div>');
                        }
                    }
                } else {
                    status.textContent = data.message || 'Lỗi khi gửi chat.';
                }
                setChatPending(false);
            }).catch(function () {
                removeTypingIndicator();
                status.textContent = 'Lỗi kết nối. Vui lòng thử lại.';
                setChatPending(false);
            });
        });

        clearForm.addEventListener('submit', function (event) {
            event.preventDefault();
            status.textContent = 'Đang xóa...';
            setChatPending(true);
            fetch(clearForm.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).then(function (response) {
                return response.json();
            }).then(function (data) {
                if (data.status === 'success') {
                    chatHistory.innerHTML = '<div class="d-flex justify-content-start mb-3"><div class="ai-message-bubble assistant"><span class="ai-message-meta">Hustler420 AI</span><div class="ai-chat-message">Chào mừng đến với Hustler420! Tôi sẽ giúp bạn tìm sản phẩm phù hợp và tư vấn nhanh.</div></div></div>';
                    status.textContent = '';
                    updateSuggestVisibility();
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
