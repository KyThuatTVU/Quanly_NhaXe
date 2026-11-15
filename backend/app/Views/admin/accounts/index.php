<div class="admin-page-head">
    <div>
        <h1>Tài khoản</h1>
        <p class="muted">Tài khoản quản trị và nhân viên vận hành</p>
    </div>
    <a class="btn btn-primary" href="<?= url('/admin/accounts/create') ?>">Thêm tài khoản</a>
</div>

<form class="admin-filters" method="get" action="<?= url('/admin/accounts') ?>">
    <div class="form-field grow">
        <label for="search">Tìm kiếm</label>
        <input id="search" name="search" value="<?= e($search) ?>" placeholder="Tên đăng nhập, email, số điện thoại...">
    </div>
    <button class="btn btn-primary" type="submit">Tìm</button>
    <a class="btn btn-light" href="<?= url('/admin/accounts') ?>">Xóa lọc</a>
</form>

<div class="admin-card">
    <div class="table-wrap">
        <table class="admin-table">
            <thead>
                <tr><th>Tài khoản</th><th>Liên hệ</th><th>Vai trò</th><th>Trạng thái</th><th>Đăng nhập cuối</th><th></th></tr>
            </thead>
            <tbody>
                <?php foreach ($users['items'] as $user): ?>
                    <tr>
                        <td>
                            <strong><?= e($user['username']) ?></strong>
                            <div class="muted small">#<?= (int) $user['user_id'] ?></div>
                        </td>
                        <td>
                            <?= e($user['email'] ?? '—') ?>
                            <div class="muted small"><?= e(\App\Support\Format::phone($user['phone'] ?? null)) ?></div>
                        </td>
                        <td><?= e($user['role']) ?></td>
                        <td>
                            <?php if ((int) $user['is_active'] === 1): ?>
                                <span class="badge success">Đang hoạt động</span>
                            <?php else: ?>
                                <span class="badge danger">Đã khóa</span>
                            <?php endif; ?>
                        </td>
                        <td><?= e(\App\Support\Format::datetime($user['last_login_at'] ?? null)) ?></td>
                        <td class="table-actions">
                            <a class="btn btn-sm btn-light" href="<?= url('/admin/accounts/' . (int) $user['user_id'] . '/edit') ?>">Sửa</a>
                            <form method="post" action="<?= url('/admin/accounts/' . (int) $user['user_id']) ?>" data-confirm="Xóa tài khoản này?">
                                <?= csrf_field() ?>
                                <input type="hidden" name="_method" value="DELETE">
                                <button class="btn btn-sm btn-danger" type="submit">Xóa</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($users['items'] === []): ?>
                    <tr><td colspan="6" class="muted">Không có tài khoản phù hợp.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="admin-card-foot">
        <span class="muted small">Tổng <?= (int) $users['total'] ?> tài khoản</span>
        <?php $view->partial('partials/pagination', ['pagination' => $pagination]); ?>
    </div>
</div>
