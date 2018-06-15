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
?>
<script>
    var page_type = 'enter_order';
    var api_url = "<?php echo site_url('orders'); ?>";
</script>
<div class="container page-title">
    <h1 class="text-center">Enter Orders</h1>
</div>

<div id="main" class="order-page">
    <div class="inner-wrapper enter_order-page">
        <div class="bet-div">
            <div>
                <label>Investor</label>
                <span><?=$investor['full_name']?></span>
            </div>
            <div>
                <label>IP Source</label>
                <span><?=$investor['full_name']?></span>
            </div>
            <div>
                <label>IP Investor</label>
                <span><?=$investor['ip']?></span>
            </div>
            <hr/>
            <div>
                <label>Sportbook</label>
                <span><?=$sportbook['title']?></span>
            </div>
            <div>
                <label>Login</label>
                <span><?=$sportbook['login_name']?></span>
            </div>
            <div>
                <label>SiteUrl</label>
                <span><?=$sportbook['siteurl']?></span>
            </div>
            <div>
                <label>Password</label>
                <span><?=$sportbook['password']?></span>
            </div>
            <hr/>
            <div>
                <label>Description</label>
                <br>

                <span><?=$sportbook['note']?></span>
            </div>
            <div class="bet-info clearfix">
                <?php 
                    if(!is_null($bet)){
                        $title = 'Single Bet';
                        $betType = $bet['bet_type'];
                        if($betType == 'rr')
                            $title = 'Round Robin';
                        else if($betType == 'parlay')
                            $title = 'Parlay';
                ?>
                <span class="title"><?=$title?></span>
                <table class="table">
                    <thead>
                        <tr>
                            <td>VRN</td>
                            <td>Line</td>
                            <td>Team</td>
                            <td>Bet</td>
                            <td>Time</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($betType == 'single') { ?>
                        <tr>
                            <td><b><?=$bet['vrn']?></b></td>
                            <td><b><?=$bet['type']?></b></td>
                            <td><?=$bet['team']?></td>
                            <td><?=$bet['amount']?></td>
                            <td><?=$bet['time']?></td>
                        </tr>
                        <?php } else{ 
                            for ($i=0; $i< $rr1; $i++) {
                        ?>
                        <tr>
                            <td><b><?=$bet[$i]['vrn']?></b></td>
                            <td><b><?=$bet[$i]['type']?></b></td>
                            <td><?=$bet[$i]['team']?></td>
                            <td><?=$bet['amount']?></td>
                            <td><?=$bet[$i]['time']?></td>
                        </tr>
                        <?php 
                            } 
                        } ?>
                    </tbody>
                </table>
                <div class="float-right">
                    <label>Bet Amount</label>
                    <span><?=$bet['amount']?></span>
                </div>
                <div class="float-right">
                    <label>Total</label>
                    <span><?=$bet['amount']?></span>
                </div>
                <?php
                    }
                ?>
            </div>
            <hr/>
            <div class="clearfix">
                <div class="float-right">
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
                <button type="button" id="save-btn" class="btn btn-success">Assign</button>
                <a href="<?=$next_url?>"><img class="next-img" src="/assets/img/prev.png" /></a>
                <input type="hidden" name="sportbookID" value="">
            </form>
            <?php } ?>

            <div class="sportbookList selectable">
                <table class="table table-bordered table-hover">
                    <tbody>
                        <?php
                            foreach ($sportbookList as $item) {
                                $clsSelected = $item['selected'] ? 'selected' : '';
                        ?>
                        <tr class="sportbook-tr <?=$clsSelected?>" data-id="<?=$item['sportbook_id']?>">
                            <td><?=$item['title']?></td>
                            <td>$<?=$item['current_balance']?></td>
                            <td><?=$item['bet_count']?></td>
                        </tr>
                        <?php
                            }
                        ?>
                    </tbody>
                    <tfoot style="font-weight: bold;">
                        <tr>
                            <td>Title</td>
                            <td>Balance</td>
                            <td>Bets</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <div class="iframe-div">
            <iframe src="<?=$iframe_src?>" onload="this.height=screen.height;">    
            </iframe>
        </div>
    </div>
</div>
<?php $this->load->view('footer', array('scripts' => $scripts)) ?>