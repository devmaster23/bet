<?php
    $page = 'picks';
    $scripts = [
        base_url('assets/js/picks.sheet.js'),
    ];
    $this->load->view('header', array('page' => $page)) ?>
<script>
    var betweek = <?php echo $betweek ?>;
    var api_url = "<?php echo site_url('picks'); ?>";
</script>
<div class="container page-title">
    <h1 class="text-center" id="pageTitle">Enter Picks</h1>
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
            <button type="button" class="btn btn-success enter-pick_save-button" onClick="updateTable()">Update</button>
        </div>
    </div>

    <div class="content-div">
        <div class="tab-content">
            <div class="tab-pane all-picks active" id="all_picks" role="tabpanel">
                <div class="sheet" data-type="all_picks">
                </div>
            </div>
            <div class="tab-pane  pick-game" id="ncaa_m" role="tabpanel">
                <div class="sheet" data-type="ncaa_m">
                </div>
            </div>
            <div class="tab-pane  pick-game" id="nba" role="tabpanel">
                <div class="sheet" data-type="nba">
                </div>
            </div>
            <div class="tab-pane  pick-game" id="football" role="tabpanel">
                <div class="sheet" data-type="football">
                </div>
            </div>
            <div class="tab-pane  pick-game" id="ncaa_f" role="tabpanel">
                <div class="sheet" data-type="ncaa_f">
                </div>
            </div>
            <div class="tab-pane  pick-game" id="soccer" role="tabpanel">
                <div class="sheet" data-type="soccer">
                </div>
            </div>
            <div class="tab-pane  pick-game" id="mlb" role="tabpanel">
                <div class="sheet" data-type="mlb">
                </div>
            </div>
        </div>
    </div>
    <div class="footer-div">
        
    </div>
    <ul id="sheets" class="nav nav-tabs bottom-sheet" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" data-type="all_picks" href="#all_picks" aria-selected="true">All Picks</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" data-type="ncaa_m" href="#ncaa_m" aria-selected="true">NCAA M</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" data-type="nba" href="#nba" aria-selected="true">NBA</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" data-type="football" href="#football" aria-selected="true">Football</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" data-type="ncaa_f" href="#ncaa_f" aria-selected="true">NCAA F</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" data-type="soccer" href="#soccer" aria-selected="true">Soccer</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" data-type="mlb" href="#mlb" aria-selected="true">MLB</a>
        </li>
    </ul>
</div>
<?php $this->load->view('footer', array('scripts' => $scripts)) ?>