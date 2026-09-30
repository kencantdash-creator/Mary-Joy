<?php
require __DIR__ . '/config.php';
$pageTitle = 'Price Estimator'; $active = 'estimator';
$db = db();
$brands = $db->query('SELECT * FROM brands ORDER BY name')->fetchAll();
$services = $db->query('SELECT * FROM services ORDER BY id')->fetchAll();
$bid = (int)old('brand'); $sid = (int)old('service');
$res = null; $submitted = isset($_GET['brand']) || isset($_GET['service']);
if ($bid && $sid) {
    $st = $db->prepare('SELECT p.price, p.est_hours, b.name AS brand, s.name AS service FROM prices p JOIN brands b ON b.id = p.brand_id JOIN services s ON s.id = p.service_id WHERE p.brand_id = ? AND p.service_id = ?');
    $st->execute([$bid, $sid]);
    $res = $st->fetch() ?: null;
}
require __DIR__ . '/includes/header.php';
echo pageHead('Repair Price Estimator', 'Choose your phone brand and the repair you need to see the expected cost and service time.');
?>
<section class="mx-auto grid max-w-6xl gap-6 px-4 py-10 sm:px-6 md:grid-cols-2">
  <form method="get" class="<?= CARD ?> space-y-4">
    <div>
      <label for="brand" class="<?= LABEL ?>">Phone brand</label>
      <select id="brand" name="brand" required class="<?= FIELD ?>">
        <option value="">Select a brand</option>
        <?php foreach ($brands as $b): ?><option value="<?= $b['id'] ?>" <?= $bid == $b['id'] ? 'selected' : '' ?>><?= e($b['name']) ?></option><?php endforeach; ?>
      </select>
    </div>
    <div>
      <label for="service" class="<?= LABEL ?>">Repair needed</label>
      <select id="service" name="service" required class="<?= FIELD ?>">
        <option value="">Select a repair</option>
        <?php foreach ($services as $s): ?><option value="<?= $s['id'] ?>" <?= $sid == $s['id'] ? 'selected' : '' ?>><?= e($s['name']) ?></option><?php endforeach; ?>
      </select>
    </div>
    <button class="<?= BTN ?> w-full"><?= icon('calc') ?>Calculate estimate</button>
  </form>

  <div class="<?= CARD ?>">
    <?php if ($res): ?>
      <p class="text-sm text-slate-600"><?= e($res['brand']) ?> &middot; <?= e($res['service']) ?></p>
      <p class="mt-2 text-4xl font-bold text-slate-900"><?= peso($res['price']) ?></p>
      <p class="mt-3 flex items-center gap-2 text-slate-700"><?= icon('clock', 'h-4 w-4') ?>Service time: about <?= hoursLabel($res['est_hours']) ?></p>
      <p class="mt-3 flex items-start gap-2 rounded-md bg-slate-100 p-3 text-sm text-slate-600"><?= icon('info', 'mt-0.5 h-4 w-4') ?>This is an estimate. The final price is confirmed after the technician inspects your phone.</p>
      <a href="appointment.php?brand=<?= $bid ?>&amp;service=<?= $sid ?>" class="<?= BTN ?> mt-5 w-full"><?= icon('calendar') ?>Book this repair</a>
    <?php elseif ($submitted): ?>
      <p class="flex items-start gap-2 text-red-700"><?= icon('alert', 'mt-0.5 h-5 w-5') ?>Select both a brand and a repair to see an estimate.</p>
    <?php else: ?>
      <div class="flex h-full min-h-[10rem] flex-col items-center justify-center text-center text-slate-500">
        <?= icon('calc', 'h-10 w-10') ?><p class="mt-3">Your estimate will appear here.</p>
      </div>
    <?php endif; ?>
  </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
