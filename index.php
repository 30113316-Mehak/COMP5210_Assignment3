<?php 
// 1. Force Active Diagnostic Error Output Reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. Fetch Active Relational Database Connection
include 'credentials.php'; 
$message = "";

if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM scp_subjects WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) { $message = "Record successfully removed."; }
    $stmt->close();
}

$result = $conn->query("SELECT * FROM scp_subjects ORDER BY item_number ASC");
$scp_items = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) { $scp_items[] = $row; }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COMP5210 Assignment 3</title>
    <link href="https://jsdelivr.net" rel="stylesheet">
    <style>
        .nav-tabs-vertical .nav-link { text-align: left; border: 1px solid #dee2e6; border-radius: 0.375rem; margin-bottom: 0.5rem; color: #212529; font-weight: 600; padding: 14px 20px; width: 100%; background: #fff; transition: all 0.2s; }
        .nav-tabs-vertical .nav-link:hover { background-color: #f8f9fa; }
        .nav-tabs-vertical .nav-link.active { color: #fff; background-color: #212529; border-color: #212529; box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
        .scp-display-card { border: 1px solid #e9ecef; border-radius: 0.75rem; background: #fff; box-shadow: 0 6px 16px rgba(0,0,0,0.04); overflow: hidden; }
        .full-scp-image { width: 100%; height: auto; max-height: 480px; object-fit: contain; background-color: #f8f9fa; display: block; margin: 0 auto; }
    </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark shadow-sm mb-5 py-4">
    <div class="container d-flex justify-content-between align-items-center">
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <img src="images/scp_logo.png" alt="Logo" height="50" class="me-3">
            <div>
                <h1 class="fs-4 fw-bold mb-0 text-light text-uppercase">COMP5210 Assignment 3</h1>
                <small class="text-muted text-uppercase tracking-wider" style="font-size: 0.75rem;">Secure Database</small>
            </div>
        </a>
        <a href="create.php" class="btn btn-outline-light px-4">Add New Entry</a>
    </div>
</nav>

<div class="container">
    <?php if (!empty($message)): ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (empty($scp_items)): ?>
        <div class="alert alert-info text-center py-5">No secure anomalous profiles located.</div>
    <?php else: ?>
        <div class="row g-4">
            <!-- Left Navigation Tabs Sidebar -->
            <div class="col-md-3">
                <div class="nav flex-column nav-tabs-vertical" id="v-pills-tab" role="tablist">
                    <?php foreach ($scp_items as $index => $item): ?>
                        <?php 
                            // Create clean textual ID labels to ensure framework scripts execute perfectly
                            $tab_id = strtolower(str_replace('-', '', $item['item_number']));
                        ?>
                        <button class="nav-link <?php echo $index === 0 ? 'active' : ''; ?>" 
                                id="btn-<?php echo $tab_id; ?>" 
                                data-bs-toggle="pill" 
                                data-bs-target="#panel-<?php echo $tab_id; ?>" 
                                type="button" 
                                role="tab"
                                aria-controls="panel-<?php echo $tab_id; ?>"
                                aria-selected="<?php echo $index === 0 ? 'true' : 'false'; ?>">
                            📁 <?php echo htmlspecialchars($item['item_number']); ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Right Dynamic Full-Display Content Panels -->
            <div class="col-md-9">
                <div class="tab-content" id="v-pills-tabContent">
                    <?php foreach ($scp_items as $index => $item): ?>
                        <?php 
                            $clean_num = strtolower(str_replace('-', '', $item['item_number']));
                            $short_num = str_replace(['scp00', 'scp0'], 'scp', $clean_num);
                            
                            $path_with_zeros = "images/" . $clean_num . "_img.png";
                            $path_short      = "images/" . $short_num . "_img.png";
                            
                            if (file_exists($path_with_zeros)) {
                                $image_path = $path_with_zeros;
                            } elseif (file_exists($path_short)) {
                                $image_path = $path_short;
                            } else {
                                $image_path = "images/scp_logo.png";
                            }
                            
                            $class = $item['object_class'];
                            $badge = "bg-secondary";
                            if ($class === 'Safe') { $badge = "bg-success"; }
                            if ($class === 'Euclid') { $badge = "bg-warning text-dark"; }
                            if ($class === 'Keter') { $badge = "bg-danger"; }
                        ?>
                        <div class="tab-pane fade <?php echo $index === 0 ? 'show active' : ''; ?>" 
                             id="panel-<?php echo $clean_num; ?>" 
                             role="tabpanel"
                             aria-labelledby="btn-<?php echo $clean_num; ?>">
                             
                            <div class="scp-display-card">
                                <!-- 🖼️ Full-Width Un-cropped Image Header Block Section -->
                                <div class="border-bottom p-2 bg-light">
                                    <img src="<?php echo $image_path; ?>" alt="Visual Asset" class="full-scp-image rounded border">
                                </div>
                                
                                <!-- 📝 Structured Data Content Details Below Image -->
                                <div class="p-4">
                                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                                        <h2 class="fw-bold mb-0 text-dark"><?php echo htmlspecialchars($item['item_name']); ?></h2>
                                        <span class="fs-5 badge bg-dark font-monospace"><?php echo htmlspecialchars($item['item_number']); ?></span>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <small class="fw-bold text-muted text-uppercase me-2" style="font-size: 0.8rem;">Classification:</small>
                                        <span class="badge <?php echo $badge; ?> px-3 py-2 fw-bold text-uppercase"><?php echo htmlspecialchars($class); ?></span>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <h5 class="fw-bold text-dark text-uppercase border-start border-3 border-dark ps-2" style="font-size: 0.95rem;">Special Containment Procedures</h5>
                                        <p class="text-secondary" style="text-align: justify; line-height: 1.6; font-size: 0.95rem; white-space: pre-line;"><?php echo htmlspecialchars($item['containment_procedures']); ?></p>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <h5 class="fw-bold text-dark text-uppercase border-start border-3 border-dark ps-2" style="font-size: 0.95rem;">Description</h5>
                                        <p class="text-secondary" style="text-align: justify; line-height: 1.6; font-size: 0.95rem; white-space: pre-line;"><?php echo htmlspecialchars($item['description']); ?></p>
                                    </div>
                                    
                                    <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                                        <a href="update.php?edit_id=<?php echo $item['id']; ?>" class="btn btn-outline-primary px-4 fw-semibold">Modify Entry</a>
                                        <a href="index.php?delete_id=<?php echo $item['id']; ?>" class="btn btn-outline-danger px-4 fw-semibold" onclick="return confirm('Purge record?');">Purge Record</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script src="https://jsdelivr.net"></script>
</body>
</html>