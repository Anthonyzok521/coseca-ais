<?php

/**
 * @var \App\View\AppView $this
 */

use Cake\Core\Configure;

$this->layout = 'CakeLte.login';
?>

<div class="card">
    <div class="card-body register-card-body">
        <p class="login-box-msg"><?= __('Register a new membership') ?></p>

        <?= $this->Form->create() ?>
        <?= $this->Form->control('first_name', [
            'placeholder' => __('Nombres'),
            'label' => false,
            'append' => '<i class="fas fa-user"></i>',
            'required' => true,
        ]) ?>
        <?= $this->Form->control('last_name', [
            'placeholder' => __('Apellidos'),
            'label' => false,
            'append' => '<i class="fas fa-user"></i>',
            'required' => true,
        ]) ?>
        <?= $this->Form->control('email', [
            'placeholder' => __('Email'),
            'label' => false,
            'append' => '<i class="fas fa-envelope"></i>',
            'required' => true,
        ]) ?>

        <?= $this->Form->control('password', [
            'placeholder' => __('Password'),
            'label' => false,
            'append' => '<i class="fas fa-lock"></i>',
            'required' => true,
        ]) ?>

        <?= $this->Form->control('password_confirm', [
            'type' => 'password',
            'placeholder' => __('Confirm Password'),
            'label' => false,
            'append' => '<i class="fas fa-lock"></i>',
            'required' => true,
        ]) ?>

        <?php if (Configure::read('Users.reCaptcha.registration')) : ?>
            <div class="row mb-3">
                <div class="col">
                    <?= $this->User->addReCaptcha() ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-8">
                <?php if (Configure::read('Users.Tos.required')) : ?>
                    <?= $this->Form->control('tos', [
                        'label' => 'I agree to the <a href="#">terms</a>',
                        'type' => 'checkbox',
                        'custom' => true,
                        'escape' => false,
                        'required' => true,
                    ]) ?>
                <?php endif; ?>
            </div>
            <div class="col-4">
                <?= $this->Form->control(__('Register'), [
                    'type' => 'submit',
                    'class' => 'btn btn-primary btn-block',
                ]) ?>
            </div>
        </div>

        <?= $this->Form->end() ?>
        <!--
        <div class="social-auth-links text-center mb-3">
            <p>- OR -</p>
            <?= $this->Html->link(
                '<i class="fab fa-facebook-f mr-2"></i>' . __('Sign up using Facebook'),
                '#',
                ['class' => 'btn btn-block btn-primary', 'escape' => false]
            ) ?>
            <?= $this->Html->link(
                '<i class="fab fa-google mr-2"></i>' . __('Sign up using Google'),
                '#',
                ['class' => 'btn btn-block btn-danger', 'escape' => false]
            ) ?>
        </div>
        -->
        <!-- /.social-auth-links -->

        <?= $this->Html->link(__('I already have a membership'), ['action' => 'login']) ?>
    </div>
    <!-- /.register-card-body -->
</div>
