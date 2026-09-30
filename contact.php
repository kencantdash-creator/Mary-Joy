<?php
require __DIR__ . '/config.php';
$pageTitle = 'Contact & Location'; $active = 'contact';
require __DIR__ . '/includes/header.php';
echo pageHead('Contact & Location', 'Visit the shop, call us, or send an email.');
$q = urlencode(SHOP['address']);
?>
<section class="mx-auto grid max-w-6xl gap-6 px-4 py-10 sm:px-6 lg:grid-cols-2">
  <div class="space-y-4">
    <div class="<?= CARD ?> space-y-4">
      <p class="flex items-start gap-3"><?= icon('pin', 'mt-0.5 h-5 w-5 text-teal-700') ?><span><span class="block font-semibold text-slate-900"><?= e(SHOP['name']) ?></span><?= e(SHOP['address']) ?></span></p>
      <p class="flex items-center gap-3"><?= icon('phone', 'h-5 w-5 text-teal-700') ?><a href="tel:<?= e(preg_replace('/\D+/', '', SHOP['phone'])) ?>" class="hover:underline"><?= e(SHOP['phone']) ?></a></p>
      <p class="flex items-center gap-3"><?= icon('mail', 'h-5 w-5 text-teal-700') ?><a href="mailto:<?= e(SHOP['email']) ?>" class="hover:underline"><?= e(SHOP['email']) ?></a></p>
      <a href="https://www.google.com/maps/search/?api=1&amp;query=<?= $q ?>" target="_blank" rel="noopener" class="<?= BTN ?>"><?= icon('pin') ?>Get directions</a>
    </div>
    <div class="<?= CARD ?>">
      <h2 class="flex items-center gap-2 font-bold text-slate-900"><?= icon('clock') ?>Opening hours</h2>
      <table class="mt-3 w-full text-sm"><tbody class="divide-y divide-slate-200">
        <?php foreach (SHOP['hours'] as [$d, $h]): ?><tr><th class="py-2 text-left font-medium text-slate-800"><?= e($d) ?></th><td class="py-2 text-right text-slate-600"><?= e($h) ?></td></tr><?php endforeach; ?>
      </tbody></table>
    </div>
  </div>
  <div class="overflow-hidden rounded-lg border border-slate-200 bg-white">
    <iframe title="Shop location map" class="h-80 w-full lg:h-full lg:min-h-[24rem]" loading="lazy" src="https://maps.google.com/maps?q=<?= $q ?>&amp;output=embed"></iframe>
  </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
