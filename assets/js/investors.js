var currentTable = undefined;

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



var custom_headers = [
    [
    'id','sportbook_id','Title', 'SiteURL', 'Date Opened', 'Opening Balance ($)', 'Login Name', 'Password',
    ]
];

var sportbookAddSettings = {
    columns: [
        {
          data: 'id',
          readOnly: true,
        },
        {
          data: 'sportbook_id',
          readOnly: true,
        },
        {
          data: 'title',
          readOnly: true
        },
        {
          data: 'siteurl',
          readOnly: true
        },
        {
          data: 'date_opened',
          type: 'date',
          dateFormat: 'MMM DD, YYYY',
          correctFormat: true,
          readOnly: false
        },  
        {
            data: 'opening_balance',
            type: 'numeric',
        },
        {
          data: 'login_name',
          readOnly: false
        },
        {
          data: 'password',
          readOnly: false
        },
        {
          data: "action",
          readOnly: true
        }
    ],
    minSpareRows: 0,
    columnSorting: true,
    colWidths: [0,0,150, 150, 150, 200, 200, 90, 50],
    rowHeights: rowHeight,
    height: tableHeight,
    className: "htCenter htMiddle",
    rowHeaders: true,
    colHeaders: true,
    hiddenColumns: {
      columns: [0,1],
      indicators: false
    },
    outsideClickDeselects: false,
    nestedHeaders: custom_headers,
    cells: function (row, col, prop) {
      var cellProperties = {};
      cellProperties.renderer = defaultValueRenderer;
      return cellProperties;
    }
};

function defaultValueRenderer(instance, td, row, col, prop, value, cellProperties) {
  var args = arguments;
  td.style.color = "#000";
  td.style.textAlign = 'left';
  td.style.fontSize = fontSize;
  Handsontable.renderers.TextRenderer.apply(this, args);
  if (prop == 'opening_balance' || prop == 'current_balance')
  {
    if(value != null)
    {
        td.style.textAlign = "right";
        td.innerHTML = value + " $";
    }
  }
  if (prop == 'action')
  {
    td.innerHTML = '<span class="remove-sportbook-icon" data-id="'+row+'"><button type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button></span>';
  }
  return td;
}


function createSheets() {
    var container = $('div#sportbook_list')[0];
    currentTable = new Handsontable(container, sportbookAddSettings);
    currentTable.loadData(userSportbookList);
}

function loadAddTable(){
    createSheets();
} 

function openSportBookModal(){
    $("#newSportBookModal input[name=sportbook_check]:checked").prop('checked', false); 
}

function addSportBook(){
    $("#newSportBookModal input[name=sportbook_check]:checked").each(function(){
        var sportbook_id = $(this).parents('tr').data('id');
        var sportbookItem = null;
        var rowExists = undefined;
        if(sportbookList.length)
        {
            sportbookItem = sportbookList.find(function(item){
                return item['id'] == sportbook_id;
            })
        }
        rowExists = userSportbookList.find(function(item){
            return item['id'] == sportbook_id;
        })

        if(sportbookItem != null && rowExists == undefined)
        {
            userSportbookList.push(sportbookItem);
        }
    })
    currentTable.render();
}


$(document).ready(function() {
    if(page_type == 'add' || page_type == 'edit')
        loadAddTable();
    var table = $('#investors_tbl').DataTable( {
        "ajax": api_url+"/loadInvestors",
        "columns": [
            {
                "className":      'details-control',
                "orderable":      false,
                "data":           null,
                "defaultContent": ""
            },
            { "data": "first_name" },
            { "data": "last_name" },
            { "data": "email" },
            { "data": "phone_number" },
            { "data": "starting_bankroll" },
            { "data": "current_balance" },
            { "data": "country" },
            { "data": "custom_action" }
        ],
        "columnDefs": [
            {
                "render": function ( data, type, row ) {
                    return '$ '+ data;
                },
                "targets": 5
            },
            {
                "render": function ( data, type, row ) {
                    return '$ '+ data;
                },
                "targets": 6
            }
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

    $("#add_submit").click(function(){
        var sportbookData = currentTable.getSourceData();
        var data = [];
        sportbookData.forEach( function(element, index) {
            data.push({
                sportbook_id: element.id,
                date_opened: element.date_opened,
                opening_balance: element.opening_balance,
                current_balance: element.current_balance,
                login_name: element.login_name,
                password: element.password
            })
        });
        $("[name=sportbook_data]").val(JSON.stringify(data));
        return true;
    })

    $("#edit_submit").click(function(){
        var sportbookData = currentTable.getSourceData();
        var data = [];
        sportbookData.forEach( function(element, index) {
            var relation_id = element.relation_id != undefined ? element.relation_id: -1;
            data.push({
                relation_id: relation_id,
                sportbook_id: element.id,
                date_opened: element.date_opened,
                opening_balance: element.opening_balance,
                current_balance: element.current_balance,
                login_name: element.login_name,
                password: element.password
            })
        });
        $("[name=sportbook_data]").val(JSON.stringify(data));
        return true;
    })

    $(document).on('click','.remove-sportbook-icon',function(){
        var row_id = $(this).data('id');
        return currentTable.alter("remove_row", row_id);
    })

    $("#back_button").click(function(){
        location.href = api_url;
    })
} );