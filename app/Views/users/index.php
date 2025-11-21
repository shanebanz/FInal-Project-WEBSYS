<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="content-header d-flex justify-content-between align-items-center">
    <h1 class="page-title">
        <i class="bi bi-people me-2"></i>User Management
    </h1>
    <a href="<?= base_url('users/create') ?>" class="btn btn-primary">
        <i class="bi bi-person-plus me-2"></i>Add User
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>User Type</th>
                        <th>Status</th>
                        <th>Created Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= esc($user['first_name'] . ' ' . $user['last_name']) ?></td>
                                <td><?= esc($user['email']) ?></td>
                                <td>
                                    <span class="badge bg-<?= $user['user_type'] === 'ITSO' ? 'primary' : ($user['user_type'] === 'Associate' ? 'info' : 'secondary') ?>">
                                        <?= esc($user['user_type']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($user['status'] === 'active'): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php elseif ($user['status'] === 'pending'): ?>
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= base_url('users/view/' . $user['id']) ?>" class="btn btn-info" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="<?= base_url('users/edit/' . $user['id']) ?>" class="btn btn-warning" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <?php if ($user['status'] === 'active'): ?>
                                            <a href="<?= base_url('users/deactivate/' . $user['id']) ?>" 
                                               class="btn btn-danger" 
                                               title="Deactivate"
                                               onclick="return confirm('Deactivate this user?')">
                                                <i class="bi bi-x-circle"></i>
                                            </a>
                                        <?php else: ?>
                                            <a href="<?= base_url('users/activate/' . $user['id']) ?>" 
                                               class="btn btn-success" 
                                               title="Activate">
                                                <i class="bi bi-check-circle"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">No users found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <?php if (isset($pager)): ?>
            <div class="mt-3">
                <?= $pager->links() ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>