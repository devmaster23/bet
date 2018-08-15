var tableObject = null;
var tableObject1 = null;
var categoryType = 0;
var categoryGroupUser = 0;
var custom_fontSize = '22px';
var custom_rowHeight = 35;

var hotSettings = {
  columns: [
      {
        data: 'title',
        readOnly: true
      },
      {
        data: 'bet_percent',
        type: 'numeric',
        readOnly: false
      },
      {
        data: 'bet_number1',
        type: 'numeric',
        readOnly: false
      },
      {
        data: 'bet_text',
        type: 'numeric',
        readOnly: true
      },
      {
        data: 'bet_number2',
        type: 'numeric',
        readOnly: false
      },
      {
        data: 'bet_number3',
        type: 'numeric',
        readOnly: false
      },
      {
        data: 'bet_number4',
        type: 'numeric',
        readOnly: false
      },
      {
        data: 'id',
        type: 'numeric',
        readOnly: false
      },
      {
        data: 'type',
        type: 'text',
        readOnly: false
      }
  ],
  colWidths: [250, 100, 80, 80, 80, 80, 80],
  rowHeights: custom_rowHeight,
  className: "htCenter htMiddle",
  rowHeaders: false,
  colHeaders: false,
  fixedRowsTop: 1,
  height: 400,
  outsideClickDeselects: false,
  autoRowSize: true,
  fixedRowsTop: 1,
  viewportColumnRenderingOffset: 100, 
  hiddenColumns: {
    columns: [7,8],
    indicators: false
  },
  cells: function (row, col, prop) {
    var cellProperties = {};
    cellProperties.renderer = defaultValueRenderer;
    if((row == 0 && col>0) || (row == 1 && col == 3) || (row >= 2 && col > 1)){
      cellProperties.readOnly = true;
    }
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
      if (prop == 'bet_percent'){
        var count_rows = tableObject.countRows();
        var bet_allocation = 0;
        for(var i=1; i< count_rows; i++)
        {
          bet_allocation += parseFloat(tableObject.getDataAtRowProp(i,'bet_percent'));
        }
        tableObject.setDataAtRowProp(0,'bet_percent',bet_allocation,"sss");
      }
    }
  }
};

var hotSettings1 = {
  columns: [
      {
        data: 'title',
        type: 'numeric',
        readOnly: true
      },
      {
        data: 'rr1',
        type: 'numeric',
        readOnly: true
      },
      {
        data: 'rr2',
        type: 'numeric',
        readOnly: true
      },
      {
        data: 'sheet',
        type: 'numeric',
        readOnly: true
      },
      {
        data: 'order',
        type: 'numeric',
        readOnly: true
      },
      {
        data: 'bets',
        type: 'numeric',
        readOnly: true
      }
  ],
  colWidths: [250,100,100,100,100,100],
  rowHeights: custom_rowHeight,
  className: "htCenter htMiddle",
  rowHeaders: false,
  colHeaders: false,
  fixedRowsTop: 1,
  width: 750,
  height: 400,
  outsideClickDeselects: false,
  formulas: true,
  cells: function (row, col, prop) {
    var cellProperties = {};
    cellProperties.renderer = defaultValueRenderer1;
    return cellProperties;
  }
};

function defaultValueRenderer(instance, td, row, col, prop, value, cellProperties) {
  var args = arguments;
  td.style.fontSize = custom_fontSize;
  td.style.color = '#000';
  td.style.backgroundColor = '#fff';  
  if((row == 0 && col>0) || (row == 1 && col == 3) || (row >= 2 && col > 1)){
    td.style.backgroundColor = '#d4d4d4';
  }
  if(col == 0)
  {
    td.style.backgroundColor = '#efefef'; 
    td.style.color = '#000';
  }
  if(row == 0)
  {
    td.style.backgroundColor = '#1d4cc3'; 
    td.style.color = '#fff';
  }
  Handsontable.renderers.TextRenderer.apply(this, args);
  if(col == 1)
  {
    td.innerHTML = value + ' %';
  }
  return td;
}

function defaultValueRenderer1(instance, td, row, col, prop, value, cellProperties) {
  var args = arguments;
  td.style.fontSize = custom_fontSize;
  td.style.color = '#000';
  td.style.backgroundColor = '#d4d4d4';
  if(col == 0)
  {
    td.style.backgroundColor = '#efefef'; 
    td.style.color = '#000';
  }
  if(row == 0)
  {
    td.style.backgroundColor = '#3d80db'; 
    td.style.color = '#fff';
  }
  Handsontable.renderers.TextRenderer.apply(this, args);
}

function mergeFields(){
  if(tableObject != null)
  {
    var hotOptions = {
      mergeCells: []
    };
    hotOptions.mergeCells = hotOptions.mergeCells.concat([
      {row: 0, col: 1, rowspan: 1, colspan: 2}
    ]);
    tableObject.updateSettings(hotOptions);
  }
  if(tableObject1 != null)
  {
    var hotOptions = {
      mergeCells: []
    };
    hotOptions.mergeCells = hotOptions.mergeCells.concat([
      {row: 0, col: 1, rowspan: 1, colspan: 2}
    ]);
    tableObject1.updateSettings(hotOptions); 
  }
}

