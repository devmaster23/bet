var allTabelTimeOut = null;
var pickTableObject = null;
var allTableObject = null;
var settingTableObject = null;
var settingTableObject1 = null;

var all_custom_headers = [
    [
      {label: 'Wrappers', colspan: 8},
      {label: 'Candy', colspan: 8},
      {label: 'Picks', colspan: 8} 
    ],
    [
      '#','Sport','VRN','SP/ML','Team','Line','Game Time','Count',
      '#','Sport','VRN','SP/ML','Team','Line','Game Time','Count',
      '#','Sport','VRN','SP/ML','Team','Line','Game Time','Count'
    ]
];

var customHotSettings = {
    columns: [
        {
          type: 'numeric',
          readOnly: false
        },
        {
          type: 'numeric',
          readOnly: false
        },
        {
          type: 'numeric',
          readOnly: false
        },
        {
          type: 'numeric',
          readOnly: false
        },
        {
          type: 'numeric',
          readOnly: false
        },
        {
          type: 'numeric',
          readOnly: false
        },
        {
          type: 'numeric',
          readOnly: false
        }
    ],
    minSpareRows: 0,
    minSpareCols: 0,
    colWidths: [50,50,50,50,50,50,50,50],
    rowHeights: rowHeight,
    className: "htCenter htMiddle",
    rowHeaders: true,
    colHeaders: true,
    height: 297,
    cells: function (row, col, prop) {
      var cellProperties = {};
      cellProperties.renderer = settingValueRenderer;
      if(row == 7 || row == 8)
        cellProperties.readOnly = true;
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
        if (row == 8 && prop == 0)
        {
          if(ref_value > 8)
          {
            ref_value = 8;
          }
          if(ref_value < 5)
          {
            ref_value = 5;
          }
           settingTableObject.setDataAtRowProp(row,prop,ref_value,"sss");
        }

        var robin1 = settingTableObject.getDataAtRowProp(8,0);

        for(var i=0; i<7; i++)
        {
          if(robin1 == 5)
          {
            settingTableObject.setCellMeta(4,i,'readOnly',true);
            settingTableObject.setCellMeta(5,i,'readOnly',true);
            settingTableObject.setCellMeta(6,i,'readOnly',true);
            settingTableObject.setDataAtRowProp(4,i,null,"sss");
            settingTableObject.setDataAtRowProp(5,i,null,"sss");
            settingTableObject.setDataAtRowProp(6,i,null,"sss");
          }
          if(robin1 == 6)
          {
            settingTableObject.setCellMeta(4,i,'readOnly',false);
            settingTableObject.setCellMeta(5,i,'readOnly',true);
            settingTableObject.setCellMeta(6,i,'readOnly',true);
            settingTableObject.setDataAtRowProp(5,i,null,"sss");
            settingTableObject.setDataAtRowProp(6,i,null,"sss");
          }
          if(robin1 == 7)
          {
            settingTableObject.setCellMeta(4,i,'readOnly',false);
            settingTableObject.setCellMeta(5,i,'readOnly',false);
            settingTableObject.setCellMeta(6,i,'readOnly',true);
            settingTableObject.setDataAtRowProp(6,i,null,"sss");
          }
          if(robin1 == 8)
          {
            settingTableObject.setCellMeta(4,i,'readOnly',false);
            settingTableObject.setCellMeta(5,i,'readOnly',false);
            settingTableObject.setCellMeta(6,i,'readOnly',false);
            settingTableObject.setDataAtRowProp(8,0,robin1,"sss");
          }
        }
      }
    }
};

var customHotSettings1 = {
    columns: [
        {
          type: 'date',
          dateFormat: 'MMM DD, YYYY',
          correctFormat: true,
          readOnly: false
        },
        {
          type: 'numeric',
          readOnly: true
        },
        {
          type: 'numeric',
          readOnly: true
        }
    ],
    minSpareRows: 0,
    minSpareCols: 0,
    colWidths: [150,100,100],
    rowHeights: rowHeight,
    className: "htCenter htMiddle",
    rowHeaders: true,
    colHeaders: true,
    height: 200,
    colHeaders: ['Date', 'Year', 'Bet Day'],
    cells: function (row, col, prop) {
      var cellProperties = {};
      cellProperties.renderer = settingValueRenderer;
      return cellProperties;
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
        },

    ],
    minSpareRows: 0,
    minSpareCols: 0,
    colWidths: [40, 100, 50,60, 250, 60,80,60,40, 100, 50,60, 250, 60,80,60,40, 100, 50,60, 250, 60,80,60],
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

