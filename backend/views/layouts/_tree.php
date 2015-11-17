<?php
use common\models\Document;

$data = [];
$documents = Document::find()->orderBy('lft')->all();
if ($documents) {
    foreach ($documents as $doc) {
        $d = [];
        $d['id'] = $doc->id;
        $d['parent'] = ($doc->parent_id)?$doc->parent_id:'#';
        $d['text'] = $doc->name.' <span class="node-id">('.$doc->id.')</span>';
        if ($doc->is_folder){
            if ($doc->status){
                $d['icon'] = '/admin/css/image/icon-folder.png';
            } else{
                $d['icon'] = '/admin/css/image/icon-folder-disable.png';
            }
        }else{
            if ($doc->status){
                $d['icon'] = '/admin/css/image/icon-file.png';
            }else{
                $d['icon'] = '/admin/css/image/icon-file-disable.png';
            }
        }
        $d['status'] = $doc->status;
        if (!$doc->depth){
            $d['state'] = ['opened' => true,];
        }
        $data[] = $d;
    }
}
if ((Yii::$app->controller->id == 'document') and (Yii::$app->controller->action->id == 'create')) {
    $parent_id = Yii::$app->request->get('parent_id');
    if ($parent_id) {
        $data[] = ['id' => '0', 'parent' => $parent_id, 'text' => ' Новый документ <span class="node-id">(0)</span>', 'icon' => '/admin/css/image/icon-file.png',  'state' => ['selected' => true], 'status' => 0];
        $select_id=0;
        foreach ($data as $k => $d){
            if ($d['id'] == $parent_id){
                $select_id = $k;
            }
        }
        if ($data[$select_id]['status']){
            $data[$select_id]['icon'] = '/admin/css/image/icon-folder.png';
        }
        else{
            $data[$select_id]['icon'] = '/admin/css/image/icon-folder-disable.png';
        }
    }
}
if ((Yii::$app->controller->id == 'document') and (Yii::$app->controller->action->id == 'update')) {
    $id = Yii::$app->request->get('id');
    if ($id) {
        $select_id=0;
        foreach ($data as $k => $d){
            if ($d['id'] == $id){
                $select_id = $k;
            }
        }
        $data[$select_id]['state'] = ['selected' => true];
    }
}
$data = json_encode($data, JSON_UNESCAPED_UNICODE);

$this->registerJs("
$('#jstree_div').jstree({
    'core' : {
        'check_callback' : function(o, n, p, i, m) {
            if(o === 'move_node' || o === 'copy_node') {
                if(this.get_node(p).id == '#') { // Нельзя перемещать документы на позицию перед и после корневого документа
                    return false;
                }
            }
            return true;
        },
        'data' : ".$data."   // Дерево документов в формате JSON
    },
        'plugins' : ['contextmenu', 'search', 'dnd'] // Подключение контекстного меню, поиска и grag_n_drop

}).bind('move_node.jstree', function(e, data){  // После перемещения документа

        var new_inst = data.new_instance;
        var next = new_inst.get_next_dom(data.node, true);
        if (next){
            next = next.context.id; // Получаем id следующего документа позиции вставки
        }
        var prev = new_inst.get_prev_dom(data.node, true);
        if (prev){
            prev = prev.context.id; // Получаем id предыдущго документа позиции вставки
        }
        // Меняем иконку нового родителя на папку в зависимости от его статуса публикации
        if (new_inst.get_icon(data.parent)=='/admin/css/image/icon-file.png'){
            new_inst.set_icon(data.parent,'/admin/css/image/icon-folder.png');
        } else if (new_inst.get_icon(data.parent)=='/admin/css/image/icon-file-disable.png'){
            new_inst.set_icon(data.parent,'/admin/css/image/icon-folder-disable.png');
        }
        //  Меняем иконку старого родителя на документ в зависимости от его статуса публикации при условии отсутвия потомков
        if (!new_inst.is_parent(data.old_parent)){
            if (new_inst.get_icon(data.old_parent) =='/admin/css/image/icon-folder.png'){
                new_inst.set_icon(data.old_parent,'/admin/css/image/icon-file.png');
            } else if(new_inst.get_icon(data.old_parent) =='/admin/css/image/icon-folder-disable.png'){
                new_inst.set_icon(data.old_parent,'/admin/css/image/icon-file-disable.png');
            }
        }
        $.ajax({
            url: '/admin/document/move',
            type: 'POST',
            data: {
                'id' : data.node.id,    // Перемещаемый документ
                'old_parent_id' : data.old_parent,  // Старый родительский документ
                'new_parent_id' : data.parent,  // Новый родительский документ
                'new_prev_id' : prev,   // Следующий документ на новой позици (в рамках одного родителя)
                'new_next_id' : next    // Предыдущий докумет на новой позиции (в рамках одного родителя)
            },
            success: function(data){
            }
        });
    });

var to = false;
$('#jstree_search_input').keyup(function () {   // Поиск документа в дереве по названию и id
    if(to) { clearTimeout(to); }
    to = setTimeout(function () {
        var v = $('#jstree_search_input').val();
        $('#jstree_div').jstree(true).search(v);
    }, 250);
});
");
?>