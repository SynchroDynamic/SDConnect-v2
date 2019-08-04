var tableCols = {};
var outgoingArraySet = {};
var whereConditionSets = {};
var whereCount = [];
tableCols = {'USER': ['id', 'username'], 'NewGate': ['id', 'Par1', 'Par2', 'Par3'], 'movement': ['id', 'x', 'y', 'z'], 'engine': ['id', 'a', 'b', 'c'], 'anotherTest': ['id', 'name', 'age']};    //add to ownfile in future
$(document).ready(function () {
    var urlVars = getUrlVars();
    var inCount = 2;
    var count = inCount === 0 ? 1 : inCount;
    $('#add').on('click', function (e) {
        e.preventDefault();
        addIncoming(count);
        count++;
        $('input#count').val(count);

    });

    var outCount = 4;
    var countOut = outCount === 0 ? 1 : outCount;
    var curentCountOut = $('#countOut').val();
    $('#addOut').on('click', function (e) {
        e.preventDefault();
        curentCountOut = $('#countOut').val();
        addOutgoing(0, curentCountOut);
        curentCountOut++;
        $('#countOut').val(curentCountOut);

    });

    if (urlVars['tid']) {



        $('#titleTrans').text("Manage Transaction: V8Engine For engine");
        $('#pageName').text("EDIT");
        $('#tName').val("V8Engine");
        $('#tType').val("0");
        $('#hasParams').val("0");
        $('#method').val("0");



        $('#pName0').val('cylinder1');
        $('#inputState0').val('0');
        addIncoming(1);
        $('#pName1').val('cylinder2');
        $('#inputState1').val('0');
        $('#countOut').val(0);
        for (var out = 0; out < 0; out += 1) {
            var selectedCount = outgoingArraySet[out].length;
            $.each($('#colNames' + out + " option"), function (e) {
                //alert(this.text);
                for (var s = 0; s < selectedCount; s += 1) {
                    if (outgoingArraySet[out][s] === this.text) {
                        //alert(outgoingArraySet[out][s] + "  " + this.text);
                        $(this).attr('selected', true);
                    }

                }


            });


        }


        $('#tableNames0').append('<option></option>');

        $.each(tableCols, function (key, value) {
            //alert(key + "");
            $('#tableNames0').append('<option>' + key + '</option>');

        });
    }

    newWhereClickEvent(0);
});

function setDataList() {

    var inputCount = $('input#count').val();
    for (var i = 0; i < inputCount; i += 1) {
        var inputName = $('#pName' + i).val();
        $('#inputVars').append('<option value="' + inputName + '">');
    }
    //

}

function updateSet(count) {

    var whereCount = $('#whereTBody' + count + ' tbody').find('tr').length;
    setColumns(whereCount, count);

}

function setColumns(count, tCount) {
    $("#colNames" + tCount + " option").each(function () {
        $(this).remove();
    });

    //now lets loop this
    for (var c = 0; c < count; c += 1) {
        $("#whereInp" + tCount + c + " option").each(function () {
            $(this).remove();
        });
    }
    selectionColumns(tCount);
}

function getUrlVars() {
    var vars = {};
    window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function (m, key, value) {
        vars[key] = value;
    });
    return vars;
}

function addWhere(count, tbody, tCount) {
    var row = '<tr>'
            + '<td><select class="form-control" id="whereInp' + tCount + count + '"name="whereInp' + tCount + +count + '"></select>'
            + '</td><td><select class="form-control" id="whereOpInp' + tCount + +count + '"name="whereOpInp' + tCount + +count + '"><option>=</option>'
            + '<option>&lt;</option><option>&gt;</option><option>&lt;&gt;</option><option>%LIKE</option>'
            + '<option>%LIKE%</option><option>LIKE%</option></select>'
            + '</td><td><input type="text" list="inputVars" class="form-control" id="whereValInp' + tCount + +count + '"name="whereValInp' + tCount + +count + '" placeholder="">'
            + '</td><td><button type="button" class="btn btn-danger" id="delete' + tCount + +count + '"><span data-feather="delete"></span></button></td></tr>';
    $('#' + tbody + ' tbody').append(row);
    $('#whereCount' + tCount).val(count + 1);
    feather.replace();
}

