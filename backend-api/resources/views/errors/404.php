<?php
/**
 * 404 error view
 */
?>

@extends('layouts.app')

@section('title', __('error_404'))

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header text-center">
                <h1 class="display-1">404</h1>
                <h2><?php echo e(__('error_404')); ?></h2>
            </div>

            <div class="card-body text-center">
                <p class="lead">
                    <?php echo e(__('error_404_message')); ?>
                </p>
                <a href="<?php echo e(route('home')); ?>" class="btn btn-primary">
                    <?php echo e(__('back_to_home')); ?>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection