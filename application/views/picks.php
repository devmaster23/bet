<?php
    $page = 'picks';
    $scripts = [
        base_url('assets/js/picks.sheet.js'),
    ];
    $this->load->view('header', array('page' => $page)) ?>
<script>
    var betweek = <?php echo $betweek ?>;
    var api_url = "<?php echo site_url('picks'); ?>";
    var pageType = "<?php echo $pageType; ?>";
    var pageTitle = "<?php echo $pageTitle; ?>";
</script>
<div class="container page-title">
    <h1 class="text-center" id="pageTitle"></h1>
</div>

<div id="main">
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
            <button type="button" class="btn btn-success btn-lg enter-pick_save-button" onClick="updateTable()">Update</button>
        </div>
    </div>

    <div class="content-div">
        <div class="tab-content">
            <div class="<?php echo $pageType == 'all_picks' ? 'all-picks' : 'pick-game'?>" id="<?php echo $pageType?>">
                <div class="sheet">
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('footer', array('scripts' => $scripts)) ?>