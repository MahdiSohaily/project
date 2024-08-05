<?php
$pageTitle = "داشبورد";
$iconUrl = 'logo.jpg';
require_once './components/header.php';
require_once '../../layouts/inventory/nav.php';
require_once '../../layouts/inventory/sidebar.php';
?>
<section class="w-full h-screen">
    <iframe id="file-frame" border="0" width="100%" height="100%" frameborder="no" framespacing="0" src="./assets/excel/ALL.htm" />
    </iframe>
</section>
<?php
require_once './components/footer.php';
?>