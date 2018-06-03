var currentTableWeek = undefined;
var currentTableYear = undefined;
var pageType='week';
var custom_headers = [
    [
    '','Date <br/>Opened', 'Opening <br/>Balance', 'Last Week <br/>Balance', 'Current <br/>Balance', 'Chagne <br/>Since Start (%)','Chagne Since <br/>Last Start (%)'
    ]
];

var custom_headers_year = [
    [
    '','Date <br/>Opened', 'Opening <br/>Balance'
    ]
];

var sportbooksSettings = {
    columns: [
        {
          data: 'title',
          readOnly: true
        },
        {
          data: 'date_opened',
          type: 'date',
          dateFormat: 'MMM DD, YYYY',
          correctFormat: true,
          readOnly: true
        },  
        {
            data: 'opening_balance',
            type: 'numeric',
            readOnly: true
        },
        {
            data: 'lastweek_balance',
            type: 'numeric',
            readOnly: true
        },
        {
            data: 'current_balance',
            type: 'numeric',
        },
        {
            data: 'current_change',
            type: 'numeric',
            readOnly: true
        },
        {
            data: 'lastweek_change',
            type: 'numeric',
            readOnly: true
        }
    ],
    minSpareRows: 0,
    columnSorting: true,
    colWidths: [200,150, 150, 150, 200, 200, 200, 90],
    rowHeights: rowHeight,
    height: 300,
    className: "htCenter htMiddle",
    rowHeaders: false,
    colHeaders: true,
    outsideClickDeselects: false,
    nestedHeaders: custom_headers,
    columnSummary: [
      {
        destinationRow: 0,
        reversedRowCoords: true,
        destinationColumn: 2,
        type: 'sum',
        forceNumeric: true
      },
      {
        destinationRow: 0,
        reversedRowCoords: true,
        destinationColumn: 3,
        type: 'sum',
        forceNumeric: true
      },
      {
        destinationRow: 0,
        reversedRowCoords: true,
        destinationColumn: 4,
        type: 'sum',
        forceNumeric: true
      }
    ],
    cells: function (row, col, prop) {
      var cellProperties = {};
      cellProperties.renderer = defaultValueRenderer;
      return cellProperties;
    }
};
var sportbooksSettingsYear = {
    columns: [
        {
          data: 'title',
          readOnly: true
        },
        {
          data: 'date_opened',
          type: 'date',
          dateFormat: 'MMM DD, YYYY',
          correctFormat: true,
          readOnly: true
        },  
        {
            data: 'opening_balance',
            type: 'numeric',
            readOnly: true
        }
    ],
    minSpareRows: 0,
    columnSorting: true,
    colWidths: [200,150, 150],
    rowHeights: rowHeight,
    height: 300,
    className: "htCenter htMiddle",
    rowHeaders: false,
    colHeaders: true,
    outsideClickDeselects: false,
    nestedHeaders: custom_headers,
    cells: function (row, col, prop) {
      var cellProperties = {};
      cellProperties.renderer = defaultValueRendererYear;
      return cellProperties;
    }
};

function defaultValueRenderer(instance, td, row, col, prop, value, cellProperties) {
  var args = arguments;
  td.style.textAlign = "left";
  td.style.fontSize = fontSize;
  Handsontable.renderers.TextRenderer.apply(this, args);
  if (prop == 'opening_balance' || prop == 'lastweek_balance' || prop == 'current_balance')
  {
    td.style.textAlign = "right";
    if(value != null)
      td.innerHTML = value + " $";
  }
  if (prop == 'current_change')
  { 
    var current_balance = eval(instance.getDataAtRowProp(row,'current_balance')),
        opening_balance = eval(instance.getDataAtRowProp(row,'opening_balance'));
    var percent = opening_balance == 0 ? '0' : (current_balance - opening_balance ) / opening_balance * 100;
    td.innerHTML = eval(percent).toFixed(2) + ' %';
  }
  if (prop == 'lastweek_change')
  { 
    var lastweek_balance = eval(instance.getDataAtRowProp(row,'lastweek_balance')),
        current_balance = eval(instance.getDataAtRowProp(row,'current_balance'));
    var percent = 'NA'
    if(lastweek_balance != 'NA')
    {
        percent = current_balance == 0 ? '0' : (lastweek_balance - current_balance ) / current_balance * 100;
        percent = eval(percent).toFixed(2) + ' %';
    }
    td.innerHTML = percent;
  }
  return td;
}

