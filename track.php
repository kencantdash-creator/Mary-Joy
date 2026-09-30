<?php
require __DIR__ . '/config.php';
$pageTitle = 'Track Repair'; $active = 'track';
$ref = strtoupper(preg_replace('/[^A-Za-z0-9-]/', '', old('ref')));
$row = null;
if ($ref !== '') {
    $st = db()->prepare('SELECT a.*, b.name AS brand, s.name AS service FROM appointments a JOIN brands b ON b.id = a.brand_id JOIN services s ON s.id = a.service_id WHERE a.ref_code = ?');
    $st->execute([$ref]);
    $row = $st->fetch() ?: null;
}
$isNew = ($_GET['new'] ?? '') === '1' && $row;
$icons = ['Scheduled' => 'calendar', 'Waiting for Inspection' => 'clip', 'Being Repaired' => 'wrench', 'Ready for Pickup' => 'box', 'Completed' => 'done'];
$msg = ['Scheduled' => 'Your visit is booked. Bring your phone at the scheduled time.',
        'Waiting for Inspection' => 'We have your phone. A technician will inspect it soon.',
        'Being Repaired' => 'A technician is working on your phone right now.',
        'Ready for Pickup' => 'Your phone is ready. Visit the shop during opening hours to collect it.',
        'Completed' => 'This repair is complete. Thank you for choosing us.'];
require __DIR__ . '/includes/header.php';
echo pageHead('Repair Status Tracker', 'Enter your reference code to see whether your phone is waiting, being repaired or ready for pickup.');
?>
<section class="mx-auto max-w-3xl px-4 py-10 sm:px-6">
  <form method="get" class="<?= CARD ?> flex flex-col gap-3 sm:flex-row sm:items-end">
    <div class="flex-1"><label for="ref" class="<?= LABEL ?>">Reference code</label>
      <input id="ref" name="ref" required maxlength="12" value="<?= e($ref) ?>" placeholder="FG-XXXXXX" class="<?= FIELD ?> uppercase"></div>
    <button class="<?= BTN ?>"><?= icon('search') ?>Check status</button>
  </form>

  <?php if ($isNew): ?>
    <div class="mt-6 flex items-start gap-3 rounded-md border border-green-200 bg-green-50 p-4 text-green-900" role="status">
      <?= icon('done', 'mt-0.5 h-5 w-5') ?><p><span class="font-semibold">Booking confirmed.</span> Save your reference code <span class="font-bold"><?= e($row['ref_code']) ?></span>. You need it to track this repair.</p>
    </div>
  <?php endif; ?>

  <?php if ($ref !== '' && !$row): ?>
    <p class="mt-6 flex items-start gap-2 rounded-md border border-red-200 bg-red-50 p-4 text-red-800"><?= icon('alert', 'mt-0.5 h-5 w-5') ?>No repair found for that code. Check the code and try again.</p>
  <?php elseif ($row): $idx = array_search($row['status'], statuses(), true); $pct = (int)round(($idx + 1) / count(statuses()) * 100); ?>
    <div class="<?= CARD ?> mt-6">
      <div class="flex flex-wrap items-start justify-between gap-3">
        <div><p class="text-sm text-slate-500">Reference code</p><p class="text-2xl font-bold text-slate-900"><?= e($row['ref_code']) ?></p></div>
        <?= statusBadge($row['status']) ?>
      </div>
      <p class="mt-4 text-slate-700"><?= e($msg[$row['status']]) ?></p>
      <div class="mt-5 h-2 overflow-hidden rounded-full bg-slate-200" role="progressbar" aria-valuenow="<?= $pct ?>" aria-valuemin="0" aria-valuemax="100"><div class="h-full bg-teal-700" style="width: <?= $pct ?>%"></div></div>
      <ol class="mt-5 grid gap-3 sm:grid-cols-5">
        <?php foreach (statuses() as $i => $s): $done = $i < $idx; $cur = $i === $idx; ?>
          <li class="flex items-center gap-3 sm:flex-col sm:text-center">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full <?= $cur ? 'bg-teal-700 text-white' : ($done ? 'bg-teal-100 text-teal-800' : 'bg-slate-200 text-slate-500') ?>"><?= icon($done ? 'check' : $icons[$s], 'h-5 w-5') ?></span>
            <span class="text-sm <?= $cur ? 'font-semibold text-slate-900' : 'text-slate-600' ?>"><?= e($s) ?></span>
          </li>
        <?php endforeach; ?>
      </ol>
      <dl class="mt-6 grid gap-4 border-t border-slate-200 pt-5 text-sm sm:grid-cols-2">
        <div><dt class="text-slate-500">Device</dt><dd class="font-medium text-slate-900"><?= e($row['brand'] . ' ' . $row['model']) ?></dd></div>
        <div><dt class="text-slate-500">Repair</dt><dd class="font-medium text-slate-900"><?= e($row['service']) ?></dd></div>
        <div><dt class="text-slate-500">Visit</dt><dd class="font-medium text-slate-900"><?= e($row['visit_type']) ?>, <?= fmtDate($row['appt_date']) ?> at <?= fmtTime($row['appt_time']) ?></dd></div>
        <div><dt class="text-slate-500">Estimated cost</dt><dd class="font-medium text-slate-900"><?= peso($row['est_price']) ?></dd></div>
      </dl>
    </div>
  <?php endif; ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
