<!DOCTYPE html>
<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "wiwik_uas_pemrograman";

// buat koneksi
$conn = mysqli_connect($servername, $username, $password, $database);

// cek koneksi
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- CSS only -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
    <title>UAS Dasar Pemrogaman Web | Wiwik Listiyarini 41119051</title>
</head>
<body class="container">
    <div class="card mt-5">
        <div class="card-header bg-primary text-white font-weight-bolder">
            Program Menghitung Tarif Pemakaian Listrik
        </div>
        <div class="card-body">
        <div class="row">
            <div class="col">
                <div class="row">
                    <div class="col">
                        <form action="index.php" method="post">
                            <div class="form-group row">
                                <label for="kategori" class="col-sm-6 col-form-label">Kategori Pelanggan</label>
                                <div class="col-sm-6">
                                    <select class="form-control" name="kategori" id="kategori">
                                        <option value="">Pilih Kategori</option>

                                        <?php
                                        $kategori = mysqli_query($conn, "select * from kategori");
                                        while($kat = mysqli_fetch_array($kategori, MYSQLI_ASSOC)){ 
                                        ?>

                                        <option value="<?= $kat['id']; ?>"><?= $kat['nama']; ?></option>

                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="pemakaian" class="col-sm-6 col-form-label">Jumlah Pemakaian Listrik</label>
                                <div class="col-sm-6">
                                    <input type="number" class="form-control" id="pemakaian" name="pemakaian" aria-describedby="help" max="300" required>
                                    <small id="help" class="form-text text-muted">Maksimal 300</small>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="offset-sm-6 col-sm-6">
                                    <button type="submit" class="btn btn-primary btn-block">Hitung</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="card">
                            <div class="card-header bg-info text-white font-weight-bolder">
                                Tabel Tarif
                            </div>
                            <div class="card-body">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Kategori</th>
                                            <th>Tarif</th>
                                            <th>Abodemen</th>
                                            <th>Pajak</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $sql_tabel = "select kategori.nama kategori, tarif.tarif, tarif.abodemen, tarif.pajak
                                                        from tarif
                                                        join kategori on kategori.id=tarif.kategori_id
                                                       ";
                                        $query_tabel = mysqli_query($conn, $sql_tabel);
                                        while($tabel = mysqli_fetch_array($query_tabel, MYSQLI_ASSOC)){
                                        ?>
                                        <tr>
                                            <td scope="row"><?= $tabel['kategori']; ?></td>
                                            <td><?= number_format($tabel['tarif'], 0, ',', '.'); ?></td>
                                            <td><?= number_format($tabel['abodemen'], 0, ',', '.'); ?></td>
                                            <td><?= $tabel['pajak']; ?>%</td>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <?php
                if(isset($_POST['kategori'])){
                    $kategori = $_POST['kategori'];
                    $pemakaian = $_POST['pemakaian'];

                    // ambil data tarif
                    $query_tarif = mysqli_query($conn, "select * from tarif where kategori_id='$kategori'");
                    $tarif = mysqli_fetch_array($query_tarif, MYSQLI_ASSOC);

                    // ambil data kategori
                    $query_kategori = mysqli_query($conn, "select * from kategori where id='$kategori'");
                    $kategori = mysqli_fetch_array($query_kategori, MYSQLI_ASSOC);

                    // hitung pajak
                    $subtotal = ($pemakaian * $tarif['tarif']) + $tarif['abodemen'];
                    $pajak = $tarif['pajak'];
                    $biaya_pajak = $subtotal * $pajak / 100;
                    $total = $subtotal + $biaya_pajak;

                ?>
                <div class="card">
                <div class="card-header bg-success text-white font-weight-bolder">
                    Jumlah Pemakaian Listrik
                </div>
                    <div class="card-body">
                        <p class="card-text">Kategori Pelanggan: <strong><?= $kategori['nama']; ?></strong></p>
                        <p class="card-text">Jumlah Pemakaian: <strong><?= $pemakaian; ?></strong></p>
                        <hr>
                        <p class="card-text">Tarif Dasar: <strong>Rp <?= number_format($tarif['tarif'], 0, ',', '.'); ?></strong></p>
                        <p class="card-text">Abodemen: <strong>Rp <?= number_format($tarif['abodemen'], 0, ',', '.'); ?></strong></p>
                        <p class="card-text">Pajak: <strong><?= $pajak . "%"; ?></strong> <small>(Rp <?= number_format($biaya_pajak, 0, ',', '.'); ?>)</small></p>
                        <p class="card-text">Subtotal: <strong>Rp <?= number_format($subtotal, 0, ',', '.'); ?></strong> <small>((<?= $pemakaian . " x Rp " . number_format($tarif['tarif'], 0, ',', '.') . ") + " . number_format($tarif['abodemen'], 0, ',', '.'); ?>)</small></p>
                        <p class="card-text">Total: <strong>Rp <?= number_format($total, 0, ',', '.'); ?></strong></p>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
        </div>
        <div class="card-footer text-muted text-center">
            Wiwik Listiyarini &copy; Universitas Dian Nusantara 2021
        </div>
    </div>


    <!-- JavaScript Bundle with Popper -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns" crossorigin="anonymous"></script>
</body>
</html>