<?php
session_start();

/* ================= DATABASE CONNECTION ================= */
$servername = getenv("DB_HOST");
$username   = getenv("DB_USER");
$password   = getenv("DB_PASSWORD");
$dbname     = getenv("DB_NAME");

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Database connection failed");
}

/* ================= REGISTER ================= */
if (isset($_POST['register'])) {
    $u = $_POST['username'];
    $p = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $conn->query("INSERT INTO users (username,password) VALUES ('$u','$p')");
}

/* ================= LOGIN ================= */
if (isset($_POST['login'])) {
    $u = $_POST['username'];
    $p = $_POST['password'];
    $res = $conn->query("SELECT * FROM users WHERE username='$u'");
    $row = $res->fetch_assoc();

    if ($row && password_verify($p, $row['password'])) {
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['username'] = $row['username'];
    }
}

/* ================= LOGOUT ================= */
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location:index.php");
}

/* ================= ENROLL COURSE ================= */
if (isset($_POST['enroll'])) {
    $uid = $_SESSION['user_id'];
    $cid = $_POST['course_id'];
    $conn->query("INSERT INTO registrations (user_id,course_id) VALUES ($uid,$cid)");
}

/* ================= COMPLETE COURSE ================= */
if (isset($_GET['complete'])) {
    $conn->query("UPDATE registrations SET status='Completed' WHERE id=".$_GET['complete']);
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Technology Learning Portal</title>
<style>
body{font-family:Segoe UI;background:#f4f6f8;margin:0}
.container{width:1000px;margin:auto}
.nav{background:#0d6efd;color:#fff;padding:15px;display:flex;justify-content:space-between}
.nav a{color:#fff;text-decoration:none;margin-left:10px}
.card{background:#fff;padding:20px;margin:20px 0;border-radius:8px;box-shadow:0 4px 10px rgba(0,0,0,.1)}
input,button{width:100%;padding:10px;margin:8px 0}
button{background:#0d6efd;color:#fff;border:none}
.grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:20px}
.footer{text-align:center;color:#666;padding:15px}
a{color:#0d6efd;text-decoration:none}
</style>
</head>

<body>

<div class="nav">
<div>🚀 Tech Learning Portal</div>
<?php if(isset($_SESSION['user_id'])): ?>
<div>
<a href="index.php">Courses</a>
<a href="?profile=true">Profile</a>
<a href="?logout=true">Logout</a>
</div>
<?php endif; ?>
</div>

<div class="container">

<?php if(!isset($_SESSION['user_id'])): ?>

<div class="card">
<h2>Login</h2>
<form method="POST">
<input name="username" placeholder="Username" required>
<input type="password" name="password" placeholder="Password" required>
<button name="login">Login</button>
</form>
</div>

<div class="card">
<h2>Register</h2>
<form method="POST">
<input name="username" placeholder="Username" required>
<input type="password" name="password" placeholder="Password" required>
<button name="register">Register</button>
</form>
</div>

<?php else: ?>

<?php if(isset($_GET['profile'])): ?>

<div class="card">
<h2>👤 Student Profile</h2>
<p><b>Name:</b> <?= htmlspecialchars($_SESSION['username']) ?></p>
</div>

<div class="card">
<h3>My Courses</h3>

<?php
$uid=$_SESSION['user_id'];
$q=$conn->query("SELECT r.id rid,c.course_name,r.status FROM registrations r JOIN courses c ON r.course_id=c.id WHERE r.user_id=$uid");
while($r=$q->fetch_assoc()):
?>
<p>
<b><?= htmlspecialchars($r['course_name']) ?></b> — <?= $r['status'] ?>
<?php if($r['status']=='Enrolled'): ?>
 | <a href="?complete=<?= $r['rid'] ?>">Mark Completed</a>
<?php else: ?>
 | <a href="?certificate=<?= $r['rid'] ?>">View Certificate</a>
<?php endif; ?>
</p>
<?php endwhile; ?>

</div>

<?php elseif(isset($_GET['certificate'])): ?>

<?php
$res=$conn->query("SELECT u.username,c.course_name FROM registrations r
JOIN users u ON r.user_id=u.id
JOIN courses c ON r.course_id=c.id
WHERE r.id=".$_GET['certificate']);
$cert=$res->fetch_assoc();
?>

<div class="card" style="text-align:center;border:5px solid #0d6efd">
<h1>🎓 Certificate of Completion</h1>
<p>This certifies that</p>
<h2><?= htmlspecialchars($cert['username']) ?></h2>
<p>has completed</p>
<h3><?= htmlspecialchars($cert['course_name']) ?></h3>
<p><?= date("d M Y") ?></p>
<button onclick="window.print()">Print</button>
</div>

<?php elseif(isset($_GET['blogs'])): ?>

<div class="card">
<h2>📖 Course Blogs</h2>
<?php
$b=$conn->query("SELECT * FROM blogs WHERE course_id=".$_GET['blogs']);
while($blog=$b->fetch_assoc()):
?>
<h4><?= htmlspecialchars($blog['title']) ?></h4>
<p><?= htmlspecialchars($blog['content']) ?></p>
<hr>
<?php endwhile; ?>
</div>

<?php else: ?>

<div class="grid">
<?php
$c=$conn->query("SELECT * FROM courses");
while($course=$c->fetch_assoc()):
?>
<div class="card">
<h3><?= htmlspecialchars($course['course_name']) ?></h3>
<p><?= htmlspecialchars($course['description']) ?></p>
<form method="POST">
<input type="hidden" name="course_id" value="<?= $course['id'] ?>">
<button name="enroll">Enroll</button>
</form>
<a href="?blogs=<?= $course['id'] ?>">View Blogs</a>
</div>
<?php endwhile; ?>
</div>

<?php endif; ?>
<?php endif; ?>

</div>

<div class="footer">
© 2026 Technology Learning Portal | PHP & MySQL
</div>

</body>
</html>
