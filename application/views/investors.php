<?php
    $page = 'investors';
    $scripts = [
        base_url('assets/js/investors.js'),
    ];
    $this->load->view('header', array('page' => $page)) ?>
<script>
    var page_type = 'index';
    var api_url = "<?php echo site_url('investors'); ?>";
</script>
<div class="container page-title">
    <h1 class="text-center"><?php echo $pageTitle;?></h1>
</div>

<div id="main" class="investor-page">
    <div class="row">
        <div class="col-md-12 text-right mb-3">
            <a href="<?=site_url('investors');?>/add" name="edit_submit" class="btn btn-success">Add New</a>
        </div>
    </div>

    <table id="investors_tbl" class="display" style="width:100%">
        <thead>
            <tr>
                <th></th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Phone Number</th>
                <th width="150px"># Sportbook Accounts</th>
                <th width="150px">Opening Balance ($)</th>
                <th width="150px">Current Balance ($)</th>
                <th>Group</th>
                <th>IP Address</th>
                <th></th>
            </tr>
        </thead>
    </table>

<?php $this->load->view('footer', array('scripts' => $scripts)) ?>