var pickTableSettings = {
    columns: [
        {
          data: 'select',
          type: 'numeric',
          readOnly: true
        },
        {
          data: 'game_type',
          readOnly: true
        },
        {
          data: 'vrn',
          type: 'numeric',
          readOnly: true
        },
        {
          data: 'type',
          readOnly: true
        },
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
          data: 'time',
          type: 'time',
          timeFormat: 'h:mm A',
          correctFormat: true,
          readOnly: true
        },
        {
          data: 'count',
          type: 'numeric',
          readOnly: true
        },

    ],
    minSpareRows: 0,
    minSpareCols: 0,
    colWidths: [50, 100, 50,60, 250, 60,80,60],
    rowHeights: rowHeight,
    className: "htCenter htMiddle",
    height: tableHeight,
    rowHeaders: true,
    colHeaders: ['','Sport','VRN','SP/ML','Team','Line','Game Time','Count'],
    cells: function (row, col, prop) {
      var cellProperties = {};
      if(prop == 'select')
        cellProperties.renderer = selectRenderer;
      else
        cellProperties.renderer = allDefaultValueRenderer;
      return cellProperties;
    }
};

function settingValueRenderer(instance, td, row, col, prop, value, cellProperties) {
  var args = arguments;
  td.style.fontSize = fontSize;
  td.style.color = '#000';
  // td.style.backgroundColor = '#fff';  
  if(row == 4 || row == 5 || row == 6){
    if(cellProperties.readOnly === true)
      td.style.backgroundColor = '#eee';
    else  
      td.style.backgroundColor = '#fff';
  }
  if(row == 7)
  {
    td.style.color = '#fff';
    td.style.backgroundColor = '#548235';
  }

  Handsontable.renderers.TextRenderer.apply(this, args);
}
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

function selectRenderer (instance, td, row, col, prop, value, cellProperties) {
  var selected = instance.getDataAtRowProp(row, 'selected');
  $(td).html("<input type='checkbox' " + (selected?"checked":"") + " name='pick_select' data-key='"+value+"' />");
  td.style.textAlign = 'center';
  return td;
}

