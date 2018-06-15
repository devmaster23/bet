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
        
    $.ajax({
        url: api_url+'/assign',
        type: 'POST',
        data: {
            'betweek': betweek,
            'investorId': investorId
        },
        success: function(data) {
            initPage();
            $(".loading-div").hide()
        }
    });
}

function initPage(){
    var betweek = $('.game-week-select').val();
    var investorId = $('.investor-select').val();

    if(mainTable != null)
    {
        mainTable.destroy();
        $('#myTable').empty();

    }

    mainTable = $('#allocations').DataTable( {
        "ajax": {
            "url": api_url+"/loadSportbooks",
            "type": 'post',
            "data" : {
                'betweek': betweek,
                'investorId': investorId
            },
        },
        "columns": [
            { "data": "title" },
            { "data": "current_balance" },
            { "data": "bet_count" },
            { "data": "percent" },
            { "data": "equal_percent" },
            { "data": "current_balance" },
            { "data": "valid_percent" },
            { "data": "valid_bet_count" }
        ],
        "columnDefs": [
            {"className": "dt-right", "targets": "_all"},
            {
                "render": function ( data, type, row ) {
                    return '$ '+ data;
                },
                "targets": 1
            },
            {
                "render": function ( data, type, row ) {
                    return '$ '+ data;
                },
                "targets": 5
            }
        ],
        "order": [[1, 'desc']]
    } );
}
$(document).ready(function() {
    initPage();
} );