function initData(data){
  cleanFomularColor();
  updateLockIcon(data['is_lock']);
  var description = data['description'];
  $('#description').val(description);

  var container = $('#bet_allocation')[0];
  tmpSetting = Object.assign({},hotSettings);
  tmpSetting['data'] = data['bet_allocation'];
  if(tableObject == undefined)
    tableObject = new Handsontable(container, tmpSetting);
  else
    tableObject.loadData(data['bet_allocation']);

  var container1 = $('#bet_analysis')[0];
  tmpSetting1 = Object.assign({},hotSettings1);
  tmpSetting1['data'] = data['bet_analysis'];
  if(tableObject1 == undefined)
    tableObject1 = new Handsontable(container1, tmpSetting1);
  else
    tableObject1.loadData(data['bet_analysis']);

  var rr_number1 = data['bet_allocation'][1]['bet_number1'],
      rr_number2 = data['bet_allocation'][1]['bet_number2'],
      rr_number3 = data['bet_allocation'][1]['bet_number3'],
      rr_number4 = data['bet_allocation'][1]['bet_number4'];

  for($i = 4; $i < data['bet_allocation'].length; $i++)
  {
    var tmpData = data['bet_allocation'][$i];

    if(tmpData['type'] == 'rr')
      updateFomularColor([tmpData['bet_number1']],[tmpData['bet_number2'],tmpData['bet_number3'],tmpData['bet_number4']],true);
  }

  updateFomularColor([rr_number1],[rr_number2,rr_number3,rr_number4], false);
  mergeFields();
}
function cleanFomularColor(){
  $("#fomularTable tbody td").removeClass('selected'); 
}
function updateFomularColor(x,y,custom)
{
  $.each(x, function(key, value)
  {
    $.each(y, function(key1, value1)
    {
      if(!value || !value1 || value1 < 2)
        return false;
      var selectClass = 'selected';
      if(custom)
        selectClass = 'custom-selected';
      $("#fomularTable tbody tr:eq("+(value-3)+") td:eq("+(value1-1)+")" ).addClass(selectClass);
    });
  });
}

function loadGroupUser(){
  $.ajax({
    url: api_url+'/loadGroupUser',
    type: 'POST',
    data: {
      categoryType: categoryType
    },
    dataType: 'json',
    success: function(data) {
      updateGroupUserList(data,categoryType);
      initPage();
    }
  });
}

function updateGroupUserList(data,categoryType){
  var html = '';
  if(categoryType != 0)
  {

    $.each(data, function(key,item){
      if(categoryGroupUser == null)
      {
        html += '<li data-value="'+item.id+'" class="'+(key == 0?'selected':'')+'">'+item.name+'</li>'; 
        if(key == 0)
          categoryGroupUser = item.id;
      }
      else{
        if( item['id'] == categoryGroupUser )
        {
          html += '<li data-value="'+item.id+'" class="selected">'+item.name+'</li>';
        }
        else
          html += '<li data-value="'+item.id+'">'+item.name+'</li>';
      }
    })
  }
  $('#category-group-user').html(html);
}

function initPage(){
  $(".loading-div").show()
  var betweek = $('.game-week-select').val()
  $.ajax({
    url: api_url+'/loadData',
    type: 'POST',
    data: {
      betweek: betweek,
      categoryType: categoryType,
      categoryGroupUser: categoryGroupUser
    },
    dataType: 'json',
    success: function(data) {
      initData(data)
      $(".loading-div").hide()
    }
  });
}

$(document).on('click','.category-select li', function(){
  $(this).siblings('li').removeClass('selected');
  $(this).addClass('selected');
});

$(document).on('click','#category-type li', function(){
  var value = $(this).data('value');
  if(value != categoryType)
  {
    categoryType = value;
    categoryGroupUser = null;
    loadGroupUser();
  }
});

$(document).on('click','#category-group-user li', function(){
  var value = $(this).data('value');
  if(value != categoryGroupUser)
  {
    categoryGroupUser = value;
    initPage();
  }
});

function updateData(){
    var betweek = $('.game-week-select').val();
    var description = $('#description').val();
    var tableData = tableObject.getData();
    $(".loading-div").show()
    $.ajax({
        url: api_url+'/saveData',
        type: 'POST',
        data: {
          betweek: betweek,
          categoryType: categoryType,
          categoryGroupUser: categoryGroupUser,
          data: JSON.stringify({
            data: tableData,
            description: description
          })
        },
        success: function(data) {
          initPage();
          $(".loading-div").hide()
        }
    });
}

function updateLockIcon(status){
  if(status){
   $("#lock-icon").addClass('locked');
  }else{
    $("#lock-icon").removeClass('locked');
  }
}

function updateLockStatus(status){
  $(".loading-div").show()
  var betweek = $('.game-week-select').val()
  $.ajax({
      url: api_url+'/savelockstatus',
      type: 'POST',
      data: {
        betweek: betweek,
        locked: status ? '1' : '0'
      },
      success: function(data) {
        $(".loading-div").hide()
      }
  });
}

$(document).ready(function(){
  categoryType = activeSetting['type'];
  categoryGroupUser = activeSetting['groupuser_id'];
  loadGroupUser();

  $("#lock-icon").on('click', function(){
    $(this).toggleClass('locked');
    updateLockStatus($(this).hasClass('locked'));
  })
})