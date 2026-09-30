</main>
<footer class="border-t border-slate-200 bg-white">
  <div class="mx-auto grid max-w-6xl gap-6 px-4 py-8 text-sm text-slate-600 sm:px-6 md:grid-cols-3">
    <div>
      <p class="font-bold text-slate-900">Fix &amp; Go</p>
      <p class="mt-1">Online booking and repair tracking for <?= e(SHOP['name']) ?>.</p>
    </div>
    <div class="space-y-1">
      <p class="flex items-center gap-2"><?= icon('pin', 'h-4 w-4') ?><?= e(SHOP['address']) ?></p>
      <p class="flex items-center gap-2"><?= icon('phone', 'h-4 w-4') ?><?= e(SHOP['phone']) ?></p>
    </div>
    <div>
      <?php foreach (SHOP['hours'] as [$d, $h]): ?><p><span class="font-medium text-slate-800"><?= e($d) ?>:</span> <?= e($h) ?></p><?php endforeach; ?>
    </div>
  </div>
  <p class="border-t border-slate-200 py-4 text-center text-xs text-slate-500">&copy; <?= date('Y') ?> Fix &amp; Go. Prepared for <?= e(SHOP['name']) ?>.</p>
</footer>
<script>
  const b = document.getElementById('menuBtn'), m = document.getElementById('mobileMenu');
  b.addEventListener('click', () => {
    const open = m.classList.toggle('hidden') === false;
    b.setAttribute('aria-expanded', open);
    document.getElementById('icoOpen').classList.toggle('hidden', open);
    document.getElementById('icoClose').classList.toggle('hidden', !open);
  });
</script>
</body>
</html>
