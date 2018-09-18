<?php
    $page = 'investors';
    $scripts = [
        base_url('assets/js/allocations.js'),
    ];
    $this->load->view('header', array('page' => $page)) ?>
<script>
    var api_url = "<?php echo site_url('allocations'); ?>";
</script>
<div class="container page-title">
    <h1 class="text-center"></h1>
</div>

<div id="main" class="allocation-page">
    <div class="header-div">
        <div class="header-left-div">
            <?php $this->load->view('partials/game-select', array('betweek' => $betweek)); ?>

            <div class="investor-select-div">
                <label>Bet Day</label>
                <select class="select2-large investor-select" name="investor-select" onchange="initPage()">
                    <?php foreach($investors as $investor) {?>
                    <option <?php if($investor['id'] == $investorId) echo "selected";?> value="<?php echo $investor['id']?>"><?php echo $investor['name']?></option>
                    <?php }?>
                </select>
            </div>
        </div>
        <?php if (!is_null($investorId)) { ?>
        <div class="pull-right">
            <button type="button" class="btn btn-success" onClick="assignBets()">Assign</button>
        </div>
        <?php }?>
    </div>
    <div class="allocations-div">
        <div class="text-center hypo-bet-div">
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
        </div>
        <?php if (!is_null($investorId)) { ?>
        <table id="allocations" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Current Balance ($)</th>
                    <th>( % )</th>
                    <th>Optimal ( % )</th>
                    <th>( % )</th>
                    <th>Desired Bets</th>
                    <th>Balance after Bets ($)</th>
                    <th>Current Bets</th>
                </tr>
            </thead>
        </table>
        <?php } ?>
    </div>
</div>
<?php $this->load->view('footer', array('scripts' => $scripts)) ?>