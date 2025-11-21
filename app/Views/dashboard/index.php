<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="content-header">
    <h1 class="page-title">
        <i class="bi bi-speedometer2 me-2"></i>Dashboard
    </h1>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-white" style="background: linear-gradient(135deg, #4a7c28, #2d5016);">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle mb-2">Total Equipment</h6>
                        <h2 class="card-title mb-0"><?= esc($total_equipment) ?></h2>
                    </div>
                    <div class="fs-1">
                        <i class="bi bi-box-seam"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card text-white" style="background: linear-gradient(135deg, #0d6efd, #0a58ca);">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle mb-2">Active Borrowings</h6>
                        <h2 class="card-title mb-0"><?= esc($active_borrowings) ?></h2>
                    </div>
                    <div class="fs-1">
                        <i class="bi bi-arrow-left-right"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card text-white" style="background: linear-gradient(135deg, #ffc107, #ff9800);">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle mb-2">Pending Reservations</h6>
                        <h2 class="card-title mb-0"><?= esc($pending_reservations) ?></h2>
                    </div>
                    <div class="fs-1">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card text-white" style="background: linear-gradient(135deg, #6f42c1, #5a32a3);">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle mb-2">Total Users</h6>
                        <h2 class="card-title mb-0"><?= esc($total_users) ?></h2>
                    </div>
                    <div class="fs-1">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="row">
    <!-- Recent Borrowings -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-clock-history me-2"></i>Recent Borrowings
            </div>
            <div class="card-body">
                <?php if (!empty($recent_borrowings)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Borrower</th>
                                    <th>Equipment</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_borrowings as $borrowing): ?>
                                    <tr>
                                        <td><?= esc($borrowing['first_name'] . ' ' . $borrowing['last_name']) ?></td>
                                        <td><?= esc($borrowing['equipment_name']) ?></td>
                                        <td><?= date('M d, Y', strtotime($borrowing['borrow_date'])) ?></td>
                                        <td>
                                            <?php if ($borrowing['status'] === 'borrowed'): ?>
                                                <span class="badge bg-primary">Borrowed</span>
                                            <?php elseif ($borrowing['status'] === 'returned'): ?>
                                                <span class="badge bg-success">Returned</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Overdue</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center mt-3">
                        <a href="<?= base_url('borrowings') ?>" class="btn btn-sm btn-outline-primary">
                            View All <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center">No recent borrowings</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Recent Reservations -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-calendar-event me-2"></i>Recent Reservations
            </div>
            <div class="card-body">
                <?php if (!empty($recent_reservations)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Equipment</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_reservations as $reservation): ?>
                                    <tr>
                                        <td><?= esc($reservation['first_name'] . ' ' . $reservation['last_name']) ?></td>
                                        <td><?= esc($reservation['equipment_name']) ?></td>
                                        <td><?= date('M d, Y', strtotime($reservation['reservation_date'])) ?></td>
                                        <td>
                                            <?php if ($reservation['status'] === 'pending'): ?>
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            <?php elseif ($reservation['status'] === 'approved'): ?>
                                                <span class="badge bg-success">Approved</span>
                                            <?php elseif ($reservation['status'] === 'cancelled'): ?>
                                                <span class="badge bg-danger">Cancelled</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Completed</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center mt-3">
                        <a href="<?= base_url('reservations') ?>" class="btn btn-sm btn-outline-primary">
                            View All <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center">No recent reservations</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>