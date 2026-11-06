<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'credentials.php';
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_number = $_POST['item_number'];
    $item_name = $_POST['item_name'];
    $object_class = $_POST['object_class'];
    $containment_procedures = $_POST['containment_procedures'];
    $description = $_POST['description'];
    $image_url = strtolower(str_replace(['-', ' '], '', $item_number)) . "_img.png";

    $stmt = $conn->prepare("INSERT INTO scp_subjects (item_number, item_name, object_class, containment_procedures, description, image_url) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $item_number, $item_name, $object_class, $containment_procedures, $description, $image_url);
    
    if ($stmt->execute()) { 
        $message = "New subject entry logged successfully inside archive."; 
    } else {
        $message = "Error saving new record: " . $stmt->error;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create SCP Record</title>
    <link href="https://jsdelivr.net" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container my-5" style="max-width: 700px;">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-dark text-white p-3">
            <h4 class="mb-0 text-uppercase tracking-wide" style="font-size: 1.1rem;">Log New SCP Archive Record</h4>
        </div>
        <div class="card-body p-4">
            <?php if(!empty($message)): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            
            <form action="create.php" method="POST">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Item Designation Number</label>
                        <input type="text" name="item_number" class="form-control" required placeholder="e.g. SCP-002">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Subject Label Name</label>
                        <input type="text" name="item_name" class="form-control" required placeholder="e.g. The Living Room">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Object Security Classification</label>
                    <select name="object_class" class="form-select" required>
                        <option value="Safe">Safe</option>
                        <option value="Euclid">Euclid</option>
                        <option value="Keter">Keter</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Special Containment Procedures</label>
                    <textarea name="containment_procedures" class="form-control" rows="4" required placeholder="Detail specific safety isolation directives..."></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Anatomical Description</label>
                    <textarea name="description" class="form-control" rows="4" required placeholder="Detail explicit physical and anomalous properties..."></textarea>
                </div>
                <div class="d-flex justify-content-between pt-2">
                    <a href="index.php" class="btn btn-outline-secondary">Return to Console</a>
                    <button type="submit" class="btn btn-dark px-4">Commit Entry to Manifest</button>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>