<script>
	/*
	This code is procedural to be practical
	*/

	handle_close_tutorial();
	setTimeout(function(){
		tut_0();
		tut_1();
		tut_2();
		tut_3();
		tut_4();
		tut_5();
		tut_6();
		tut_7();
		tut_8();
	}, 3 * 1000);

	function update_tutorial_block(title, text) {
		$('#tutorial_block').fadeIn();
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
			}, 10 * 1000);
		}
	}
	function tut_1() {
		if (account['tutorial'] == 1) {
			let html = `Welcome to the world. Use <span class="btn btn-success btn-sm disabled"><span class="fa fa-map" aria-hidden="true"></span></span> toggle in the menu to switch between borders and terrain. <span style="text-shadow: 1px 1px 0 #000; color:${fertile_color}">Fertile</span> and <span style="text-shadow: 1px 1px 0 #000; color:${coastal_color}">coastal</span> land are needed for food. <span style="text-shadow: 1px 1px 0 #000; color:${mountain_color}">Mountains</span> and <span style="text-shadow: 1px 1px 0 #000; color:${barren_color}">barren</span> are useful for rare resources. Click on any unclaimed land to start your nation.`;
			update_tutorial_block('Explore The World', html);
		}
	}
	function tut_2() {
		if (account['tutorial'] == 2) {
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
		if (account['tutorial'] == 3) {
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
		if (account['tutorial'] == 4) {
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
			let html = `When you have enough Agriculture and Energy, create a new town. Townships provide large amounts of GDP, and the ability to convert basic supplies into complex supplies. Upgrading townships require certain supplies and a larger population. Having diverse food will grow your population quicker.`;
			update_tutorial_block('Grow your Empire', html);
		}
	}
	function update_tutorial_after_set_township() {
		if (account['tutorial'] == 5) {
			ajax_post(`user/update_tutorial/${world_key}/6`, {}, function(response) {
				account['tutorial'] = 6;
				tut_6();
			});
		}
	}
	function tut_6() {
		if (account['tutorial'] == 6) {
			let html = `Click on Industry to assign an industry to your new town. Some industries require certain supplies in order to produce. For instance, Manufacturing requires Timber, Fiber, and Ore to create Merchandise. Have a healthy buffer to avoid running into shortages. Industries may also require certain terrain or a minimum township size. Trade the surplus with other players.`;
			update_tutorial_block('Industrialize', html);
		}
	}
	function update_tutorial_after_set_industry() {
		if (account['tutorial'] == 6) {
			ajax_post(`user/update_tutorial/${world_key}/7`, {}, function(response) {
				account['tutorial'] = 7;
				tut_7();
			});
		}
	}
	function tut_7() {
		if (account['tutorial'] == 7) {
			let html = `Click on Diplomacy to send a diplomatic request. Trade is essential for quick growth and making allies. You can also declare war at a cost of ${support_cost_declare_war} support. Click on Leaders to view who has the most of a supply. Chat is great for discussing potential trades and forming alliances.`;
			update_tutorial_block('Conduct Diplomacy', html);
		}
	}
	function update_tutorial_after_send_trade_request() {
		if (account['tutorial'] == 7) {
			ajax_post(`user/update_tutorial/${world_key}/8`, {}, function(response) {
				account['tutorial'] = 8;
				tut_8();
			});
		}
	}
	function tut_8() {
		if (account['tutorial'] == 8) {
			let html = `You are now ready to take on the world. To win, you must construct one of the three a victory industries. World Government, World Currency, or Space Colonization. Be good, good luck, and have fun!`;
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