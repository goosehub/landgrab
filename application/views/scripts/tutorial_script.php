<script>
	/*
	This code is procedural on purpose
	*/

	setTimeout(function(){
		tut_0();
		tut_1();
		tut_2();
		tut_3();
		tut_4();
		tut_5();
		tut_6();
		tut_7();
	}, 6 * 1000);

	function update_tutorial_block(title, text) {
		$('#tutorial_block').show();
		$('#tutorial_title').html(title);
		$('#tutorial_text').html(text);
	}
	function handle_close_tutorial() {
		$('.exit_tutorial').click(function(){
			$('#tutorial_block').hide();
		});
	}

	function tut_0() {
		if (!account) {
			setTimeout(function(){
				let html = `<p class="text-center"><a href="#" class="register_button btn btn-primary btn-lg">Start your nation!</a></p>`;
				update_tutorial_block('Become A Leader', html);
			}, 6 * 1000);
		}
	}
	function tut_1() {
		if (account['tutorial'] == 1) {
			console.log('tutorial 1');
			let html = `Welcome to the world. Use <span class="btn btn-success btn-sm disabled"><span class="fa fa-map" aria-hidden="true"></span></span> toggle in the menu to switch between borders and terrain. <span style="text-shadow: 1px 1px 0 #000; color:${fertile_color}">Fertile</span> and <span style="text-shadow: 1px 1px 0 #000; color:${coastal_color}">coastal</span> land are needed for food. <span style="text-shadow: 1px 1px 0 #000; color:${mountain_color}">Mountains</span> and <span style="text-shadow: 1px 1px 0 #000; color:${barren_color}">barren</span> are useful for rare resources. Click on any unclaimed land to start your nation.`;
			update_tutorial_block('Explore The World', html);
		}
	}
	function tut_2() {
		if (account['tutorial'] == 2) {
			console.log('tutorial 2');
			$('#government_block').show();
			let html = `Manage your laws, finances, and supplies here. Use laws to balance income, corruption, and support. The production cycle runs every ${cycle_minutes} minutes, bringing in tax income, and producing and consuming supplies. Keep your cash and other supplies in the green to grow your nation.`;
			update_tutorial_block('Form Your Government', html);
		}
		$('#exit_government').click(function(){
			ajax_post(`user/update_tutorial/${world_key}/3`, {}, function(response) {
				account['tutorial'] = 3;
				tut_3();
			});
		});
		$('#pass_new_laws_button').click(function(){
			ajax_post(`user/update_tutorial/${world_key}/3`, {}, function(response) {
				account['tutorial'] = 3;
				tut_3();
			});
		});
	}
	function tut_3() {
		console.log(account['tutorial']);
		if (account['tutorial'] == 3) {
			console.log('tutorial 3');
			let html = `It's time to expand your nation. Use <span class="btn btn-success btn-sm disabled"><span class="fa fa-fist-raised" aria-hidden="true"></span></span> to toggle unit visibility. Drag your unit to an adjacent tile to capture that land at a cost of ${support_cost_capture_land} support. Your Capitol and bases are able to enlist new units. Infantry, Tanks, and Airforce have a Rock Paper Scissors battle dyanmic. Units become Navy on the Ocean. Townships will form militias if left undefended. Use Terrain and Townships to improve your odds of winning a battle.`;
			update_tutorial_block('Manifest Destiny', html);
		}
	}
	function update_tutorial_after_move_unit() {
		if (account['tutorial'] == 3) {
			ajax_post(`user/update_tutorial/${world_key}/4`, {}, function(response) {
				account['tutorial'] = 4;
				tut_4();
			});
		}
	}
	function tut_4() {
		console.log('tut_4');
		console.log(account['tutorial']);
		if (account['tutorial'] == 4) {
			console.log('tutorial 4');
			let html = `Click on your new land to create a settlement. Agriculture and Energy are important early on for creating more townships. Materials are useful later in Manufacturing industries. Diverse Cash Crops grow your support quicker, but you must trade with other players for more variety.`;
			update_tutorial_block('Feed the people', html);
		}
	}
	function update_tutorial_after_set_settlement() {
		if (account['tutorial'] == 4) {
			ajax_post(`user/update_tutorial/${world_key}/5`, {}, function(response) {
				account['tutorial'] = 5;
				tut_5();
			});
		}
	}
	function tut_5() {
		if (account['tutorial'] == 5) {
			console.log('tutorial 5');
			let html = `When you have enough Agriculture and Energy, create a new town and assign it an industry. Townships provide large amounts of GDP, the ability to convert basic supplies into complex supplies, and bases can enlist units. Upgrading townships require certain supplies and a larger population. Having diverse food will grow your population quicker.`;
			update_tutorial_block('Grow your Empire', html);
		}
	}
	function update_tutorial_after_set_industry() {
		if (account['tutorial'] == 5) {
			ajax_post(`user/update_tutorial/${world_key}/6`, {}, function(response) {
				account['tutorial'] = 6;
				tut_6();
			});
		}
	}
	function tut_6() {
		if (account['tutorial'] == 6) {
			console.log('tutorial 6');
			let html = `Send a diplomatic request. Trade supplies for mutual benefit.`;
			update_tutorial_block('Conduct Diplomacy', html);
		}
	}
	function tut_7() {
		if (account['tutorial'] == 7) {
			console.log('tutorial 7');
			let html = `Explain the win condition, leaderboard, final tips`;
			update_tutorial_block('The World Is Yours', html);
			update_tutorial_after_last_tutorial_seen();
		}
	}
	function update_tutorial_after_last_tutorial_seen() {
		// Finish after this is seen
		ajax_post(`user/update_tutorial/${world_key}/99`, {}, function(response) {
			account['tutorial'] = 99;
		});
	}
</script>