<?php
require __DIR__ . '/config.php';
$pageTitle = 'Device Information'; $active = 'device';
$db = db();
$brands = $db->query('SELECT * FROM brands ORDER BY name')->fetchAll();
$cats = [
    'Cracked or unresponsive screen' => [1, 'Stop using the phone if glass is loose, and avoid pressing on the cracks.'],
    'Battery drains fast or phone shuts off' => [2, 'Lower screen brightness and avoid charging overnight until it is checked.'],
    'Not charging or loose charging port' => [3, 'Try a different cable and adapter first, and do not force the connector.'],
    'Freezing, slow or software errors' => [4, 'Back up your files if you can before you bring the phone in.'],
    'Water damage' => [0, 'Turn the phone off, do not charge it and do not use rice or heat.'],
    'Something else' => [0, 'Describe what happens and when it started so the technician can check it.'],
];
$ages = ['Less than 1 year', '1 to 2 years', '2 to 4 years', 'More than 4 years'];
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $brand = (int)old('brand'); $model = old('model'); $age = old('age'); $cat = old('category'); $desc = old('description');
    $phone = preg_replace('/\D+/', '', old('phone'));
    $chk = $db->prepare('SELECT COUNT(*) FROM brands WHERE id = ?'); $chk->execute([$brand]);
    if (!$chk->fetchColumn()) $errors[] = 'Choose a phone brand.';
    if (mb_strlen($model) < 2 || mb_strlen($model) > 80) $errors[] = 'Enter your phone model.';
    if (!in_array($age, $ages, true)) $errors[] = 'Choose how old the phone is.';
    if (!isset($cats[$cat])) $errors[] = 'Choose the type of problem.';
    if (mb_strlen($desc) < 5 || mb_strlen($desc) > 1000) $errors[] = 'Describe the problem in at least 5 characters.';
    if ($phone !== '' && (strlen($phone) < 10 || strlen($phone) > 13)) $errors[] = 'Enter a valid mobile number or leave it blank.';
    if (!$errors) {
        $db->prepare('INSERT INTO device_reports (brand_id, model, age, category, description, phone) VALUES (?,?,?,?,?,?)')
           ->execute([$brand, $model, $age, $cat, $desc, $phone ?: null]);
        $_SESSION['device_saved'] = ['brand' => $brand, 'model' => $model, 'cat' => $cat];
        redirect('device.php?saved=1');
    }
}
$saved = null;
if (isset($_GET['saved']) && isset($_SESSION['device_saved'])) { $saved = $_SESSION['device_saved']; unset($_SESSION['device_saved']); }
require __DIR__ . '/includes/header.php';
echo pageHead('Device Information', 'Tell us your phone model and what is wrong so the technician is prepared before you arrive.');
?>
<section class="mx-auto max-w-3xl px-4 py-10 sm:px-6">
  <?php if ($saved): $q = ['brand' => $saved['brand'], 'model' => $saved['model']]; if ($cats[$saved['cat']][0]) $q['service'] = $cats[$saved['cat']][0]; ?>
    <div class="<?= CARD ?> border-green-200 bg-green-50">
      <h2 class="flex items-center gap-2 text-lg font-bold text-green-900"><?= icon('done') ?>Device details received</h2>
      <p class="mt-2 text-green-900"><span class="font-medium">Tip:</span> <?= e($cats[$saved['cat']][1]) ?></p>
      <div class="mt-4 flex flex-col gap-3 sm:flex-row">
        <a href="appointment.php?<?= e(http_build_query($q)) ?>" class="<?= BTN ?>"><?= icon('calendar') ?>Book a repair for this phone</a>
        <a href="device.php" class="<?= BTN2 ?>">Add another device</a>
      </div>
    </div>
  <?php else: ?>
  <form method="post" class="<?= CARD ?> space-y-4" novalidate>
    <?php if ($errors): ?>
      <div class="rounded-md border border-red-200 bg-red-50 p-4 text-sm text-red-800" role="alert">
        <p class="flex items-center gap-2 font-semibold"><?= icon('alert', 'h-4 w-4') ?>Please fix the following:</p>
        <ul class="mt-2 list-disc space-y-1 pl-6"><?php foreach ($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?></ul>
      </div>
    <?php endif; ?>
    <div class="grid gap-4 sm:grid-cols-2">
      <div><label for="brand" class="<?= LABEL ?>">Phone brand</label>
        <select id="brand" name="brand" required class="<?= FIELD ?>"><option value="">Select a brand</option>
          <?php foreach ($brands as $b): ?><option value="<?= $b['id'] ?>" <?= (int)old('brand') === (int)$b['id'] ? 'selected' : '' ?>><?= e($b['name']) ?></option><?php endforeach; ?>
        </select></div>
      <div><label for="model" class="<?= LABEL ?>">Phone model</label><input id="model" name="model" required maxlength="80" value="<?= e(old('model')) ?>" class="<?= FIELD ?>"></div>
      <div><label for="age" class="<?= LABEL ?>">Phone age</label>
        <select id="age" name="age" required class="<?= FIELD ?>"><option value="">Select</option>
          <?php foreach ($ages as $a): ?><option <?= old('age') === $a ? 'selected' : '' ?>><?= e($a) ?></option><?php endforeach; ?>
        </select></div>
      <div><label for="category" class="<?= LABEL ?>">Type of problem</label>
        <select id="category" name="category" required class="<?= FIELD ?>"><option value="">Select</option>
          <?php foreach ($cats as $c => $_): ?><option <?= old('category') === $c ? 'selected' : '' ?>><?= e($c) ?></option><?php endforeach; ?>
        </select></div>
      <div class="sm:col-span-2"><label for="description" class="<?= LABEL ?>">Describe the problem</label><textarea id="description" name="description" rows="4" required maxlength="1000" class="<?= FIELD ?>"><?= e(old('description')) ?></textarea></div>
      <div class="sm:col-span-2"><label for="phone" class="<?= LABEL ?>">Mobile number (optional)</label><input id="phone" name="phone" type="tel" maxlength="20" value="<?= e(old('phone')) ?>" class="<?= FIELD ?>"></div>
    </div>
    <button class="<?= BTN ?>"><?= icon('check') ?>Send device details</button>
  </form>
  <?php endif; ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
