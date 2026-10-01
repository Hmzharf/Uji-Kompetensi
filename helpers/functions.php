    <?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    /**
     * Helper Functions
     */

    // Function format mata uang rupiah
    function rupiah($angka) {
        return 'Rp ' . number_format((float)$angka, 0, ',', '.');
    }

    // Function format tanggal Indonesia
    function tgl_indo($tanggal) {
        if (!$tanggal) return '-';
        return date('d/m/Y H:i', strtotime($tanggal));
    }

    // Function flash message session
    function setFlash($pesan, $tipe = 'success') {
        $_SESSION['flash'] = ['pesan' => $pesan, 'tipe' => $tipe];
    }

    function getFlash() {
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return "<div class='alert alert-{$flash['tipe']} alert-dismissible fade show' role='alert'>
                        {$flash['pesan']}
                        <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                    </div>";
        }
        return '';
    }