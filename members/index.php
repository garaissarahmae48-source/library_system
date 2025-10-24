<?php
require '../db.php';

$search = trim($_GET['search'] ?? '');
if ($search !== '') {
    $stmt = $mysqli->prepare("SELECT id, name, email, joined FROM members WHERE name LIKE CONCAT('%',?,'%') OR email LIKE CONCAT('%',?,'%') ORDER BY id DESC");
    $stmt->bind_param('ss', $search, $search);
} else {
    $stmt = $mysqli->prepare("SELECT id, name, email, joined FROM members ORDER BY id DESC");
}
$stmt->execute();
$result = $stmt->get_result();

include '../header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h2 class="fw-bold"><i class="bi bi-people-fill me-2"></i>Members</h2>
  <a href="create.php" class="btn btn-success"><i class="bi bi-person-plus me-1"></i> Add Member</a>
</div>

<form class="mb-4" method="get" action="index.php">
  <div class="input-group">
    <input type="text" name="search" class="form-control" placeholder="Search name or email..." value="<?= htmlspecialchars($search); ?>">
    <button class="btn btn-outline-primary"><i class="bi bi-search"></i></button>
    <a class="btn btn-outline-secondary" href="index.php"><i class="bi bi-x-circle"></i></a>
  </div>
</form>

<div class="card shadow-sm border-0">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0 align-middle">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>Name</th>
            <th>Email</th>
            <th>Joined</th>
            <th class="text-center">Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?= $row['id']; ?></td>
            <td class="fw-semibold"><?= htmlspecialchars($row['name']); ?></td>
            <td><?= htmlspecialchars($row['email']); ?></td>
            <td><?= htmlspecialchars($row['joined']); ?></td>
            <td class="text-center">
              <a href="update.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-outline-secondary me-1">
                <i class="bi bi-pencil-square"></i> Edit
              </a>
              <button class="btn btn-sm btn-outline-danger"
                      data-bs-toggle="modal"
                      data-bs-target="#deleteModal"
                      data-id="<?= $row['id']; ?>"
                      data-name="<?= htmlspecialchars($row['name']); ?>">
                <i class="bi bi-trash3"></i> Delete
              </button>
            </td>
          </tr>
        <?php endwhile; $stmt->close(); $mysqli->close(); ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title"><i class="bi bi-trash3 me-2"></i>Confirm Delete</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p id="deleteText" class="mb-0"></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
        <a href="#" id="confirmDelete" class="btn btn-danger">Yes, Delete</a>
      </div>
    </div>
  </div>
</div>

<?php include '../footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Delete Modal setup
const deleteModal = document.getElementById('deleteModal');
deleteModal.addEventListener('show.bs.modal', event => {
  const button = event.relatedTarget;
  const id = button.getAttribute('data-id');
  const name = button.getAttribute('data-name');
  document.getElementById('deleteText').textContent =
    `Are you sure you want to delete the member "${name}"? This action cannot be undone.`;
  document.getElementById('confirmDelete').href = `delete.php?id=${id}`;
});
</script>
