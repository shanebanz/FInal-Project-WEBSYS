<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="content-header d-flex justify-content-between align-items-center">
    <h1 class="page-title">
        <i class="bi bi-box-seam me-2"></i>Equipment Management
    </h1>
    <a href="<?= base_url('equipment/create') ?>" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Add Equipment
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Equipment ID</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Total Qty</th>
                        <th>Available</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($equipment)): ?>
                        <?php foreach ($equipment as $item): ?>
                            <tr>
                                <td>
                                    <?php if ($item['image']): ?>
                                        <img src="<?= base_url('uploads/equipment/thumbnails/' . $item['image']) ?>" 
                                             alt="<?= esc($item['name']) ?>" 
                                             style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                                    <?php else: ?>
                                        <div style="width: 50px; height: 50px; background: #e9ecef; border-radius: 5px; display: flex; align-items: center; justify-content: center;">
                                            <i class="bi bi-image text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td><?= esc($item['equipment_id']) ?></td>
                                <td><?= esc($item['name']) ?></td>
                                <td><?= esc($item['category_name']) ?></td>
                                <td><?= esc($item['total_quantity']) ?></td>
                                <td><?= esc($item['available_quantity']) ?></td>
                                <td>
                                    <?php if ($item['status'] === 'active'): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php elseif ($item['status'] === 'maintenance'): ?>
                                        <span class="badge bg-warning">Maintenance</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= base_url('equipment/view/' . $item['id']) ?>" class="btn btn-info" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="<?= base_url('equipment/edit/' . $item['id']) ?>" class="btn btn-warning" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <?php if ($item['status'] === 'active'): ?>
                                            <a href="<?= base_url('equipment/delete/' . $item['id']) ?>" 
                                               class="btn btn-danger" 
                                               title="Deactivate"
                                               onclick="return confirm('Are you sure you want to deactivate this equipment?')">
                                                <i class="bi bi-x-circle"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted">No equipment found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>