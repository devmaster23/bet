var pageTitle = "NCAA M";
var currentTable = null;
var defaultRowTpl ={
  '5' : '@'
};

var custom_headers = [
    [
        '',
        {label: '', colspan: 6}, 
        {label: '<img src="./assets/img/icon_game.png"> Game', colspan: 5},
        {label: '<img src="./assets/img/icon_firsthalf.png"> 1st Half', colspan: 5}
    ],
    [
        '','Date','Time','VRN','Away Team','@','Home Team','PTS','ML','Total','PTS','ML','PTS','ML','Total','PTS','ML'
    ]
];

var custom_headers_mlb = [
    [
        '',
        {label: '', colspan: 7}, 
        {label: '<img src="./assets/img/icon_game.png"> Game', colspan: 11},
        {label: '<img src="./assets/img/icon_firsthalf.png"> 1st Half' , colspan: 3}
    ],
    [
        '','Date','Time','VRN','Away Team','@','VRN','Home Team','ML','RL', 'RL ML','RRL','RRL ML','ML','RL', 'RL ML','RRL','RRL ML','Total','ML','ML','Total'
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
          data: 'date',
          type: 'date',
          dateFormat: 'MMM DD, YYYY',
          correctFormat: true,
          readOnly: false
        },  
        {
          data: 'time',
          type: 'time',
          timeFormat: 'h:mm A',
          correctFormat: true,
          readOnly: false
        },
        {
          data: 'vrn1',
          type: 'numeric',
          readOnly: false
        },
        {
          data: 'team1',
          readOnly: false
        },
        {
          data: 'alpha',
          readOnly: true
        },
        {
          data: 'team2',
          readOnly: false
        },
        {
          data: 'team1_game_pts',
          type: 'numeric',
          readOnly: false
        },
        {
          data: 'team1_game_ml',
          type: 'numeric',
          readOnly: false
        },
        {
          data: 'game_total',
          type: 'numeric',
          readOnly: false
        },
        {
          data: 'team2_game_pts',
          type: 'numeric',
          readOnly: true
        },
        {
          data: 'team2_game_ml',
          type: 'numeric',
          readOnly: false
        },
        {
          data: 'team1_first_half_pts',
          type: 'numeric',
          readOnly: false
        },
        {
          data: 'team1_first_half_ml',
          type: 'numeric',
          readOnly: false
        },
        {
          data: 'first_half_total',
          type: 'numeric',
          readOnly: false
        },
        {
          data: 'team2_first_half_pts',
          type: 'numeric',
          readOnly: true
        },
        {
          data: 'team2_first_half_ml',
          type: 'numeric',
          readOnly: false
        },
    ],
    minSpareRows: 1,
    colWidths: [110, 80, 60, 170, 50, 170, 80, 80, 80, 80, 80, 80, 80, 80, 80, 80, 80],
    rowHeights: rowHeight,
    className: "htCenter htMiddle",
    rowHeaders: true,
    colHeaders: true,
    width: 1600,
    height: tableHeight,
    outsideClickDeselects: false,
    nestedHeaders: custom_headers,
    cells: function (row, col, prop) {
      var cellProperties = {};
      cellProperties.renderer = defaultValueRenderer;
      return cellProperties;
    },
    afterChange: function (change, source) {
      if(source == "sss")
        return;
      if(change)
      {
        var row = change[0][0],
            prop = change[0][1],
            ref_value = change[0][3];
        if (prop == 'team1_game_pts'){
          currentTable.setDataAtRowProp(row,'team2_game_pts',ref_value*(-1),"sss");
        }
        if (prop == 'team1_first_half_pts'){
          currentTable.setDataAtRowProp(row,'team2_first_half_pts',ref_value*(-1),"sss");
        }
      }
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
          data: 'date',
          type: 'date',
          dateFormat: 'MMM DD, YYYY',
          correctFormat: true,
          readOnly: false
        },  
        {
          data: 'time',
          type: 'time',
          timeFormat: 'h:mm A',
          correctFormat: true,
          readOnly: false
        },
        {
          data: 'vrn1',
          type: 'numeric',
          readOnly: false
        },
        {
          data: 'team1',
          readOnly: false
        },
        {
          data: 'alpha',
          readOnly: true
        },
        {
          data: 'vrn2',
          type: 'numeric',
          readOnly: true
        },
        {
          data: 'team2',
          readOnly: false
        },
        {
          data: 'team1_game_ml',
          type: 'numeric',
          readOnly: false
        },
        {
          data: 'team1_game_rl',
          editor: 'select',
          selectOptions: ['-1.5', '1.5']
        },
        {
          data: 'team1_game_rl_ml',
          type: 'numeric',
          readOnly: false
        },
        {
          data: 'team1_game_rrl',
          type: 'numeric',
          readOnly: true
        },
        {
          data: 'team1_game_rrl_ml',
          type: 'numeric',
          readOnly: false
        },
        {
          data: 'team2_game_ml',
          type: 'numeric',
          readOnly: false
        },
        {
          data: 'team2_game_rl',
          type: 'numeric',
          readOnly: true
        },
        {
          data: 'team2_game_rl_ml',
          type: 'numeric',
          readOnly: false
        },
        {
          data: 'team2_game_rrl',
          type: 'numeric',
          readOnly: true
        },
        {
          data: 'team2_game_rrl_ml',
          type: 'numeric',
          readOnly: false
        },
        {
          data: 'game_total',
          type: 'numeric',
          readOnly: false
        },
        {
          data: 'team1_first_half_ml',
          type: 'numeric',
          readOnly: false
        },
        {
          data: 'team2_first_half_ml',
          type: 'numeric',
          readOnly: false
        },
        {
          data: 'first_half_total',
          type: 'numeric',
          readOnly: false
        },
    ],
    minSpareRows: 1,
    colWidths: [110, 80, 60, 150, 50, 60, 150, 75, 75, 75, 75, 75, 75,75, 75, 75, 75,75, 75, 75, 75],
    rowHeights: rowHeight,
    className: "htCenter htMiddle",
    rowHeaders: true,
    colHeaders: true,
    width: 1900,
    height: tableHeight,
    outsideClickDeselects: false,
    nestedHeaders: custom_headers_mlb,
    cells: function (row, col, prop) {
      var cellProperties = {};
      cellProperties.renderer = defaultValueRenderer;
      return cellProperties;
    },
    afterChange: function (change, source) {
      if(source == "sss")
        return;
      if(change)
      {
        var row = change[0][0],
            prop = change[0][1],
            ref_value = change[0][3];
        if (prop == 'team1_game_rl'){
          currentTable.setDataAtRowProp(row,'team1_game_rrl',ref_value * (-1),"sss");
          currentTable.setDataAtRowProp(row,'team2_game_rl',ref_value * (-1),"sss");
          currentTable.setDataAtRowProp(row,'team2_game_rrl',ref_value,"sss");
        }

      }
    }
};

