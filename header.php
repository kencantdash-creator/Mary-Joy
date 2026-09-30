<?php
$pageTitle = $pageTitle ?? 'Home';
$active = $active ?? '';
$nav = [
    'services'    => ['Services', 'services.php'],
    'estimator'   => ['Price Estimator', 'estimator.php'],
    'appointment' => ['Book Repair', 'appointment.php'],
    'track'       => ['Track Repair', 'track.php'],
    'device'      => ['Device Info', 'device.php'],
    'history'     => ['Service History', 'history.php'],
    'contact'     => ['Contact', 'contact.php'],
];
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($pageTitle) ?> | Fix &amp; Go</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex min-h-screen flex-col bg-slate-50 text-slate-800 antialiased">
<header class="sticky top-0 z-40 border-b border-slate-200 bg-white">
  <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-3 sm:px-6">
    <a href="index.php" class="flex items-center gap-2.5">
      <span class="flex h-9 w-9 items-center justify-center rounded-md bg-teal-700 text-white"><?= icon('wrench', 'h-5 w-5') ?></span>
      <span class="text-lg font-bold text-slate-900">Fix &amp; Go</span>
    </a>
    <nav class="hidden items-center gap-1 lg:flex" aria-label="Main">
      <?php foreach ($nav as $k => [$label, $url]): ?>
        <a href="<?= $url ?>" class="rounded-md px-2.5 py-2 text-sm <?= $active === $k ? 'bg-slate-100 font-semibold text-slate-900' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' ?>"><?= $label ?></a>
      <?php endforeach; ?>
    </nav>
    <button id="menuBtn" type="button" class="rounded-md p-2 text-slate-700 hover:bg-slate-100 lg:hidden" aria-label="Toggle menu" aria-expanded="false">
      <span id="icoOpen"><?= icon('menu', 'h-6 w-6') ?></span><span id="icoClose" class="hidden"><?= icon('x', 'h-6 w-6') ?></span>
    </button>
  </div>
  <nav id="mobileMenu" class="hidden border-t border-slate-200 bg-white px-4 py-2 lg:hidden" aria-label="Mobile">
    <?php foreach ($nav as $k => [$label, $url]): ?>
      <a href="<?= $url ?>" class="block rounded-md px-3 py-3 <?= $active === $k ? 'bg-slate-100 font-semibold text-slate-900' : 'text-slate-700' ?>"><?= $label ?></a>
    <?php endforeach; ?>
  </nav>
</header>
<main class="flex-1">
