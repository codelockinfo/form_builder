<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reorder Table Rows</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: left;
        }
        .placeholder {
            background: #f0f0f0;
        }
    </style>
</head>
<body>

<table id="sortable-table">
    <thead>
        <tr>
            <th>Item</th>
            <th>Details</th>
        </tr>
    </thead>
    <tbody>
        <tr data-id="1">
            <td>Item 1</td>
            <td>Details 1</td>
        </tr>
        <tr data-id="2">
            <td>Item 2</td>
            <td>Details 2</td>
        </tr>
        <tr data-id="3">
            <td>Item 3</td>
            <td>Details 3</td>
        </tr>
    </tbody>
</table>
<div class="selected_element_set" >
<table  id="sortable-table">

    <div  class="builder-item-wrapper clsselected_element" ><tr data-postionid="1" data-formdataid="3"><td> sweweweewe item 1</td></tr><div>
    <div  class="builder-item-wrapper clsselected_element" ><tr data-postionid="2" data-formdataid="1"><td> eweffdf item 2</td></tr><div>
    <div  class="builder-item-wrapper clsselected_element" ><tr data-postionid="4" data-formdataid="6"><td> erererer item 4</td></tr><div>
    <div  class="builder-item-wrapper clsselected_element" ><tr data-postionid="5" data-formdataid="7"><td> 5tfhgjgjhj item 5</td></tr><div>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script>
    $(function() {
        $("#sortable-table tbody").sortable({
            placeholder: "placeholder",
            update: function(event, ui) {
                console.log(ui);
                var order = $(this).sortable("toArray", { attribute: "data-id" });
                var id = $(this).sortable("toArray", { attribute: "data-formdataid" });
                $.post("update_order.php", { order: order,id:id });
            }
        }).disableSelection();
       $html = `
    <div  class="builder-item-wrapper clsselected_element" ><tr data-postionid="11" data-formdataid="3"><td>      item 11</td></tr><div>
    <div  class="builder-item-wrapper clsselected_element" ><tr data-postionid="12" data-formdataid="1"><td>      item 12</td></tr><div>
    <div  class="builder-item-wrapper clsselected_element" ><tr data-postionid="13" data-formdataid="6"><td>      item 13</td></tr><div>
    <div  class="builder-item-wrapper clsselected_element" ><tr data-postionid="14" data-formdataid="7"><td>      item 14</td></tr><div>`;
    $("#sortable-table").append($html);
    });
</script>

</body>
</html>