function addOutgoing(whereCnt, count) {
    var row = '<div class="jumbotron" style="background: #fff;">'
            + '<div class="form-group row"><div class="col">'
            + '<label for="tableNames' + count + '" class="col col-form-label">Table</label>'
            + '<select id="tableNames' + count + '" name="tableNames' + count + '" class="form-control" onchange="updateSet(' + count + ')">'
            + '</select></div><div class="col"><label for="colNames' + count + '" class="col col-form-label">Columns</label>'
            + '<select multiple id="colNames' + count + '" name="colNames' + count + '[]" class="form-control">'
            + '</select></div></div><button type="button" class="btn btn-warning" id="addWhere' + count + '" data-a="' + count + '">'
            + '<span data-feather="plus-circle"></span>  WHERE Clause</button><div class="form-group row"><div class="table-responsive">'
            + '<input type="hidden" id="whereCount' + count + '" name="whereCount' + count + '" value="0"><table class="table table-striped table-sm" id="whereTBody' + count + '">'
            + '<thead><tr><th>Where</th><th>Operator</th><th>Where Value</th><th>Delete</th></tr></thead><tbody>'
            + '<tr><td><select class="form-control" id="whereInp' + count + '0" aria-describedby="whereInput" name="whereInp' + count + '0"></select>'
            + '<small id="whereInput" class="form-text text-muted">each Column to Compare separated by ","</small></td><td>'
            + '<select class="form-control" id="whereOpInp' + count + '0" aria-describedby="whereOpInput" name="whereOpInp' + count + '0"><option>=</option>'
            + '<option>&lt;</option><option>&gt;</option><option>&lt;&gt;</option><option>%LIKE</option>'
            + '<option>%LIKE%</option><option>LIKE%</option></select>'
            + '<small id="whereOpInput" class="form-text text-muted">each comparative Operator to Compare separated by "," (ex. =, <\>, <, >)</small>'
            + '</td><td><input type="text" list="inputVars" class="form-control" id="whereValInp' + count + '0" aria-describedby="whereValInput" name="whereValInp' + count + '0" placeholder="">'
            + '<small id="whereValInput" class="form-text text-muted">each input string name: Use ##parametername for incoming Parameters, and static values otherwise</small>'
            + '</td><td><button type="button" class="btn btn-danger" id="delete' + count + '0"><span data-feather="delete"></span></button></td>'
            + '</tr></tbody></table></div></div></div>';
    $('#OutgoingForm').append(row);

    feather.replace();



    $("#colNames" + count + " option").each(function () {
        $(this).remove();
    });

    $('#tableNames' + count).append('<option></option>');

    $.each(tableCols, function (key, value) {

        $('#tableNames' + count).append('<option>' + key + '</option>');

    });
    newWhereClickEvent(count);
    //selectonColumns(count);

}

function newWhereClickEvent(count) {

    whereCount[count] = 1;
    $('#addWhere' + count + '[data-a]').on('click', function (e) {
        e.preventDefault();
        var whereButData = 'whereTBody' + count;
        //alert(whereButData + " >> " + whereCount[count] + " >> " + count);
        addWhere(whereCount[count], whereButData, count);
        $('#whereCount' + count).val(whereCount[count]);
        setForWhere(whereCount[count], count);
        whereCount[count]++;



    });
}

function setForWhere(count, tCount) {

    selectionForWhere(tCount, count);
}

function selectionColumns(count) {
    var selectedTable = $('#tableNames' + count + ' option:selected').text();
    var whereCount = $('#whereTBody' + count + ' tbody').find('tr').length;
    //alert(whereCount + " WHERE COUNT " + '#whereTBody ' + count + ' tbody');
    $.each(tableCols, function (key, value) {
        //alert('KEY ' + key);
        if (key === selectedTable) {
            //alert("FOUND " + key);
            $.each(value, function (i, val) {

                $('#colNames' + count).append('<option>' + val + '</option>');
                for (var c = 0; c < whereCount; c += 1) {
                    //alert('#whereInp' + count + c);
                    $('#whereInp' + count + c).append('<option>' + val + '</option>');
                }
            });
        }
    });
}

function selectionForWhere(count, whereCount) {
    var selectedTable = $('#tableNames' + count + ' option:selected').text();
    //var whereCount = $('#whereTBody' + count + ' tbody').find('tr').length;
    //alert(whereCount + " WHERE COUNT " + '#whereTBody ' + count + ' tbody');
    $.each(tableCols, function (key, value) {
        //alert('KEY ' + key);
        if (key === selectedTable) {
            //alert("FOUND " + key);
            $.each(value, function (i, val) {
                //alert('#whereInp' + count + whereCount);
                $('#whereInp' + count + whereCount).append('<option>' + val + '</option>');

            });
        }
    });
}

function addIncoming(count) {
    var row = '<tr><td><input type="text" class="form-control" id="pName' + count + '" name="pName' + count + '" placeholder="">'
            + '</td><td><select id="inputState' + count + '" name="inputState' + count + '" class="form-control"><option value="0" selected>Integer</option>'
            + '<option  value="1">Double</option><option value="2">String</option><option value="3">DateTime</option><option value="4">Boolean</option>'
            + '</select></td>'
            + "<td><button type='button' class='btn btn-danger' id='delete" + count + "'><span data-feather='delete'></span</button></td></tr>";
    $('tbody#incomingTBody').append(row);

    feather.replace();
}
