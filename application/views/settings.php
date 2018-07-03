<?php
    $page = 'settings';
    $scripts = [
        base_url('assets/js/setting.js'),
    ];
    $this->load->view('header', array('page' => $page)) ?>
<?php 
  $categoryType = $activeSetting['type'];
  $categoryGroupUser = $activeSetting['groupuser_id'];
?>
<script>
    var betweek = <?php echo $betweek ?>;
    var fomularData = <?php echo json_encode($fomularData) ?>;
    var activeSetting = <?php echo json_encode($activeSetting) ?>;
    var api_url = "<?php echo site_url('settings'); ?>";
</script>
<div class="container page-title">
  <h1 class="text-center">Control Panel</h1>
</div>

<div id="main">
    <div class="header-div">
      <div class="game-week-select-div">
        <label>Bet Day</label>
        <select class="select2 game-week-select" name="game-week-select" onchange="loadData()">
          <?php for($i=1; $i<=60; $i++) {?>
            <option <?php if($i == $betweek) echo "selected";?> value="<?php echo $i?>"><?php echo $i?></option>
            <?php }?>
        </select>
      </div>
      <div class="save-button-div">
        <button type="button" class="btn btn-success btn-lg enter-game_save-button" onClick="updateData()">Update</button>
      </div>
    </div>

    <div class="content-div">
      <div class="setting-div">
        <div class="user-group-div">
          <ul class="category-select" id="category-type">
            <li data-value="0" class="<?php echo ($categoryType == 0 ? 'selected':'')?>">All</li>
            <li data-value="1" class="<?php echo ($categoryType == 1 ? 'selected':'')?>">Groups</li>
            <li data-value="2" class="<?php echo ($categoryType == 2 ? 'selected':'')?>">Individuals</li>
          </ul>
          <ul class="category-select" id="category-group-user">
          </ul>
          <div class="clearfix"></div>
        </div>
        <div>
          <div class="metrics-div">
            <div class="metrics-inner-table">
              <div class="sheet" id="bet_allocation">
              </div>
            </div>
            <div class="metrics-inner-table">
              <div class="sheet" id="bet_analysis">
              </div>
            </div>
          </div>
          <div class="description-div">
            <p class="title">Special Instruction</p>
            <textarea id="description" placeholder="Create Custom Description"></textarea>
          </div>
        </div>
        <div class="number-pick-table">
          <p>Number of Picks Chosen</p>
          <p class="vertical-text">Number of Teams Selected</p>
          <table id="fomularTable">
            <thead>
              <tr>
                <td></td>
                <?php for($i = $numberOfPicks['min']; $i <= $numberOfPicks['max']; $i++): ?>
                <td><?php echo $i?></td>
                <?php endfor;?>
              </tr>
            </thead>
            <tbody>
              <?php for($i = $numberOfTeams['min']; $i <= $numberOfTeams['max']; $i++): ?> 
              <tr>
                <td><?php echo $i?></td>
                  <?php for($j = $numberOfPicks['min']; $j <= $numberOfPicks['max']; $j++): ?> 
                  <td><?php echo $fomularData[$i][$j]?></td>
                  <?php endfor;?>
              </tr>
              <?php endfor;?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
</div>
<?php $this->load->view('footer', array('scripts' => $scripts)) ?>