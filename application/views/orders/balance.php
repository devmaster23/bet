<?php
    $page = 'orders';
    $scripts = [
        base_url('assets/js/orders.js'),
    ];
    $this->load->view('header', array('page' => $page)); 

    if($sportbook_id <= 1)
        $prev_url = './balance?id='.$investor['id'].'&sportbook_id='.$sportbook_count;
    else    
        $prev_url = './balance?id='.$investor['id'].'&sportbook_id='.($sportbook_id-1);
    if($sportbook_id >= $sportbook_count)
        $next_url = './balance?id='.$investor['id'].'&sportbook_id=1';
    else    
        $next_url = './balance?id='.$investor['id'].'&sportbook_id='.($sportbook_id+1);
?>
<script>
    var page_type = 'enter_order';
    var api_url = "<?php echo site_url('orders'); ?>";
</script>
<div class="container page-title">
    <h1 class="text-center">Accouunt Balance</h1>
</div>

<div id="main" class="order-page">
    <div class="inner-wrapper enter_order-page">
        <div class="bet-div">
            <div>
                <label>Investor</label>
                <span><?=$investor['full_name']?></span>
            </div>
            <div>
                <label>Last Updated</label>
                <span>?</span>
            </div>
            <div>
                <label>IP Investor</label>
                <span>?</span>
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
                <br/>
                <br/>
                <label>Bet Allocation</label>
                <span><?=$setting['pick_allocation']?> %</span>
            </div>
            <hr/>
            <div class="clearfix">
                <div class="float-right">
                    <span class="summary"> Bet <?=$sportbook_id?> of <?=$sportbook_count?></span>
                </div>
            </div>
            
            <div class="action-div">
                <a href="<?=$prev_url?>"><img class="prev-img" src="/assets/img/prev.png" /></a>
                <span>$ <?=$total_bet?></span>
                <a href="<?=$next_url?>"><img class="next-img" src="/assets/img/prev.png" /></a>
            </div>
            <div class="sportbookList">
                <table class="table table-bordered table-hover">
                    <tbody>
                        <?php
                            foreach ($sportbookList as $item) {
                                $clsSelected = $item['id'] == $sportbook['id'] ? 'selected' : '';
                        ?>
                        <tr class="sportbook-tr <?=$clsSelected?>" data-id="<?=$item['sportbook_id']?>">
                            <td><?=$item['title']?></td>
                            <td><?=$item['current_balance']?></td>
                            <td><?=$item['current_balance_bet']?></td>
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
                            <td>Bet</td>
                            <td>#Bets</td>
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