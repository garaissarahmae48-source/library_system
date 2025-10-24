<?php
require '../db.php';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $author = trim($_POST['author'] ?? '');
    $year = trim($_POST['year_published'] ?? '');
    $copies = trim($_POST['copies'] ?? '');

    if ($title === '') $errors[] = "Title required.";
    if ($author === '') $errors[] = "Author required.";
    if ($copies === '' || !is_numeric($copies) || $copies < 0) $errors[] = "Copies must be a non-negative number.";

    if (empty($errors)) {
        if ($year === '') {
            $stmt = $mysqli->prepare("INSERT INTO books (title, author, year_published, copies) VALUES (?, ?, NULL, ?)");
            $stmt->bind_param('ssi', $title, $author, $copies);
        } else {
            $y = (int)$year;
            $stmt = $mysqli->prepare("INSERT INTO books (title, author, year_published, copies) VALUES (?, ?, ?, ?)");
            $stmt->bind_param('ssii', $title, $author, $y, $copies);
        }

        if ($stmt->execute()) {
            header('Location: index.php');
            exit;
        } else {
            $errors[] = "DB error: " . $stmt->error;
        }
    }
}

include '../header.php';
?>
<h2>Add Book</h2>

<?php if ($errors): ?>
  <div class="alert alert-danger">
    <ul class="mb-0">
      <?php foreach ($errors as $e) echo "<li>" . htmlspecialchars($e) . "</li>"; ?>
    </ul>
  </div>
<?php endif; ?>

<form method="post" action="create.php">
  <div class="mb-3">
    <label class="form-label">Title *</label>
    <input name="title" class="form-control" required value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>">
  </div>

  <div class="mb-3">
    <label class="form-label">Author *</label>
    <input name="author" class="form-control" required value="<?php echo htmlspecialchars($_POST['author'] ?? ''); ?>">
  </div>

  <div class="mb-3">
    <label class="form-label">Year Published</label>
    <input type="number" name="year_published" class="form-control" value="<?php echo htmlspecialchars($_POST['year_published'] ?? ''); ?>">
  </div>

  <div class="mb-3">
    <label class="form-label">Copies *</label>
    <input type="number" name="copies" class="form-control" min="0" required value="<?php echo htmlspecialchars($_POST['copies'] ?? ''); ?>">
  </div>

  <button class="btn btn-primary">Save</button>
  <a href="index.php" class="btn btn-secondary">Cancel</a>
</form>

<?php include '../footer.php'; ?>
