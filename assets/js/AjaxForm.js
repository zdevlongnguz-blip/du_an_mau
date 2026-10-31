class AjaxForm {
    constructor(formSelector, actionUrl, onSuccessCallback) {
        if (typeof formSelector === 'string') {
            this.form = document.querySelector(formSelector);
        } else {
            this.form = formSelector;
        }

        this.url = actionUrl || (this.form ? this.form.getAttribute('action') : '');
        this.errors = {};
        this.isSubmitting = false;
        this.onSuccessCallback = typeof onSuccessCallback === 'function' ? onSuccessCallback : null;

        if (this.form) {
            this.registerEvent();
        }
    }

    registerEvent() {
        this.form.addEventListener('submit', (e) => {
            e.preventDefault();
            this.clearErrors();
            this.submit();
        });
    }

    clearErrors() {
        this.errors = {};
        this.form.querySelectorAll('.error-msg').forEach(el => el.innerText = '');
    }

    submit() {
        if (this.isSubmitting) {
            return;
        }

        this.isSubmitting = true;
        this.disableSubmitButton(true);

        const formData = new FormData(this.form);

        fetch(this.url, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                this.onSuccess(data);
            } else if (data.status === 'error') {
                this.errors = data.errors || {};
                this.renderErrors();
            }
        })
        .catch(err => {
            console.error('Lỗi kết nối Server:', err);
        })
        .finally(() => {
            this.isSubmitting = false;
            this.disableSubmitButton(false);
        });
    }

    disableSubmitButton(isDisabled) {
        const submitButton = this.form.querySelector('button[type="submit"]');
        if (!submitButton) {
            return;
        }

        submitButton.disabled = isDisabled;

        if (isDisabled) {
            submitButton.dataset.originalText = submitButton.innerText;
            submitButton.innerText = 'Đang xử lý...';
        } else if (submitButton.dataset.originalText) {
            submitButton.innerText = submitButton.dataset.originalText;
        }
    }

    renderErrors() {
        for (let key in this.errors) {
            const errorElement = this.form.querySelector(`.error-${key}`);
            if (errorElement) {
                errorElement.innerText = `🛡️ ${this.errors[key]}`;
            } else {
                console.warn(`Cảnh báo: Không tìm thấy thẻ hiển thị lỗi cho trường [.error-${key}]`);
            }
        }
    }

    onSuccess(response) {
        if (this.onSuccessCallback) {
            return this.onSuccessCallback(response);
        }
        alert(response.message || 'Thao tác thành công!');
    }
}