function selectViewRenderer (instance, td, row, col, prop, value, cellProperties) {
  var selected = instance.getDataAtRowProp(row, 'selected');
  $(td).html("<div class='"+(selected?"pick-check":"")+"'><label></label></div>");
  td.style.textAlign = 'center';
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


function createAllPickSheets(data){
  var key = 'bets';
  var container = $('div.sheet[data-type="'+key+'"]')[0];
  allHotSettings['data'] = data;
  if(allTableObject == null)
    allTableObject = new Handsontable(container, allHotSettings);
  else
    allTableObject.loadData(data);  
}

function createPickSheets(data){
  var key = 'bets_pick';
  var container = $('div.sheet[data-type="'+key+'"]')[0];
  pickTableSettings['data'] = data;
  if(pickTableObject == null)
    pickTableObject = new Handsontable(container, pickTableSettings);
  else
    pickTableObject.loadData(data);   
}

function createSettingSheet(data){
  var container = $('div.sheet[data-type="setting_sheet"]')[0];
  customHotSettings['data'] = data['sheet_data'];
  if(settingTableObject == null)
    settingTableObject = new Handsontable(container, customHotSettings);
  else
    settingTableObject.loadData(data['sheet_data']);
  mergeFields();

  var container1 = $('div.sheet[data-type="setting_sheet1"]')[0];
  customHotSettings1['data'] = data['date_info'];
  if(settingTableObject1 == null)
    settingTableObject1 = new Handsontable(container1, customHotSettings1);
  else
    settingTableObject1.loadData(data['date_info']);  
}

function createBetSheets(data){
  var container = $('div#bet_sheet');
  container.html("");
  $.each(data.data, function(key, row_item){
    var tblItem = "<div class='sheet_block_wrapper'>";
    $.each(row_item, function(key2, item){
      var cls = item.disabled.length ? "disabled" : "";
      var is_parlay = item.is_parlay ? "selected" : "";
      tblItem += "<div class='sheet_block "+cls+"' id='"+item.title+"'>"+
                "<span class='remove-icon'></span>"+
                "<span class='parlay-icon "+is_parlay+"' data-id='"+key+"_"+key2+"'></span>"+
                "<table><tbody>";
        $.each(item, function(key3, team_item){
          if(['title','disabled','is_parlay'].indexOf(key3) != -1)
            return true;

          var disableCls = item.disabled.indexOf(eval(key3)) > -1 ? "disabled" : "";
          if(team_item == null || team_item.team == null)
          {
            tblItem +="<tr>"+
                  "<td width='10%'></td>"+
                  "<td width='20%'></td>"+
                  "<td width='40%' class='team'></td>"+
                  "<td width='10%'></td>"+
                  "<td width='20%'></td>"+
                "</tr>"
          }else{
            tblItem +="<tr class='"+disableCls+"'>"+
                  "<td width='10%'>"+team_item.vrn+"</td>"+
                  "<td width='20%'>"+team_item.type+"</td>"+
                  "<td width='40%' class='team'>"+team_item.team+"</td>"+
                  "<td width='10%'>"+team_item.line+"</td>"+
                  "<td width='20%'>"+team_item.time+"</td>"+
                "</tr>"
          }
            tblItem +="<tr>"+
                  "<td width='10%'></td>"+
                  "<td width='20%'></td>"+
                  "<td width='40%' class='team'></td>"+
                  "<td width='10%'></td>"+
                  "<td width='20%'></td>"+
                "</tr>"
        });
        tblItem +="<tr>"+
            "<td width='30%' colspan=2>Alternates</td>"+
            "<td width='40%' class='team'></td>"+
            "<td width='10%'></td>"+
            "<td width='20%'></td>"+
          "</tr>"
      tblItem += "</tbody></table>"+
                "<div class='mark-div'>"+item.title+"</div><div class='clearfix'></div>"+
                "<div class='bottom-div'><span>"+data.type+"</span><span></span>"+data.date+"<span></span>Bet Day"+data.betday+"</div>"+
                "<div class='warning'></div>"+
                "</div>";   
    });
    tblItem += "</div>";
    container.append(tblItem);
  });
}

function createBetSummary(data){
  var container = $('div#bet_summary');
  container.html("");
  var tblItem = "<table>"+
        "<thead><tr>"+
          "<td colspan='2'></td>"+
          "<td colspan='5'>Round Robbins</td>"+
          "<td colspan='2'>Parlay</td>"+
          "<td colspan='2'>Individual Picks</td>"+
        "</tr></thead>";

  $.each(data.summary, function(key, row_item){
    var tilte = "All";
    var selected = (row_item.id == settingId)? "selectecd" : "";
    tblItem += "<tbody><tr>"+
          "<td class='title "+selected+"'><a href='"+api_url+"?id="+row_item.id+"'>"+row_item.title+"</a></td>"+
          "<td class='total_allocation'>"+(row_item.bet_allocation ? row_item.bet_allocation+"%":"")+"</td>"+
          "<td>"+(row_item.rr_allocation ? row_item.rr_allocation+"%":"")+"</td>"+
          "<td>"+row_item.rr_number1+"</td>"+
          "<td>by</td>"+
          "<td>"+row_item.rr_number2+"</td>"+
          "<td>"+row_item.rr_number3+"</td>"+
          "<td>"+(row_item.parlay_allocation ? row_item.parlay_allocation+"%":"")+"</td>"+
          "<td>"+row_item.parlay_number1+"</td>"+
          "<td>"+(row_item.pick_allocation ? row_item.pick_allocation+"%":"")+"</td>"+
          "<td>"+row_item.pick_number1+"</td>"+
        "</tr></tbody>";
  });
  tblItem += "</table>";
  container.append(tblItem);
}

function loadSettingTable(){
  var betweek = $('.game-week-select').val()
  $.ajax({
      url: api_url+'/loadBetSetting',
      type: 'POST',
      data: {
        betweek: betweek,
        settingId: settingId
      },
      success: function(data) {
          createSettingSheet(data);
      }
  });
}

function loadAllPickTable(){
  $(".loading-div").show()
  var betweek = $('.game-week-select').val()
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

function loadBetSheet(){
  $(".loading-div").show()
  var betweek = $('.game-week-select').val()
  $.ajax({
      url: api_url+'/loadBetSheet',
      type: 'POST',
      data: {
        betweek: betweek
      },
      success: function(data) {
          createBetSheets(data);
          $(".loading-div").hide()
      }
  }); 
}

function loadPickData(){
  $(".loading-div").show()
  var betweek = $('.game-week-select').val()
  $.ajax({
      url: api_url+'/loadPickData',
      type: 'POST',
      data: {
        betweek: betweek
      },
      success: function(data) {
        createPickSheets(data);
        $(".loading-div").hide()
      }
  });  
}

function loadSummary(){
  $(".loading-div").show()
  var betweek = $('.game-week-select').val()
  $.ajax({
      url: api_url+'/loadSummary',
      type: 'POST',
      data: {
        betweek: betweek
      },
      success: function(data) {
        createBetSummary(data);
        $(".loading-div").hide()
      }
  });   
}

function mergeFields(){
  if(settingTableObject != null)
  {
    var cleanedGridData = [];
    $.each( settingTableObject.getData(), function( rowKey, object) {
        if (!settingTableObject.isEmptyRow(rowKey)) cleanedGridData.push(object);
    }); 
    var hotOptions = {
      mergeCells: []
    };
    hotOptions.mergeCells = hotOptions.mergeCells.concat([
      {row: 7, col: 0, rowspan: 1, colspan: 7}
    ]);
    settingTableObject.updateSettings(hotOptions);
  }
}

function updateTable(){
  $(".loading-div").show()
  var betweek = $('.game-week-select').val()
  var tableData = settingTableObject.getData();
  var tableData1 = settingTableObject1.getData();
  var selectType = $('#sheets .nav-link.active').data('type');
  var url = api_url+'/saveData';
  var postData = {
    betweek: betweek,
    setting: JSON.stringify({
      data: tableData,
      data1: tableData1
    })
  }
  if(selectType == 'bets_pick')
  {
    let selectArr = [];
    $("input:checkbox[name=pick_select]:not(:checked)").each(function(){
      selectArr.push($(this).data('key'));
    });
    url = api_url+'/savePickSelect';
    postData = {
      betweek: betweek,
      data: JSON.stringify({
        data: selectArr
      })
    }
  }
  $.ajax({
      url: url,
      type: 'POST',
      data: postData,
      success: function(data) {
        initPage()
        $(".loading-div").hide()
      }
  });
}

$(document).on('click','#sheets .nav-link',function(){
  initPage();  
});

function initPage(){
  loadSettingTable();
  var selectType = $('#sheets .nav-link.active').data('type');
  if(selectType == 'bet_summary')
  {
    $(".save-button-div").hide();
    loadSummary();
  }
  if(selectType == 'bets')
  {
    $(".save-button-div").show();
    loadAllPickTable();
    // allTabelTimeOut = setInterval(function(){
    //   loadAllPickTable();
    // }, 3000);

  }else{
    // clearInterval(allTabelTimeOut);
  }
  if(selectType == 'bet_sheet')
  {
    $(".save-button-div").hide();
    loadBetSheet();
  }
  if(selectType == 'bets_pick')
  {
    $(".save-button-div").show();
    loadPickData();
  }
}

$(document).on('click','.parlay-icon', function(){
  $(this).toggleClass('selected');
  var parlayObjs = $('.parlay-icon.selected');
  var betweek = $('.game-week-select').val()
  var paralyIds = [];
  $.each(parlayObjs, function(key, item){
    paralyIds.push($(item).data('id'));
  });
  $(".loading-div").show()
  $.ajax({
      url: api_url+'/updateParlay',
      type: 'POST',
      data: {
        betweek: betweek,
        data: JSON.stringify(paralyIds)
      },
      success: function(data) {
        $(".loading-div").show()
      }
  });
})

$(document).ready(function() {

  initPage();

  $('body').click(function(event) {
    if($(event.target).parents('.popover-body').length == 0 && !$(event.target).hasClass('pick-check-box')){
      $(".popover").popover('hide');
    }
  });
  $(document).on('change','.pick-checkbox',function(){
    var selectType = $('#sheets .nav-link.active').data('type');
    var hot = tableObject[selectType],
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

  if($('#bet_sheet'))
    $('#bet_sheet').css('max-height', tableHeight+'px');

});
