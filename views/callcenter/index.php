<?php
$pageTitle = "مرکز تماس";
$iconUrl = 'callcenter.svg';
require_once './components/header.php';
require_once '../../app/controller/callcenter/DashboardController.php';
require_once '../../app/controller/components/DashboardCardsController.php';
require_once '../../layouts/callcenter/nav.php';
require_once '../../layouts/callcenter/sidebar.php';
$dateTime = jdate('Y-m-d')
?>
<style>
    body {
        background-color: #F3F4F6 !important;
    }

    .bg-gradient::after {
        background: radial-gradient(600px circle at var(--mouse-x) var(--mouse-y), rgba(0, 0, 0, 1), transparent 20%);
    }
</style>

<!-- ------------------------------------------------ Dashboard card section ---------------------------------------------------- -->
<section class="mx-auto px-5 pb-5 bg-gray-100">
    <?php
    include_once '../components/dashboardCards.php';
    ?>
</section>

<!-- ---------------------------------------------- Dashboard users and calender ---------------------------------------------------- -->
<section class="mx-auto rtl bg-gray-100 mb-5">
    <div class="grid grid-cols-1 lg:grid-cols-2 px-5 gap-5">
        <?php
        include_once '../components/callAgency.php';
        include_once '../components/calender.php';
        ?>
    </div>
</section>
<?php
require_once './components/footer.php';
