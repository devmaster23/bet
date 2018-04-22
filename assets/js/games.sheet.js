var tableObject = [];
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
          data: 'team2',
          readOnly: false
        },
        {
          data: 'alpha',
          readOnly: true
        },
        {
          data: 'team1',
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
    colWidths: [110, 80, 60, 250, 50, 200, 70, 70, 70, 70, 70, 70],
    rowHeights: rowHeight,
    className: "htCenter htMiddle",
    rowHeaders: true,
    colHeaders: true,
    height: tableHeight,
    outsideClickDeselects: false,
    nestedHeaders: custom_headers,
    // hiddenColumns: {
    //   columns: [0],
    //   indicators: false
    // },
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
  td.style.fontSize = fontSize;
  Handsontable.renderers.TextRenderer.apply(this, args);
}
  
function createSheets(games) {
	var types ={
    'ncaa_m': 'NCAA M', 
    'nba': 'NBA', 
    'football': 'FOOTBALL',
    'ncaa_f': 'NCAA F',
    'soccer': 'SOCCER',
    'mlb': 'MLB'
  };
  $.each(types, function(key, value){
    var data = games[value] || [];
    var container = $('div.sheet[data-type="'+key+'"]')[0];
    var title = (key == 'ncaa_m')? value+'(College Basketball)': value;
    tmpSetting = Object.assign({},hotSettings);
    tmpSetting['nestedHeaders'][0][0].label = '<label class="enter-game__header-item" data-class-name="enter-game__header1-title">'+title+'</label>';
    tmpSetting['data'] = data;
    if(tableObject[key] == undefined)
      tableObject[key] = new Handsontable(container, tmpSetting);
    else
      tableObject[key].loadData(data);
  });
}

function loadTable(){
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
            currentTable = tableObject[selectType];
        }
    });
} 

function updateTable(){
    var betweek = $('.game-week-select').val()
    var tableData = currentTable.getData();
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
            $(".notification-box").show()
            setTimeout(function() {
              $(".notification-box").hide()
            }, 2000);
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
