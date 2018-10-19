var mainTable = null;
/* Formatting function for row details - modify as you need */
function format ( d ) {
    // `d` is the original data object for the row
    var result = '';
    var title = '';
    if(d.action != 'balance')
    {
        title = 'Single Bet';
        var data = JSON.parse(d.data);
        console.log(data);
        var setting = data.rrArr;

        if(d.bet_type == 'rr')
            title = 'Round Robin';
        else if(d.bet_type == 'parlay')
            title = 'Parlay';
        else if(d.bet_type == 'crr')
            title = 'Custom RR';
        else if(d.bet_type == 'cparlay')
            title = 'Custom Parlay';

        result = `<div class="bet-info">
            <h5 class="form-header">Description</h5>
            <div class="description-div">
                <div class="description-div_type">
                    <img src="assets/img/`+d.logo+`">
                    <span class="setting-span">`+data.game_type+`</span>
                    <span class="setting-span">`+title+`</span>
                </div>`;
        if(d.bet_type != 'single')
        {
            var rr1 = setting.rr1 ? setting.rr1 : '';
            var rr2 = setting.rr2 ? setting.rr2 : '';
            var rr3 = setting.rr3 ? setting.rr3 : '';
            var rr4 = setting.rr4 ? setting.rr4 : '';
            if (d.bet_type == 'parlay' || d.bet_type == 'cparlay') {
                rr2 = rr3 = rr4 = '';
            }
            result += `<div>
                    <span class="setting-span number red">`+rr1+`</span>
                    <span class="setting-span number">`+rr2+`</span>
                    <span class="setting-span number">`+rr3+`</span>
                    <span class="setting-span number">`+rr4+`</span>
                </div>`;
        }
        result += `</div>
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

        if(d.bet_type == 'single')
        {
            result += `<tr class="">
                    <td class="bold">`+data.vrn+`</td>
                    <td class="bold">`+data.type+`</td>
                    <td>`+data.team+`</td>
                    <td>`+data.time+`</td>
                </tr>`;
        }else{
            $.each(data.data, function(key, item){
                if(item['vrn'] == undefined)
                    return false;
                result += `<tr class="">
                    <td class="bold">`+item.vrn+`</td>
                    <td class="bold">`+item.type+`</td>
                    <td>`+item.team+`</td>
                    <td>`+item.time+`</td>
                </tr>`;
            });
        }

        result += `</tbody>
            </table>

            <div class="total-div">
                <label style="text-transform: uppercase;">Bet Amount:</label>
                <span>$ ` + data.bet_amount + `</span>
            </div>
            <div class="total-div">
                <label style="text-transform: uppercase;">Total:</label>
                <span>$ ` + data.total_amount + `</span>
            </div>
            <div class="clearfix"></div>
        </div>`;
    }
    return result;
}

function initPage(){
  loadData();
}

function loadData(){
    var betweek = $('.game-week-select').val();
    if(mainTable != null)
    {
        mainTable.destroy();
        $('#myTable').empty();

    }

    mainTable = $('#logs_tbl').DataTable( {
        "ajax": {
            "url": api_url+"/loadData",
            "type": 'post',
            "data" : {
                'betweek': betweek
            },
        },
        "pageLength": 50,
        "columns": [
            {
                "className":      'details-control',
                "orderable":      false,
                "data":           null,
                "defaultContent": ""
            },
            { 
                "data": "id" ,
                "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                    $(nTd).html("<a href='"+oData.order_url+"' target='_blank'>"+oData.id+"</a>");
                }
            },
            { "data": "date" },
            { "data": "time" },
            { "data": "user_name" },
            { "data": "investor_name" },
            { "data": "sportbook_name" },
            { "data": "action_title" },
            { "data": "amount" }
        ],
        "order": [[0, 'asc']]
    } );
     
    // Add event listener for opening and closing details
    $('#logs_tbl tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = mainTable.row( tr );
 
        if ( row.child.isShown() ) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            // Open this row
            row.child( format(row.data()) ).show();
            tr.addClass('shown');
        }
    } );
}

$(document).ready(function() {
    initPage();
} );