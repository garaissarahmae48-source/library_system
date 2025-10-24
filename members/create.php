<?php
require '../db.php';
$errors = [];
$showDuplicateModal = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $joined = trim($_POST['joined'] ?? '');

    // Basic validation
    if ($name === '') $errors[] = "Name is required.";
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required.";
    if ($joined === '') $errors[] = "Joined date is required.";

    if (empty($errors)) {
        // Check for duplicate (same name or email)
        $check = $mysqli->prepare("SELECT COUNT(*) AS cnt FROM members WHERE name = ? OR email = ?");
        $check->bind_param('ss', $name, $email);
        $check->execute();
        $exists = $check->get_result()->fetch_assoc()['cnt'] ?? 0;

        if ($exists > 0) {
            // May existing record
            $showDuplicateModal = true;
        } else {
            // Insert new member
            $stmt = $mysqli->prepare("INSERT INTO members (name, email, joined) VALUES (?, ?, ?)");
            $stmt->bind_param('sss', $name, $email, $joined);
            if ($stmt->execute()) {
                header('Location: index.php');
                exit;
            } else {
                $errors[] = "Database error: " . $stmt->error;
            }
        }
    }
}

include '../header.php';
?>

<div class="container py-5">
  <div class="card shadow border-0">
    <div class="card-header bg-primary text-white">
      <h5 class="mb-0"><i class="bi bi-person-plus me-2"></i>Add New Member</h5>
    </div>
    <div class="card-body">
      <?php if ($errors): ?>
        <div class="alert alert-danger">
          <ul class="mb-0"><?php foreach ($errors as $e) echo "<li>" . htmlspecialchars($e) . "</li>"; ?></ul>
        </div>
      <?php endif; ?>

      <form method="post" action="create.php" class="needs-validation" novalidate>
        <div class="mb-3">
          <label class="form-label fw-semibold">Full Name *</label>
          <input name="name" class="form-control" required value="<?= htmlspecialchars($_POST['name'] ?? ''); ?>">
        </div>
        <div class="mb-3">
          <label class="form-label fw-semibold">Email Address *</label>
          <input name="email" type="email" class="form-control" required value="<?= htmlspecialchars($_POST['email'] ?? ''); ?>">
        </div>
        <div class="mb-3">
          <label class="form-label fw-semibold">Joined Date *</label>
          <input name="joined" type="date" class="form-control" required value="<?= htmlspecialchars($_POST['joined'] ?? date('Y-m-d')); ?>">
        </div>
        <div class="text-end">
          <a href="index.php" class="btn btn-outline-secondary me-2"><i class="bi bi-x-circle"></i> Cancel</a>
          <button type="submit" class="btn btn-success"><i class="bi bi-save"></i> Save Member</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Duplicate Member Modal -->
<div class="modal fade" id="duplicateModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4">
      <div class="modal-header bg-warning text-dark">
        <h5 class="modal-title"><i class="bi bi-exclamation-triangle me-2"></i> Duplicate Member</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p class="mb-0">A member with this name or email already exists in the system.<br>
        Please try again with different information.</p>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<?php include '../footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<?php if ($showDuplicateModal): ?>
<script>
  const duplicateModal = new bootstrap.Modal(document.getElementById('duplicateModal'));
  duplicateModal.show();
</script>
<?php endif; ?>
