var tableObject = null;
var allTabelTimeOut = null;
var pageTitle = 'All Picks';

var title_types ={
    'ncaa_m': 'NCAA M', 
    'nba': 'NBA', 
    'football': 'NFL',
    'ncaa_f': 'NCAA F',
    'soccer': 'SOC',
    'mlb': 'MLB'
};

var custom_headers = [
    [
      '',
      '',
      {label: 'NCAA M(College Basketball)', colspan: 3}, 
      {label: 'Game', colspan: 3},
      {label: '1st Half', colspan: 3}
    ],
    [
      '','VRN','Date','Time','Team','PTS','ML','Total','PTS','ML','Total'
    ]
];

var custom_headers_mlb = [
    [
      '',
      '',
      {label: 'MLB', colspan: 3}, 
      {label: 'Game', colspan: 4},
      {label: '1st Five', colspan: 2}
    ],
    [
      '','VRN','Date','Time','Team','ML','RL','RRL','Total','ML','Total'
    ]
];


var all_custom_headers = [
    [
      {label: 'Wrappers', colspan: 8},
      {label: 'Candy', colspan: 8},
      {label: 'Picks', colspan: 8} 
    ],
    [
      '#','Sport','VRN','SP / ML<br/>(Ov / Un)','Team','Line','Game Time','Count',
      '#','Sport','VRN','SP / ML<br/>(Ov / Un)','Team','Line','Game Time','Count',
      '#','Sport','VRN','SP / ML<br/>(Ov / Un)','Team','Line','Game Time','Count'
    ]
];

var hotSettings = {
    columns: [
        {
          data: 'id',
          type: 'numeric',
          readOnly: true
        },
        {
          data: 'vrn',
          type: 'numeric',
          readOnly: true
        },
        {
          data: 'date',
          type: 'date',
          dateFormat: 'MMM DD, YYYY',
          correctFormat: true,
          readOnly: true
        },  
        {
          data: 'time',
          type: 'time',
          timeFormat: 'h:mm A',
          correctFormat: true,
          readOnly: true
        },
        {
          data: 'team',
          readOnly: true
        },
        {
          data: 'game_pts',
          renderer: coverRenderer,
          readOnly: true
        },
        {
          data: 'game_ml',
          renderer: coverRenderer,
          readOnly: true
        },
        {
          data: 'game_total',
          renderer: coverRenderer,
          readOnly: true
        },
        {
          data: 'first_half_pts',
          renderer: coverRenderer,
          readOnly: true
        },
        {
          data: 'first_half_ml',
          renderer: coverRenderer,
          readOnly: true
        },
        {
          data: 'first_half_total',
          renderer: coverRenderer,
          readOnly: true
        },
    ],
    minSpareRows: 0,
    minSpareCols: 0,
    colWidths: [60, 130, 100, 250, 90,90,90,90,90,90],
    rowHeights: rowHeight,
    className: "htCenter htMiddle",
    height: tableHeight,
    rowHeaders: false,
    colHeaders: true,
    nestedHeaders: custom_headers,
    cells: function (row, col, prop) {
      if(col >=0 && col <=4)
      {
        var cellProperties = {};
        cellProperties.renderer = defaultValueRenderer;
        return cellProperties;
      }
    },
    afterLoadData: function (change, source) {
      mergeFields();
      setStyle();
    }
};

var hotSettings_mlb = {
    columns: [
        {
          data: 'id',
          type: 'numeric',
          readOnly: true
        },
        {
          data: 'vrn',
          type: 'numeric',
          readOnly: true
        },
        {
          data: 'date',
          type: 'date',
          dateFormat: 'MMM DD, YYYY',
          correctFormat: true,
          readOnly: true
        },  
        {
          data: 'time',
          type: 'time',
          timeFormat: 'h:mm A',
          correctFormat: true,
          readOnly: true
        },
        {
          data: 'team',
          readOnly: true
        },
        {
          data: 'game_ml',
          renderer: coverRenderer,
          readOnly: true
        },
        {
          data: 'game_rl',
          renderer: coverRenderer,
          readOnly: true
        },
        {
          data: 'game_rrl',
          renderer: coverRenderer,
          readOnly: true
        },
        {
          data: 'game_total',
          renderer: coverRenderer,
          readOnly: true
        },
        {
          data: 'first_half_ml',
          renderer: coverRenderer,
          readOnly: true
        },
        {
          data: 'first_half_total',
          renderer: coverRenderer,
          readOnly: true
        },
    ],
    minSpareRows: 0,
    minSpareCols: 0,
    colWidths: [60, 130, 100, 250, 90,90,90,90,90,90],
    rowHeights: rowHeight,
    className: "htCenter htMiddle",
    height: tableHeight,
    rowHeaders: false,
    colHeaders: true,
    nestedHeaders: custom_headers_mlb,
    cells: function (row, col, prop) {
      if(col >=0 && col <=4)
      {
        var cellProperties = {};
        cellProperties.renderer = defaultValueRenderer;
        return cellProperties;
      }
    },
    afterLoadData: function (change, source) {
      mergeFields();
      setStyle();
    }
};


