# Bộ hướng dẫn cho Copilot: đổi giao diện người dùng du_an_mau

Chép **cả thư mục này vào gốc dự án** (cạnh `index.php`). Gồm:

| File | Dùng để |
|---|---|
| `.github/copilot-instructions.md` | Luật chung, Copilot tự đọc mỗi lượt chat |
| `.github/instructions/user-views.instructions.md` | Luật riêng khi sửa `views/main.php`, `views/web|auth|user` |
| `.github/instructions/protected-zone.instructions.md` | Khóa vùng backend và admin |
| `.github/prompts/redesign-page.prompt.md` | Prompt dùng lại cho từng trang (`/redesign-page`) |
| `docs/ui-redesign/CONTRACT.md` | Hợp đồng dữ liệu, form, id, route của từng trang |
| `docs/ui-redesign/WORKFLOW.md` | Quy trình từng bước, checklist kiểm tra, cách lùi |
| `docs/ui-redesign/PROJECT-REVIEW.md` | Những chỗ sai/chưa đồng bộ và rủi ro tìm thấy |
| `tools/check-ui-scope.sh` | Script báo lỗi nếu có file ngoài vùng được phép bị đổi |

Đọc `PROJECT-REVIEW.md` mục C trước khi làm gì khác.
