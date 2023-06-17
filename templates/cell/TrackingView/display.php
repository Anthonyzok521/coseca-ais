<?php

/**
 * @var \App\View\AppView $this
 */

use App\Enum\ActionColor;
use App\Model\Field\StageField;
use CakeLteTools\Utility\FaIcon;

$user = $this->request->getAttribute('identity');
$trackingDates = $student?->lapse?->getDates(StageField::TRACKING);

?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><?= __('Seguimiento: {0}', $student->lapse->name ?? $this->App->nan()) ?></h3>
    </div>
    <div class="card-body">
        <?= $this->cell('TrackingView::info', ['student_id' => $student->id]) ?>
    </div>
</div>

<?php foreach ($adscriptions as $adscription) : ?>
    <?php
    $canAddTracking = $user->can('addTracking', $adscription);
    $canDeleteTracking = $user->can('deleteTracking', $adscription);
    $canValidateAdscription = $user->can('validate', $adscription);
    $count = 0;
    $sumHours = 0;
    ?>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <?= h($adscription->institution_project->label_name) ?>
                <?= $this->App->badge($adscription->getStatus()) ?>
            </h3>
            <div class="card-tools">
                <?php
                if ($canValidateAdscription && !empty($urlList['validate'])) :
                    echo $this->Button->openModal([
                        'label' => __('Validar horas del proyecto'),
                        'data-target' => '#validateAdscription' . $adscription->id,
                        'class' => 'btn-sm',
                        'actionColor' => ActionColor::VALIDATE,
                        'icon' => FaIcon::get('validate', 'fa-fw'),
                    ]);
                endif;
                ?>
                <?php if ($canAddTracking) : ?>
                    <?= $this->Button->openModal([
                        'label' => __('Agregar Actividad'),
                        'data-target' => '#addTracking' . $adscription->id,
                        'class' => 'btn-sm',
                        'icon' => FaIcon::get('tasks', 'fa-fw'),
                    ]) ?>
                <?php endif ?>
            </div>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>#</th>
                        <th><?= __('Fecha') ?></th>
                        <th><?= __('Actividad') ?></th>
                        <th><?= __('Horas') ?></th>
                        <th class="actions"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($adscription->student_tracking)) : ?>
                        <tr>
                            <td colspan="5">
                                <?= __('No se encontraron actividades en este proyecto') ?>
                            </td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ($adscription->student_tracking as $tracking) : ?>
                            <?php
                            $count++;
                            $sumHours += $tracking->hours;
                            ?>
                            <tr>
                                <td><?= $count ?></td>
                                <td><?= h($tracking->date) ?></td>
                                <td><?= h($tracking->description) ?></td>
                                <td><?= h($tracking->hours) ?></td>
                                <td class="actions">
                                    <?php if ($canDeleteTracking) : ?>
                                        <?php $urlDelete = array_merge($urlList['delete'] ?? ['action' => 'delete'], [$tracking->id]) ?>
                                        <?= $this->Button->remove([
                                            'url' => $urlDelete,
                                            'class' => 'btn-xs',
                                            'confirm' => __('¿Está seguro de eliminar esta actividad?'),
                                            'label' => null,
                                        ]) ?>
                                    <?php endif ?>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    <?php endif ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-right">
                            <strong><?= __('Total horas') ?></strong>
                        </td>
                        <td>
                            <strong><?= $sumHours ?></strong>
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <?php if ($canAddTracking) : ?>
        <div class="modal fade" id="<?= 'addTracking' . $adscription->id ?>" tabindex="-1" role="dialog" aria-labelledby="addTrackingModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <?= __('Agregar Actividad') ?>
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <?php $urlAdd = $urlList['add'] ?? ['action' => 'add'] ?>
                    <?= $this->Form->create(null, ['url' => $urlAdd]) ?>
                    <?= $this->Form->hidden('student_adscription_id', ['value' => $adscription->id]) ?>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col">
                                <?= $this->App->control('project', [
                                    'label' => __('Proyecto'),
                                    'value' => h($adscription->institution_project->label_name),
                                ]) ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-8">
                                <?= $this->Form->control('date', [
                                    'label' => __('Fecha'),
                                    'type' => 'date',
                                    'required' => true,
                                    'min' => $trackingDates['start_date']?->format('Y-m-d') ?? '',
                                    'max' => $trackingDates['end_date']?->format('Y-m-d') ?? '',
                                ]) ?>
                            </div>
                            <div class="col-sm-4">
                                <?= $this->Form->control('hours', [
                                    'label' => __('Horas'),
                                    'type' => 'number',
                                    'required' => true,
                                    'step' => '0.25',
                                    'min' => '0.25',
                                    'max' => '12',
                                ]) ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <?= $this->Form->control('description', [
                                    'label' => __('Actividad'),
                                    'required' => true,
                                ]) ?>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <?= $this->Button->save() ?>
                        <?= $this->Button->closeModal() ?>
                    </div>
                    <?= $this->Form->end() ?>
                </div>
            </div>
        </div>
    <?php endif ?>

    <?php if ($canValidateAdscription && !empty($urlList['validate'])) : ?>
        <div class="modal fade" id="<?= 'validateAdscription' . $adscription->id ?>" tabindex="-1" role="dialog" aria-labelledby="addTrackingModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <?= __('Validar horas del proyecto') ?>
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <?php $urlValidate = array_merge($urlList['validate'] ?? ['action' => 'validate'], [$adscription->id]) ?>
                    <?= $this->Form->create(null, ['url' => $urlValidate]) ?>
                    <div class="modal-body">
                        <?= $this->Form->hidden('redirect', ['value' => $this->Url->build()]) ?>
                        <?= $this->App->control('validate_token', [
                            'label' => __('Código de validación'),
                            'value' => 'wqdwqdwqd',
                        ]) ?>
                        <?= $this->Form->control('confirm', [
                            'type' => 'checkbox',
                            'label' => __('Confirmo que las actividades registradas son correctas'),
                            'required' => true,
                        ])  ?>
                    </div>
                    <div class="modal-footer">
                        <?= $this->Button->validate([
                            'label' => __('Validar'),
                            'confirm' => false,
                        ]) ?>
                        <?= $this->Button->closeModal() ?>
                    </div>
                    <?= $this->Form->end() ?>
                </div>
            </div>
        </div>
    <?php endif ?>

<?php endforeach ?>