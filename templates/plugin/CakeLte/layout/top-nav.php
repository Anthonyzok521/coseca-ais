<?php

/**
 * @var \App\View\AppView $this
 * @var \CakeLte\View\Helper\CakeLteHelper $this->CakeLte
 */

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $this->fetch('title') . ' | ' . strip_tags($this->CakeLte->getConfig('app-name')) ?></title>

    <?= $this->Html->meta('icon') ?>
    <?= $this->fetch('meta') ?>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome Icons -->
    <?= $this->Html->css('/adminlte/plugins/fontawesome-free/css/all.min.css') ?>
    <!-- Theme style -->
    <?= $this->Html->css('/adminlte/dist/css/adminlte.min.css') ?>
    <?= $this->Html->css('CakeLte.style') ?>
    <?= $this->Html->css('app') ?>
    <?= $this->element('CakeLte.extra/css') ?>
    <?= $this->fetch('css') ?>
</head>

<body class="hold-transition layout-top-nav <?= $this->CakeLte->getBodyClass() ?>">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand-md <?= $this->CakeLte->getHeaderClass() ?>">
            <div class="container">
                <a href="<?= $this->Url->build('/') ?>" class="navbar-brand">
                    <?= $this->Html->image($this->CakeLte->getConfig('app-logo'), ['alt' => $this->CakeLte->getConfig('app-name') . ' logo', 'class' => 'brand-image']) ?>
                    <span class="brand-text font-weight-light"><?= $this->CakeLte->getConfig('app-name') ?></span>
                </a>

                <button class="navbar-toggler order-1" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse order-3" id="navbarCollapse">
                    <div class="navbar-text">
                        <?= $this->Student->get()->tenant->name ?>
                    </div>

                    <!-- SEARCH FORM -->
                    <?php //echo $this->element('CakeLte.header/search-default') 
                    ?>
                </div>

                <!-- Right navbar links -->
                <ul class="order-1 order-md-3 navbar-nav navbar-no-expand ml-auto">
                    <!-- Messages Dropdown Menu -->
                    <?php //echo $this->element('CakeLte.header/messages') 
                    ?>

                    <!-- Notifications Dropdown Menu -->
                    <?php //echo $this->element('CakeLte.header/notifications') 
                    ?>

                    <!--
                    <li class="nav-item">
                        <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                            <i class="fas fa-expand-arrows-alt"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#" role="button">
                            <i class="fas fa-th-large"></i>
                        </a>
                    </li> -->
                    <li class="nav-item dropdown">
                        <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">
                            <?= $this->Student->getUser()->email ?>
                        </a>
                        <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow" style="left: 0px; right: inherit;">
                            <li>
                                <?= $this->Html->link(__('Perfil'), '/profile', ['class' => 'dropdown-item']) ?>
                            </li>
                            <li class="dropdown-divider"></li>
                            <li>
                                <?= $this->Html->link(__('Cerrar Sesión'), '/logout', ['class' => 'dropdown-item']) ?>
                            </li>
                        </ul>
                    </li>

                </ul>
            </div>
        </nav>
        <!-- /.navbar -->

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container">
                    <?= $this->element('CakeLte.content/header') ?>
                </div><!-- /.container-fluid -->
            </div>

            <!-- Main content -->
            <div class="content">
                <div class="container">
                    <?= $this->Flash->render() ?>
                    <?= $this->fetch('content') ?>
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
            <?= $this->element('CakeLte.aside/main') ?>
        </aside>
        <!-- /.control-sidebar -->

        <!-- Main Footer -->
        <footer class="main-footer">
            <div class="container">
                <?= $this->element('CakeLte.footer/main') ?>
            </div>
        </footer>
    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    <?= $this->Html->script('/adminlte/plugins/jquery/jquery.min.js') ?>
    <!-- Bootstrap 4 -->
    <?= $this->Html->script('/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') ?>
    <!-- AdminLTE App -->
    <?= $this->Html->script('/adminlte/dist/js/adminlte.min.js') ?>

    <?= $this->element('CakeLte.extra/script') ?>
    <?= $this->fetch('script') ?>
</body>

</html>