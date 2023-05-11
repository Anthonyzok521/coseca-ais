<?php

/**
 * @var \App\View\AppView $this
 */

use App\Model\Field\StageStatus;

$this->student_id = $student_id;
$this->active = 'prints';
$this->extend('/Admin/Common/view_student');

$this->assign('title', __('Estudiante'));
$this->Breadcrumbs->add([
    ['title' => __('Inicio'), 'url' => '/'],
    ['title' => __('Estudiantes'), 'url' => ['controller' => 'Students', 'action' => 'index']],
    ['title' => __('Ver'), 'url' => ['controller' => 'Students', 'action' => 'view', $student_id]],
    ['title' => __('Planillas')],
]);
?>

<div class="card-body">
    <div class="alert alert-info">
        Prints
    </div>

    <?= $this->Html->link('Formato de Actividades', ['controller' => 'StudentDocuments', 'action' => 'download', $student_id, 'activities'], ['class' => 'btn btn-primary']) ?>

</div>