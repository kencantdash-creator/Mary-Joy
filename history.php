<?php
require __DIR__ . '/config.php';
$pageTitle = 'Service History'; $active = 'history';
$digits = preg_replace('/\D+/', '', old('phone'));
$rows = null;
if ($digits !== '' && strlen($digits) >= 10) {
    $st = db()->prepare('SELECT a.*, b.name AS brand, s.name AS service FROM appointments a JOIN brands b ON b.id = a.brand_id JOIN services s ON s.id = a.service_id WHERE a.phone = ? ORDER BY a.appt_date DESC, a.appt_time DESC');
    $st->execute([$digits]);
    $rows = $st->fetchAll();
}
require __DIR__ . '/includes/header.php';
echo pageHead('Service History', 'Look up the repairs previously made through this website using your mobile number.');
?>
<section class="mx-auto max-w-3xl px-4 py-10 sm:px-6">
  <form method="get" class="<?= CARD ?> flex flex-col gap-3 sm:flex-row sm:items-end">
    <div class="flex-1"><label for="phone" class="<?= LABEL ?>">Mobile number used when booking</label>
      <input id="phone" name="phone" type="tel" required maxlength="20" value="<?= e(old('phone')) ?>" placeholder="09171234567" class="<?= FIELD ?>"></div>
    <button class="<?= BTN ?>"><?= icon('history') ?>View history</button>
  </form>
  <?php if ($digits !== '' && $rows === null): ?>
    <p class="mt-6 flex items-start gap-2 rounded-md border border-red-200 bg-red-50 p-4 text-red-800"><?= icon('alert', 'mt-0.5 h-5 w-5') ?>Enter a valid mobile number (at least 10 digits).</p>
  <?php elseif ($rows !== null && !$rows): ?>
    <p class="mt-6 flex items-start gap-2 rounded-md border border-slate-200 bg-white p-4 text-slate-700"><?= icon('info', 'mt-0.5 h-5 w-5') ?>No repairs found for that number yet.</p>
  <?php elseif ($rows): ?>
    <p class="mt-6 text-sm text-slate-600"><?= count($rows) ?> record<?= count($rows) > 1 ? 's' : '' ?> found.</p>
    <div class="mt-3 space-y-3">
      <?php foreach ($rows as $r): ?>
        <article class="<?= CARD ?>">
          <div class="flex flex-wrap items-start justify-between gap-2">
            <div><h2 class="font-bold text-slate-900"><?= e($r['service']) ?></h2><p class="text-sm text-slate-600"><?= e($r['brand'] . ' ' . $r['model']) ?></p></div>
            <?= statusBadge($r['status']) ?>
          </div>
          <div class="mt-3 flex flex-wrap gap-x-6 gap-y-1 text-sm text-slate-600">
            <span class="flex items-center gap-1.5"><?= icon('calendar', 'h-4 w-4') ?><?= fmtDate($r['appt_date']) ?>, <?= fmtTime($r['appt_time']) ?></span>
            <span>Cost: <?= peso($r['est_price']) ?></span>
            <a href="track.php?ref=<?= e($r['ref_code']) ?>" class="font-semibold text-teal-700 hover:underline"><?= e($r['ref_code']) ?></a>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
