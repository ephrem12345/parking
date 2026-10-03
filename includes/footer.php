    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= $config['base_url'] ?>/assets/js/app.js"></script>
    <?php if (!empty($customScripts)): ?>
        <?= $customScripts ?>
    <?php endif; ?>
</body>
</html>
