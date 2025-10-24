<?php
require '../db.php';
$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: index.php'); exit; }

$stmt = $mysqli->prepare("SELECT id, name, email, joined FROM members WHERE id = ?");
$stmt->bind_param('i', $id); $stmt->execute();
$res = $stmt->get_result(); $member = $res->fetch_assoc();
if (!$member) { header('Location: index.php'); exit; }

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $joined = trim($_POST['joined'] ?? '');

    if ($name === '') $errors[] = "Name required.";
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email required.";
    if ($joined === '') $errors[] = "Joined date required.";

    if (empty($errors)) {
        $stmt = $mysqli->prepare("UPDATE members SET name = ?, email = ?, joined = ? WHERE id = ?");
        $stmt->bind_param('sssi', $name, $email, $joined, $id);
        if ($stmt->execute()) { header('Location: index.php'); exit; }
        else $errors[] = "DB error: " . $stmt->error;
    }
}
include '../header.php';
?>
<h2>Edit Member</h2>
<?php if ($errors): ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $e) echo "<li>".htmlspecialchars($e)."</li>"; ?></ul></div><?php endif; ?>
<form method="post" action="update.php?id=<?php echo $member['id']; ?>">
  <div class="mb-3"><label class="form-label">Name *</label><input name="name" class="form-control" required value="<?php echo htmlspecialchars($_POST['name'] ?? $member['name']); ?>"></div>
  <div class="mb-3"><label class="form-label">Email *</label><input name="email" type="email" class="form-control" required value="<?php echo htmlspecialchars($_POST['email'] ?? $member['email']); ?>"></div>
  <div class="mb-3"><label class="form-label">Joined *</label><input name="joined" type="date" class="form-control" required value="<?php echo htmlspecialchars($_POST['joined'] ?? $member['joined']); ?>"></div>
  <button class="btn btn-primary">Update</button>
  <a href="index.php" class="btn btn-secondary">Cancel</a>
</form>
<?php include '../footer.php'; ?>
