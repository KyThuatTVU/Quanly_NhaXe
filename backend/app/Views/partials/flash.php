<?php
$success = flash_message('success');
$error = flash_message('error');
?>
<?php if ($success !== null): ?>
    <div class="container"><div class="alert alert-success"><?= e($success) ?></div></div>
<?php endif; ?>
<?php if ($error !== null): ?>
    <div class="container"><div class="alert alert-danger"><?= e($error) ?></div></div>
<?php endif; ?>
