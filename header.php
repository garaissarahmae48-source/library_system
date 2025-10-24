<?php
// Detect current page for active nav highlight
$current = basename($_SERVER['PHP_SELF']);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Library System</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
    }
    .navbar {
      box-shadow: 0 2px 6px rgba(0,0,0,0.15);
    }
    .navbar-brand {
      font-weight: 700;
      letter-spacing: 0.5px;
      color: #fff !important;
    }
    .nav-link {
      font-weight: 500;
      color: #ffffff !important; /* white text */
      transition: 0.3s ease;
    }
    .nav-link:hover {
      color: #ffd43b !important; /* golden hover */
      transform: translateY(-1px);
    }
    .nav-link.active {
      color: #ffffff !important;
      background-color: rgba(255, 255, 255, 0.25);
      border-radius: 6px;
      padding-inline: 12px;
    }
    .navbar-toggler {
      border-color: rgba(255,255,255,0.4);
    }
    .navbar-toggler-icon {
      filter: brightness(0) invert(1);
    }
  </style>
</head>

<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="/library_system/">
      <i class="bi bi-book-half me-2 fs-4"></i> Library System
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navMain">
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link <?= (strpos($_SERVER['REQUEST_URI'], 'books') !== false) ? 'active' : ''; ?>" href="/library_system/books/index.php">
            <i class="bi bi-book me-1"></i> Books
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= (strpos($_SERVER['REQUEST_URI'], 'members') !== false) ? 'active' : ''; ?>" href="/library_system/members/index.php">
            <i class="bi bi-people me-1"></i> Members
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= (strpos($_SERVER['REQUEST_URI'], 'loans') !== false) ? 'active' : ''; ?>" href="/library_system/loans/index.php">
            <i class="bi bi-journal-text me-1"></i> Loans
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="container py-4">
