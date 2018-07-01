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
    <h1 class="text-center" id="pageTitle"><?php echo $pageTitle?></h1>
</div>

<div id="main" class="worksheets">
    <div class="header-div">
        <div class="game-week-select-div">
            <label>Bet Day</label>
            <select class="select2 game-week-select" name="game-week-select" onchange="initPage()">
                <?php for($i=1; $i<=60; $i++) {?>
                <option <?php if($i == $betweek) echo "selected";?> value="<?php echo $i?>"><?php echo $i?></option>
                <?php }?>
            </select>
        </div>
        <div class="save-button-div">
            <button type="button" class="btn btn-success enter-pick_save-button" onClick="updateTable()">Update</button>
        </div>
    </div>
    <div class="control-header">
        <p><?=$setting['title']?></p>
    </div>
    <div class="content-div">
        <div id="bets_custom">
            <div id="bets_custom_inner-wrapper">
                <div id="bets_custom_inner">
                
                </div>
                <div class="clearfix"></div>
                <div class="action-div">
                    <button type="button" class="btn btn-success new_bet_setting">+ New</button>
                </div>
            </div>
            <div class="sheet" data-type="bets_custom_sheet">
            
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('footer', array('scripts' => $scripts)) ?>