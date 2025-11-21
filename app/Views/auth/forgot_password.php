<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - ITSO Equipment Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-green: #2d5016;
            --light-green: #4a7c28;
        }
        body {
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--light-green) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
        }
        .forgot-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            max-width: 500px;
            width: 100%;
            padding: 40px;
        }
        .btn-primary {
            background: var(--light-green);
            border-color: var(--light-green);
            padding: 12px;
            font-weight: 600;
        }
        .btn-primary:hover {
            background: var(--primary-green);
            border-color: var(--primary-green);
        }
    </style>
</head>
<body>
    <div class="forgot-container">
        <div class="text-center mb-4">
            <i class="bi bi-lock fs-1 text-success"></i>
            <h3 class="mt-3">Forgot Password?</h3>
            <p class="text-muted">Enter your email to reset your password</p>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= esc($error) ?></div>
        <?php endif; ?>

        <form action="<?= base_url('forgot-password') ?>" method="post">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-envelope me-2"></i>Send Reset Link
            </button>
            <div class="text-center mt-3">
                <a href="<?= base_url('login') ?>" class="text-decoration-none">Back to Login</a>
            </div>
        </form>
    </div>
</body>
</html>