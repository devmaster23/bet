var mainTable = null;

/* Formatting function for row details - modify as you need */
function format ( sportbook_id, bets ) {
    var html = '<div class="bets">';
    for (var i = 0;i < bets.length;i++) {
        // `d` is the original data object for the row
        var bet = bets[i];
        var is_group = bet.is_group || 0;
        html += `
        <div class="bet-info">
            <div class="element-box no-border-radius gray-top clearfix">
                <h5 class="form-header">Description</h5>
                <div class="description-div">
                    <div class="description-div_type">
                        <img src="/assets/img/${bet.logo}">
                        <span class="setting-span">${bet.game_type}</span>
                        <span class="setting-span">${bet.bet_title}</span>
                    </div>`;
        if (is_group) {
            html += `<div>
                        <span class="setting-span number red">${bet.rrArr.rr1 || ''}</span>
                        <span class="setting-span number">${bet.rrArr.rr2 || ''}</span>
                        <span class="setting-span number">${bet.rrArr.rr3 || ''}</span>
                        <span class="setting-span number">${bet.rrArr.rr4 || ''}</span>
                    </div>`;
        }
                    
        html += `</div>
                <table class="table table-striped table-lightfont">
                    <thead>
                        <tr>
                            <th>VRN</th>
                            <th>Line</th>
                            <th>Team</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>`;
        if (!is_group) {
            var tr_class = bet.rush ? 'red' : '';
            html += `
                        <tr class="${tr_class}" >
                            <td class="bold">${bet.vrn}</td>
                            <td class="bold">${bet.type}</td>
                            <td>${bet.team}</td>
                            <td>${bet.time}</td>
                        </tr>
            `;
        }
        else {
            for (var j = 0;j < bet.rrArr.rr1;j ++) {
                if (!bet.data[j].vrn) continue;
                var tr_class = bet.data[j].rush ? 'red' : '';
                html += `
                        <tr class="${tr_class}">
                            <td class="bold">${bet.data[j].vrn}</td>
                            <td class="bold">${bet.data[j].type}</td>
                            <td class="text-left">${bet.data[j].team}</td>
                            <td>${bet.data[j].time}</td>
                        </tr>
                `;
            }
        }
        html += `
                    </tbody>
                </table>

                <div class="total-div">
                    <label style="text-transform: uppercase;">Bet Amount:</label>
                    <span>$ ${bet.bet_amount}</span>
                </div>
                <div class="total-div">
                    <label style="text-transform: uppercase;">Total:</label>
                    <span>$ ${bet.total_amount}</span>
                </div>
                <div class="clearfix"></div>
                <button onclick="javascript:updateAssignForm(${sportbook_id},${bet.order_id},${bet.bet_amount});" 
                data-toggle="modal" data-target="#reassign_modal" class="reassign_btn btn btn-primary">Reassign</button>
            </div>
        </div>
        `
    }
    html += '</div>';

    return html;
}
    
function assignBets(){
    $(".loading-div").show()

    var betweek = $('.game-week-select').val();
    var investorId = $('.investor-select').val();
    var bet_amount = $("input[name='current_bet']").val();
        
    $.ajax({
        url: api_url+'/assign',
        type: 'POST',
        data: {
            'betweek': betweek,
            'investorId': investorId,
            'bet_amount': bet_amount
        },
        success: function(data) {
            initPage();
            $(".loading-div").hide()
        }
    });
}
function updateAssignForm(sportbook_from, order_id, valid_bet_amount) {
    $('#reassign_sportbook_id').empty()
    $('#bet_amount').val('')
    var bet_id = '';
    for (var i = 0;i < window.Bets.length;i ++) {
        if (window.Bets[i].order_id == order_id) {
            bet_id = i;
            break;
        }
    }
    window.Allocations.forEach(function(item) {
        if (item.id != sportbook_from) {
            $('#reassign_sportbook_id').append($(`<option value=${item.id}>${item.title}</option>`))
        }
    });

    $('#submit-form').data('valid_bet_amount', valid_bet_amount);

    var betweek = $('.game-week-select').val();
    var investorId = $('.investor-select').val();
    $('#betweek').val(betweek);
    $('#investor_id').val(investorId);
    $('#sportbook_from').val(sportbook_from);
    $('#bet_id').val(bet_id);
}
function loadData(data){
    
    $("span[id='hypo_bet_amount']").html(data['hypo_bet_amount']);
    $("input[name='current_bet']").val(data['current_bet_amount']);

    if(mainTable != null)
    {
        mainTable.destroy();
        $('#myTable').empty();

    }
    mainTable = $('#allocations').DataTable( {
        "data": data['data'],
        "columns": [
            {
                "className":      'details-control',
                "orderable":      false,
                "data":           null,
                "defaultContent": ""
            },
            { "data": "title" },
            { 
                "data": "current_balance",
                render: $.fn.dataTable.render.number( ',', '.', 2)
            },
            { "data": "percent" },
            { "data": "equal_percent" },
            // { "data": "valid_percent" },
            { "data": "bet_count" },
            { 
                "data": "balance_left",
                render: $.fn.dataTable.render.number( ',', '.', 2)
            },
            // { "data": "bet_count" },
        ],
        "columnDefs": [
            {"className": "dt-right", "targets": "_all"}
        ],
        "order": [[2, 'desc']]
    } );

    // Add event listener for opening and closing details
    $('#allocations tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = mainTable.row( tr );
        var sportbook_id = row.data().id
        var sportbook_bets = [];
        for (var i in window.Bets) {
            var bet = window.Bets[i];
            if (bet.sportbook_id == sportbook_id) {
                sportbook_bets.push(bet);
            }
        }
 
        if ( row.child.isShown() ) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            // Open this row
            row.child( format(sportbook_id, sportbook_bets) ).show();
            tr.addClass('shown');
        }
    } );
}
function initPage(){
    var betweek = $('.game-week-select').val();
    var investorId = $('.investor-select').val();
    if(investorId){
        $.ajax({
            url: api_url+'/loadSportbooks',
            type: 'POST',
            data: {
                'betweek': betweek,
                'investorId': investorId
            },
            success: function(data) {
                window.Allocations = data.data
                window.Bets = data.bets
                loadData(data)
                $(".loading-div").hide()
            }
        });
    }
}
$(document).ready(function() {
    initPage();

    $('#submit-form #reassign').click(function(e) {
        var valid_bet_amount = $('#submit-form').data('valid_bet_amount');
        var sportbookID = $("#reassign_sportbook_id").val(),
        new_bet_amount = parseFloat($("#bet_amount").val())

        if(new_bet_amount > valid_bet_amount){
            alert("Bet amount should be less or equal to " + valid_bet_amount);
            return false;
        }

        if(!new_bet_amount) {
            alert("Bet amount should be positive!");
            return false;
        }
    })
} );