<?php
	$page = 'games';
	$scripts = [
		base_url('assets/js/games.sheet.js'),
	];
	$this->load->view('header', array('page' => $page)) ?>
<script>
	var betweek = <?php echo $betweek ?>;
	var api_url = "<?php echo site_url('games'); ?>";
	var pageType = "<?php echo $pageType; ?>";
	var pageTitle = "<?php echo $pageTitle; ?>";
</script>
<div class="container page-title">
	<h1 class="text-center" id="pageTitle"></h1>
</div>

<div id="main">
	<div class="header-div">
		<div class="game-title-wrapper">
			<span class="game-title">
				<?php if(!is_null($pageTitleIcon)) { ?>
        	<img src="<?php echo base_url('assets/img/'.$pageTitleIcon)?>">
        <?php }?>
				<?=$pageTitle?>
			</span>
			<div class="game-week-select-div">
				<label>Bet Day</label>
				<select class="select2 game-week-select" name="game-week-select" onchange="loadTable()">
					<?php for($i=1; $i<=60; $i++) {?>
				  	<option <?php if($i == $betweek) echo "selected";?> value="<?php echo $i?>"><?php echo $i?></option>
				  	<?php }?>
				</select>
			</div>
		</div>
		<div class="save-button-div">
			<button type="button" class="btn btn-success btn-lg enter-game_save-button" onClick="updateTable()">Update</button>
		</div>
	</div>
	<div class="content-div">
		<div class="enter-game">
			<div class="element-box no-border-radius gray-top">
				<div id="<?php echo $pageType?>">
					<div class="sheet"></div>
				</div>
			</div>
		</div>
	</div>	
</div>
<?php $this->load->view('footer', array('scripts' => $scripts)) ?>