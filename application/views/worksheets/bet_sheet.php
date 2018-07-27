<?php
    $page = 'worksheets';
    $scripts = [
        base_url('assets/js/worksheet.js'),
    ];
    $this->load->view('header', array('page' => $page)) ?>
<script>
    var betweek = <?php echo $betweek ?>;
    var settingId = <?php echo $settingId;?>;
    var api_url = "<?php echo site_url('worksheets'); ?>";
    var pageType = "<?php echo $pageType; ?>";
    var pageTitle = "<?php echo $pageTitle; ?>";
</script>
<div class="container page-title">
    <h1 class="text-center" id="pageTitle"></h1>
</div>

<div id="main" class="worksheets">
    <div class="header-div">
        <div class="col-md-4">
            <div class="game-week-select-div">
                <label>Bet Day</label>
                <i class="fa fa-chevron-left bet-week-prev"></i>
                <select class="select2 game-week-select" name="game-week-select" onchange="initPage()">
                    <?php for($i=1; $i<=60; $i++) {?>
                    <option <?php if($i == $betweek) echo "selected";?> value="<?php echo $i?>"><?php echo $i?></option>
                    <?php }?>
                </select>
                <i class="fa fa-chevron-right bet-week-next"></i>
            </div>
        </div>
        <div class="col-md-4">
        </div>
        <div class="save-button-div col-md-4">
            <a class="btn btn-success btn-lg " href="<?=site_url('worksheets')?>/downloadPDF" target="_blank">Download PDF</a>
        </div>
    </div>
    <div class="content-div">
        <div id="bet_sheet">
        </div>
    </div>
</div>
<?php $this->load->view('footer', array('scripts' => $scripts)) ?>