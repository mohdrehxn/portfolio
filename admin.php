<?php
/**
 * admin.php — Submissions Admin Panel
 * ─────────────────────────────────────
 * Password-protected dashboard to view all contact form submissions.
 * Access: yourdomain.com/admin.php
 */

require_once __DIR__ . '/db.php';
session_start();

/* ── Auth ───────────────────────────────────────────────── */
$authError = '';

if (isset($_POST['logout'])) {
    session_destroy();
    header('Location: admin.php');
    exit;
}

if (isset($_POST['login_user'])) {
    if ($_POST['login_user'] === ADMIN_USER && $_POST['login_pass'] === ADMIN_PASS) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_time']      = time();
        header('Location: admin.php');
        exit;
    } else {
        $authError = 'Invalid username or password.';
        // Slow brute-force attempts
        sleep(1);
    }
}

// Auto-logout after 2 hours
if (isset($_SESSION['admin_logged_in']) && (time() - $_SESSION['admin_time']) > 7200) {
    session_destroy();
    header('Location: admin.php');
    exit;
}

$loggedIn = !empty($_SESSION['admin_logged_in']);

/* ── DB actions (only when logged in) ─────────────────── */
$actionMsg = '';

if ($loggedIn) {
    ensureTable();
    $pdo = getDB();

    // Mark as read/replied/archived
    if (!empty($_GET['status']) && !empty($_GET['id'])) {
        $allowed = ['read', 'replied', 'archived', 'new'];
        $newStatus = in_array($_GET['status'], $allowed) ? $_GET['status'] : 'new';
        $sid = (int)$_GET['id'];
        $pdo->prepare("UPDATE contacts SET status=? WHERE id=?")->execute([$newStatus, $sid]);
        header('Location: admin.php?page=' . ($_GET['page'] ?? 1) . '&filter=' . ($_GET['filter'] ?? 'all') . '&msg=updated');
        exit;
    }

    // Delete single
    if (!empty($_GET['delete']) && !empty($_GET['id'])) {
        $sid = (int)$_GET['id'];
        $pdo->prepare("DELETE FROM contacts WHERE id=?")->execute([$sid]);
        header('Location: admin.php?msg=deleted');
        exit;
    }

    // Delete all archived
    if (!empty($_GET['purge'])) {
        $pdo->exec("DELETE FROM contacts WHERE status='archived'");
        header('Location: admin.php?msg=purged');
        exit;
    }

    if ($_GET['msg'] ?? '' === 'updated') $actionMsg = '✅ Status updated.';
    if ($_GET['msg'] ?? '' === 'deleted') $actionMsg = '🗑️ Entry deleted.';
    if ($_GET['msg'] ?? '' === 'purged')  $actionMsg = '🧹 Archived entries purged.';

    /* ── Fetch data ─────────────────────────── */
    $filter  = in_array($_GET['filter'] ?? '', ['new','read','replied','archived']) ? $_GET['filter'] : 'all';
    $search  = trim($_GET['search'] ?? '');
    $perPage = 10;
    $page    = max(1, (int)($_GET['page'] ?? 1));
    $offset  = ($page - 1) * $perPage;

    $where  = $filter !== 'all' ? "WHERE status = :status" : "WHERE 1";
    $params = $filter !== 'all' ? [':status' => $filter] : [];

    if ($search !== '') {
        $where  .= " AND (name LIKE :s OR email LIKE :s2 OR message LIKE :s3)";
        $params[':s']  = "%{$search}%";
        $params[':s2'] = "%{$search}%";
        $params[':s3'] = "%{$search}%";
    }

    $totalStmt = $pdo->prepare("SELECT COUNT(*) FROM contacts {$where}");
    $totalStmt->execute($params);
    $total     = (int)$totalStmt->fetchColumn();
    $totalPages = max(1, (int)ceil($total / $perPage));

    $dataStmt = $pdo->prepare("SELECT * FROM contacts {$where} ORDER BY created_at DESC LIMIT {$perPage} OFFSET {$offset}");
    $dataStmt->execute($params);
    $rows = $dataStmt->fetchAll();

    /* ── Stats ──────────────────────────────── */
    $stats = $pdo->query("
        SELECT
            COUNT(*) AS total,
            SUM(status='new')      AS new_count,
            SUM(status='read')     AS read_count,
            SUM(status='replied')  AS replied_count,
            SUM(status='archived') AS archived_count
        FROM contacts
    ")->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Panel — Mohd Rehan</title>
<style>
:root {
  --bg:      #060b12;
  --surface: #0f1a27;
  --surface2:#14212f;
  --accent:  #ff6b4a;
  --text:    #f5f7fb;
  --muted:   #8a9ab5;
  --border:  rgba(255,255,255,.08);
  --green:   #4caf8a;
  --yellow:  #ffc947;
  --blue:    #5b9cf6;
  --red:     #e05050;
}
*, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }
html { scroll-behavior:smooth; }
body { font-family:system-ui,sans-serif; background:var(--bg); color:var(--text); min-height:100vh; line-height:1.5; }

/* ── LOGIN ── */
.login-wrap {
  min-height:100vh;
  display:flex; align-items:center; justify-content:center;
  background:radial-gradient(circle at 50% 20%, rgba(255,107,74,.1), transparent 60%);
}
.login-card {
  background:var(--surface);
  border:1px solid var(--border);
  border-radius:20px;
  padding:3rem 2.5rem;
  width:100%; max-width:380px;
  box-shadow:0 20px 60px rgba(0,0,0,.4);
}
.login-card h1 { font-size:1.6rem; margin-bottom:.3rem; }
.login-card p  { color:var(--muted); font-size:.9rem; margin-bottom:2rem; }
.login-card label { display:block; font-size:.77rem; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:var(--muted); margin-bottom:.35rem; margin-top:1rem; }
.login-card input {
  width:100%; padding:11px 14px;
  background:#111c2a; border:1.5px solid var(--border);
  border-radius:10px; color:var(--text);
  font-size:.94rem; outline:none;
  transition:.25s;
}
.login-card input:focus { border-color:rgba(255,107,74,.45); box-shadow:0 0 0 3px rgba(255,107,74,.08); }
.login-card button {
  width:100%; margin-top:1.5rem;
  padding:13px; background:var(--accent);
  border:none; border-radius:50px;
  color:#fff; font-size:.95rem; font-weight:600;
  cursor:pointer; transition:.3s;
}
.login-card button:hover { background:#ff9473; transform:translateY(-1px); }
.err-msg { background:rgba(224,80,80,.1); border:1px solid rgba(224,80,80,.3); border-radius:10px; padding:10px 14px; font-size:.85rem; color:#e05050; margin-top:1rem; }

/* ── LAYOUT ── */
.admin-wrap { display:grid; grid-template-columns:230px 1fr; min-height:100vh; }

/* ── SIDEBAR ── */
.sidebar {
  background:var(--surface);
  border-right:1px solid var(--border);
  padding:2rem 1.25rem;
  position:sticky; top:0; height:100vh;
  overflow-y:auto;
  display:flex; flex-direction:column;
}
.sidebar-logo { font-size:1.1rem; font-weight:800; color:var(--text); margin-bottom:2rem; padding-bottom:1.25rem; border-bottom:1px solid var(--border); }
.sidebar-logo span { color:var(--accent); }

.sidebar-nav { flex:1; }
.nav-item {
  display:flex; align-items:center; gap:.6rem;
  padding:.6rem .9rem; border-radius:10px;
  font-size:.88rem; font-weight:500;
  color:var(--muted);
  text-decoration:none; margin-bottom:.2rem;
  transition:.25s;
}
.nav-item:hover { background:var(--surface2); color:var(--text); }
.nav-item.active { background:rgba(255,107,74,.12); color:var(--accent); }
.nav-badge {
  margin-left:auto;
  background:var(--accent);
  color:#fff; font-size:.68rem; font-weight:700;
  padding:1px 7px; border-radius:20px;
}

.sidebar-footer { border-top:1px solid var(--border); padding-top:1.25rem; }
.sidebar-footer form button {
  width:100%; padding:.65rem;
  background:transparent;
  border:1px solid var(--border);
  border-radius:10px;
  color:var(--muted); font-size:.86rem;
  cursor:pointer; transition:.25s;
}
.sidebar-footer form button:hover { border-color:var(--red); color:var(--red); }

/* ── MAIN ── */
.main { padding:2rem 2.5rem; overflow-x:hidden; }

.top-bar {
  display:flex; align-items:center; justify-content:space-between;
  margin-bottom:2rem; flex-wrap:wrap; gap:1rem;
}
.top-bar h1 { font-size:1.5rem; }
.top-bar p  { color:var(--muted); font-size:.85rem; margin-top:.15rem; }

/* Search */
.search-box {
  display:flex; align-items:center;
  background:var(--surface);
  border:1px solid var(--border);
  border-radius:50px; padding:0 1rem;
  gap:.5rem;
}
.search-box input {
  background:none; border:none; outline:none;
  color:var(--text); font-size:.88rem;
  padding:.6rem 0; min-width:200px;
}
.search-box input::placeholder { color:var(--muted); }
.search-box button {
  background:none; border:none;
  color:var(--muted); cursor:pointer; font-size:.9rem;
}

/* Stats cards */
.stats-row { display:grid; grid-template-columns:repeat(auto-fit,minmax(130px,1fr)); gap:1rem; margin-bottom:2rem; }
.stat-card {
  background:var(--surface);
  border:1px solid var(--border);
  border-radius:14px; padding:1.2rem 1rem;
  text-align:center;
}
.stat-card .snum { font-size:2rem; font-weight:800; display:block; line-height:1; }
.stat-card .slbl { font-size:.77rem; color:var(--muted); margin-top:.3rem; display:block; }
.c-total   .snum { color:var(--accent); }
.c-new     .snum { color:var(--yellow); }
.c-read    .snum { color:var(--blue); }
.c-replied .snum { color:var(--green); }
.c-archived .snum { color:var(--muted); }

/* Action msg */
.action-msg {
  background:rgba(76,175,138,.1);
  border:1px solid rgba(76,175,138,.25);
  border-radius:10px;
  padding:10px 16px;
  font-size:.86rem; color:var(--green);
  margin-bottom:1.5rem;
}

/* Table */
.table-wrap {
  background:var(--surface);
  border:1px solid var(--border);
  border-radius:16px;
  overflow:hidden;
}
.table-header {
  display:flex; align-items:center; justify-content:space-between;
  padding:1.1rem 1.5rem;
  border-bottom:1px solid var(--border);
  gap:1rem; flex-wrap:wrap;
}
.table-header h2 { font-size:1rem; }
.purge-btn {
  background:transparent;
  border:1px solid var(--border);
  border-radius:50px;
  color:var(--muted); font-size:.78rem;
  padding:5px 14px; cursor:pointer; transition:.25s;
  text-decoration:none; display:inline-block;
}
.purge-btn:hover { border-color:var(--red); color:var(--red); }

table { width:100%; border-collapse:collapse; }
thead tr { background:var(--surface2); }
th {
  font-size:.73rem; font-weight:700;
  text-transform:uppercase; letter-spacing:.07em;
  color:var(--muted); padding:.8rem 1rem;
  text-align:left; white-space:nowrap;
}
td {
  padding:.85rem 1rem;
  font-size:.87rem;
  border-bottom:1px solid var(--border);
  vertical-align:top;
}
tr:last-child td { border-bottom:none; }
tr:hover td { background:rgba(255,255,255,.02); }

.badge {
  display:inline-block;
  padding:3px 10px; border-radius:20px;
  font-size:.7rem; font-weight:700; letter-spacing:.04em;
  text-transform:uppercase;
}
.badge-new      { background:rgba(255,201,71,.15); color:var(--yellow); border:1px solid rgba(255,201,71,.3); }
.badge-read     { background:rgba(91,156,246,.15); color:var(--blue);   border:1px solid rgba(91,156,246,.3); }
.badge-replied  { background:rgba(76,175,138,.15); color:var(--green);  border:1px solid rgba(76,175,138,.3); }
.badge-archived { background:rgba(138,154,181,.1); color:var(--muted);  border:1px solid var(--border); }

.msg-preview { color:var(--muted); font-size:.83rem; max-width:260px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }

/* Actions dropdown */
.actions { display:flex; gap:.4rem; flex-wrap:wrap; }
.act-btn {
  font-size:.72rem; font-weight:600;
  padding:3px 10px; border-radius:20px;
  border:1px solid var(--border);
  color:var(--muted); background:transparent;
  cursor:pointer; text-decoration:none;
  transition:.2s; white-space:nowrap;
}
.act-btn:hover         { border-color:var(--blue);   color:var(--blue); }
.act-btn.act-replied   { }
.act-btn.act-replied:hover { border-color:var(--green);  color:var(--green); }
.act-btn.act-archive:hover { border-color:var(--muted);  color:var(--muted); }
.act-btn.act-delete:hover  { border-color:var(--red);    color:var(--red); }

/* Pagination */
.pagination {
  display:flex; align-items:center; justify-content:center;
  gap:.5rem; padding:1.25rem;
  border-top:1px solid var(--border);
}
.page-btn {
  padding:5px 13px; border-radius:8px;
  border:1px solid var(--border);
  background:transparent;
  color:var(--muted); font-size:.84rem;
  cursor:pointer; text-decoration:none;
  transition:.25s;
}
.page-btn:hover   { border-color:var(--accent); color:var(--accent); }
.page-btn.current { background:var(--accent); color:#fff; border-color:var(--accent); pointer-events:none; }

/* Empty state */
.empty-state {
  text-align:center; padding:4rem 2rem;
  color:var(--muted);
}
.empty-state .icon { font-size:3rem; display:block; margin-bottom:1rem; }

/* Modal overlay for full message */
.modal-bg {
  display:none; position:fixed; inset:0;
  background:rgba(0,0,0,.65);
  z-index:500;
  align-items:center; justify-content:center;
}
.modal-bg.open { display:flex; }
.modal {
  background:var(--surface);
  border:1px solid var(--border);
  border-radius:18px;
  padding:2rem;
  max-width:560px; width:90%;
  max-height:80vh; overflow-y:auto;
  position:relative;
}
.modal h3    { margin-bottom:.75rem; }
.modal .meta { font-size:.8rem; color:var(--muted); margin-bottom:1.2rem; }
.modal .full-msg { color:var(--muted); line-height:1.8; white-space:pre-wrap; font-size:.92rem; }
.modal-close {
  position:absolute; top:1rem; right:1rem;
  background:none; border:none;
  color:var(--muted); font-size:1.3rem; cursor:pointer;
  line-height:1; transition:color .2s;
}
.modal-close:hover { color:var(--red); }

@media(max-width:768px){
  .admin-wrap { grid-template-columns:1fr; }
  .sidebar { display:none; }
  .main { padding:1.5rem 1rem; }
}
</style>
</head>
<body>

<?php if (!$loggedIn): ?>
<!-- ══════════ LOGIN PAGE ══════════ -->
<div class="login-wrap">
  <div class="login-card">
    <h1>🔐 Admin Login</h1>
    <p>Mohd Rehan — Portfolio Dashboard</p>

    <?php if ($authError): ?>
    <div class="err-msg"><?= htmlspecialchars($authError) ?></div>
    <?php endif; ?>

    <form method="POST">
      <label>Username</label>
      <input type="text" name="login_user" placeholder="admin" autocomplete="username" required>
      <label>Password</label>
      <input type="password" name="login_pass" placeholder="••••••••" autocomplete="current-password" required>
      <button type="submit">Sign In →</button>
    </form>
  </div>
</div>

<?php else: ?>
<!-- ══════════ ADMIN DASHBOARD ══════════ -->
<div class="admin-wrap">

  <!-- SIDEBAR -->
  <aside class="sidebar">
    <div class="sidebar-logo">MR<span>.</span> Admin</div>

    <nav class="sidebar-nav">
      <a href="admin.php" class="nav-item <?= $filter==='all' ? 'active' : '' ?>">
        📊 All Submissions
        <?php if ($stats['total'] > 0): ?>
        <span class="nav-badge"><?= $stats['total'] ?></span>
        <?php endif; ?>
      </a>
      <a href="admin.php?filter=new" class="nav-item <?= $filter==='new' ? 'active' : '' ?>">
        🔔 New
        <?php if ($stats['new_count'] > 0): ?>
        <span class="nav-badge"><?= $stats['new_count'] ?></span>
        <?php endif; ?>
      </a>
      <a href="admin.php?filter=read" class="nav-item <?= $filter==='read' ? 'active' : '' ?>">
        👁️ Read
      </a>
      <a href="admin.php?filter=replied" class="nav-item <?= $filter==='replied' ? 'active' : '' ?>">
        ✅ Replied
      </a>
      <a href="admin.php?filter=archived" class="nav-item <?= $filter==='archived' ? 'active' : '' ?>">
        🗄️ Archived
      </a>
    </nav>

    <div class="sidebar-footer">
      <form method="POST">
        <button type="submit" name="logout">🚪 Logout</button>
      </form>
    </div>
  </aside>

  <!-- MAIN CONTENT -->
  <main class="main">

    <!-- Top bar -->
    <div class="top-bar">
      <div>
        <h1>Contact Submissions</h1>
        <p>
          <?= $total ?> result<?= $total !== 1 ? 's' : '' ?>
          <?= $filter !== 'all' ? "· Filtered: <strong>{$filter}</strong>" : '' ?>
          <?= $search ? "· Search: <strong>".htmlspecialchars($search)."</strong>" : '' ?>
        </p>
      </div>

      <form method="GET" class="search-box">
        <?php if ($filter !== 'all'): ?>
        <input type="hidden" name="filter" value="<?= htmlspecialchars($filter) ?>">
        <?php endif; ?>
        <input type="text" name="search" placeholder="Search name, email, message…" value="<?= htmlspecialchars($search) ?>">
        <button type="submit">🔍</button>
      </form>
    </div>

    <!-- Stats -->
    <div class="stats-row">
      <div class="stat-card c-total">
        <span class="snum"><?= $stats['total'] ?></span>
        <span class="slbl">Total</span>
      </div>
      <div class="stat-card c-new">
        <span class="snum"><?= $stats['new_count'] ?></span>
        <span class="slbl">New</span>
      </div>
      <div class="stat-card c-read">
        <span class="snum"><?= $stats['read_count'] ?></span>
        <span class="slbl">Read</span>
      </div>
      <div class="stat-card c-replied">
        <span class="snum"><?= $stats['replied_count'] ?></span>
        <span class="slbl">Replied</span>
      </div>
      <div class="stat-card c-archived">
        <span class="snum"><?= $stats['archived_count'] ?></span>
        <span class="slbl">Archived</span>
      </div>
    </div>

    <?php if ($actionMsg): ?>
    <div class="action-msg"><?= $actionMsg ?></div>
    <?php endif; ?>

    <!-- Table -->
    <div class="table-wrap">
      <div class="table-header">
        <h2>📋 Messages</h2>
        <?php if ($stats['archived_count'] > 0): ?>
        <a href="admin.php?purge=1" class="purge-btn" onclick="return confirm('Delete all archived entries permanently?')">
          🧹 Purge Archived (<?= $stats['archived_count'] ?>)
        </a>
        <?php endif; ?>
      </div>

      <?php if (empty($rows)): ?>
      <div class="empty-state">
        <span class="icon">📭</span>
        <p>No submissions found.</p>
      </div>
      <?php else: ?>
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>Name</th>
            <th>Email</th>
            <th>Budget</th>
            <th>Message</th>
            <th>Status</th>
            <th>Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($rows as $row):
            $esc = array_map('htmlspecialchars', $row);
            $date = date('d M Y, H:i', strtotime($row['created_at']));
          ?>
          <tr>
            <td style="color:var(--muted);font-size:.8rem;"><?= $esc['id'] ?></td>
            <td><strong><?= $esc['name'] ?></strong></td>
            <td>
              <a href="mailto:<?= $esc['email'] ?>" style="color:var(--accent);text-decoration:none;">
                <?= $esc['email'] ?>
              </a>
            </td>
            <td style="font-size:.82rem;color:var(--muted);"><?= $esc['budget'] ?></td>
            <td>
              <div class="msg-preview"><?= $esc['message'] ?></div>
              <button class="act-btn" style="margin-top:4px;" onclick="openModal(<?= $row['id'] ?>, '<?= addslashes($esc['name']) ?>', '<?= addslashes($esc['email']) ?>', '<?= addslashes(str_replace(["\r","\n"], ['','\\n'], $esc['message'])) ?>', '<?= $date ?>')">
                Read more
              </button>
            </td>
            <td>
              <span class="badge badge-<?= $esc['status'] ?>"><?= $esc['status'] ?></span>
            </td>
            <td style="font-size:.8rem;color:var(--muted);white-space:nowrap;"><?= $date ?></td>
            <td>
              <div class="actions">
                <?php $base = "admin.php?id={$row['id']}&filter={$filter}&page={$page}"; ?>
                <?php if ($row['status'] !== 'read'): ?>
                <a href="<?= $base ?>&status=read" class="act-btn">Mark Read</a>
                <?php endif; ?>
                <?php if ($row['status'] !== 'replied'): ?>
                <a href="<?= $base ?>&status=replied" class="act-btn act-replied">Replied</a>
                <?php endif; ?>
                <?php if ($row['status'] !== 'archived'): ?>
                <a href="<?= $base ?>&status=archived" class="act-btn act-archive">Archive</a>
                <?php endif; ?>
                <a href="<?= $base ?>&delete=1" class="act-btn act-delete" onclick="return confirm('Delete this entry permanently?')">Delete</a>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

      <!-- Pagination -->
      <?php if ($totalPages > 1): ?>
      <div class="pagination">
        <?php if ($page > 1): ?>
        <a href="admin.php?page=<?= $page-1 ?>&filter=<?= $filter ?>&search=<?= urlencode($search) ?>" class="page-btn">← Prev</a>
        <?php endif; ?>

        <?php for ($p = max(1,$page-2); $p <= min($totalPages,$page+2); $p++): ?>
        <a href="admin.php?page=<?= $p ?>&filter=<?= $filter ?>&search=<?= urlencode($search) ?>"
           class="page-btn <?= $p===$page ? 'current' : '' ?>"><?= $p ?></a>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
        <a href="admin.php?page=<?= $page+1 ?>&filter=<?= $filter ?>&search=<?= urlencode($search) ?>" class="page-btn">Next →</a>
        <?php endif; ?>
      </div>
      <?php endif; ?>
      <?php endif; ?>
    </div>
  </main>
</div>

<!-- MODAL: Full message -->
<div class="modal-bg" id="modalBg" onclick="if(event.target===this)closeModal()">
  <div class="modal">
    <button class="modal-close" onclick="closeModal()">✕</button>
    <h3 id="modalName"></h3>
    <div class="meta" id="modalMeta"></div>
    <div class="full-msg" id="modalMsg"></div>
  </div>
</div>

<script>
function openModal(id, name, email, msg, date) {
  document.getElementById('modalName').textContent = name;
  document.getElementById('modalMeta').textContent = email + ' · ' + date;
  document.getElementById('modalMsg').textContent  = msg.replace(/\\n/g, '\n');
  document.getElementById('modalBg').classList.add('open');
}
function closeModal() {
  document.getElementById('modalBg').classList.remove('open');
}
document.addEventListener('keydown', function(e){ if(e.key==='Escape') closeModal(); });
</script>

<?php endif; ?>
</body>
</html>
