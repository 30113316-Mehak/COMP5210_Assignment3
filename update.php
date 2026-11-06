<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'credentials.php';
$message = "";

$id = $item_number = $item_name = $object_class = $containment_procedures = $description = "";

if (isset($_GET['edit_id'])) {
    $id = intval($_GET['edit_id']);
    $stmt = $conn->prepare("SELECT * FROM scp_subjects WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $item_number = $row['item_number'];
        $item_name = $row['item_name'];
        $object_class = $row['object_class'];
        $containment_procedures = $row['containment_procedures'];
        $description = $row['description'];
    }
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = intval($_POST['id']);
    $item_number = $_POST['item_number'];
    $item_name = $_POST['item_name'];
    $object_class = $_POST['object_class'];
    $containment_procedures = $_POST['containment_procedures'];
    $description = $_POST['description'];

    $stmt = $conn->prepare("UPDATE scp_subjects SET item_number=?, item_name=?, object_class=?, containment_procedures=?, description=? WHERE id=?");
    $stmt->bind_param("sssssi", $item_number, $item_name, $object_class, $containment_procedures, $description, $id);
    if ($stmt->execute()) { 
        $message = "Subject entry updated successfully."; 
    } else {
        $message = "Update operation error: " . $stmt->error;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modify SCP Record</title>
    <link href="https://jsdelivr.net" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container my-5" style="max-width: 700px;">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-dark text-white p-3">
            <h4 class="mb-0 text-uppercase tracking-wide" style="font-size: 1.1rem;">Modify SCP Manifest Entry</h4>
        </div>
        <div class="card-body p-4">
            <?php if(!empty($message)): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            
            <form action="update.php?edit_id=<?php echo $id; ?>" method="POST">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Item Designation Number</label>
                        <input type="text" name="item_number" class="form-control" value="<?php echo htmlspecialchars($item_number); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Subject Label Name</label>
                        <input type="text" name="item_name" class="form-control" value="<?php echo htmlspecialchars($item_name); ?>" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Object Security Classification</label>
                    <select name="object_class" class="form-select" required>
                        <option value="Safe" <?php if($object_class == 'Safe') echo 'selected'; ?>>Safe</option>
                        <option value="Euclid" <?php if($object_class == 'Euclid') echo 'selected'; ?>>Euclid</option>
                        <option value="Keter" <?php if($object_class == 'Keter') echo 'selected'; ?>>Keter</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Special Containment Procedures</label>
                    <textarea name="containment_procedures" class="form-control" rows="4" required><?php echo htmlspecialchars($containment_procedures); ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Anatomical Description</label>
                    <textarea name="description" class="form-control" rows="4" required><?php echo htmlspecialchars($description); ?></textarea>
                </div>
                <div class="d-flex justify-content-between pt-2">
                    <a href="index.php" class="btn btn-outline-secondary">Return to Console</a>
                    <button type="submit" class="btn btn-dark px-4">Save Database Update Modifications</button>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>