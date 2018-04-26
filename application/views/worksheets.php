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
</script>
<div class="container title">
    <h1 class="text-center">Work Sheet</h1>
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

    <div class="alert-div">
        <div class="alert alert-success notification-box">
          <strong>Notification! </strong>Data is successfully updated.
        </div>
    </div>
    <div class="content-div">
        <div class="tab-content">
            <div class="tab-pane active" id="bet_summary" role="tabpanel">
            </div>
            <div class="tab-pane" id="bets" role="tabpanel">
                <div class="setting_div">
                    <div class="sheet setting_sheet" data-type="setting_sheet">
                    </div>
                    <div class="sheet setting_sheet1" data-type="setting_sheet1">
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="all-picks">
                    <div class="sheet" data-type="bets">
                    </div>
                </div>
            </div>
            <div class="tab-pane" id="bet_sheet" role="tabpanel">
            </div>
            <div class="tab-pane" id="bets_pick" role="tabpanel">
                <div class="sheet" data-type="bets_pick">
                </div>
            </div>
        </div>
    </div>
    <div class="footer-div">
        
    </div>
    <ul id="sheets" class="nav nav-tabs bottom-sheet" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" data-type="bet_summary" href="#bet_summary" aria-selected="true">Bet Summary</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" data-type="bets" href="#bets" aria-selected="true">Bets</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" data-type="bet_sheet" href="#bet_sheet" aria-selected="true">Bet Instruction Sheets PR Parl</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" data-type="bets_pick" href="#bets_pick" aria-selected="true">Bet Instruction Sheets Pick</a>
        </li>
    </ul>
</div>
<?php $this->load->view('footer', array('scripts' => $scripts)) ?>