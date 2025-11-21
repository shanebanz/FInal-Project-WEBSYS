<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="content-header">
    <h1 class="page-title">
        <i class="bi bi-plus-circle me-2"></i>Add New Equipment
    </h1>
</div>

<div class="card">
    <div class="card-body">
        <form action="<?= base_url('equipment/create') ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="equipment_id" class="form-label">Equipment ID *</label>
                        <input type="text" class="form-control <?= isset($validation) && $validation->hasError('equipment_id') ? 'is-invalid' : '' ?>" 
                               id="equipment_id" name="equipment_id" value="<?= old('equipment_id') ?>" required>
                        <?php if (isset($validation) && $validation->hasError('equipment_id')): ?>
                            <div class="invalid-feedback"><?= $validation->getError('equipment_id') ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="category_id" class="form-label">Category *</label>
                        <select class="form-select <?= isset($validation) && $validation->hasError('category_id') ? 'is-invalid' : '' ?>" 
                                id="category_id" name="category_id" required>
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['id'] ?>" <?= old('category_id') == $category['id'] ? 'selected' : '' ?>>
                                    <?= esc($category['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($validation) && $validation->hasError('category_id')): ?>
                            <div class="invalid-feedback"><?= $validation->getError('category_id') ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="name" class="form-label">Equipment Name *</label>
                <input type="text" class="form-control <?= isset($validation) && $validation->hasError('name') ? 'is-invalid' : '' ?>" 
                       id="name" name="name" value="<?= old('name') ?>" required>
                <?php if (isset($validation) && $validation->hasError('name')): ?>
                    <div class="invalid-feedback"><?= $validation->getError('name') ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3"><?= old('description') ?></textarea>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="total_quantity" class="form-label">Total Quantity *</label>
                        <input type="number" class="form-control <?= isset($validation) && $validation->hasError('total_quantity') ? 'is-invalid' : '' ?>" 
                               id="total_quantity" name="total_quantity" value="<?= old('total_quantity') ?>" min="0" required>
                        <?php if (isset($validation) && $validation->hasError('total_quantity')): ?>
                            <div class="invalid-feedback"><?= $validation->getError('total_quantity') ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="image" class="form-label">Equipment Image</label>
                        <input type="file" class="form-control <?= isset($validation) && $validation->hasError('image') ? 'is-invalid' : '' ?>" 
                               id="image" name="image" accept="image/*">
                        <small class="form-text text-muted">Max size: 2MB. Supported formats: JPG, PNG, GIF</small>
                        <?php if (isset($validation) && $validation->hasError('image')): ?>
                            <div class="invalid-feedback"><?= $validation->getError('image') ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="<?= base_url('equipment') ?>" class="btn btn-secondary">
                    <i class="bi bi-x-circle me-2"></i>Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-2"></i>Save Equipment
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>