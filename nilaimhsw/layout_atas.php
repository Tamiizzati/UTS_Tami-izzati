<!DOCTYPE html>
<html lang="id" data-bs-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($judul) ?></title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" crossorigin="anonymous">
  <link rel="stylesheet" href="css/adminlte.min.css">
</head>
<body class="bg-body-tertiary">
<div class="app-wrapper" style="min-height:100vh">
  <main class="app-main">
    <div class="app-content-header">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6"><h3 class="mb-0"><?= htmlspecialchars($judul) ?></h3></div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-end">
              <li class="breadcrumb-item"><a href="bacanilai.php">Beranda</a></li>
              <li class="breadcrumb-item active">Nilai</li>
            </ol>
          </div>
        </div>
      </div>
    </div>
    <div class="app-content">
      <div class="container-fluid">
