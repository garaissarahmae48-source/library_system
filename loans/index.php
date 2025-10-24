<?php
require '../db.php';

// Handle mark-as-returned action
if (isset($_GET['action']) && $_GET['action'] === 'return' && isset($_GET['id'])) {
    $rid = (int)$_GET['id'];
    if ($rid > 0) {
        $stmt = $mysqli->prepare("UPDATE loans SET returned_at = CURDATE() WHERE id = ?");
        $stmt->bind_param('i', $rid);
        $stmt->execute();
        header('Location: index.php');
        exit;
    }
}

$sql = "SELECT l.id, l.member_id, l.book_id, l.loaned_at, l.due_at, l.returned_at,
               m.name AS member_name, b.title AS book_title
        FROM loans l
        JOIN members m ON l.member_id = m.id
        JOIN books b ON l.book_id = b.id
        ORDER BY l.id DESC";
$stmt = $mysqli->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
include '../header.php';
?>

<div class="d-flex justify-content-between mb-3">
  <h2>Loans</h2>
  <a href="create.php" class="btn btn-success">New Loan</a>
</div>

<div class="card shadow-sm">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-striped mb-0 align-middle">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>Member</th>
            <th>Book</th>
            <th>Loaned</th>
            <th>Due</th>
            <th>Returned</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): 
            $status = $row['returned_at'] ? 'Returned' : (strtotime($row['due_at']) < time() ? 'Overdue' : 'On Loan');
        ?>
          <tr>
            <td><?= $row['id']; ?></td>
            <td><?= htmlspecialchars($row['member_name']); ?></td>
            <td><?= htmlspecialchars($row['book_title']); ?></td>
            <td><?= htmlspecialchars($row['loaned_at']); ?></td>
            <td><?= htmlspecialchars($row['due_at']); ?></td>
            <td><?= $row['returned_at'] ?: '-'; ?></td>
            <td>
              <span class="badge 
                <?= $status === 'Returned' ? 'bg-success' : ($status === 'Overdue' ? 'bg-danger' : 'bg-warning text-dark'); ?>">
                <?= $status; ?>
              </span>
            </td>
            <td>
              <a href="update.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
              
              <?php if (!$row['returned_at']): ?>
                <button class="btn btn-sm btn-success" 
                        data-bs-toggle="modal" 
                        data-bs-target="#returnModal" 
                        data-id="<?= $row['id']; ?>" 
                        data-member="<?= htmlspecialchars($row['member_name']); ?>"
                        data-book="<?= htmlspecialchars($row['book_title']); ?>">
                  Mark as Returned
                </button>
              <?php endif; ?>
              
              <button class="btn btn-sm btn-outline-danger" 
                      data-bs-toggle="modal" 
                      data-bs-target="#deleteModal" 
                      data-id="<?= $row['id']; ?>" 
                      data-member="<?= htmlspecialchars($row['member_name']); ?>"
                      data-book="<?= htmlspecialchars($row['book_title']); ?>">
                Delete
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
        <p id="deleteText"></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
        <a href="#" id="confirmDelete" class="btn btn-danger">Yes, Delete</a>
      </div>
    </div>
  </div>
</div>

<!-- Return Confirmation Modal -->
<div class="modal fade" id="returnModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title"><i class="bi bi-arrow-return-left me-2"></i>Mark as Returned</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p id="returnText"></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
        <a href="#" id="confirmReturn" class="btn btn-success">Yes, Mark as Returned</a>
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
  const member = button.getAttribute('data-member');
  const book = button.getAttribute('data-book');
  document.getElementById('deleteText').textContent = 
    `Are you sure you want to delete the loan record of "${book}" borrowed by ${member}?`;
  document.getElementById('confirmDelete').href = `delete.php?id=${id}`;
});

// Return Modal setup
const returnModal = document.getElementById('returnModal');
returnModal.addEventListener('show.bs.modal', event => {
  const button = event.relatedTarget;
  const id = button.getAttribute('data-id');
  const member = button.getAttribute('data-member');
  const book = button.getAttribute('data-book');
  document.getElementById('returnText').textContent = 
    `Mark the book "${book}" borrowed by ${member} as returned?`;
  document.getElementById('confirmReturn').href = `index.php?action=return&id=${id}`;
});
</script>
