<?php
    $page = 'investors';
    $scripts = [
        base_url('assets/js/investor_sportbooks.js'),
    ];
    $this->load->view('header', array('page' => $page)) ?>
<script>
    var page_type = 'sportbooks';
    var api_url = "<?php echo site_url('investors'); ?>";
    var betweek = <?php echo $betweek ?>;
    var investorId = "<?=$investor['id']; ?>";
</script>
<div class="container page-title">
    <h1 class="text-center">SportBooks</h1>
</div>

<div id="main" class="investor-page">
    <div class="header-div">
        <div class="game-week-select-div">
            <label>Bet Day</label>
            <select class="select2 game-week-select" name="game-week-select" onchange="loadPage()">
                <?php for($i=1; $i<=60; $i++) {?>
                <option <?php if($i == $betweek) echo "selected";?> value="<?php echo $i?>"><?php echo $i?></option>
                <?php }?>
            </select>
        </div>
        <div class="save-button-div">
            <button type="button" class="btn btn-success enter-game_save-button" onClick="updateTable()">Update</button>
        </div>
    </div>
    <div class="mb-3">
        <span class="sub-title"><?=$investor['full_name']?>'s Sportsbook Accounts</span>
    </div>
    <div class="mb-3">
        <div class="btn-group btn-group-toggle" data-toggle="buttons">
          <label class="btn btn-secondary active">
            <input type="radio" name="pageTypeOption" value="week" autocomplete="off" checked> Week
          </label>
          <label class="btn btn-secondary">
            <input type="radio" name="pageTypeOption" value="year" autocomplete="off"> Year
          </label>
        </div>
    </div>
    <div>
        <div id="user_sportbook_week_table" class="hot handsontable htRowHeaders htColumnHeaders">
        </div>
        <div id="user_sportbook_year_table" class="hot handsontable htRowHeaders htColumnHeaders">
        </div>
    </div>

    <div class="sportbook-select-div">
        <label>Sportbooks</label>
        <select class="select2-large sportbook-select" onchange="loadRules()">
            <?php foreach ($investor['sportbooks'] as $item) {
            ?>
            <option value="<?php echo $item['id']?>"><?=$item['title']?></option>
            <?php }?>
        </select>
    </div>
    <div class="row rule-div">
        <div class="col-md-12 mt-5">
            <h4>Single Bet</h4>
        </div>
        <div class="col-md-4">
            <div>
                <label for="singlebet_min">Min</label>
                <input disabled type="text" name="singlebet_min" id="singlebet_min">
            </div>
            <div>
                <label for="singlebet_max">Max</label>
                <input disabled type="text" name="singlebet_max" id="singlebet_max">
            </div>
        </div>
        <div class="col-md-8">
            
        </div>

        <div class="col-md-12 mt-5">
            <h4>Parlay</h4>
        </div>
        <div class="col-md-4">
            <div>
                <label for="parlay_min_team">Min Number of Teams</label>
                <input disabled type="text" name="parlay_min_team" id="parlay_min_team">
            </div>
            <div>
                <label for="parlay_max_team">Maxium Number of Teams</label>
                <input disabled type="text" name="parlay_max_team" id="parlay_max_team">
            </div>
            <div class="mt-3"></div>
            <div>
                <label for="parlay_min_bet">Min Bet</label>
                <input disabled type="text" name="parlay_min_bet" id="parlay_min_bet">
            </div>
            <div>
                <label for="parlay_max_bet">Max Bet</label>
                <input disabled type="text" name="parlay_max_bet" id="parlay_max_bet">
            </div>
        </div>
        <div class="col-md-8">
            <div id="parlay_team_table"></div>
            <div id="parlay_outcome_table"></div>
        </div>

        <div class="col-md-12 mt-5">
            <h4>Round Robin</h4>
        </div>
        <div class="col-md-4">
            <div>
                <label for="rr_min_team">Min Number of Teams</label>
                <input disabled type="text" name="rr_min_team" id="rr_min_team">
            </div>
            <div>
                <label for="rr_max_team">Maxium Number of Teams</label>
                <input disabled type="text" name="rr_max_team" id="rr_max_team">
            </div>
            <div>
                <label for="rr_max_combination">Maxium Combination</label>
                <input disabled type="text" name="rr_max_combination" id="rr_max_combination">
            </div>
            <div class="mt-3"></div>
            <div>
                <label for="rr_min_bet">Min Bet</label>
                <input disabled type="text" name="rr_min_bet" id="rr_min_bet">
            </div>
            <div>
                <label for="rr_max_bet">Max Bet</label>
                <input disabled type="text" name="rr_max_bet" id="rr_max_bet">
            </div>
        </div>
        <div class="col-md-8">
            <div id="rr_team_table"></div>
            <div id="rr1_outcome_table" class="mb-5"></div>
            <div id="rr2_outcome_table" class="mb-5"></div>
        </div>
    </div>
</div>
<?php $this->load->view('footer', array('scripts' => $scripts)) ?>