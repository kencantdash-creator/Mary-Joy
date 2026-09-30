<?php
require __DIR__ . '/config.php';
$pageTitle = 'Book a Repair'; $active = 'appointment';
$db = db();
$brands = $db->query('SELECT * FROM brands ORDER BY name')->fetchAll();
$services = $db->query('SELECT * FROM services ORDER BY id')->fetchAll();
$errors = [];

function refcode(): string {
    $a = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; $r = 'FG-';
    for ($i = 0; $i < 6; $i++) { $r .= $a[random_int(0, strlen($a) - 1)]; }
    return $r;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = old('name'); $digits = preg_replace('/\D+/', '', old('phone')); $email = old('email');
    $brand = (int)old('brand'); $service = (int)old('service'); $model = old('model'); $issue = old('issue');
    $type = old('visit_type'); $date = old('date'); $time = old('time');

    if (mb_strlen($name) < 2 || mb_strlen($name) > 100) $errors[] = 'Enter your full name.';
    if (strlen($digits) < 10 || strlen($digits) > 13) $errors[] = 'Enter a valid mobile number (10 to 13 digits).';
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email address or leave it blank.';
    $st = $db->prepare('SELECT price FROM prices WHERE brand_id = ? AND service_id = ?');
    $st->execute([$brand, $service]);
    $price = $st->fetchColumn();
    if ($price === false) $errors[] = 'Choose a phone brand and a repair service.';
    if (mb_strlen($model) < 2 || mb_strlen($model) > 80) $errors[] = 'Enter your phone model.';
    if (mb_strlen($issue) < 5 || mb_strlen($issue) > 1000) $errors[] = 'Describe the problem in at least 5 characters.';
    if (!in_array($type, ['Drop-off', 'Repair appointment'], true)) $errors[] = 'Choose a visit type.';

    $d = DateTime::createFromFormat('!Y-m-d', $date);
    if (!$d || $d->format('Y-m-d') !== $date) $errors[] = 'Choose a valid date.';
    elseif ($d < new DateTime('today') || $d > new DateTime('+60 days')) $errors[] = 'Choose a date within the next 60 days.';
    elseif ($d->format('w') === '0') $errors[] = 'We are closed on Sundays. Choose another day.';
    if (!in_array($time, slots(), true)) $errors[] = 'Choose a time slot.';
    elseif ($d && $d->format('Y-m-d') === date('Y-m-d') && $time <= date('H:i')) $errors[] = 'That time has already passed today. Choose a later slot.';

    if (!$errors) {
        $c = $db->prepare("SELECT COUNT(*) FROM appointments WHERE appt_date = ? AND appt_time = ? AND status <> 'Completed'");
        $c->execute([$date, $time . ':00']);
        if ((int)$c->fetchColumn() >= 2) $errors[] = 'That time slot is full. Choose another time.';
    }
    if (!$errors) {
        $ins = $db->prepare('INSERT INTO appointments (ref_code, customer_name, phone, email, brand_id, model, service_id, issue, visit_type, appt_date, appt_time, est_price) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)');
        for ($try = 0; $try < 5; $try++) {
            $ref = refcode();
            try {
                $ins->execute([$ref, $name, $digits, $email ?: null, $brand, $model, $service, $issue, $type, $date, $time . ':00', $price]);
                redirect('track.php?ref=' . $ref . '&new=1');
            } catch (PDOException $ex) {
                if ($ex->getCode() !== '23000') throw $ex;
            }
        }
        $errors[] = 'Could not save your booking. Please try again.';
    }
}
require __DIR__ . '/includes/header.php';
echo pageHead('Book a Repair', 'Choose a date and time to drop off your phone or bring it in for a repair appointment.');
$vt = old('visit_type', 'Drop-off');
?>
<section class="mx-auto grid max-w-6xl gap-6 px-4 py-10 sm:px-6 lg:grid-cols-3">
  <form method="post" class="<?= CARD ?> space-y-5 lg:col-span-2" novalidate>
    <?php if ($errors): ?>
      <div class="rounded-md border border-red-200 bg-red-50 p-4 text-sm text-red-800" role="alert">
        <p class="flex items-center gap-2 font-semibold"><?= icon('alert', 'h-4 w-4') ?>Please fix the following:</p>
        <ul class="mt-2 list-disc space-y-1 pl-6"><?php foreach ($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?></ul>
      </div>
    <?php endif; ?>
    <div class="grid gap-4 sm:grid-cols-2">
      <div><label for="name" class="<?= LABEL ?>">Full name</label><input id="name" name="name" required maxlength="100" value="<?= e(old('name')) ?>" class="<?= FIELD ?>"></div>
      <div><label for="phone" class="<?= LABEL ?>">Mobile number</label><input id="phone" name="phone" type="tel" required maxlength="20" value="<?= e(old('phone')) ?>" placeholder="09171234567" class="<?= FIELD ?>"></div>
      <div class="sm:col-span-2"><label for="email" class="<?= LABEL ?>">Email (optional)</label><input id="email" name="email" type="email" maxlength="120" value="<?= e(old('email')) ?>" class="<?= FIELD ?>"></div>
      <div>
        <label for="brand" class="<?= LABEL ?>">Phone brand</label>
        <select id="brand" name="brand" required class="<?= FIELD ?>"><option value="">Select a brand</option>
          <?php foreach ($brands as $b): ?><option value="<?= $b['id'] ?>" <?= (int)old('brand') === (int)$b['id'] ? 'selected' : '' ?>><?= e($b['name']) ?></option><?php endforeach; ?>
        </select>
      </div>
      <div><label for="model" class="<?= LABEL ?>">Phone model</label><input id="model" name="model" required maxlength="80" value="<?= e(old('model')) ?>" placeholder="Galaxy A54" class="<?= FIELD ?>"></div>
      <div class="sm:col-span-2">
        <label for="service" class="<?= LABEL ?>">Repair needed</label>
        <select id="service" name="service" required class="<?= FIELD ?>"><option value="">Select a repair</option>
          <?php foreach ($services as $s): ?><option value="<?= $s['id'] ?>" <?= (int)old('service') === (int)$s['id'] ? 'selected' : '' ?>><?= e($s['name']) ?></option><?php endforeach; ?>
        </select>
      </div>
      <div class="sm:col-span-2"><label for="issue" class="<?= LABEL ?>">Describe the problem</label><textarea id="issue" name="issue" rows="3" required maxlength="1000" class="<?= FIELD ?>"><?= e(old('issue')) ?></textarea></div>
    </div>
    <fieldset>
      <legend class="<?= LABEL ?>">Visit type</legend>
      <div class="flex flex-col gap-2 sm:flex-row sm:gap-6">
        <?php foreach (['Drop-off', 'Repair appointment'] as $t): ?>
          <label class="flex items-center gap-2"><input type="radio" name="visit_type" value="<?= $t ?>" <?= $vt === $t ? 'checked' : '' ?> class="h-4 w-4 accent-teal-700"><?= $t ?></label>
        <?php endforeach; ?>
      </div>
    </fieldset>
    <div class="grid gap-4 sm:grid-cols-2">
      <div><label for="date" class="<?= LABEL ?>">Date</label><input id="date" name="date" type="date" required min="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d', strtotime('+60 days')) ?>" value="<?= e(old('date')) ?>" class="<?= FIELD ?>"></div>
      <div>
        <label for="time" class="<?= LABEL ?>">Time</label>
        <select id="time" name="time" required class="<?= FIELD ?>"><option value="">Select a time</option>
          <?php foreach (slots() as $sl): ?><option value="<?= $sl ?>" <?= old('time') === $sl ? 'selected' : '' ?>><?= fmtTime($sl) ?></option><?php endforeach; ?>
        </select>
      </div>
    </div>
    <button class="<?= BTN ?> w-full sm:w-auto"><?= icon('check') ?>Confirm booking</button>
  </form>

  <aside class="space-y-4">
    <div class="<?= CARD ?>">
      <h2 class="flex items-center gap-2 font-bold text-slate-900"><?= icon('clock', 'h-5 w-5') ?>Shop hours</h2>
      <?php foreach (SHOP['hours'] as [$d, $h]): ?><p class="mt-2 text-sm text-slate-600"><span class="font-medium text-slate-800"><?= e($d) ?></span><br><?= e($h) ?></p><?php endforeach; ?>
    </div>
    <div class="<?= CARD ?> text-sm text-slate-600">
      <h2 class="flex items-center gap-2 font-bold text-slate-900"><?= icon('info', 'h-5 w-5') ?>Before you come</h2>
      <p class="mt-2">Back up your data and remove any screen lock code you do not want shared. You will get a reference code after booking to track your repair.</p>
    </div>
  </aside>
</section>
<script>
  document.getElementById('date').addEventListener('change', function () {
    const day = this.value ? new Date(this.value + 'T00:00:00').getDay() : -1;
    this.setCustomValidity(day === 0 ? 'We are closed on Sundays.' : '');
    if (day === 0) this.reportValidity();
  });
</script>
<?php require __DIR__ . '/includes/footer.php'; ?>
