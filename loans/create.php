<?php
require '../db.php';
$errors = [];

$members = $mysqli->query("SELECT id, name FROM members ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);
$books = $mysqli->query("SELECT id, title FROM books ORDER BY title ASC")->fetch_all(MYSQLI_ASSOC);

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
            $stmt = $mysqli->prepare("INSERT INTO loans (member_id, book_id, loaned_at, due_at, returned_at) VALUES (?, ?, ?, ?, NULL)");
            $stmt->bind_param('iiss', $member_id, $book_id, $loaned_at, $due_at);
        } else {
            $stmt = $mysqli->prepare("INSERT INTO loans (member_id, book_id, loaned_at, due_at, returned_at) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param('iisss', $member_id, $book_id, $loaned_at, $due_at, $returned_at);
        }
        if ($stmt->execute()) { header('Location: index.php'); exit; }
        else $errors[] = "DB error: " . $stmt->error;
    }
}
include '../header.php';
?>
<h2>New Loan</h2>
<?php if ($errors): ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $e) echo "<li>".htmlspecialchars($e)."</li>"; ?></ul></div><?php endif; ?>
<form method="post" action="create.php">
  <div class="mb-3">
    <label class="form-label">Member *</label>
    <select name="member_id" class="form-select" required>
      <option value="">-- Select member --</option>
      <?php foreach ($members as $m): ?>
        <option value="<?php echo $m['id']; ?>" <?php if (isset($_POST['member_id']) && $_POST['member_id']==$m['id']) echo 'selected'; ?>><?php echo htmlspecialchars($m['name']); ?></option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="mb-3">
    <label class="form-label">Book *</label>
    <select name="book_id" class="form-select" required>
      <option value="">-- Select book --</option>
      <?php foreach ($books as $b): ?>
        <option value="<?php echo $b['id']; ?>" <?php if (isset($_POST['book_id']) && $_POST['book_id']==$b['id']) echo 'selected'; ?>><?php echo htmlspecialchars($b['title']); ?></option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="mb-3"><label class="form-label">Loaned At *</label><input type="date" name="loaned_at" class="form-control" required value="<?php echo htmlspecialchars($_POST['loaned_at'] ?? date('Y-m-d')); ?>"></div>
  <div class="mb-3"><label class="form-label">Due At *</label><input type="date" name="due_at" class="form-control" required value="<?php echo htmlspecialchars($_POST['due_at'] ?? date('Y-m-d', strtotime('+14 days'))); ?>"></div>
  <div class="mb-3"><label class="form-label">Returned At (optional)</label><input type="date" name="returned_at" class="form-control" value="<?php echo htmlspecialchars($_POST['returned_at'] ?? ''); ?>"></div>

  <button class="btn btn-primary">Save Loan</button>
  <a href="index.php" class="btn btn-secondary">Cancel</a>
</form>
<?php include '../footer.php'; ?>
