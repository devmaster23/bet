<?php
    $page = 'investors';
    $scripts = [
        base_url('assets/js/allocations.js'),
    ];
    $this->load->view('header', array('page' => $page)) ?>
<script>
    var api_url = "<?php echo site_url('allocations'); ?>";
</script>

<style type="text/css">
.bets {
    display: flex;
    flex-wrap: wrap;
    justify-content: flex-start;
}
.bets .bet-info {
    flex: 0 0 30%;
    display: flex;
    justify-content: flex-start;
    align-items: center;
    flex-direction: column;
}
.bet-info .description-div {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 10px;
}
.bet-info .description-div_type {
    display: flex;
    align-items: center;
    justify-content: flex-start;
}
.bet-info .setting-span:first-child {
    padding-right: 15px;
}
.bet-info .setting-span.number {
    font-size: 22px;
    font-weight: bold;
}
.bet-info .red {
    color: red;
}
.bet-info .reassign_btn {
    margin-top: 15px;
}
</style>
<div class="container page-title">
    <h1 class="text-center"></h1>
</div>

<div id="main" class="allocation-page">
    <div class="header-div">
        <div class="header-left-div">
            <?php $this->load->view('partials/game-select', array('betweek' => $betweek)); ?>

            <div class="investor-select-div">
                <label>Investors</label>
                <select class="select2-large investor-select" name="investor-select" onchange="initPage()">
                    <?php foreach($investors as $investor) {?>
                    <option <?php if($investor['id'] == $investorId) echo "selected";?> value="<?php echo $investor['id']?>"><?php echo $investor['name']?></option>
                    <?php }?>
                </select>
            </div>
        </div>
        <?php if (!is_null($investorId)) { ?>
        <div class="pull-right">
            <!-- <button type="button" class="btn btn-success" onClick="assignBets()">Assign</button> -->
        </div>
        <?php }?>
    </div>
    <div class="allocations-div">
        <!-- <div class="text-center hypo-bet-div">
            <?php if (!is_null($investorId)) { ?>
                <div>
                    <label>Hypothetical Bet Amount: $<span id="hypo_bet_amount"></span></label>
                </div>
                <div>
                    <label>Current Bet Amount: <input type="number" name="current_bet" /></label>
                </div>
            <?php } else { ?>
                <div>
                    <label>No Investors</label>
                </div>
            <?php } ?>
        </div> -->
        <?php if (!is_null($investorId)) { ?>
        <table id="allocations" class="display" style="width:100%">
            <thead>
                <tr>
                    <th></th>
                    <th>Sportsbook</th>
                    <th>Current Balance ($)</th>
                    <th>% Total</th>
                    <th>Optimal ( % )</th>
                    <!-- <th>( % )</th> -->
                    <th>Bets</th>
                    <th>Balance after Bets ($)</th>
                    <!-- <th>Current Bets</th> -->
                </tr>
            </thead>
        </table>
        <?php } ?>
    </div>
</div>

<div class="modal fade" id="reassign_modal" tabindex="-1" role="dialog" aria-labelledby="reassign_modalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <form method="post" action="/allocations/reassign" id="submit-form">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Reassign</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group">
            <label for="reassign_sportbook_id"> Choose Sportbook You Will Reassign To</label>
            <select class="form-control" id="reassign_sportbook_id" name="reassign_sportbook_id">
            </select>
        </div>

        <div class="form-group">
            <label for="bet_amount"> Bet Amount to Reassign </label>
            <input class="form-control" type="number" id="bet_amount" name="bet_amount" align="center">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" id="reassign" class="btn btn-primary">Reassign</button>
      </div>
        <input type="hidden" id="sportbook_from" name="sportbook_from" value="">
        <input type="hidden" id="bet_id" name="bet_id" value="">
        <input type="hidden" id="betweek" name="betweek" value="">
        <input type="hidden" id="investor_id" name="investor_id" value="">
    </div>
    </form>
  </div>
</div>
<?php $this->load->view('footer', array('scripts' => $scripts)) ?>