<?php 
include 'db.php'; 

// 1. Logika Navigasi
$page = isset($_GET['page']) ? $_GET['page'] : 'mood';
$status_msg = "";

if(isset($_GET['status']) && $_GET['status'] == 'sukses') {
    $status_msg = "<div class='alert'>Data berhasil disimpan!</div>";
}

// 2. Ambil Data untuk Grafik Mood (Insight Mingguan)
$labels = [];
$counts = [];
$query_grafik = mysqli_query($conn, "SELECT suasana_hati, COUNT(*) as jumlah FROM mood_tracker GROUP BY suasana_hati");
while($row = mysqli_fetch_assoc($query_grafik)) {
    $labels[] = $row['suasana_hati'];
    $counts[] = $row['jumlah'];
}

// 3. Ambil Insight Mingguan (Mood Terbanyak)
$query_insight = mysqli_query($conn, "SELECT suasana_hati, COUNT(suasana_hati) AS total FROM mood_tracker GROUP BY suasana_hati ORDER BY total DESC LIMIT 1");
$insight = mysqli_fetch_assoc($query_insight);
$mood_dominan = $insight ? $insight['suasana_hati'] : "Belum ada data";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teman Psikologi Harian</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root { --primary: #4facfe; --secondary: #2c3e50; --bg: #f4f7f6; --white: #ffffff; }
        body { font-family: 'Segoe UI', sans-serif; background-color: var(--bg); margin: 0; display: flex; min-height: 100vh; }
        
        /* Sidebar */
        .sidebar { width: 260px; background: var(--secondary); color: white; padding: 25px; box-shadow: 2px 0 10px rgba(0,0,0,0.1); }
        .sidebar h2 { font-size: 1.2rem; text-align: center; border-bottom: 1px solid #455a64; padding-bottom: 15px; }
        .menu-item { padding: 12px; color: #bdc3c7; text-decoration: none; display: block; border-radius: 8px; margin-bottom: 5px; transition: 0.3s; }
        .menu-item:hover, .menu-item.active { background: #34495e; color: white; }

        /* Main */
        .main-content { flex: 1; padding: 40px; display: flex; justify-content: center; }
        .container { width: 100%; max-width: 800px; }
        .card { background: var(--white); padding: 30px; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        .alert { background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center; }

        /* Form */
        label { font-weight: bold; display: block; margin-bottom: 5px; }
        input, select, textarea { width: 100%; padding: 12px; margin-bottom: 20px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; }
        .btn { background: var(--primary); color: white; padding: 12px; border: none; border-radius: 8px; cursor: pointer; width: 100%; font-weight: bold; }
        
        /* Animasi Pernapasan */
        .breathing-circle { width: 120px; height: 120px; background: var(--primary); border-radius: 50%; margin: 40px auto; animation: breathe 19s infinite ease-in-out; display: flex; align-items: center; justify-content: center; color: white; font-size: 0.8rem; }
        @keyframes breathe { 0%, 100% { transform: scale(1); } 21% { transform: scale(1.6); } 58% { transform: scale(1.6); } }

        .tips-box { background: #e1f5fe; border-left: 5px solid #03a9f4; padding: 15px; margin-top: 15px; border-radius: 4px; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Teman Psikologi</h2>
    <a href="?page=mood" class="menu-item <?= $page=='mood'?'active':'' ?>">1. Pencatat Suasana Hati</a>
    <a href="?page=jurnal" class="menu-item <?= $page=='jurnal'?'active':'' ?>">2. Self-Reflection Journal</a>
    <a href="?page=relaksasi" class="menu-item <?= $page=='relaksasi'?'active':'' ?>">3. Relaksasi & Mindfulness</a>
    <a href="?page=tips" class="menu-item <?= $page=='tips'?'active':'' ?>">4. Tips Psikolog</a>
    <a href="?page=insight" class="menu-item <?= $page=='insight'?'active':'' ?>">5. Insight Mingguan</a>
</div>

<div class="main-content">
    <div class="container">
        <?= $status_msg ?>
        
        <div class="card">
            <?php if($page == 'mood'): ?>
                <h3>Bagaimana perasaanmu hari ini?</h3>
                <form method="POST" action="simpan_aksi.php">
                    <label>Suasana Hati:</label>
                    <select name="mood">
                        <option>😊 Senang</option><option>😔 Sedih</option>
                        <option>😰 Stres</option><option>😟 Cemas</option><option>😐 Biasa Saja</option>
                    </select>
                    <label>Catatan Singkat:</label>
                    <textarea name="catatan" placeholder="Contoh: Hari ini capek karena tugas menumpuk..." required></textarea>
                    <button type="submit" name="simpan_mood" class="btn">Simpan Suasana Hati</button>
                </form>

            <?php elseif($page == 'jurnal'): ?>
                <h3>Self-Reflection Journal</h3>
                <form method="POST" action="simpan_aksi.php">
                    <p><strong>Tanya:</strong> Apa hal terbaik yang terjadi hari ini?</p>
                    <textarea name="jawaban" placeholder="Tulis hal positif..." required></textarea>
                    <p><strong>Refleksi:</strong> Apa yang membuatmu cemas & apa yang bisa diubah?</p>
                    <textarea name="refleksi" placeholder="Tulis refleksimu..." required></textarea>
                    <button type="submit" name="simpan_jurnal" class="btn" style="background:#2ecc71;">Simpan Jurnal</button>
                </form>

            <?php elseif($page == 'relaksasi'): ?>
                <h3 style="text-align:center;">Relaksasi Pernapasan 4-7-8</h3>
                <p style="text-align:center;">Tarik (4s) -> Tahan (7s) -> Hembus (8s)</p>
                <div class="breathing-circle">BERNAPAS</div>

            <?php elseif($page == 'tips'): ?>
                <h3>Tips Psikolog Harian</h3>
                <div class="tips-box">
                    <?php 
                        $tips_list = ["Jangan lupa minum air putih.", "Validasi perasaanmu itu penting.", "Istirahat sejenak dari layar gadget.", "Cobalah teknik grounding 5-4-3-2-1."];
                        echo "<em>\"" . $tips_list[array_rand($tips_list)] . "\"</em>";
                    ?>
                </div>

            <?php elseif($page == 'insight'): ?>
                <h3>Analisis Insight Mingguan</h3>
                <canvas id="moodChart" style="max-height: 300px;"></canvas>
                <div class="tips-box">
                    <p><strong>Mood Dominan:</strong> <?= $mood_dominan ?></p>
                    <p><strong>Saran:</strong> 
                        <?php if(strpos($mood_dominan, 'Stres') !== false || strpos($mood_dominan, 'Sedih') !== false): ?>
                            Kamu sedang banyak pikiran, gunakan fitur Relaksasi (Menu 3) untuk menenangkan diri.
                        <?php else: ?>
                            Pertahankan energi positifmu dan tetaplah bersyukur!
                        <?php endif; ?>
                    </p>
                </div>
                <script>
                    const ctx = document.getElementById('moodChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: <?= json_encode($labels) ?>,
                            datasets: [{
                                label: 'Jumlah Mood',
                                data: <?= json_encode($counts) ?>,
                                backgroundColor: '#4facfe',
                                borderRadius: 5
                            }]
                        }
                    });
                </script>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>