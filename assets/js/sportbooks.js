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

$(document).ready(function() {
    var table = $('#sportbooks').DataTable( {
        "ajax": api_url+"/loadSportbooks",
        "columns": [
            {
                "className":      'details-control',
                "orderable":      false,
                "data":           null,
                "defaultContent": ""
            },
            { "data": "title" },
            { "data": "siteurl" },
            { "data": "city" },
            { "data": "state" },
            { "data": "country" },
            { "data": "contact_name" },
            { "data": "phone_number" },
            { "data": "custom_action" }
        ],
        "order": [[0, 'asc']]
    } );
     
    // Add event listener for opening and closing details
    $('#sportbooks tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = table.row( tr );
 
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

    $('#sportbooks tbody').on('click', '.delete', function () {
        var id = $(this).parents('.action-div').data('id');
        if(confirm("Are you sure you want to remove this sportbook?"))
        {
            $.ajax({
                url: api_url+'/delete',
                type: 'POST',
                data: {
                  id: id
                },
                success: function(data) {
                    location.href = api_url;
                }
            });
        }
    } );
} );