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
		tut_8();
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
			}, 30 * 1000);
		}
	}
	function tut_1() {
		if (account['tutorial'] == 1) {
			console.log('tutorial 1');
			let html = `Welcome to the world. Use the <span class="btn btn-default btn-sm disabled"><span class="fa fa-map" aria-hidden="true"></span></span> toggle in the menu to switch between borders and terrain. Fertile and coastal land are needed for food. Mountains and barren are useful for rare resources. Click on any unclaimed land to start your nation.`;
			update_tutorial_block('Explore The World', html);
		}
	}
	function tut_2() {
		if (account['tutorial'] == 2) {
			console.log('tutorial 2');
			$('#government_block').show();
			let html = `Manage your nations laws, finances, and supplies here. Use laws to balance income, corruption, and support. The production cycle runs every ${cycle_minutes} minutes, bringing in tax income, and producing and consuming supplies. Keep your cash and other supplies in the green to grow your nation.`;
			update_tutorial_block('Form Your Government', html);
		}
		$('#exit_government').click(function(){
			account['tutorial'] === 3;
			ajax_post(`user/update_tutorial/${world_key}/3`, {}, function(response) {
				tut_3();
			});
		});
		$('#pass_new_laws_button').click(function(){
			account['tutorial'] === 3;
			ajax_post(`user/update_tutorial/${world_key}/3`, {}, function(response) {
				tut_3();
			});
		});
	}
	function tut_3() {
		if (account['tutorial'] == 3) {
			console.log('tutorial 3');
			let html = ``;
			update_tutorial_block('', html);
		}
	}
	function tut_4() {
		if (account['tutorial'] == 4) {
			console.log('tutorial 4');
			let html = ``;
			update_tutorial_block('Manifest Destiny', html);
		}
	}
	function tut_5() {
		if (account['tutorial'] == 5) {
			console.log('tutorial 5');
			let html = ``;
			update_tutorial_block('Manage The Masses', html);
		}
	}
	function tut_6() {
		if (account['tutorial'] == 6) {
			console.log('tutorial 6');
			let html = ``;
			update_tutorial_block('Industrialize', html);
		}
	}
	function tut_7() {
		if (account['tutorial'] == 7) {
			console.log('tutorial 7');
			let html = ``;
			update_tutorial_block('Conduct Diplomacy', html);
		}
	}
	function tut_8() {
		if (account['tutorial'] == 8) {
			console.log('tutorial 8');
			let html = ``;
			update_tutorial_block('The World Is Yours', html);
		}
	}

	// update_tutorial_block('Explore The World', 'Use all 5 Toggles, explain terrain, what to look for, basic mechanics');
	// update_tutorial_block('Start your nation', 'Claim first land');
	// update_tutorial_block('Form Your government', 'Set a Government, Tax Rate, and Ideology. Autocracy is great for early quick expansion. Keep your tax rates low so your support will regenerate quicker. Free Markets are a safer bet for new players.');
	// update_tutorial_block('Manifest Destiny', 'Explain units, moving, capturing land, enlisting');

	// update_tutorial_block('Feed the Masses', 'Start a farm');
	// update_tutorial_block('Power The People', 'Have a source of energy');
	// update_tutorial_block('Grow Your Empire', 'Create a new town.');

	// update_tutorial_block('Industrialize', 'Have your second city assigned an Industry. Obtaining and producing the right supplies is key to success.');

	// update_tutorial_block('Meet Great Leaders', 'Click on the leaders tab');
	// update_tutorial_block('Conduct Diplomacy', 'Send a diplomatic request. Trade supplies for mutual benefit.');
	// update_tutorial_block('The World Is Yours', 'Explain the win condition, final tips');
</script>