function defaultValueRenderer(instance, td, row, col, prop, value, cellProperties) {
  var args = arguments;
  td.style.fontSize = fontSize;
  td.style.color = '#000';
  if(col == 1)
  {
    td.style.fontWeight = 'bold';
    td.style.fontSize = fontSize+1;
  }
  if (prop == 'team')
  {
    td.style.textAlign = "left";
  }
  Handsontable.renderers.TextRenderer.apply(this, args);
}
function coverRenderer (instance, td, row, col, prop, value, cellProperties) {
  var jsonObj = {},
    checked = false,
    checkedList = [];
  
  var displayValueIndex = prop + '_value';
  var displayValue = instance.getDataAtRowProp(row, displayValueIndex);

  try{
    jsonObj = JSON.parse(value);
    if(jsonObj != null)
    {
      $.each(jsonObj, function(i, obj) {
        if(obj)
        {
          checked = true;
          if(i == 'pick')
            checkedList[0] = true;
          if(i == 'wrapper')
            checkedList[1] = true;
          if(i == 'candy')
            checkedList[2] = true;
        }
      });
    }
  }catch(e){
  }

  $(td).html('<div class="pick-check-box" checked="'+checked+'" data-pick="'+checkedList[0]+'" data-wrapper="'+checkedList[1]+'" data-candy="'+checkedList[2]+'" data-toggle="popover" data-placement="bottom" data-html="true" data-row="'+row+'" data-prop="'+prop+'">'+displayValue+'</div>');
  return td;
}

function isEmptyRow(instance, row) {

    var rowData = instance.countRows();

    for (var i = 0, ilen = rowData.length; i < ilen; i++) {

      if (rowData[i] !== null) {
        return false;
      }
    }

    return true;
  }

function createSheets(picks) {
  

  var data = picks[pageType] || [];
  var container = $('div.sheet')[0];

  if(pageType == 'mlb')
  {
    tmpSetting = Object.assign({},hotSettings_mlb);
  }else{
    tmpSetting = Object.assign({},hotSettings);
  }
  tmpSetting['nestedHeaders'][0][1].label = '<label class="enter-game__header-item" data-class-name="enter-game__header1-title">'+pageTitle+'</label>';

  tableObject = new Handsontable(container, tmpSetting);
  tableObject.loadData(data);
}

function createAllPickSheets(data){
  
  var wrapperList = [],
      candyList   = [],
      pickList = [];
  $.each(data, function(index, item){

    if(item['wrapper_vrn'] != null)
    {
      wrapperList.push(new Array(
        item['wrapper_game_type'],
        item['wrapper_vrn'],
        item['wrapper_type'],
        item['wrapper_team'],
        item['wrapper_line'],
        item['wrapper_time'],
        item['wrapper_count']
      ));
    }

    if(item['candy_vrn'] != null)
    {
      candyList.push(new Array(
        item['candy_game_type'],
        item['candy_vrn'],
        item['candy_type'],
        item['candy_team'],
        item['candy_line'],
        item['candy_time'],
        item['candy_count']
      ));
    }

    if(item['pick_vrn'] != null)
    {
      pickList.push(new Array(
        item['pick_game_type'],
        item['pick_vrn'],
        item['pick_type'],
        item['pick_team'],
        item['pick_line'],
        item['pick_time'],
        item['pick_count']
      ));
    }

  })
  
  $('.picks .all-picks #wrapper-table tbody').empty().html(buildTableBody(wrapperList));
  $('.picks .all-picks #candy-table tbody').empty().html(buildTableBody(candyList));
  $('.picks .all-picks #pick-table tbody').empty().html(buildTableBody(pickList));
}

function buildTableBody(arr){
  var tbodyHtml = '';
  $.each(arr, function(index, item){
    tbodyHtml += '<tr>';
    tbodyHtml += '<td>'+index+'</td>';
    $.each(item, function(index1, value){
      tbodyHtml += '<td>'+value+'</td>';
    })
    tbodyHtml += '</tr>';
  });

  return tbodyHtml;
}

function loadAllPickTable(){
  var betweek = $('.game-week-select').val()
  $(".loading-div").show()
  $.ajax({
      url: api_url+'/loadAllPickData',
      type: 'POST',
      data: {
        betweek: betweek,
        type: pageType,
      },
      success: function(data) {
        pageTitle = data['pageTitle'];
        createAllPickSheets(data);
        $(".loading-div").hide()
      }
  });
}

