<?php
// admin/manage_stops.php
require_once __DIR__ . '/../config/db.php';
requireLogin('admin');

$db = getDB();
$flash = getFlash();

// Handle Form Submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        $stop_name = sanitize($_POST['stop_name'] ?? '');
        if (!empty($stop_name)) {
            try {
                $stmt = $db->prepare("INSERT INTO bus_stops (stop_name) VALUES (?)");
                $stmt->execute([$stop_name]);
                redirectWithMessage(APP_URL . '/admin/manage_stops.php', 'success', "Bus stop '$stop_name' added successfully.");
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) {
                    redirectWithMessage(APP_URL . '/admin/manage_stops.php', 'danger', "Bus stop '$stop_name' already exists.");
                } else {
                    redirectWithMessage(APP_URL . '/admin/manage_stops.php', 'danger', "Failed to add bus stop: " . $e->getMessage());
                }
            }
        } else {
            redirectWithMessage(APP_URL . '/admin/manage_stops.php', 'danger', "Bus stop name cannot be empty.");
        }
    } elseif ($action === 'delete') {
        $stop_id = (int)($_POST['stop_id'] ?? 0);
        if ($stop_id > 0) {
            $stmt = $db->prepare("DELETE FROM bus_stops WHERE stop_id = ?");
            $stmt->execute([$stop_id]);
            redirectWithMessage(APP_URL . '/admin/manage_stops.php', 'success', "Bus stop deleted successfully.");
        }
    }
}

// Pagination & Search
$search = trim($_GET['search'] ?? '');
$page = max(1, (int)($_GET['p'] ?? 1));
$limit = 15;
$offset = ($page - 1) * $limit;

if ($search !== '') {
    $stmtCount = $db->prepare("SELECT COUNT(*) FROM bus_stops WHERE stop_name LIKE ?");
    $stmtCount->execute(["%$search%"]);
    $total = $stmtCount->fetchColumn();
    
    $stmt = $db->prepare("SELECT * FROM bus_stops WHERE stop_name LIKE ? ORDER BY stop_name ASC LIMIT $limit OFFSET $offset");
    $stmt->execute(["%$search%"]);
} else {
    $total = $db->query("SELECT COUNT(*) FROM bus_stops")->fetchColumn();
    $stmt = $db->prepare("SELECT * FROM bus_stops ORDER BY stop_name ASC LIMIT $limit OFFSET $offset");
    $stmt->execute();
}

$stops = $stmt->fetchAll();
$pages = ceil($total / $limit);
$pageTitle = 'Manage Bus Stops';

include __DIR__ . '/../includes/header.php';
?>
<div class="app-layout">
    <?php include __DIR__ . '/../includes/sidebar_admin.php'; ?>
    <div class="main-content">
        <div class="topbar">
            <button class="sidebar-toggle" id="sidebarToggle"><i class="fa fa-bars"></i></button>
            <div class="topbar-title">📍 Manage Bus Stops</div>
        </div>
        
        <div class="page-content">
            <?php if (!empty($flash['msg'])): ?>
                <div class="flash-banner flash-<?= htmlspecialchars($flash['type']) ?> mb-3">
                    <?= htmlspecialchars($flash['msg']) ?>
                </div>
            <?php endif; ?>
            
            <div class="table-card">
                <div class="table-header d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <h5 class="mb-0"><i class="fa fa-map-pin me-2 text-primary"></i>Bus Stops (<?= $total ?>)</h5>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <form method="GET" class="d-flex align-items-center gap-1" style="max-width: 250px;">
                            <input type="text" name="search" class="form-control-custom py-1" style="font-size: 13px;" placeholder="Search stop..." value="<?= htmlspecialchars($search) ?>">
                            <button type="submit" class="btn-primary-custom btn-sm-custom px-2"><i class="fa fa-search"></i></button>
                            <?php if ($search !== ''): ?>
                                <a href="manage_stops.php" class="btn btn-sm btn-outline-secondary px-2" style="border-radius: 8px;"><i class="fa fa-times"></i></a>
                            <?php endif; ?>
                        </form>
                        <button class="btn-primary-custom btn-sm-custom" data-bs-toggle="modal" data-bs-target="#addStopModal">
                            <i class="fa fa-plus"></i> Add Stop
                        </button>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th style="width: 80px;">ID</th>
                                <th>Stop Name</th>
                                <th style="width: 120px;" class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($stops)): ?>
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">
                                        <i class="fa fa-map-signs d-block mb-2 style-logo" style="font-size: 24px;"></i>
                                        No bus stops found.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($stops as $s): ?>
                                    <tr class="animate-fade">
                                        <td><strong>#<?= $s['stop_id'] ?></strong></td>
                                        <td><?= htmlspecialchars($s['stop_name']) ?></td>
                                        <td class="text-end">
                                            <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this bus stop?')">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="stop_id" value="<?= $s['stop_id'] ?>">
                                                <button type="submit" class="btn-icon delete" title="Delete Stop"><i class="fa fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Pagination -->
            <?php if ($pages > 1): ?>
                <nav class="mt-4 d-flex justify-content-center gap-2">
                    <?php if ($page > 1): ?>
                        <a href="?p=<?= $page - 1 ?>&search=<?= urlencode($search) ?>" class="btn btn-sm btn-outline-secondary"><i class="fa fa-chevron-left"></i></a>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $pages; $i++): ?>
                        <a href="?p=<?= $i ?>&search=<?= urlencode($search) ?>" class="btn btn-sm <?= $i === $page ? 'btn-primary' : 'btn-outline-secondary' ?>"><?= $i ?></a>
                    <?php endfor; ?>
                    
                    <?php if ($page < $pages): ?>
                        <a href="?p=<?= $page + 1 ?>&search=<?= urlencode($search) ?>" class="btn btn-sm btn-outline-secondary"><i class="fa fa-chevron-right"></i></a>
                    <?php endif; ?>
                </nav>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Add Stop Modal -->
<div class="modal fade" id="addStopModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: 12px; overflow: hidden; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1)">
            <div class="modal-header text-white" style="background: var(--primary); border: none;">
                <h5 class="modal-title"><i class="fa fa-map-pin me-2"></i>Add Bus Stop</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="add">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600;">Bus Stop Name</label>
                        <input type="text" name="stop_name" class="form-control-custom" placeholder="e.g., K.t.c Nagar" required autocomplete="off">
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid #f1f3f5;">
                    <button type="button" class="btn btn-light" style="border-radius: 10px;" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-primary-custom">Add Stop</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