function getPTS(ref_value, type='ML'){
  var ptsObj = refData.find(function(item){
    return item.PTS == ref_value;
  });
  return ptsObj?ptsObj[type]:0;
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

function defaultValueRenderer(instance, td, row, col, prop, value, cellProperties) {
  var args = arguments;
  if (args[5] === null && isEmptyRow(instance, row)) {
      args[5] = defaultRowTpl[col];
  }
  td.style.color = "#000";
  if (prop == 'team1' || prop == 'team2')
  {
    td.style.textAlign = "left";
  }
  Handsontable.renderers.TextRenderer.apply(this, args);
  if (prop == 'vrn2')
  {
    var vrn1 = instance.getDataAtRowProp(row,'vrn1');
    if(vrn1 != null)
      td.innerHTML = eval(vrn1) + 1;
  }

  td.style.fontSize = fontSize;
  return td;
}
  
function createSheets(games) {


  var data = games[pageType] || [];
  var container = $('div.sheet')[0];

  if (pageType == 'mlb')
    tmpSetting = Object.assign({},hotSettings_mlb);
  else
    tmpSetting = Object.assign({},hotSettings);

  currentTable = new Handsontable(container, tmpSetting);
  currentTable.loadData(data);
}

function initPage(){
  $(".loading-div").show()
  var betweek = $('.game-week-select').val()
  $.ajax({
      url: api_url+'/loadData',
      type: 'POST',
      data: {
        betweek: betweek,
        pageType: pageType
      },
      dataType: 'json',
      success: function(data) {
          pageTitle = data['pageTitle']
          createSheets(data['games']);
          $(".loading-div").hide()
      }
  });
} 

function updateTable(){
    $(".loading-div").show()

    var betweek = $('.game-week-select').val()
    var tableData = currentTable.getSourceData();
    var cleanedGridData = {};
    $.each( tableData, function( rowKey, object) {
        if (!currentTable.isEmptyRow(rowKey)) cleanedGridData[rowKey] = object;
    });

    $.ajax({
        url: api_url+'/saveData',
        type: 'POST',
        data: {
          betweek: betweek,
          game_type: pageType,
          games: JSON.stringify({data: cleanedGridData})
        },
        success: function(data) {
            initPage()
            $(".loading-div").hide()
        }
    });
}


$(document).ready(function() {
  initPage();
});
