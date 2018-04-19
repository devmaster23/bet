<?php
	$page = 'games';
	$scripts = [
		base_url('assets/js/games.sheet.js'),
	];
	$this->load->view('header', array('page' => $page)) ?>
<script>
	var betweek = <?php echo $betweek ?>;
	var api_url = "<?php echo site_url('games'); ?>";
</script>
<div class="container title">
	<h1 class="text-center">Enter Games</h1>
</div>

<div id="main">
	<div class="header-div">
		<div class="game-week-select-div">
			<label>Bet Day</label>
			<select class="select2 game-week-select" name="game-week-select" onchange="loadTable()">
				<?php for($i=1; $i<=60; $i++) {?>
			  	<option <?php if($i == $betweek) echo "selected";?> value="<?php echo $i?>"><?php echo $i?></option>
			  	<?php }?>
			</select>
		</div>
		<div class="save-button-div">
			<button type="button" class="btn btn-success enter-game_save-button" onClick="updateTable()">Update</button>
		</div>
	</div>
	<div class="alert-div">
		<div class="alert alert-success notification-box">
		  <strong>Notification! </strong>Data is successfully updated.
		</div>
	</div>
	<div class="content-div">
		<div class="tab-content enter-game">
			<div class="tab-pane active" id="ncaa_m" role="tabpanel">
				<div class="sheet" data-type="ncaa_m">
				</div>
			</div>
			<div class="tab-pane" id="nba" role="tabpanel">
				<div class="sheet" data-type="nba">
				</div>
			</div>
			<div class="tab-pane" id="football" role="tabpanel">
				<div class="sheet" data-type="football">
				</div>
			</div>
			<div class="tab-pane" id="ncaa_f" role="tabpanel">
				<div class="sheet" data-type="ncaa_f">
				</div>
			</div>
			<div class="tab-pane" id="soccer" role="tabpanel">
				<div class="sheet" data-type="soccer">
				</div>
			</div>
			<div class="tab-pane" id="mlb" role="tabpanel">
				<div class="sheet" data-type="mlb">
				</div>
			</div>
		</div>
	</div>
	<div class="footer-div">
	
	</div>
	<ul id="sheets" class="nav nav-tabs bottom-sheet" role="tablist">
		<li class="nav-item">
			<a class="nav-link active" data-toggle="tab" data-type="ncaa_m" href="#ncaa_m" aria-selected="true">NCAA M</a>
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