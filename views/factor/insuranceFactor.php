<?php
$pageTitle = "فاکتور یدک شاپ";
$iconUrl = 'factor.svg';
$logo = "./assets/img/insurance.png"; 
$title = 'شرق یدک';
$subTitle = 'لوازم یدکی هیوندای و کیا';
$factorType = 'insurance';

require_once './components/header.php';
require_once '../../app/controller/factor/DisplayFactorController.php';
require_once '../../layouts/callcenter/nav.php';
require_once '../../layouts/callcenter/sidebar.php';
?>
<link rel="stylesheet" href="./assets/css/bill.css" />
<script src="./assets/js/html2pdf.js"></script>
<script>
    const factorType = '<?= $factorType ?>';
    let bill_number = null;
    const customerInfo = <?= json_encode($customerInfo) ?>;
    const BillInfo = <?= json_encode($BillInfo) ?>;
    const billItems = <?= ($billItems) ?>;
</script>
<div id="bill_body_pdf" class="bill insuranceBill bill_body_pdf">
    <?php
    require_once './components/bill/header.php';
    require_once './components/bill/body.php';
    require_once './components/bill/insuranceDetails.php';
    require_once './components/bill/actionMenu.php';
    ?>
</div>

<script src="./assets/js/displayFactor/factor.js"></script>
<?php
require_once './components/footer.php';
