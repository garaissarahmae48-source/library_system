<?php
require '../db.php';

// Get book ID
$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: index.php'); exit; }

// Fetch existing book record
$stmt = $mysqli->prepare("SELECT id, title, author, year_published, copies FROM books WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
$book = $res->fetch_assoc();
if (!$book) { header('Location: index.php'); exit; }

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
            $stmt = $mysqli->prepare("UPDATE books SET title = ?, author = ?, year_published = NULL, copies = ? WHERE id = ?");
            $stmt->bind_param('ssii', $title, $author, $copies, $id);
        } else {
            $y = (int)$year;
            $stmt = $mysqli->prepare("UPDATE books SET title = ?, author = ?, year_published = ?, copies = ? WHERE id = ?");
            $stmt->bind_param('ssiii', $title, $author, $y, $copies, $id);
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

<h2>Edit Book</h2>

<?php if ($errors): ?>
<div class="alert alert-danger">
  <ul class="mb-0">
    <?php foreach ($errors as $e) echo "<li>".htmlspecialchars($e)."</li>"; ?>
  </ul>
</div>
<?php endif; ?>

<form method="post" action="update.php?id=<?php echo $book['id']; ?>">
  <div class="mb-3">
    <label class="form-label">Title *</label>
    <input name="title" class="form-control" required value="<?php echo htmlspecialchars($_POST['title'] ?? $book['title']); ?>">
  </div>

  <div class="mb-3">
    <label class="form-label">Author *</label>
    <input name="author" class="form-control" required value="<?php echo htmlspecialchars($_POST['author'] ?? $book['author']); ?>">
  </div>

  <div class="mb-3">
    <label class="form-label">Year Published</label>
    <input type="number" name="year_published" class="form-control" value="<?php echo htmlspecialchars($_POST['year_published'] ?? $book['year_published']); ?>">
  </div>

  <div class="mb-3">
    <label class="form-label">Copies *</label>
    <input type="number" name="copies" class="form-control" min="0" required value="<?php echo htmlspecialchars($_POST['copies'] ?? $book['copies']); ?>">
  </div>

  <button class="btn btn-primary">Update</button>
  <a href="index.php" class="btn btn-secondary">Cancel</a>
</form>

<?php include '../footer.php'; ?>
