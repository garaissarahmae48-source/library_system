<?php
require '../db.php';
$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: index.php'); exit; }

$stmt = $mysqli->prepare("SELECT * FROM loans WHERE id = ?");
$stmt->bind_param('i', $id); $stmt->execute();
$loan = $stmt->get_result()->fetch_assoc();
if (!$loan) { header('Location: index.php'); exit; }

$members = $mysqli->query("SELECT id, name FROM members ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);
$books = $mysqli->query("SELECT id, title FROM books ORDER BY title ASC")->fetch_all(MYSQLI_ASSOC);

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $member_id = (int)($_POST['member_id'] ?? 0);
    $book_id = (int)($_POST['book_id'] ?? 0);
    $loaned_at = trim($_POST['loaned_at'] ?? '');
    $due_at = trim($_POST['due_at'] ?? '');
    $returned_at = trim($_POST['returned_at'] ?? '');

    if ($member_id <= 0) $errors[] = "Select a member.";
    if ($book_id <= 0) $errors[] = "Select a book.";
    if ($loaned_at === '') $errors[] = "Loaned date required.";
    if ($due_at === '') $errors[] = "Due date required.";

    if (empty($errors)) {
        if ($returned_at === '') {
            $stmt = $mysqli->prepare("UPDATE loans SET member_id = ?, book_id = ?, loaned_at = ?, due_at = ?, returned_at = NULL WHERE id = ?");
            $stmt->bind_param('iissi', $member_id, $book_id, $loaned_at, $due_at, $id);
        } else {
            $stmt = $mysqli->prepare("UPDATE loans SET member_id = ?, book_id = ?, loaned_at = ?, due_at = ?, returned_at = ? WHERE id = ?");
            $stmt->bind_param('iisssi', $member_id, $book_id, $loaned_at, $due_at, $returned_at, $id);
        }
        if ($stmt->execute()) { header('Location: index.php'); exit; }
        else $errors[] = "DB error: " . $stmt->error;
    }
}
include '../header.php';
?>
<h2>Edit Loan</h2>
<?php if ($errors): ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $e) echo "<li>".htmlspecialchars($e)."</li>"; ?></ul></div><?php endif; ?>
<form method="post" action="update.php?id=<?php echo $loan['id']; ?>">
  <div class="mb-3">
    <label class="form-label">Member *</label>
    <select name="member_id" class="form-select" required>
      <option value="">-- Select member --</option>
      <?php foreach ($members as $m): $sel = (isset($_POST['member_id'])?$_POST['member_id']:$loan['member_id']) == $m['id']; ?>
        <option value="<?php echo $m['id']; ?>" <?php if ($sel) echo 'selected'; ?>><?php echo htmlspecialchars($m['name']); ?></option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="mb-3">
    <label class="form-label">Book *</label>
    <select name="book_id" class="form-select" required>
      <option value="">-- Select book --</option>
      <?php foreach ($books as $b): $sel = (isset($_POST['book_id'])?$_POST['book_id']:$loan['book_id']) == $b['id']; ?>
        <option value="<?php echo $b['id']; ?>" <?php if ($sel) echo 'selected'; ?>><?php echo htmlspecialchars($b['title']); ?></option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="mb-3"><label class="form-label">Loaned At *</label><input type="date" name="loaned_at" class="form-control" required value="<?php echo htmlspecialchars($_POST['loaned_at'] ?? $loan['loaned_at']); ?>"></div>
  <div class="mb-3"><label class="form-label">Due At *</label><input type="date" name="due_at" class="form-control" required value="<?php echo htmlspecialchars($_POST['due_at'] ?? $loan['due_at']); ?>"></div>
  <div class="mb-3"><label class="form-label">Returned At (optional)</label><input type="date" name="returned_at" class="form-control" value="<?php echo htmlspecialchars($_POST['returned_at'] ?? $loan['returned_at']); ?>"></div>

  <button class="btn btn-primary">Update Loan</button>
  <a href="index.php" class="btn btn-secondary">Cancel</a>
</form>
<?php include '../footer.php'; ?>
