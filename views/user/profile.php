<?php
// dd($user);

// Xử lý ảnh đại diện mặc định nếu avatar bị NULL
$avatarUrl = (BASE_ASSETS_UPLOADS . $user['avatar']) ?? 'https://cdn-icons-png.flaticon.com/512/149/149071.png';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            
            <div class="card shadow-sm border-0 rounded-3">
                
                <div class="card-header bg-primary bg-gradient text-white text-center py-4 rounded-top-3">
                    <img src="<?php echo $avatarUrl; ?>" 
                         alt="Avatar" 
                         class="rounded-circle border border-4 border-white mb-3" 
                         style="width: 120px; height: 120px; object-fit: cover;">


                    <h3 class="mb-1 fw-bold"><?php echo htmlspecialchars($user['fullname']); ?></h3>
                    <span class="badge bg-light text-primary text-uppercase px-3 py-2 rounded-pill fw-semibold">
                        <i class="bi bi-shield-lock-fill me-1"></i> <?php echo $user['role']; ?>
                    </span>
                </div>

                <a href="<?= url('?action=profile-avatar-change') ?>" class="btn btn-success w-100 rounded-0 rounded-bottom-3 text-center py-2">
                    <i class="bi bi-camera me-1"></i> Thay đổi ảnh đại diện
                </a>

                <div class="card-body p-4">
                    <h5 class="text-secondary mb-4 border-bottom pb-2">Thông tin tài khoản</h5>
                    
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted fw-medium">User ID / Username:</div>
                        <div class="col-sm-8 fw-semibold">
                            #<?php echo $user['id']; ?> — @<?php echo htmlspecialchars($user['username']); ?>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted fw-medium">Email:</div>
                        <div class="col-sm-8">
                            <a href="mailto:<?php echo $user['email']; ?>" class="text-decoration-none">
                                <?php echo htmlspecialchars($user['email']); ?>
                            </a>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted fw-medium">Ngày tham gia:</div>
                        <div class="col-sm-8 text-secondary">
                            <i class="bi bi-calendar3 me-1"></i> 
                            <?php echo date('d/m/Y H:i:s', strtotime($user['created_at'])); ?>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-sm-4 text-muted fw-medium">Trạng thái:</div>
                        <div class="col-sm-8">
                            <?php if ($user['is_active'] === 1): ?>
                                <span class="text-success fw-bold">
                                    <i class="bi bi-check-circle-fill me-1"></i> Đang hoạt động
                                </span>
                            <?php else: ?>
                                <span class="text-danger fw-bold">
                                    <i class="bi bi-x-circle-fill me-1"></i> Bị khóa
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-sm-flex justify-content-sm-end border-top pt-3">
                        <a href="<?= url('?action=profile-in4-change') ?>" class="btn btn-outline-secondary btn-sm px-4 me-sm-2">
                            <i class="bi bi-pencil-square me-1"></i> Đổi mật khẩu
                        </a>
                        <a href="<?= url('?action=logout') ?>" class="btn btn-danger btn-sm px-4">
                            <i class="bi bi-box-arrow-right me-1"></i> Đăng xuất
                        </a>
                    </div>

                </div>
            </div> </div>
    </div>
</div>