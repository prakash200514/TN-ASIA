<?php 
// includes/footer.php
if (!isset($hideContactFooter) || !$hideContactFooter) {
    include __DIR__ . '/contact_footer.php';
}
?>
  <!-- Lenis Smooth Scroll JS -->
  <script src="https://cdn.jsdelivr.net/npm/@studio-freight/lenis@1.0.42/dist/lenis.min.js"></script>
  <!-- Bootstrap 5 JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <!-- TNSTC Main JS -->
  <script src="<?= APP_URL ?>/assets/js/main.js"></script>
  <?php if (!empty($extraScripts)) echo $extraScripts; ?>
</body>
</html>
