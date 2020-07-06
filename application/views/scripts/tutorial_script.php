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
	}, 1 * 1000);

	function update_tutorial_block(title, text) {
		if ($('#tutorial_block').is(':visible')) {
			$('#tutorial_block').fadeOut(300);
			setTimeout(function(){
				$('#tutorial_title').html(title);
				$('#tutorial_text').html(text);
				$('#tutorial_block').fadeIn(600);
			}, 1 * 1000);
		}
		else {
			$('#tutorial_title').html(title);
			$('#tutorial_text').html(text);
			$('#tutorial_block').fadeIn(600);
		}
	}
	function handle_close_tutorial() {
		$('.exit_tutorial').click(function(){
			$('#tutorial_block').fadeOut(300);
			// update_tutorial_after_last_tutorial_seen();
		});
		console.log('marco');
		$('#tutorial_block').on('click', '.register_button', function(){
			console.log('polo');
			$('#tutorial_block').fadeOut(300);
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
			let html = `Use the <span class="btn btn-success btn-sm disabled"><span class="fa fa-map" aria-hidden="true"></span></span> toggle in the menu to switch between borders and terrain. <span style="text-shadow: 1px 1px 0 #000; color:${fertile_color}">Fertile</span> and <span style="text-shadow: 1px 1px 0 #000; color:${coastal_color}">coastal</span> land are needed for food. <span style="text-shadow: 1px 1px 0 #000; color:${mountain_color}">Mountains</span> and <span style="text-shadow: 1px 1px 0 #000; color:${barren_color}">barren</span> sometimes contain useful rare resources. Click on any unclaimed land to start your nation.`;
			update_tutorial_block('Explore The World', html);
		}
	}
	function tut_2() {
		if (account['tutorial'] == 2) {
			$('#government_block').fadeIn(600);
			let html = `The production cycle runs every <strong class="text-info">${cycle_minutes}</strong> minutes, bringing in tax income, and producing and consuming supplies. Keep your cash and other supplies in the green to grow your nation. Use laws to balance income, corruption, and support.`;
			update_tutorial_block('Form Your Government', html);
		}
		$('#exit_government').click(function(){
			if (account['tutorial'] == 2) {
				ajax_post(`user/update_tutorial/${world_key}/3`, {}, function(response) {
					account['tutorial'] = 3;
					tut_3();
				});
			}
		});
	}
	function tut_3() {
		if (account['tutorial'] == 3) {
			let html = `Use the <span class="btn btn-success btn-sm disabled"><span class="fa fa-fist-raised" aria-hidden="true"></span></span> toggle to control unit visibility. Drag your <img class="tutorial_image" src="${base_url}/resources/icons/units/1-own.png"/> unit to capture land. Capturing land costs <strong class="text-danger">${support_cost_capture_land}</strong> support. Capitols can enlist new units. Units have a Rock Paper Scissors battle dyanmic. Units moving on ocean become Navy. Undefended Townships will form militias. Use Terrain to improve your odds of winning a battle.`;
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
			let html = `Click on your new land to create a settlement. Grain <img class="tutorial_image" src="${base_url}/resources/icons/settlements/6.png"/> is a good choice early on. Agriculture and Energy are important for supplying your townships. Materials are used for Manufacturing industries. Diverse Cash Crops grow your support quicker, but you must trade with other players for more variety.`;
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
			let html = `When you have enough Agriculture and Energy, create a new town <img class="tutorial_image" src="${base_url}/resources/icons/settlements/3.png">. Townships provide large amounts of GDP, and the ability to convert basic supplies into complex supplies. Upgrading townships require certain supplies and a larger population. Having diverse food will grow your population quicker.`;
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
			let html = `Click a township and click <span class="btn btn-primary btn-sm disabled">Industry</span>. Industries may also require certain terrain or a minimum township size. Most industries require supplies in order to produce. For instance, Manufacturing <img class="tutorial_image" src="${base_url}/resources/icons/industries/9.png"/> requires Timber <img class="tutorial_image" src="${base_url}/resources/icons/settlements/11.png"/>, Fiber <img class="tutorial_image" src="${base_url}/resources/icons/settlements/12.png"/>, and Ore <img class="tutorial_image" src="${base_url}/resources/icons/settlements/13.png"/> to create Merchandise used for larger Townships. Have a healthy buffer to avoid running into shortages and trade the surplus with other players.`;
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
			let html = `Click on <span class="btn btn-default btn-sm disabled">Diplomacy</span> to view the diplomacy screen. Trade is essential for quick growth and making allies. You can also declare war at a cost of <strong class="text-danger">${support_cost_declare_war}</strong> support. Chat is great for discussing potential trades and forming alliances.`;
			update_tutorial_block('Conduct Diplomacy', html);
		}
		$('.diplomacy_dropdown').click(function(){
			update_tutorial_after_diplomacy_or_leaders();
		});
	}
	function update_tutorial_after_diplomacy_or_leaders() {
		if (account['tutorial'] == 7) {
			ajax_post(`user/update_tutorial/${world_key}/8`, {}, function(response) {
				account['tutorial'] = 8;
				tut_8();
			});
		}
	}
	function tut_8() {
		if (account['tutorial'] == 8) {
			let html = `To win a round, you must establish one of the three victory industries. World Government <img class="tutorial_image" src="${base_url}/resources/icons/industries/26.png"/>, World Currency <img class="tutorial_image" src="${base_url}/resources/icons/industries/27.png"/>, or Space Colonization <img class="tutorial_image" src="${base_url}/resources/icons/industries/28.png"/>. Good luck, have fun, and be excellent to each other!`;
			update_tutorial_block('The World Is Yours', html);
			update_tutorial_after_last_tutorial_seen();
			setTimeout(function(){
				$('#tutorial_block').fadeOut(300);
			}, 30 * 1000);
		}
	}
	function update_tutorial_after_last_tutorial_seen() {
		// Finish after this is seen
		ajax_post(`user/update_tutorial/${world_key}/99`, {}, function(response) {
			account['tutorial'] = 99;
		});
	}
</script>