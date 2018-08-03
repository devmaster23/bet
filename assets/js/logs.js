var mainTable = null;
/* Formatting function for row details - modify as you need */
function format ( d ) {
    // `d` is the original data object for the row
    var result = '';
    if(d.action != 'balance')
    {
        result = `<div class="bet-info">
            <h5 class="form-header">Description</h5>
            <div class="description-div">
                <div class="description-div_type">
                    <img src="assets/img/icon_soccer.png">
                    <span class="setting-span">SOC</span>
                    <span class="setting-span">Round Robin</span>
                </div>
                <div>
                    <span class="setting-span number red">4</span>
                    <span class="setting-span number">3</span>
                    <span class="setting-span number">2</span>
                    <span class="setting-span number">1</span>
                </div>
            </div>
            <table class="table table-striped table-lightfont">
                <thead>
                    <tr>
                        <th>VRN</th>
                        <th>Line</th>
                        <th>Team</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="">
                        <td class="bold">701</td>
                        <td class="bold">PTS</td>
                        <td class="text-left">Detroit Pistons</td>
                        <td class="">12:10 PM</td>
                    </tr>
                </tbody>
            </table>

            <div class="total-div">
                <label style="text-transform: uppercase;">Bet Amount:</label>
                <span>$ 123</span>
            </div>
            <div class="total-div">
                <label style="text-transform: uppercase;">Total:</label>
                <span>$ 123</span>
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
    console.log($('#logs_tbl'))
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
                    $(nTd).html("<a href='#'>"+oData.id+"</a>");
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