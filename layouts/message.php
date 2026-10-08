<?php if (isset($_SESSION['flash_message'])): 
    $message = $_SESSION['flash_message'];
    unset($_SESSION['flash_message']);
?>
    <div class="section-messages" id="flash-message">
        <h4><?= htmlspecialchars($message) ?></h4>
    </div>
<?php endif; ?>