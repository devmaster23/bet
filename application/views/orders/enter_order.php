<?php
    $page = 'orders';
    $scripts = [
        base_url('assets/js/orders.js'),
    ];
    $this->load->view('header', array('page' => $page)); 

    if($bet_index <= 1)
        $prev_url = './enter_order?id='.$investor['id'].'&bet_id='.$bet_count;
    else    
        $prev_url = './enter_order?id='.$investor['id'].'&bet_id='.($bet_index-1);
    if($bet_index >= $bet_count)
        $next_url = './enter_order?id='.$investor['id'].'&bet_id=1';
    else    
        $next_url = './enter_order?id='.$investor['id'].'&bet_id='.($bet_index+1);

    date_default_timezone_set('America/Los_Angeles');
    $west_date = date('h:i A');
?>
<script>
    var page_type = 'enter_order';
    var api_url = "<?php echo site_url('orders'); ?>";
</script>
<!-- <div class="container page-title">
    <h1 class="text-center"></h1>
</div>
 -->
<div id="main" class="order-page no-sub-menu">
    <div class="inner-wrapper enter_order-page">
        <div class="bet-div">
            <div class="bet-div_inner-wrapper">
                <div>
                    <span class="investor_name"><i class="os-icon os-icon-user"></i><?=$investor['full_name']?></span>
                </div>
                <hr/>
                <div>
                    <label>West Coast Time:</label>
                    <span><?=$west_date?></span>
                </div>
                <div>
                    <label>IP Source:</label>
                    <span><?=$ip_source?></span>
                    <?php if($ip_source != $investor['ip']){?>
                        <span class="warn warning"></span>
                    <?php }?>
                </div>
                <div>
                    <label>IP Investor:</label>
                    <span><?=$investor['ip']?></span>
                </div>
                <div>
                    <label>Sportbook:</label>
                    <span><a href="<?=$sportbook['siteurl']?>" target="_blank"><?=$sportbook['siteurl']?></a></span>
                </div>
                <div>
                    <label>Login:</label>
                    <span><?=$sportbook['login_name']?></span>
                </div>
                <div>
                    <label>Password:</label>
                    <span class="red"><?=$sportbook['password']?></span>
                </div>
                <hr/>
                <div class="bet-info">
                    <div class="element-box no-border-radius gray-top clearfix">
                        <h5 class="form-header">Description</h5>
                        <?php 
                            $is_group = 0;
                            if(!is_null($bet)){
                                $is_group = $bet['is_group'];
                        ?>
                        <div class="description-div">
                            <div class="description-div_type">
                                <img src="<?php echo base_url('assets/img/'.$bet['logo'])?>">
                                <span class="setting-span"><?=$bet['game_type']?></span>
                                <span class="setting-span"><?=$bet['bet_title']?></span>
                            </div>
                            <div>
                                <?php if($is_group) { 
                                    ?>
                                <span class="setting-span number red"><?=$bet['rrArr']['rr1']?></span>
                                <span class="setting-span number"><?=$bet['rrArr']['rr2']?></span>
                                <span class="setting-span number"><?=$bet['rrArr']['rr3']?></span>
                                <span class="setting-span number"><?=$bet['rrArr']['rr4']?></span>
                                <?php } ?>
                            </div>
                        </div>
                        <table class="table table-striped table-lightfont">
                            <thead>
                                <tr>
                                    <th>VRN</th>
                                    <th>Line</th>
                                    <th>Team</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!$is_group) { ?>
                                <tr class="<?=$bet['rush']? 'red': ''?>" >
                                    <td class="bold"><?=$bet['vrn']?></td>
                                    <td class="bold"><?=$bet['type']?></td>
                                    <td><?=$bet['team']?></td>
                                    <td><?=$bet['time']?></td>
                                </tr>
                                <?php } else{ 
                                    for ($i=0; $i< $bet['rrArr']['rr1']; $i++) {
                                        if(!isset($bet['data'][$i]['vrn']) || empty($bet['data'][$i]['vrn']))
                                            continue;
                                ?>
                                <tr class="<?=$bet['data'][$i]['rush']? 'red': ''?>">
                                    <td class="bold"><?=$bet['data'][$i]['vrn']?></td>
                                    <td class="bold"><?=$bet['data'][$i]['type']?></td>
                                    <td class="text-left"><?=$bet['data'][$i]['team']?></td>
                                    <td class="<?php ?>"><?=$bet['data'][$i]['time']?></td>
                                </tr>
                                <?php 
                                    } 
                                } ?>
                            </tbody>
                        </table>

                        <div class="total-div">
                            <label style="text-transform: uppercase;">Bet Amount:</label>
                            <span>$ <?=$bet['amount']?></span>
                        </div>
                        <div class="total-div">
                            <label style="text-transform: uppercase;">Total:</label>
                            <span>$ <?=$bet['total_amount']?></span>
                        </div>
                        <div class="clearfix"></div>
                        <?php } ?>
                    </div>
                </div>

                <div class="element-box no-border-radius gray-top sportbookList selectable clearfix">
                    <div class="clearfix">
                        <div class="text-center">
                            <?php if(is_null($bet)){ ?>
                            <span class="summary"> No Bets</span>
                            <?php }else{?>
                            <span class="summary"> Bet <?=$bet_index?> of <?=$bet_count?></span>
                            <?php }?>
                        </div>
                    </div>
                    <?php if(!is_null($bet)){ ?>
                    <form  method="post"  id="submit-form" class="action-div">
                        <a href="<?=$prev_url?>"><img class="prev-img" src="/assets/img/prev.png" /></a>
                        <div>
                            <button type="button" data-type="no_bet" class="btn btn-danger no-bet save-button"><img class="camera-img" src="/assets/img/camera_icon.png" /></button>
                            <button type="button" data-type="reassign" class="btn btn-warning reassign save-button" data-toggle="modal" data-target="#reassign_modal"><img class="camera-img" src="/assets/img/camera_icon.png" /></button>
                            <button type="button" data-type="placed" class="btn btn-success bet-placed save-button"><img class="camera-img" src="/assets/img/camera_icon.png" /></button>
                        </div>
                        <a href="<?=$next_url?>"><img class="next-img" src="/assets/img/prev.png" /></a>
                        <input type="hidden" name="sportbookID" value="">
                        <input type="hidden" name="betAmount" value="">
                        <input type="hidden" name="submit_type" value="reassign">
                    </form>
                    <?php } ?>
                    <div class="sportbookList_inner-wrapper">
                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Balance</th>
                                    <th># Bets</th>
                                    <th>Placed</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    foreach ($sportbookList as $item) {
                                        $clsSelected = $item['selected'] ? 'selected' : '';
                                ?>
                                <tr class="sportbook-tr <?=$clsSelected?> <?=$item['status']?>" data-id="<?=$item['sportbook_id']?>">
                                    <td><?=$item['title']?></td>
                                    <td>$<?=number_format($item['current_balance'],2)?></td>
                                    <td><?=$item['bet_left']?></td>
                                    <td><?=$item['bet_placed']?></td>
                                </tr>
                                <?php
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="iframe-div">
            <?php if($ip_source == $investor['ip']){ ?>
            <iframe src="<?=$iframe_src?>">    
            </iframe>
            <?php } else { ?>
            <span class="vpn-alert"><b>Access is forbidden </b><br/> Check your VPN Connection</span>
            <?php }?>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="reassign_modal" tabindex="-1" role="dialog" aria-labelledby="reassign_modalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Reassign Modal</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group">
            <label for="reassign_sportbook_id"> Sportbook</label>
            <select class="form-control" id="reassign_sportbook_id" name="reassign_sportbook_id">
                <?php
                foreach ($sportbookList as $item) {
                    if($item['selected'])
                        continue;
                ?>
                    <option value="<?=$item['id']?>"><?=$item['title']?></option>
                <?php
                    }
                ?>
            </select>
        </div>

        <div class="form-group">
            <label for="bet_amount"> Bet Amount </label>
            <input class="form-control" type="number" id="bet_amount" name="bet_amount" align="center">
        </div>
        <input type="hidden" id="valid_bet_amount" value="<?=$bet['total_amount']?>">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        <button type="button" id="reassign" class="btn btn-warning">Reassign</button>
      </div>
    </div>
  </div>
</div>
<?php $this->load->view('footer', array('scripts' => $scripts)) ?>