<!DOCTYPE html>
<html lang="<?php echo e(app()->getLocale()); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo e(config('app.name')); ?> - <?php echo e($title ?? 'Welcome'); ?></title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="<?php echo e(asset('favicon.ico')); ?>" type="image/x-icon">

    <!-- CSS -->
    <link rel="stylesheet" href="<?php echo e(asset('css/bootstrap.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/fontawesome.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/app.css')); ?>">

    <!-- Custom CSS -->
    <?php echo $styles ?? ''; ?>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container">
                <a class="navbar-brand" href="<?php echo e(route('home')); ?>">
                    <?php echo e(config('app.name')); ?>
                </a>

                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo e(route('home')); ?>">
                                <?php echo e(__('home')); ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo e(route('contests.index')); ?>">
                                <?php echo e(__('contests')); ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo e(route('about')); ?>">
                                <?php echo e(__('about')); ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo e(route('contact')); ?>">
                                <?php echo e(__('contact')); ?>
                            </a>
                        </li>
                    </ul>

                    <ul class="navbar-nav">
                        <?php if(auth()->check()): ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown">
                                    <?php echo e(auth()->user()->username); ?>
                                </a>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="<?php echo e(route('profile')); ?>">
                                        <?php echo e(__('profile')); ?>
                                    </a>
                                    <a class="dropdown-item" href="<?php echo e(route('settings')); ?>">
                                        <?php echo e(__('settings')); ?>
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="<?php echo e(route('logout')); ?>" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <?php echo e(__('logout')); ?>
                                    </a>
                                    <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" style="display: none;">
                                        <?php echo csrf_field(); ?>
                                    </form>
                                </div>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo e(route('login')); ?>">
                                    <?php echo e(__('login')); ?>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo e(route('register')); ?>">
                                    <?php echo e(__('register')); ?>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="main">
        <div class="container">
            <?php if(session('success')): ?>
                <div class="alert alert-success">
                    <?php echo e(session('success')); ?>
                </div>
            <?php endif; ?>

            <?php if(session('error')): ?>
                <div class="alert alert-danger">
                    <?php echo e(session('error')); ?>
                </div>
            <?php endif; ?>

            <?php if(session('warning')): ?>
                <div class="alert alert-warning">
                    <?php echo e(session('warning')); ?>
                </div>
            <?php endif; ?>

            <?php if(session('info')): ?>
                <div class="alert alert-info">
                    <?php echo e(session('info')); ?>
                </div>
            <?php endif; ?>

            <?php echo $content ?? ''; ?>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><?php echo e(config('app.name')); ?></h5>
                    <p><?php echo e(config('app.description')); ?></p>
                </div>
                <div class="col-md-3">
                    <h5><?php echo e(__('links')); ?></h5>
                    <ul class="list-unstyled">
                        <li><a href="<?php echo e(route('home')); ?>"><?php echo e(__('home')); ?></a></li>
                        <li><a href="<?php echo e(route('about')); ?>"><?php echo e(__('about')); ?></a></li>
                        <li><a href="<?php echo e(route('contact')); ?>"><?php echo e(__('contact')); ?></a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5><?php echo e(__('contact')); ?></h5>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-envelope"></i> <?php echo e(config('app.email')); ?></li>
                        <li><i class="fas fa-phone"></i> <?php echo e(config('app.phone')); ?></li>
                        <li><i class="fas fa-map-marker-alt"></i> <?php echo e(config('app.address')); ?></li>
                    </ul>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-6">
                    <p>&copy; <?php echo e(date('Y')); ?> <?php echo e(config('app.name')); ?>. <?php echo e(__('all_rights_reserved')); ?></p>
                </div>
                <div class="col-md-6 text-right">
                    <a href="/privacy">Privacy Policy</a>
                    <a href="/terms">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="<?php echo e(asset('js/jquery.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/bootstrap.bundle.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/fontawesome.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/app.js')); ?>"></script>

    <!-- Custom JavaScript -->
    <?php echo $scripts ?? ''; ?>
</body>
</html>