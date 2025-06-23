            </div><!-- End content-container -->
        </div><!-- End main-content -->
    </div><!-- End admin-panel -->

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <p> <?php echo date('Y'); ?> Tây Bắc Store </p>
        </div>
    </footer>

    <?php if (isset($extra_js)): ?>
        <?php foreach ($extra_js as $js): ?>
            <script src="<?php echo $js; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html> 