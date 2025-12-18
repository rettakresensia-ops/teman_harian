<?php
include 'db.php';

if (isset($_POST['simpan_mood'])) {
    $mood = $_POST['mood'];
    $catatan = mysqli_real_escape_string($conn, $_POST['catatan']);
    
    $query = "INSERT INTO mood_tracker (suasana_hati, catatan) VALUES ('$mood', '$catatan')";
    mysqli_query($conn, $query);
    header("Location: index.php?page=mood&status=sukses");
}

if (isset($_POST['simpan_jurnal'])) {
    $refleksi = mysqli_real_escape_string($conn, $_POST['refleksi']);
    $jawaban = mysqli_real_escape_string($conn, $_POST['jawaban']);
    
    $query = "INSERT INTO jurnal (refleksi, jawaban_panduan) VALUES ('$refleksi', '$jawaban')";
    mysqli_query($conn, $query);
    header("Location: index.php?page=jurnal&status=sukses");
}
?>