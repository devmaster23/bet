<?php
    $page = 'exceptions';
    $scripts = [
    ];
    $this->load->view('header', array('page' => $page)) ?>
<script>
    var api_url = "<?php echo site_url('exceptions'); ?>";
</script>
<div class="container page-title">
    <h1 class="text-center"><?=$message?></h1>
</div>

<?php $this->load->view('footer', array('scripts' => $scripts)) ?>