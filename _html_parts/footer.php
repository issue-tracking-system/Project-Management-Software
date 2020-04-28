<?php
if (!defined('APPLICATION_LOADED') || !APPLICATION_LOADED) {
    die('No direct script access.');
}
if (isset($_SESSION['logged']) || isset($_COOKIE['logged'])) {
    ?>
    <footer class="blue-gradient">
        <div>
            Project Management Software  
            &middot;
            <a href="https://github.com/issue-tracking-system/Project-Management-Software/issues" target="_blank">Report a Bug <i class="fa fa-bug" aria-hidden="true"></i></a>
        </div>
        <div class="pm"><?= $_SERVER['HTTP_HOST'] ?></div>
    </footer>
    </div>
<?php } ?>
<script src="<?= base_url('assets/bootstrap-select-1.9.4/js/bootstrap-select.min.js') ?>"></script>
<script src="<?= base_url('assets/bootstrap-select-1.9.4/js/i18n/defaults-en_US.min.js') ?>"></script>
<script src="<?= base_url('assets/js/bootstrap.min.js') ?>"></script>
<script src="<?= base_url('assets/js/placeholders.min.js') ?>"></script>
<script>
    $(function () {
        $('[data-toggle="popover"]').popover();
    })
</script>
<?php
if (DEBUG_MODE === true) {
    ?>
    <div id="debugmode" class="container">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th colspan="2" class="text-center">MySQL Queries</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($this->connections as $c) {
                        ?>
                        <tr class="warning">
                            <td><?= $c['time'] ?></td>
                            <td><?= $c['result'] ?></td>
                        </tr>
                        <?php
                    }
                    foreach ($this->queries as $q) {
                        ?>
                        <tr class="success">
                            <td><?= $q['time'] ?></td>
                            <td><?= htmlspecialchars($q['query']) ?></td>
                        </tr>
                    <?php }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
<?php }
?>
</body>
</html>
