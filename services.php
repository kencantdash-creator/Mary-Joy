<?php
require __DIR__ . '/config.php';
$pageTitle = 'Repair Services'; $active = 'services';
$db = db();
$services = $db->query('SELECT * FROM services ORDER BY id')->fetchAll();
$brands = $db->query('SELECT * FROM brands ORDER BY name')->fetchAll();
$m = [];
foreach ($db->query('SELECT brand_id, service_id, price, est_hours FROM prices') as $r) { $m[$r['brand_id']][$r['service_id']] = $r; }
require __DIR__ . '/includes/header.php';
echo pageHead('Repair Services', 'The most common phone repairs we handle, with a price guide for each supported brand.');
?>
<section class="mx-auto max-w-6xl px-4 py-10 sm:px-6">
  <div class="grid gap-5 md:grid-cols-2">
    <?php foreach ($services as $s): ?>
      <article class="<?= CARD ?>">
        <div class="flex items-start gap-4">
          <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-md bg-teal-50 text-teal-700"><?= icon($s['icon'], 'h-6 w-6') ?></span>
          <div>
            <h2 class="text-lg font-bold text-slate-900"><?= e($s['name']) ?></h2>
            <p class="mt-1 text-slate-600"><?= e($s['description']) ?></p>
          </div>
        </div>
        <div class="mt-4 flex flex-wrap gap-3">
          <a href="estimator.php?service=<?= (int)$s['id'] ?>" class="<?= BTN2 ?> !py-2 text-sm"><?= icon('calc', 'h-4 w-4') ?>Get estimate</a>
          <a href="appointment.php?service=<?= (int)$s['id'] ?>" class="<?= BTN ?> !py-2 text-sm"><?= icon('calendar', 'h-4 w-4') ?>Book this repair</a>
        </div>
      </article>
    <?php endforeach; ?>
  </div>

  <h2 class="mt-12 text-xl font-bold text-slate-900">Price guide</h2>
  <p class="mt-1 text-sm text-slate-600">Estimates only. The final price is confirmed after inspection.</p>
  <div class="mt-4 overflow-x-auto rounded-lg border border-slate-200 bg-white">
    <table class="w-full min-w-[640px] text-left text-sm">
      <thead class="bg-slate-100 text-slate-700">
        <tr><th class="px-4 py-3 font-semibold">Brand</th><?php foreach ($services as $s): ?><th class="px-4 py-3 font-semibold"><?= e($s['name']) ?></th><?php endforeach; ?></tr>
      </thead>
      <tbody class="divide-y divide-slate-200">
        <?php foreach ($brands as $b): ?>
          <tr>
            <th class="px-4 py-3 font-semibold text-slate-900"><?= e($b['name']) ?></th>
            <?php foreach ($services as $s): $c = $m[$b['id']][$s['id']] ?? null; ?>
              <td class="px-4 py-3"><?= $c ? peso($c['price']) . '<span class="block text-xs text-slate-500">' . hoursLabel($c['est_hours']) . '</span>' : 'N/A' ?></td>
            <?php endforeach; ?>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
