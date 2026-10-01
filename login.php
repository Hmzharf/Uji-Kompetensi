    <?php
    require_once 'helpers/functions.php';
    require_once 'classes/Petugas.php';

    if (isset($_SESSION['user'])) {
        header('Location: index.php');
        exit;
    }

    $error = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username']);
        $password = $_POST['password'];

        $petugasModel = new Petugas();
        $user = $petugasModel->login($username, $password);

        if ($user) {
            $_SESSION['user'] = [
                'id_petugas'   => $user['id_petugas'],
                'username'     => $user['username'],
                'nama_lengkap' => $user['nama_petugas'],
                'level'        => $user['level']
            ];
            header('Location: index.php');
            exit;
        } else {
            $error = 'Username atau password salah!';
        }
    }
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login - Aplikasi SPP</title>
        <link rel="stylesheet" href="assets/css/bootstrap.min.css">
        <style>
            body { background: #f0f2f5; display: flex; align-items: center; justify-content: center; height: 100vh; }
            .login-card { width: 100%; max-width: 400px; padding: 25px; border-radius: 12px; background: #fff; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        </style>
    </head>
    <body>
    <div class="login-card">
        <h4 class="text-center mb-1 fw-bold text-primary">Aplikasi SPP</h4>
        <p class="text-center text-muted mb-4 small">Silakan login untuk melanjutkan</p>

        <?php if ($error): ?>
            <div class="alert alert-danger py-2 small"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label small fw-bold">Username</label>
                <input type="text" name="username" class="form-control" placeholder="admin / siswa" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label small fw-bold">Password</label>
                <input type="password" name="password" class="form-control" placeholder="admin123 / siswa123" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">Masuk</button>
        </form>
    </div>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    </body>
    </html>