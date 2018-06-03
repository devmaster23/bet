
var currentTable = null;
var defaultRowTpl ={
  '5' : '@'
};

var custom_headers = [
    [
        '',
        {label: 'NCAA M(College Basketball)', colspan: 6}, 
        {label: 'Game', colspan: 3},
        {label: '1st Half', colspan: 3}
    ],
    [
        '','Date','Time','VRN','Away Team','@','Home Team','PTS','ML','Total','PTS','ML','Total'
    ]
];

var custom_headers_mlb = [
    [
        '',
        {label: 'MLB', colspan: 7}, 
        {label: 'Game', colspan: 7},
        {label: '1st Half', colspan: 3}
    ],
    [
        '','Date','Time','VRN','Away Team','@','VRN','Home Team','RL', 'RL ML','ML','RL', 'RL ML','ML','Total','ML','ML','Total'
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
          data: 'game_pts',
          type: 'numeric',
          readOnly: false
        },
        {
          data: 'game_ml',
          type: 'numeric',
          readOnly: true
        },
        {
          data: 'game_total',
          type: 'numeric',
          readOnly: false
        },
        {
          data: 'first_half_pts',
          type: 'numeric',
          readOnly: true
        },
        {
          data: 'first_half_ml',
          type: 'numeric',
          readOnly: true
        },
        {
          data: 'first_half_total',
          type: 'numeric',
          readOnly: true
        },
    ],
    minSpareRows: 1,
    colWidths: [110, 80, 60, 250, 50, 200, 90, 90, 90, 90, 90, 90],
    rowHeights: rowHeight,
    className: "htCenter htMiddle",
    rowHeaders: true,
    colHeaders: true,
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
        if (prop == 'game_pts'){
          var ptsObj = getPTS(ref_value);

          var game_ml         = getPTS(ref_value),
              first_half_pts  = (ref_value? Math.floor(ref_value) / 2 : 0).toFixed(1);
              first_half_pts = parseFloat(first_half_pts) * 10 / 10;
              first_half_ml = getPTS(first_half_pts);
          currentTable.setDataAtRowProp(row,'game_ml',game_ml,"sss");
          currentTable.setDataAtRowProp(row,'first_half_pts',first_half_pts,"sss");
          currentTable.setDataAtRowProp(row,'first_half_ml',first_half_ml,"sss");
        }

        if (prop == 'game_total'){
          var value = ref_value?ref_value/2:0;
          currentTable.setDataAtRowProp(row,'first_half_total',value,"sss");
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
          data: 'game_rl',
          editor: 'select',
          selectOptions: ['-1.5', '1.5']
        },
        {
          data: 'game_rl_ml',
          type: 'numeric',
          readOnly: false
        },
        {
          data: 'game_ml',
          type: 'numeric',
          readOnly: false
        },
        {
          data: 'home_game_rl',
          type: 'numeric',
          readOnly: true
        },
        {
          data: 'home_game_rl_ml',
          type: 'numeric',
          readOnly: true
        },
        {
          data: 'home_game_ml',
          type: 'numeric',
          readOnly: true
        },
        {
          data: 'game_total',
          type: 'numeric',
          readOnly: false
        },
        {
          data: 'first_half_ml',
          type: 'numeric',
          readOnly: true
        },
        {
          data: 'home_first_half_ml',
          type: 'numeric',
          readOnly: true
        },
        {
          data: 'first_half_total',
          type: 'numeric',
          readOnly: true
        },
    ],
    minSpareRows: 1,
    colWidths: [110, 80, 60, 150, 50, 60, 150, 80, 80, 80, 80, 80, 80,80, 80, 80, 80],
    rowHeights: rowHeight,
    className: "htCenter htMiddle",
    rowHeaders: true,
    colHeaders: true,
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
        if (prop == 'game_ml'){
          var value = ref_value?ref_value/2:0;
          currentTable.setDataAtRowProp(row,'first_half_ml',value,"sss");
        }

        if (prop == 'game_total'){
          var value = ref_value?ref_value/2:0;
          currentTable.setDataAtRowProp(row,'first_half_total',value,"sss");
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

  if (prop == 'home_game_rl' || prop == 'home_game_rl_ml' || prop == 'home_game_ml')
  {
    var org_prop = prop.substring(5);
    var org_val = instance.getDataAtRowProp(row,org_prop);
    if(org_val != null)
      td.innerHTML = eval(org_val) * -1;
  }

  if (prop == 'home_first_half_ml'){
    var org_val = instance.getDataAtRowProp(row,'first_half_ml');
    if(org_val != null)
      td.innerHTML = eval(org_val) * -1;
  }

  td.style.fontSize = fontSize;
  return td;
}
  
function createSheets(games) {

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

  var data = games[selectType] || [];
  var container = $('div.sheet[data-type="'+selectType+'"]')[0];

  var title = (selectType == 'ncaa_m')? title+'(College Basketball)': title;

  if (selectType == 'mlb')
    tmpSetting = Object.assign({},hotSettings_mlb);
  else
    tmpSetting = Object.assign({},hotSettings);

  tmpSetting['nestedHeaders'][0][1].label = '<label class="enter-game__header-item" data-class-name="enter-game__header1-title">'+title+'</label>';

  currentTable = new Handsontable(container, tmpSetting);
  currentTable.loadData(data);
}

function loadTable(){
  $(".loading-div").show()
  var betweek = $('.game-week-select').val()
  $.ajax({
      url: api_url+'/loadData',
      type: 'POST',
      data: {
        betweek: betweek
      },
      dataType: 'json',
      success: function(data) {
          createSheets(data['games']);
          var selectType = $('#sheets .nav-link.active').data('type');
          $(".loading-div").hide()
      }
  });
} 

function updateTable(){
    $(".loading-div").show()

    var betweek = $('.game-week-select').val()
    var tableData = currentTable.getSourceData();
    var selectType = $('#sheets .nav-link.active').data('type');
    var cleanedGridData = {};
    $.each( tableData, function( rowKey, object) {
        if (!currentTable.isEmptyRow(rowKey)) cleanedGridData[rowKey] = object;
    });

    $.ajax({
        url: api_url+'/saveData',
        type: 'POST',
        data: {
          betweek: betweek,
          game_type: selectType,
          games: JSON.stringify({data: cleanedGridData})
        },
        success: function(data) {
            loadTable()
            $(".loading-div").hide()
        }
    });
}

$(document).on('click','#sheets .nav-link',function(){
  loadTable();
});

$(document).ready(function() {
  // $('#sheets').tab();
  loadTable();
});
