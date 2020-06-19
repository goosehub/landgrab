<script>
    handle_open_leaderboard();
    handle_leaderboard_supply_select();

    function handle_leaderboard_supply_select() {
        $('#leaderboard_supply_select').change(function(){
            current_leaderboard_supply = $('#leaderboard_supply_select').val();
            update_leaderboard();
        });
    }

    function handle_open_leaderboard() {
        $('#leaderboard_dropdown').click(function(){
            $('.center_block').hide();
            $('#leaderboard_block').show();
            update_leaderboard();
        });
    }

    function update_leaderboard() {
        ajax_get('game/leaderboard/' + world_key + '/' + current_leaderboard_supply, function(response) {
            render_leaderboard(response.leaders);
        });
    }
    function render_leaderboard(leaders) {
        let html = '';
        if (!leaders.length) {
            html = `<p class="text-center">No nations have this supply</p>`;
            $('#leaderboard').html(html);
            return;
        }
        let header_row = render_leaderboard_header_row(leaders)
        let data_rows = render_leaderboard_data_rows(leaders)
        html = `
        <table id="leaderboard_table" class="table table-bordered table-hover table-condensed">
            <thead>
                <tr>
                    ${header_row}
                </tr>
            </thead>
            <tbody>
                <tr>
                    ${data_rows}
                </tr>
            </tbody>
        </table>
        `;
        $('#leaderboard').html(html);
    }
    function render_leaderboard_header_row(leaders) {
        let html = `
            <th>Rank</th>
            <th colspan="2">Leader</th>
            <th colspan="2">Nation</th>
            <th>${leaders[0].label}</th>
        `;
        return html;
    }
    function render_leaderboard_data_rows(leaders) {
        let html = '';
        for (var i in leaders) {
        // for (let i = 0; i < leaders.length; i++) {
            let leader = leaders[i];
            if (!leader.username) {
                continue;
            }
            let rank = parseInt(i) + 1;
            let row_data = render_leaderboard_row(leader, rank);
            html += `<tr>${row_data}</tr>`;
        }
        return html;
    }
    function render_leaderboard_row(leader, rank) {
        console.log('marco');
        console.log(leader);
        let html = `
        <td>
            <strong class="text-default">${rank}</strong>
        </td>
        <td>
            <a href="#" onclick="new_diplomacy(${leader.account_key});">
                <span class="glyphicon glyphicon-user" aria-hidden="true" style="color: ${leader.color}"></span>
                <strong class="text-default">${leader.username}</strong>
            </a>
        </td>
        <td>
            <a href="${base_url}/uploads/${leader.leader_portrait}" target="_blank">
                <img class="leaderboard_leader_portrait" src="${base_url}/uploads/${leader.leader_portrait}">
            </a>
        </td>
        <td>
            <strong class="text-default">${leader.nation_name}</strong>
        </td>
        <td>
            <a href="${base_url}/uploads/${leader.nation_flag}" target="_blank">
                <img class="leaderboard_leader_portrait" src="${base_url}/uploads/${leader.nation_flag}">
            </a>
        </td>
        <td>
            <strong class="text-default">${format_number(leader.amount)} ${leader.suffix}</strong>
        </td>
        `;
        return html;
    }
</script>