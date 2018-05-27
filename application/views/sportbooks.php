<?php
    $page = 'sportbooks';
    $scripts = [
        base_url('assets/js/sportbooks.js'),
    ];
    $this->load->view('header', array('page' => $page)) ?>
<script>
    var api_url = "<?php echo site_url('sportbooks'); ?>";
</script>
<div class="container page-title">
    <h1 class="text-center">SportBooks</h1>
</div>

<div id="main" class="sportbook-page">
    <div class="row">
        <div class="col-md-12 text-right mb-3">
            <a href="<?=site_url('sportbooks');?>/add" name="edit_submit" class="btn btn-success">Add New</a>
        </div>
    </div>
    <table id="sportbooks" class="display" style="width:100%">
        <thead>
            <tr>
                <th></th>
                <th>Title</th>
                <th>URL</th>
                <th>City</th>
                <th>State</th>
                <th>Country</th>
                <th>Contact Name</th>
                <th>Phone Number</th>
                <th></th>
            </tr>
        </thead>
    </table>
</div>
<?php $this->load->view('footer', array('scripts' => $scripts)) ?>