function loadTable(){
  var betweek = $('.game-week-select').val()
  $(".loading-div").show()
  $.ajax({
      url: api_url+'/loadData',
      type: 'POST',
      data: {
        betweek: betweek,
        type: pageType,
      },
      success: function(data) {
        pageTitle = data['pageTitle'];
        createSheets(data['picks']);
        $(".loading-div").hide()
      }
  });
} 
function refreshTable(tableItem, data){
  tableItem.loadData(data);
  setStyle();
}
function updateTable(){
    $(".loading-div").show()

    var betweek = $('.game-week-select').val()
    var hot = tableObject;
    var tableData = hot.getSourceData();

    var cleanedGridData = {};
    $.each( tableData, function( rowKey, object) {
      if (!hot.isEmptyRow(rowKey)) cleanedGridData[rowKey] = object;
    });
    
    $.ajax({
        url: api_url+'/saveData',
        type: 'POST',
        data: {
          betweek: betweek,
          game_type: pageType,
          picks: JSON.stringify({data: cleanedGridData})
        },
        success: function(data) {
          $(".popover").popover('hide');
          loadTable()
          $(".loading-div").hide()
        }
    });
}

function setStyle(){
  $(document).on("click","[data-toggle=popover]",function(e) {
    $(".popover").popover('hide');
    $(this).popover({ 
      html: true,
      content: function() {
        var row = $(this).data('row');
        var prop = $(this).data('prop');
        var html = '<div id="popup-div" data-row="'+row+'" data-prop="'+prop+'">'+
          '<div><input type="checkbox" '+($(this).data('pick') == true?'checked':'')+' class="pick-checkbox" id="pick"/><label for="pick">PICK</label></div>'+
          '<div><input type="checkbox" '+($(this).data('wrapper') == true?'checked':'')+' class="pick-checkbox" id="wrapper"/><label for="wrapper">WRAPPER</label></div>'+
          '<div><input type="checkbox" '+($(this).data('candy') == true?'checked':'')+' class="pick-checkbox" id="candy"/><label for="candy">CANDY</label></div>'+
          '</div>';
        return html;
      }
    });
    $(this).popover('show');
  });
}

function mergeFields(){
  if(tableObject != null)
  {
    var title = title_types[pageType];
    title = (pageType == 'ncaa_m')? title+'(College Basketball)': title;

    var cleanedGridData = [];
    $.each( tableObject.getData(), function( rowKey, object) {
        if (!tableObject.isEmptyRow(rowKey)) cleanedGridData.push(object);
    }); 
    var row_count = cleanedGridData.length;
    var hotOptions = {
      mergeCells: []
    };
    hotSettings['nestedHeaders'][0][2].label = '<label class="enter-pick__header-item" data-class-name="enter-pick__header1-title">'+title+'</label>';
    if(row_count != 0)
    {
      for(row = 0; row < row_count; row++){
        if(row == 0 || row % 2 == 0)
        {  
          hotOptions.mergeCells = hotOptions.mergeCells.concat([
            {row: row, col: 2, rowspan: 2, colspan: 1},
            {row: row, col: 3, rowspan: 2, colspan: 1},
          ]);
        }
      }
    }
    tableObject.updateSettings(hotOptions);
  }
}

function initPage(){
  if(pageType == 'all_picks')
  {
    $(".save-button-div").hide();
    loadAllPickTable();
    // allTabelTimeOut = setInterval(function(){
    //   loadAllPickTable();
    // }, 3000);
  }else{
    $(".save-button-div").show();
    // clearInterval(allTabelTimeOut)
    loadTable();  
  }
}

$(document).ready(function() {

  initPage();

  $('body').click(function(event) {
    if($(event.target).parents('.popover-body').length == 0 && !$(event.target).hasClass('pick-check-box')){
      $(".popover").popover('hide');
    }
  });
  $(document).on('change','.pick-checkbox',function(){

    var hot = tableObject,
        tableData = hot.getSourceData(),
        $popoverDiv = $(this).parents('#popup-div'),
        row = $popoverDiv.data('row'),
        prop = $popoverDiv.data('prop');
    var jsonObj = {
        'pick': $popoverDiv.find('[id="pick"]').is(':checked'),
        'wrapper': $popoverDiv.find('[id="wrapper"]').is(':checked'),
        'candy': $popoverDiv.find('[id="candy"]').is(':checked'),
    }    

    tableData[row][prop] = JSON.stringify(jsonObj);
    refreshTable(hot,tableData);
  })

});
