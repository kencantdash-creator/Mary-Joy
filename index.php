<?php
require __DIR__ . '/config.php';
$pageTitle = 'Home'; $active = 'home';
$services = db()->query('SELECT s.*, MIN(p.price) AS min_price FROM services s LEFT JOIN prices p ON p.service_id = s.id GROUP BY s.id ORDER BY s.id')->fetchAll();
require __DIR__ . '/includes/header.php';
$steps = [
    ['Find your repair', 'Pick your phone brand and the problem you are having.', 'services.php', 'smartphone'],
    ['See the estimate', 'Check the expected cost and service time before you visit.', 'estimator.php', 'calc'],
    ['Book a visit', 'Choose a date and time for drop-off or a repair appointment.', 'appointment.php', 'calendar'],
    ['Track your phone', 'See whether it is waiting, being repaired, or ready for pickup.', 'track.php', 'search'],
];
?>
<section class="bg-slate-900 text-white">
  <div class="mx-auto grid max-w-6xl items-center gap-10 px-4 py-12 sm:px-6 lg:grid-cols-2 lg:py-20">
    <div>
      <h1 class="text-3xl font-bold leading-tight sm:text-4xl lg:text-5xl">Fix your phone, then get on with your day.</h1>
      <p class="mt-4 max-w-lg text-lg text-slate-300">Check prices, book a repair slot and follow your device's progress from one simple website.</p>
      <div class="mt-8 flex flex-col gap-3 sm:flex-row">
        <a href="appointment.php" class="<?= BTN ?>"><?= icon('calendar') ?>Book a repair</a>
        <a href="estimator.php" class="inline-flex items-center justify-center gap-2 rounded-md border border-slate-500 px-5 py-2.5 font-semibold text-white hover:bg-slate-800"><?= icon('calc') ?>Get a price estimate</a>
      </div>
    </div>
    <div class="rounded-lg bg-white p-6 text-slate-800">
      <h2 class="flex items-center gap-2 text-lg font-bold text-slate-900"><?= icon('search') ?>Track your repair</h2>
      <p class="mt-1 text-sm text-slate-600">Enter the reference code you received when you booked.</p>
      <form action="track.php" method="get" class="mt-4 flex flex-col gap-3 sm:flex-row">
        <label for="ref" class="sr-only">Reference code</label>
        <input id="ref" name="ref" required maxlength="12" placeholder="FG-XXXXXX" class="<?= FIELD ?> uppercase">
        <button class="<?= BTN ?>">Track</button>
      </form>
      <p class="mt-5 flex items-start gap-2 border-t border-slate-200 pt-4 text-sm text-slate-600"><?= icon('clock', 'mt-0.5 h-4 w-4') ?><span>Open <?= e(SHOP['hours'][0][0]) ?>, <?= e(SHOP['hours'][0][1]) ?>. Closed Sundays.</span></p>
    </div>
  </div>
</section>

<section class="mx-auto max-w-6xl px-4 py-12 sm:px-6">
  <h2 class="text-2xl font-bold text-slate-900">How it works</h2>
  <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <?php foreach ($steps as [$t, $d, $u, $i]): ?>
      <a href="<?= $u ?>" class="<?= CARD ?> block hover:border-teal-700">
        <span class="flex h-10 w-10 items-center justify-center rounded-md bg-teal-50 text-teal-700"><?= icon($i, 'h-5 w-5') ?></span>
        <h3 class="mt-3 font-semibold text-slate-900"><?= $t ?></h3>
        <p class="mt-1 text-sm text-slate-600"><?= $d ?></p>
      </a>
    <?php endforeach; ?>
  </div>
</section>

<section class="border-y border-slate-200 bg-white">
  <div class="mx-auto max-w-6xl px-4 py-12 sm:px-6">
    <div class="flex flex-wrap items-end justify-between gap-3">
      <h2 class="text-2xl font-bold text-slate-900">Common repairs</h2>
      <a href="services.php" class="font-semibold text-teal-700 hover:underline">See the full price guide</a>
    </div>
    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
      <?php foreach ($services as $s): ?>
        <div class="rounded-lg border border-slate-200 p-5">
          <?= icon($s['icon'], 'h-7 w-7 text-teal-700') ?>
          <h3 class="mt-3 font-semibold text-slate-900"><?= e($s['name']) ?></h3>
          <p class="mt-1 text-sm text-slate-600"><?= e($s['description']) ?></p>
          <p class="mt-3 text-sm font-semibold text-slate-900">From <?= peso($s['min_price']) ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