function defaultValueRendererYear(instance, td, row, col, prop, value, cellProperties) {
  var args = arguments;
  
  Handsontable.renderers.TextRenderer.apply(this, args);
  td.style.textAlign = "right";
  return td;
}
function defaultValueRendererRule(instance, td, row, col, prop, value, cellProperties) {
  var args = arguments;
  if (prop == 'before' || prop == 'after' || prop == 'parlay_win' || prop == 'line')
  {
    td.style.textAlign = "right";
  }
  Handsontable.renderers.TextRenderer.apply(this, args);
  return td;
}

var parlayTeamSetting = {
  columns: [
      {
        data: 'team',
        readOnly: true
      },
      {
        data: 'line',
        type: 'numeric',
        readOnly: true
      }
  ],
  minSpareRows: 0,
  columnSorting: true,
  colWidths: [250,150,],
  rowHeights: rowHeight,
  height: 300,
  className: "htCenter htMiddle",
  rowHeaders: false,
  colHeaders: ['Team', 'Money Line'],
  outsideClickDeselects: false,
  cells: function (row, col, prop) {
    var cellProperties = {};
    cellProperties.renderer = defaultValueRendererRule;
    return cellProperties;
  }
}

var parlayOutcomeSetting = {
  columns: [
      {
        data: 'title',
        readOnly: true
      },
      {
        data: 'before',
        type: 'numeric',
        readOnly: true
      },
      {
        data: 'payout_win',
        type: 'numeric',
        readOnly: true
      },
      {
        data: 'after',
        type: 'numeric',
        readOnly: true
      },
  ],
  minSpareRows: 0,
  columnSorting: true,
  colWidths: [200,250,150,150],
  rowHeights: rowHeight,
  height: 300,
  className: "htCenter htMiddle",
  rowHeaders: false,
  colHeaders: ['', 'Before', 'Payout w/Win', 'After'],
  outsideClickDeselects: false,
  cells: function (row, col, prop) {
    var cellProperties = {};
    cellProperties.renderer = defaultValueRendererRule;
    return cellProperties;
  }
}

var rrTeamSetting = {
  columns: [
      {
        data: 'team',
        readOnly: true
      },
      {
        data: 'line',
        type: 'numeric',
        readOnly: true
      }
  ],
  minSpareRows: 0,
  columnSorting: true,
  colWidths: [250,150,],
  rowHeights: rowHeight,
  height: 300,
  className: "htCenter htMiddle",
  rowHeaders: false,
  colHeaders: ['Team', 'Money Line'],
  outsideClickDeselects: false,
  cells: function (row, col, prop) {
    var cellProperties = {};
    cellProperties.renderer = defaultValueRendererRule;
    return cellProperties;
  }
}

var rrTeamOutcomeSetting = {
  columns: [
      {
        data: 'team',
        readOnly: true
      },
      {
        data: 'line',
        type: 'numeric',
        readOnly: true
      },
      {
        data: 'bet',
        type: 'numeric',
        readOnly: true
      },
      {
        data: 'outcome',
        type: 'numeric',
        readOnly: true
      }
  ],
  minSpareRows: 0,
  columnSorting: true,
  colWidths: [250,250,150,150],
  rowHeights: rowHeight,
  height: 300,
  className: "htCenter htMiddle",
  rowHeaders: false,
  colHeaders: ['Team', 'Money Line', 'Bet', 'Outcome'],
  outsideClickDeselects: false,
  cells: function (row, col, prop) {
    var cellProperties = {};
    cellProperties.renderer = defaultValueRendererRule;
    return cellProperties;
  }
}

function createSheets(data) {
    var container = $('div#user_sportbook_week_table')[0];
    var dataWeek = data.slice()
    dataWeek.push({});
    sportbooksSettings.data = dataWeek;
    currentTableWeek = new Handsontable(container, sportbooksSettings);

    var container1 = $('div#user_sportbook_year_table')[0];
    var dataYear = data.slice()
    sportbooksSettingsYear.data = dataYear;
    for(var i=1; i<=53; i++)
    {
      custom_headers_year[0].push('Week ' + i);
      sportbooksSettingsYear.columns.push({
        data: 'current_balance_'+i,
        type: 'numeric',
        readOnly: true
      })
      sportbooksSettingsYear.colWidths.push(80);
      sportbooksSettingsYear.nestedHeaders = custom_headers_year;
    }
    currentTableYear = new Handsontable(container1, sportbooksSettingsYear);
}

