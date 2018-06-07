<?php
    $page = 'orders';
    $scripts = [
        base_url('assets/js/orders.js'),
    ];
    $this->load->view('header', array('page' => $page)) ?>
<script>
    var page_type = 'index';
    var api_url = "<?php echo site_url('orders'); ?>";
</script>
<div class="container page-title">
    <h1 class="text-center">Orders</h1>
</div>

<div id="main" class="order-page">

    <table id="investors_tbl" class="display" style="width:100%">
        <thead>
            <tr>
                <th></th>
                <th>Full Name</th>
                <th># Bets</th>
                <th># Accounts</th>
                <th></th>
            </tr>
        </thead>
    </table>

<?php $this->load->view('footer', array('scripts' => $scripts)) ?>