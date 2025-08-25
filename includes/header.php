<?php
// includes/header.php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="th">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ระบบจัดการแบนเนอร์</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/styles.css">
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@100..900&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Noto Sans Thai', sans-serif;
    }
  </style>
</head>

<body class="bg-gray-100 font-noto">
  <nav class="bg-blue-400 text-white shadow-lg">
    <div class="container mx-auto px-4 py-3 flex justify-between items-center">
      <a href="<?= BASE_URL ?>" class="text-2xl">ระบบจัดการแบนเนอร์</a>
      <div class="space-x-4">
        <a href="<?= BASE_URL ?>/banners/" class="hover:bg-blue-700 px-3 py-2 rounded-full">แบนเนอร์</a>
        <a href="<?= BASE_URL ?>/platforms/" class="hover:bg-blue-700 px-3 py-2 rounded-full">แพลตฟอร์ม</a>
        <a href="<?= BASE_URL ?>/banner-platforms/manage.php" class="hover:bg-blue-700 px-3 py-2 rounded-full">การกำหนดแสดงผล</a>
      </div>
    </div>
  </nav>

  <main class="container mx-auto px-4 py-6"></main>