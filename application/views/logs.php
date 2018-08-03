<?php
    $page = 'Logs';
    $scripts = [
        base_url('assets/js/logs.js'),
    ];
    $this->load->view('header', array('page' => $page)) ?>
<script>
    var page_type = 'index';
    var api_url = "<?php echo site_url('logs'); ?>";
</script>
<div class="container page-title">
    <h1 class="text-center"></h1>
</div>

<div id="main" class="log-page">
    <div class="header-div row">
        <div class="col-md-4">
            <?php $this->load->view('partials/game-select', array('betweek' => $betweek)); ?>
        </div>
        <div class="setting-header col-md-4">
            <h1 class="text-center">Order Log</h1>
        </div>
        <div class="col-md-4">
        </div>
    </div>
    <div class="logs_tbl-wrapper">
        <table id="logs_tbl" class="display" style="width:100%">
            <thead>
                <tr>
                    <th></th>
                    <th>Order #</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Order Entry Person</th>
                    <th>Investor</th>
                    <th>Sportsbook</th>
                    <th>Action</th>
                    <th>Amount</th>
                </tr>
            </thead>
        </table>
    </div>

<?php $this->load->view('footer', array('scripts' => $scripts)) ?>