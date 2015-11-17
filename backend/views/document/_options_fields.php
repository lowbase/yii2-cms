<?php
use common\models\Template;
use yii\helpers\Html;

?>
<div class="box box-panel">
    <div class="box-header with-border">
        <h3 class="box-title">
            <i class="glyphicon glyphicon-th-list"></i> Расширенные «быстрые» поля
        </h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse">
                <i class="glyphicon glyphicon-minus"></i>
            </button>
        </div>
    </div>
    <div class="box-body">

<?php
if (isset($template) && $template) {
    for ($i = 1; $i <= Template::OPTIONS_COUNT; $i++) {
?>
        <div class="row option">
            <div class="col-sm-12">
                <?= $this->render('_option', [
                    'model' => $model,
                    'i' => $i,
                    'template' => $template,
                ]);?>
            </div>
        </div>
<?php
    }
}
?>
    </div>
</div>

<div class="box box-panel">
    <div class="box-header with-border">
        <h3 class="box-title">
            <i class="glyphicon glyphicon-th-list"></i> Дополнительные поля
        </h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse">
                <i class="glyphicon glyphicon-minus"></i>
            </button>
        </div>
    </div>
    <div class="box-body">

<?php
if (isset($template) && $template) {
    if ($model->fields) {
        foreach ($model->fields as $option_id => $option) {
            if ($model->fields[$option_id]['multiple']) {
                echo "<div class='multi-field' id='field-" . $option_id . "'>";
                echo "<div class='row'><div class='col-sm-12 multi-label'>Мультиполе: ";
                echo Html::a('Добавить еще одно значение', 'javascript:void(0)', [
                    'class' => 'add-field', 'id' => 'add-field-' . $option_id]);
                echo "</div></div>";
            } else {
                echo "<div class='field' id='field-" . $option_id . "'>";
            }
            foreach ($option['value'] as $field_id => $field) {
                echo $this->render('_field', [
                    'model' => $model,
                    'option_id' => $option_id,
                    'option' => $option,
                    'field_id' => $field_id,
                    'field' => $field
                  ]);
            }
            echo "</div>";
        }
    }
}
?>
    </div>
</div>