
/* Formatting function for row details - modify as you need */
function format ( d ) {
    // `d` is the original data object for the row
    var html = '<h4>SportBooks</h4><table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">'+
        '<thead>'+
            '<tr><td>ID</td><td>Title</td><td>SiteURL</td><td>Date Opened</td><td>Opening Balance ($)</td><td>Current Balance ($)</td><td>Login Name</td><td>Password</td></tr>'+
        '</thead>'+
        '<tbody>';

    $.each(d.sportbooks, function(key, item){
        html += '<tr><td>'+(key + 1)+'</td><td>'+item.title+'</td><td>'+item.siteurl+'</td><td>'+item.date_opened+'</td><td>'+item.opening_balance+'</td><td>'+item.current_balance+'</td><td>'+item.login_name+'</td><td>'+item.password+'</td></tr>';
    });

    html += '</tbody>'+
    '</table>';
    return html;
}

$(document).ready(function() {
    var table = $('#investors_tbl').DataTable( {
        "ajax": api_url+"/loadInvestors",
        "columns": [
            {
                "className":      'details-control',
                "orderable":      false,
                "data":           null,
                "defaultContent": ""
            },
            { "data": "full_name" },
            { "data": "bets" },
            { "data": "accounts" },
            { "data": "custom_action" }
        ],
        "columnDefs": [
            {"className": "dt-center", "targets": "_all"}
        ],
        "order": [[0, 'asc']]
    } );
     
    // Add event listener for opening and closing details
    $('#investors_tbl tbody').on('click', 'td.details-control', function () {
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

    $('#investors_tbl tbody').on('click', '.delete', function () {
        var id = $(this).parents('.action-div').data('id');
        if(confirm("Are you sure you want to remove this Investor and Sportbooks?"))
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

    $('.sportbookList.selectable .sportbook-tr').on('click', function(){
        $('.sportbook-tr').removeClass('selected');
        $(this).addClass('selected');
    })

    $('#submit-form .save-button').on('click',function(){
        var $objSelected = $('.sportbook-tr.selected');
        var sportbookID = null;
        
        if($objSelected.length)
            sportbookID = $($objSelected[0]).data('id');

        var submit_type = $(this).data('type');
        $('#submit-form input[name=sportbookID]').val(sportbookID);
        $('#submit-form input[name=submit_type]').val(submit_type);
        $('#submit-form').submit();
    })
} );