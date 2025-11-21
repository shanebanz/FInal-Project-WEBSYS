<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="content-header d-flex justify-content-between align-items-center">
    <h1 class="page-title">
        <i class="bi bi-info-circle me-2"></i>Equipment Details
    </h1>
    <div>
        <a href="<?= base_url('equipment/edit/' . $equipment['id']) ?>" class="btn btn-warning">
            <i class="bi bi-pencil me-2"></i>Edit
        </a>
        <a href="<?= base_url('equipment') ?>" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <?php if ($equipment['image']): ?>
                    <img src="<?= base_url('uploads/equipment/' . $equipment['image']) ?>" 
                         alt="<?= esc($equipment['name']) ?>" 
                         class="img-fluid rounded">
                <?php else: ?>
                    <div style="width: 100%; height: 300px; background: #e9ecef; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-image text-muted" style="font-size: 5rem;"></i>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                Equipment Information
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="30%">Equipment ID:</th>
                        <td><?= esc($equipment['equipment_id']) ?></td>
                    </tr>
                    <tr>
                        <th>Name:</th>
                        <td><?= esc($equipment['name']) ?></td>
                    </tr>
                    <tr>
                        <th>Category:</th>
                        <td><?= esc($equipment['category_name']) ?></td>
                    </tr>
                    <tr>
                        <th>Description:</th>
                        <td><?= esc($equipment['description']) ?: '-' ?></td>
                    </tr>
                    <tr>
                        <th>Total Quantity:</th>
                        <td><?= esc($equipment['total_quantity']) ?></td>
                    </tr>
                    <tr>
                        <th>Available Quantity:</th>
                        <td>
                            <span class="badge bg-<?= $equipment['available_quantity'] > 0 ? 'success' : 'danger' ?>">
                                <?= esc($equipment['available_quantity']) ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Status:</th>
                        <td>
                            <?php if ($equipment['status'] === 'active'): ?>
                                <span class="badge bg-success">Active</span>
                            <?php elseif ($equipment['status'] === 'maintenance'): ?>
                                <span class="badge bg-warning">Maintenance</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Inactive</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Created:</th>
                        <td><?= date('F d, Y h:i A', strtotime($equipment['created_at'])) ?></td>
                    </tr>
                    <tr>
                        <th>Last Updated:</th>
                        <td><?= date('F d, Y h:i A', strtotime($equipment['updated_at'])) ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>