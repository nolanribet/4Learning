<?php
// =============================================================
//  footer.php — Pied de page commun inclus dans chaque page
// =============================================================
$base = $base_path ?? '';
?>
<footer class="footer">
    <div class="container">
        <img src="<?= $base ?>assets/img/Logo 4Learn .png" alt="4Learning" class="footer__logo">
        <p>© 2026 4Learning — Groupe 11, L1 MISPI — Université Savoie Mont Blanc</p>
        <nav class="footer__links">
            <a href="<?= $base ?>pages/questions.php">Questions</a>
            <a href="<?= $base ?>pages/classement.php">Classement</a>
            <a href="<?= $base ?>pages/profil.php">Profil</a>
        </nav>
    </div>
</footer>

<?php if (!empty($page_script)): ?>
<script src="<?= htmlspecialchars($page_script) ?>"></script>
<?php endif; ?>
</body>
</html>
