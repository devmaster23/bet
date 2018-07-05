var allTabelTimeOut = null;
var pickTableObject = null;
var customBetTableObject = null;
var allTableObject = null;
var settingTableObject = null;
var settingTableObject1 = null;
var betData = null;

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
    colWidths: [50, 100, 50,100, 150, 120,80,60],
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

var customBetTableSettings = {
    columns: [
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
        }
    ],
    minSpareRows: 0,
    minSpareCols: 0,
    colWidths: [100, 50,80, 250, 120, 100],
    rowHeights: rowHeight,
    className: "htCenter htMiddle",
    height: tableHeight,
    rowHeaders: true,
    colHeaders: ['Sport','VRN','SP/ML','Team','Line','Game Time'],
    cells: function (row, col, prop) {
      var cellProperties = {};
      cellProperties.renderer = allDefaultValueRenderer;
      return cellProperties;
    }
};

function settingValueRendererRobin(instance, td, row, col, prop, value, cellProperties) {
  var args = arguments;
  td.style.fontSize = fontSize;
  td.style.color = '#000';
  td.style.backgroundColor = '#fff';  
  
  var robin1 = instance.getDataAtRowProp(8,0);
  if(row > robin1-2)
    cellProperties.readOnly = true;
  if(row < 7)
  {
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

function settingValueRenderer(instance, td, row, col, prop, value, cellProperties) {
  var args = arguments;
  td.style.fontSize = fontSize;
  td.style.color = '#000';
  Handsontable.renderers.TextRenderer.apply(this, args);
}

function allDefaultValueRenderer(instance, td, row, col, prop, value, cellProperties) {
  var args = arguments;
  td.style.fontSize = fontSize;
  td.style.color = '#000';
  if (prop == 'pick_team' || prop == 'wrapper_team' || prop == 'candy_team' || prop == 'team')
  {
    td.style.textAlign = "left";
  }
  Handsontable.renderers.TextRenderer.apply(this, args);
}

function selectRenderer (instance, td, row, col, prop, value, cellProperties) {
  var selected = instance.getDataAtRowProp(row, 'selected');
  $(td).html("<input type='checkbox' " + (selected?"checked":"") + " name='pick_select' data-key='"+value+"' />");
  td.style.textAlign = 'center';
  td.style.verticalAlign = 'middle';
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
  
  $('.worksheets #wrapper-table tbody').empty().html(buildTableBody(wrapperList));
  $('.worksheets #candy-table tbody').empty().html(buildTableBody(candyList));
  $('.worksheets #pick-table tbody').empty().html(buildTableBody(pickList));
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

function createPickSheets(data){
  var container = $('#bets_pick')[0];
  pickTableSettings['data'] = data;
  if(pickTableObject == null)
    pickTableObject = new Handsontable(container, pickTableSettings);
  else
    pickTableObject.loadData(data);   
}

function createSettingSheet(data){

  var rr_number1 = data['active_setting']['rr_number1'],
      rr_number2 = data['active_setting']['rr_number2'],
      rr_number3 = data['active_setting']['rr_number3'],
      rr_number4 = data['active_setting']['rr_number4'];
  
  $('.worksheets #rr1').html(rr_number1);
  $('.worksheets #rr2').html(rr_number2);
  $('.worksheets #rr3').html(rr_number3);
  $('.worksheets #rr4').html(rr_number4);
  $settingTable = $('.worksheets #setting-table');
  $('tbody',$settingTable).empty();
  $.each(data['sheet_data'], function(index, item){
    if(index<7)
    {
      if(index+1 < rr_number1)
        tr_html = '<tr>';
      else
        tr_html = '<tr class="tr-disabled">';
      tr_html += '<td>'+(index+1)+'</td>';
      $.each(item, function(index1, value){
        tr_html += '<td>'+value+'</td>';
      });
      tr_html += '</tr>';
      $('tbody',$settingTable).append(tr_html);
    }
    
    $('#setting-table').editableTableWidget({ editor: $('<input>'), preventColumns: [ 1 ], preventRowsAfter: rr_number1 });
  });

  $('.worksheets #betday').val(data['date_info']);
  $('.worksheets #betday').daterangepicker({ "singleDatePicker": true });
}

function createBetSheets(data){
  var container = $('div#bet_sheet');
  container.html("");
  $.each(data.data, function(key, row_item){
    var tblItem = "<div class='sheet_block_wrapper'>";
    $.each(row_item, function(key2, item){
      var cls = item.disabled.length ? "disabled" : "";
      var is_parlay = item.is_parlay ? "selected" : "";
      tblItem += "<div class='sheet_block "+cls + " " +is_parlay+"'  id='"+item.title+"'>"+
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
                  "<td width='35%' class='team'></td>"+
                  "<td width='20%'></td>"+
                  "<td width='15%'></td>"+
                "</tr>"
          }else{
            tblItem +="<tr class='"+disableCls+"'>"+
                  "<td width='10%'>"+team_item.vrn+"</td>"+
                  "<td width='20%'>"+team_item.type+"</td>"+
                  "<td width='35%' class='team'>"+team_item.team+"</td>"+
                  "<td width='20%'>"+team_item.line+"</td>"+
                  "<td width='15%'>"+team_item.time+"</td>"+
                "</tr>"
          }
            tblItem +="<tr>"+
                  "<td width='10%'></td>"+
                  "<td width='20%'></td>"+
                  "<td width='35%' class='team'></td>"+
                  "<td width='20%'></td>"+
                  "<td width='15%'></td>"+
                "</tr>"
        });
        tblItem +="<tr>"+
            "<td colspan=2>Alternates</td>"+
            "<td class='team'></td>"+
            "<td></td>"+
            "<td></td>"+
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
          "<td colspan='2'>Parlays</td>"+
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

function createCustomBet(data){
  var betSettings = data['data'];
  var html = '';
  betData = data['bets'];

  $.each(betSettings, function(key, settingItem){
    html += getCustomBetContent(settingItem);
  })
  $("#bets_custom_inner").html(html);
  createCustomeBetSheet(betData)
  initBetSettings();
}
  
function initBetSettings(){
  $('.bet-select').select2({
      width: '500px'
  });  
}

function createCustomeBetSheet(data){
  var key = 'bets_custom_sheet';
  var container = $('div.sheet[data-type="'+key+'"]')[0];
  customBetTableSettings['data'] = data;
  if(customBetTableObject == null)
    customBetTableObject = new Handsontable(container, customBetTableSettings);
  else
    customBetTableObject.loadData(data);   
}

function getCustomBetContent(settingItem){
  var html = '';
  var setting_id = -1,
      rr_number1 = 1,
      rr_number2 = 0,
      parlay_number = 0,
      rr_bets = [];
  if(settingItem != null)
  {
      setting_id = settingItem.id;
      rr_number1 = settingItem.rr_number1;
      rr_number2 = settingItem.rr_number2;
      parlay_number = settingItem.parlay_number;
      rr_bets = JSON.parse(settingItem.rr_bets);
      parlay_bets = JSON.parse(settingItem.parlay_bets);
  }
  html += '<div class="custom-bet-item" data-setting-id="'+setting_id+'">'+
      '<span class="remove-betsetting-icon"><button type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button></span>'+
      '<div class="bet-item-header rr-header">'+
      '<span>Round Robbin</span><input type="number" disabled rr-number1 value="'+rr_number1+'" min="1"/><input type="number" rr-number2 value="'+rr_number2+'" min="0"/>'+
      '<button type="button" data-type="rr" class="btn btn-success new_game">+</button>'+
      '</div>'+
      '<div class="rr-content">';
  
  for(var i=0; i< rr_number1; i++)
  {
    html += '<div class="select-div"><select rr-bet-select class="bet-select">';
    $.each(betData, function(key, betItem){
      var text      = betItem.game_type+' '+betItem.vrn+' '+betItem.type+' '+betItem.team+' '+betItem.line;
      var optionKey = betItem.select;
      var selected  = rr_bets[i] == optionKey ? 'selected' : '';
      html +='<option '+selected+' value="'+optionKey+'">'+text+'</span></option>';
    });
    html +=  '</select><span data-type="rr" class="remove-select-icon"><button type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button></sapn></div>';
  } 

  html += '</div><div class="bet-item-header parlay_header"><label for="parlay_number">Parlay</label><input parlay-number disabled type="number" value="'+parlay_number+'" min="0">'+
    '<button type="button" data-type="parlay" class="btn btn-success new_game">+</button>'+ 
    '</div>'+
    '<div class="parlay-content">';
  
  for(var i=0; i< parlay_number; i++)
  {
    html += '<div class="select-div"><select parlay-bet-select class="bet-select">';
    $.each(betData, function(key, betItem){
      var text      = betItem.game_type+' '+betItem.vrn+' '+betItem.type+' '+betItem.team+' '+betItem.line;
      var optionKey = betItem.select;
      var selected  = parlay_bets[i] == optionKey ? 'selected' : '';
      html +='<option '+selected+' value="'+optionKey+'">'+text+'</span></option>';
    });
    html +=  '</select><span data-type="parlay" class="remove-select-icon"><button type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button></sapn></div>';
  } 
  html += '</div>';
  html += '</div>'+
      '</div><div class="clearfix"></div>';

  return html;
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

function loadBetCustomData(){
  $(".loading-div").show()
  var betweek = $('.game-week-select').val()
  $.ajax({
      url: api_url+'/loadCustomBet',
      type: 'POST',
      data: {
        betweek: betweek
      },
      success: function(data) {
        createCustomBet(data)
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
  if(pageType == 'bets')
  {
    var tableData = [];
    var tableData1 = [];
    $settingTable = $('.worksheets #setting-table');
    $.each($('tbody tr',$settingTable), function(index, tr_item){
      var arrItem = [];
      $.each($('td', $(tr_item)), function(index1, td_item){
        if(index1 == 0)
          return true;
        arrItem.push($(td_item).html().trim());
      });
      tableData.push(arrItem);
    });

    var betday = $('.worksheets #betday').val().trim();

    var url = api_url+'/saveData';
    var postData = {
      betweek: betweek,
      setting: JSON.stringify({
        data: tableData,
        betday: betday
      })
    }
  }
  if(pageType == 'bets_pick')
  {
    let selectArr = [];
    $("input:checkbox[name=pick_select]:checked").each(function(){
      selectArr.push($(this).data('key'));
    });
    url = api_url+'/savePickSelect';
    postData = {
      betweek: betweek,
      data: JSON.stringify({
        data: selectArr
      })
    }
  }else if(pageType == 'bets_custom')
  {
    url = api_url+'/saveCustomBet';
    let data = [];

    $.each($(".custom-bet-item"),function(key, item){
      var obj = $(item);
      var data_id = obj.data('setting-id'),
          rr_number1 = obj.find("[rr-number1]").val(),
          rr_number2 = obj.find("[rr-number2]").val(),
          parlay_number = obj.find("[parlay-number]").val(),
          rr_bets = [];
          parlay_bets = [];
      var rr_betSelList = obj.find("[rr-bet-select]");
      $.each(rr_betSelList,function(key, betSelItem){
        rr_bets.push($(betSelItem).val());
      });

      var parlay_betSelList = obj.find("[parlay-bet-select]");
      $.each(parlay_betSelList,function(key, betSelItem){
        parlay_bets.push($(betSelItem).val());
      });
      console.log(parlay_bets);
      var data_item = {
        id: data_id,
        rr_number1: rr_number1,
        rr_number2: rr_number2,
        parlay_number: parlay_number,
        rr_bets: rr_bets,
        parlay_bets: parlay_bets
      }

      data.push(data_item);
    })

    postData = {
      betweek: betweek,
      data: JSON.stringify({
        data: data
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

function initPage(){
  
    
  if(pageType == 'bet_summary')
  {
    $(".save-button-div").hide();
    loadSummary();
  }
  else if(pageType == 'bets')
  {
    loadSettingTable();
    $(".save-button-div").show();
    loadAllPickTable();
    // allTabelTimeOut = setInterval(function(){
    //   loadAllPickTable();
    // }, 3000);

  }else{
    // clearInterval(allTabelTimeOut);
  }
  if(pageType == 'bet_sheet')
  {
    $(".save-button-div").hide();
    loadBetSheet();
  }
  if(pageType == 'bets_pick')
  {
    $(".save-button-div").show();
    loadPickData();
  }

  if(pageType == 'bets_custom')
  {
    $(".save-button-div").show();
    loadBetCustomData();
  }
}

$(document).on('click','.parlay-icon', function(){
  $(this).toggleClass('selected');
  var parlayObjs = $('.parlay-icon.selected');
  var betweek = $('.game-week-select').val()
  var parlayIds = [];
  $.each(parlayObjs, function(key, item){
    parlayIds.push($(item).data('id'));
  });
  $(".loading-div").show()
  $.ajax({
      url: api_url+'/updateParlay',
      type: 'POST',
      data: {
        betweek: betweek,
        data: JSON.stringify(parlayIds)
      },
      success: function(data) {
        loadBetSheet();
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
    var hot = tableObject[pageType],
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

  $(document).on('click','.new_game', function(){
    var betType = $(this).data('type');

    var rrObj = undefined;
    if(betType == 'rr')
      rrObj = $(this).parents(".custom-bet-item").find('[rr-number1]');
    else
      rrObj = $(this).parents(".custom-bet-item").find('[parlay-number]');

    html = '<div class="select-div"><select '+betType+'-bet-select class="bet-select">';
    $.each(betData, function(key, betItem){
      var text      = betItem.game_type+' '+betItem.vrn+' '+betItem.type+' '+betItem.team+' '+betItem.line;
      var optionKey = betItem.select;
      html +='<option value="'+optionKey+'">'+text+'</span></option>';
    });
    html +=  '</select><span data-type="'+betType+'" class="remove-select-icon"><button type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button></sapn></div>';
    rrObj.val(eval(rrObj.val()) + 1);
    $(this).parents(".custom-bet-item").find('.'+betType+'-content').append(html);

    initBetSettings();
  })



  $(document).on('click','.remove-select-icon', function(){
    var betType = $(this).data('type');
    var rrObj = undefined;
    if(betType == 'rr')
      rrObj = $(this).parents(".custom-bet-item").find('[rr-number1]');
    else
      rrObj = $(this).parents(".custom-bet-item").find('[parlay-number]');
    rrObj.val(eval(rrObj.val()) - 1);
    $(this).parents('.select-div').remove();
  });

  $(document).on('click','.remove-betsetting-icon', function(){
    $(this).parents(".custom-bet-item").remove();
  });

  $(document).on('click','.new_bet_setting', function(){
    var newBestSetting = getCustomBetContent(null);
    $(this).parents('#bets_custom_inner-wrapper').find('#bets_custom_inner').append(newBestSetting);
    initBetSettings();
  });

});
