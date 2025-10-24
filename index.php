<?php
include 'header.php';
require 'db.php';

// Kunin counts
$books = $mysqli->query("SELECT COUNT(*) AS total FROM books")->fetch_assoc()['total'] ?? 0;
$members = $mysqli->query("SELECT COUNT(*) AS total FROM members")->fetch_assoc()['total'] ?? 0;
$loans = $mysqli->query("SELECT COUNT(*) AS total FROM loans")->fetch_assoc()['total'] ?? 0;
?>

<div class="container py-5">
  <div class="text-center mb-5">
    <h1 class="fw-bold mb-3">📚 Library Management System</h1>
    <p class="text-muted fs-5">Manage your books, members, and loan records easily.</p>
  </div>

  <div class="row g-4">
    <!-- Books Card -->
    <div class="col-md-4">
      <div class="card shadow-sm border-0 h-100">
        <div class="card-body text-center">
          <div class="mb-3 text-primary" style="font-size:48px;">
            <i class="bi bi-book"></i>
          </div>
          <h5 class="card-title fw-bold">Books</h5>
          <p class="card-text text-muted mb-3">Manage your collection of books in the library.</p>
          <h3 class="fw-bold text-primary mb-3"><?= $books ?></h3>
          <a href="books/index.php" class="btn btn-outline-primary w-100">Go to Books</a>
        </div>
      </div>
    </div>

    <!-- Members Card -->
    <div class="col-md-4">
      <div class="card shadow-sm border-0 h-100">
        <div class="card-body text-center">
          <div class="mb-3 text-success" style="font-size:48px;">
            <i class="bi bi-people"></i>
          </div>
          <h5 class="card-title fw-bold">Members</h5>
          <p class="card-text text-muted mb-3">View and manage library members’ information.</p>
          <h3 class="fw-bold text-success mb-3"><?= $members ?></h3>
          <a href="members/index.php" class="btn btn-outline-success w-100">Go to Members</a>
        </div>
      </div>
    </div>

    <!-- Loans Card -->
    <div class="col-md-4">
      <div class="card shadow-sm border-0 h-100">
        <div class="card-body text-center">
          <div class="mb-3 text-warning" style="font-size:48px;">
            <i class="bi bi-journal-arrow-up"></i>
          </div>
          <h5 class="card-title fw-bold">Loans</h5>
          <p class="card-text text-muted mb-3">Track borrowed books and their due dates.</p>
          <h3 class="fw-bold text-warning mb-3"><?= $loans ?></h3>
          <a href="loans/index.php" class="btn btn-outline-warning w-100">Go to Loans</a>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
