var mainTable = null;

/* Formatting function for row details - modify as you need */
function format ( d ) {
    // `d` is the original data object for the row
    return '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;" class="sportbook_detail_table">'+
        '<tr>'+
            '<td>Address:</td>'+
            '<td>'+d.address1+'' + d.address2 + '</td>'+
        '</tr>'+
        '<tr>'+
            '<td>Single Bet:</td>'+
            '<td>'+
                '<div><span class="label">Min</span><span>$ '+d.singlebet_min+'</span></div>'+
                '<div><span class="label">Max</span><span>$ '+d.singlebet_max+'</span></div>'+
            '</td>'+
        '</tr>'+
        '<tr>'+
            '<td>Parlay Bet:</td>'+
            '<td>'+
                '<div><span class="label">Min Number of Teams</span><span>  '+d.parlay_min_team+'</span></div>'+
                '<div><span class="label">Maximum Number of Teams</span><span>  '+d.parlay_max_team+'</span></div>'+
                '<div class="mt-3"></div>'+
                '<div><span class="label">Min Bet</span><span>$ '+d.parlay_min_bet+'</span></div>'+
                '<div><span class="label">Max Bet</span><span>$ '+d.parlay_max_bet+'</span></div>'+
            '</td>'+
        '</tr>'+
        '<tr>'+
            '<td>Parlay Bet:</td>'+
            '<td>'+
                '<div><span class="label">Min Number of Teams</span><span>  '+d.rr_min_team+'</span></div>'+
                '<div><span class="label">Maximum Number of Teams</span><span>  '+d.rr_max_team+'</span></div>'+
                '<div><span class="label">Maximum Combination</span><span>  '+d.rr_max_combination+'</span></div>'+
                '<div class="mt-3"></div>'+
                '<div><span class="label">Min Bet</span><span>$ '+d.rr_min_bet+'</span></div>'+
                '<div><span class="label">Max Bet</span><span>$ '+d.rr_min_team+'</span></div>'+
            '</td>'+
        '</tr>'+
    '</table>';
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
            { "data": "title" },
            { 
                "data": "current_balance",
                render: $.fn.dataTable.render.number( ',', '.', 2)
            },
            { "data": "percent" },
            { "data": "equal_percent" },
            { "data": "valid_percent" },
            { "data": "valid_bet_count" },
            { 
                "data": "balance_left",
                render: $.fn.dataTable.render.number( ',', '.', 2)
            },
            { "data": "bet_count" },
        ],
        "columnDefs": [
            {"className": "dt-right", "targets": "_all"},
        ],
        "order": [[1, 'desc']]
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
                loadData(data);
                $(".loading-div").hide()
            }
        });
    }
}
$(document).ready(function() {
    initPage();
} );