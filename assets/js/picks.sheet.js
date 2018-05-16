var tableObject = null;
var allTableObject = null;
var allTabelTimeOut = null;

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
      {label: 'Game', colspan: 3},
      {label: '1st Five', colspan: 2}
    ],
    [
      '','VRN','Date','Time','Team','Run Line','Money Line','Total','Money Line','Total'
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
    colWidths: [60, 130, 100, 250, 90,90,90,90,90],
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

var allHotSettings = {
    columns: [
        {
          data: 'id',
          type: 'numeric',
          readOnly: true
        },
        {
          data: 'wrapper_game_type',
          readOnly: true
        },
        {
          data: 'wrapper_vrn',
          type: 'numeric',
          readOnly: true
        },
        {
          data: 'wrapper_type',
          readOnly: true
        },
        {
          data: 'wrapper_team',
          readOnly: true
        },
        {
          data: 'wrapper_line',
          type: 'numeric',
          readOnly: true
        },
        {
          data: 'wrapper_time',
          type: 'time',
          timeFormat: 'h:mm A',
          correctFormat: true,
          readOnly: true
        },
        {
          data: 'wrapper_count',
          type: 'numeric',
          readOnly: true
        },
        {
          data: 'id',
          type: 'numeric',
          readOnly: true
        },
        {
          data: 'candy_game_type',
          readOnly: true
        },
        {
          data: 'candy_vrn',
          type: 'numeric',
          readOnly: true
        },
        {
          data: 'candy_type',
          readOnly: true
        },
        {
          data: 'candy_team',
          readOnly: true
        },
        {
          data: 'candy_line',
          type: 'numeric',
          readOnly: true
        },
        {
          data: 'candy_time',
          type: 'time',
          timeFormat: 'h:mm A',
          correctFormat: true,
          readOnly: true
        },
        {
          data: 'candy_count',
          type: 'numeric',
          readOnly: true
        },
        {
          data: 'id',
          type: 'numeric',
          readOnly: true
        },
        {
          data: 'pick_game_type',
          readOnly: true
        },
        {
          data: 'pick_vrn',
          type: 'numeric',
          readOnly: true
        },
        {
          data: 'pick_type',
          readOnly: true
        },
        {
          data: 'pick_team',
          readOnly: true
        },
        {
          data: 'pick_line',
          type: 'numeric',
          readOnly: true
        },
        {
          data: 'pick_time',
          type: 'time',
          timeFormat: 'h:mm A',
          correctFormat: true,
          readOnly: true
        },
        {
          data: 'pick_count',
          type: 'numeric',
          readOnly: true
        }
    ],
    minSpareRows: 0,
    minSpareCols: 0,
    colWidths: [40, 100, 50,100, 250, 60,80,60,40, 100, 50,100, 250, 60,80,60,40, 100, 50,100, 250, 60,80,60],
    rowHeights: rowHeight,
    className: "htCenter htMiddle",
    height: tableHeight,
    rowHeaders: false,
    colHeaders: true,
    nestedHeaders: all_custom_headers,
    cells: function (row, col, prop) {
      var cellProperties = {};
      cellProperties.renderer = allDefaultValueRenderer;
      return cellProperties;
    }
};

function allDefaultValueRenderer(instance, td, row, col, prop, value, cellProperties) {
  var args = arguments;
  td.style.fontSize = fontSize;
  td.style.color = '#000';
  if (prop == 'pick_team' || prop == 'wrapper_team' || prop == 'candy_team')
  {
    td.style.textAlign = "left";
  }
  Handsontable.renderers.TextRenderer.apply(this, args);
}

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
  // td.style.backgroundColor = 'yellow';
  var jsonObj = {};
  var checked = false;
  var checkedList = [];

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

  $(td).html('<div class="pick-check-box" checked="'+checked+'" data-pick="'+checkedList[0]+'" data-wrapper="'+checkedList[1]+'" data-candy="'+checkedList[2]+'" data-toggle="popover" data-placement="bottom" data-html="true" data-row="'+row+'" data-prop="'+prop+'"></div>');
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
  var title_types ={
    'ncaa_m': 'NCAA M', 
    'nba': 'NBA', 
    'football': 'NFL',
    'ncaa_f': 'NCAA F',
    'soccer': 'SOC',
    'mlb': 'MLB'
  };

  var selectType = $('#sheets .nav-link.active').data('type');
  var title = title_types[selectType];

  var data = picks[selectType] || [];
  var container = $('div.sheet[data-type="'+selectType+'"]')[0];
  var title = (selectType == 'ncaa_m')? title+'(College Basketball)': title;

  

  if(selectType == 'mlb')
  {
    tmpSetting = Object.assign({},hotSettings_mlb);
  }else{
    tmpSetting = Object.assign({},hotSettings);
  }

  tmpSetting['nestedHeaders'][0][1].label = '<label class="enter-game__header-item" data-class-name="enter-game__header1-title">'+title+'</label>';

  tableObject = new Handsontable(container, tmpSetting);
  tableObject.loadData(data);
}

function createAllPickSheets(data){
  var key = 'all_picks';
  var container = $('div.sheet[data-type="'+key+'"]')[0];
  allHotSettings['data'] = data;
  if(allTableObject == null)
    allTableObject = new Handsontable(container, allHotSettings);
  else
    allTableObject.loadData(data);  
}

function loadAllPickTable(){
  var betweek = $('.game-week-select').val()
  $(".loading-div").show()
  $.ajax({
      url: api_url+'/loadAllPickData',
      type: 'POST',
      data: {
        betweek: betweek
      },
      success: function(data) {
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
        betweek: betweek
      },
      success: function(data) {
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
    var selectType = $('#sheets .nav-link.active').data('type');
    var hot = tableObject;
    var tableData = hot.getData();

    var cleanedGridData = {};
    $.each( tableData, function( rowKey, object) {
        if(rowKey % 2 != 0)
        {
          object[1] = tableData[rowKey-1][1];
          object[2] = tableData[rowKey-1][2];
        }
        if(selectType == 'mlb')
        {
          object.splice(8, 0, "");
        }
        if (!hot.isEmptyRow(rowKey)) cleanedGridData[rowKey] = object;
    });

    $.ajax({
        url: api_url+'/saveData',
        type: 'POST',
        data: {
          betweek: betweek,
          game_type: selectType,
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
    var selectType = $('#sheets .nav-link.active').data('type');
    var title = title_types[selectType];
    title = (selectType == 'ncaa_m')? title+'(College Basketball)': title;

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
      tableObject.updateSettings(hotOptions);
    }
  }
}

$(document).on('click','#sheets .nav-link',function(){
  initPage();  
});

function initPage(){
  var selectType = $('#sheets .nav-link.active').data('type');
  if(selectType == 'all_picks')
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

    var selectType = $('#sheets .nav-link.active').data('type');
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