function loadTable(){
  $(".loading-div").show()
  var betweek = $('.game-week-select').val()
  $.ajax({
      url: api_url+'/loadsportbooks',
      type: 'POST',
      data: {
        investorId: investorId,
        betweek: betweek
      },
      dataType: 'json',
      success: function(data) {
          createSheets(data['sportbook_list']);
          $(".loading-div").hide()
      }
  });
}
function updateRules(data){
  $("#singlebet_min").val(data.rules.singlebet_min)
  $("#singlebet_max").val(data.rules.singlebet_max)
  $("#parlay_min_team").val(data.rules.parlay_min_team)
  $("#parlay_max_team").val(data.rules.parlay_max_team)
  $("#parlay_min_bet").val(data.rules.parlay_min_bet)
  $("#parlay_max_bet").val(data.rules.parlay_max_bet)
  $("#rr_min_team").val(data.rules.rr_min_team)
  $("#rr_max_team").val(data.rules.rr_max_team)
  $("#rr_max_combination").val(data.rules.rr_max_combination)
  $("#rr_min_bet").val(data.rules.rr_min_bet)
  $("#rr_max_bet").val(data.rules.rr_max_bet)

  loadParlayTeamTable(data.parlay);
  loadParlayOutcome(data.parlay_outcome);
  loadRRTeamTable(data.roundrobin);
  loadRROutcome(data.rr_outcome);
}

function loadRRTeamTable(data) {
  var container = $('div#rr_team_table')[0];
  parlayTeamSetting.data = data;
  new Handsontable(container, parlayTeamSetting);
}

function loadRROutcome(data) {
  var container1 = $('div#rr1_outcome_table')[0];
  rr1TeamOutcomeSetting = Object.assign({}, rrTeamOutcomeSetting);
  rr1TeamOutcomeSetting.data = data.sheet1;
  new Handsontable(container1, rr1TeamOutcomeSetting);

  var container2 = $('div#rr2_outcome_table')[0];
  rr2TeamOutcomeSetting = Object.assign({}, rrTeamOutcomeSetting);
  rr2TeamOutcomeSetting.data = data.sheet2;
  new Handsontable(container2, rr2TeamOutcomeSetting);
}

function loadParlayTeamTable(data){
    var container = $('div#parlay_team_table')[0];
    teamData = [];
    if(data.length)
      teamData = data[0];
    parlayTeamSetting.data = teamData;
    new Handsontable(container, parlayTeamSetting);
}

function loadParlayOutcome(data){
  var container = $('div#parlay_outcome_table')[0];
  parlayOutcomeSetting.data = data;
  new Handsontable(container, parlayOutcomeSetting);
}


function loadRules(){
  $(".loading-div").show()
  var betweek = $('.game-week-select').val()
  var sportbookId = $('.sportbook-select').val()
  $.ajax({
      url: api_url+'/loadRules',
      type: 'POST',
      data: {
        betweek: betweek,
        sportbookId: sportbookId
      },
      dataType: 'json',
      success: function(data) {
        updateRules(data);
        $(".loading-div").hide()
      }
  });
}

function updateTable(){
  $(".loading-div").show()
  var betweek = $('.game-week-select').val()
  var tableData = currentTableWeek.getSourceData();
  var data = [];
  $.each(tableData, function(key, item){
    var tmpArr = {
      'id': item.relation_id,
      'current_balance': item.current_balance
    }
    data.push(tmpArr);
  })
  var postData = {
    betweek: betweek,
    data: JSON.stringify({
      data: data
    })
  }

  $.ajax({
      url: api_url+'/saveSportbookData',
      type: 'POST',
      data: postData,
      success: function(data) {
        loadTable()
        $(".loading-div").hide()
      }
  });
}

function loadPage(){
  loadTable();
  loadRules(); 
}

$(document).ready(function() {
  loadPage();
  $('input:radio[name="pageTypeOption"]').change(function(){
    if ($(this).is(':checked') && $(this).val() == 'week') {
      $("#user_sportbook_week_table").show();
      $("#user_sportbook_year_table").hide();  
    }else{
      $("#user_sportbook_week_table").hide();
      $("#user_sportbook_year_table").show();
    }
    loadTable();
  });
} );