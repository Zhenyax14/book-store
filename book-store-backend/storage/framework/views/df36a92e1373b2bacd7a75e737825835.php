<?php $__env->startSection('title', 'Featured — Bookshop'); ?>

<?php $__env->startSection('content'); ?>
    <div class="content">
        <section class="section">

            <div class="section__header">
                <h2 class="section__title">FEATURED</h2>
                <p class="section__subtitle">
                    A carefully curated selection of the best titles from our bookstore
                </p>
                <div class="section__line"></div>
            </div>

            <form method="GET" action="<?php echo e(route('catalog.index')); ?>" class="filters-bar" id="filters-form">
                <div class="filters-right">
                    
                    <select name="tag" class="sort-select" onchange="this.form.submit()">
                        <option value="" <?php if(empty($filters['tag'])): echo 'selected'; endif; ?>>All tags</option>
                        <?php $__currentLoopData = $tags ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($tag->slug); ?>" <?php if(($filters['tag'] ?? '') === $tag->slug): echo 'selected'; endif; ?>>
                                <?php echo e($tag->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>

                    <select name="access_type" class="sort-select" onchange="this.form.submit()">
                        <option value=""             <?php if(empty($filters['access_type'])): echo 'selected'; endif; ?>>All access types</option>
                        <option value="free"         <?php if(($filters['access_type'] ?? '') === 'free'): echo 'selected'; endif; ?>>Free</option>
                        <option value="purchase"     <?php if(($filters['access_type'] ?? '') === 'purchase'): echo 'selected'; endif; ?>>Purchase</option>
                        <option value="subscription" <?php if(($filters['access_type'] ?? '') === 'subscription'): echo 'selected'; endif; ?>>Subscription</option>
                    </select>
                </div>
            </form>

            <div class="products-wrapper">
                <?php if(!empty($books)): ?>
                    <div class="products-grid">
                        <?php $__currentLoopData = $books; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $book): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if (isset($component)) { $__componentOriginal0c084bff631e462ba0c57b43011cbddb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0c084bff631e462ba0c57b43011cbddb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.book.book-card','data' => ['book' => $book]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('book.book-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['book' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($book)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0c084bff631e462ba0c57b43011cbddb)): ?>
<?php $attributes = $__attributesOriginal0c084bff631e462ba0c57b43011cbddb; ?>
<?php unset($__attributesOriginal0c084bff631e462ba0c57b43011cbddb); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0c084bff631e462ba0c57b43011cbddb)): ?>
<?php $component = $__componentOriginal0c084bff631e462ba0c57b43011cbddb; ?>
<?php unset($__componentOriginal0c084bff631e462ba0c57b43011cbddb); ?>
<?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center py-5">No books found for the selected filters.</p>
                <?php endif; ?>
            </div>

            
            <?php
                $currentPage = $meta['current_page'] ?? 1;
                $totalPages  = $meta['total_pages']  ?? 1;
            ?>

            <?php if($totalPages > 1): ?>
                <nav class="d-flex justify-content-center gap-2 mt-4" aria-label="Catalog pages">

                    <?php if($currentPage > 1): ?>
                        <a href="<?php echo e(request()->fullUrlWithQuery(['page' => $currentPage - 1])); ?>"
                           class="btn btn-outline-secondary btn-sm" rel="prev">‹</a>
                    <?php endif; ?>

                    <?php for($p = max(1, $currentPage - 2); $p <= min($totalPages, $currentPage + 2); $p++): ?>
                        <a href="<?php echo e(request()->fullUrlWithQuery(['page' => $p])); ?>"
                           class="btn btn-sm <?php echo e($p === $currentPage ? 'btn-warning' : 'btn-outline-secondary'); ?>">
                            <?php echo e($p); ?>

                        </a>
                    <?php endfor; ?>

                    <?php if($currentPage < $totalPages): ?>
                        <a href="<?php echo e(request()->fullUrlWithQuery(['page' => $currentPage + 1])); ?>"
                           class="btn btn-outline-secondary btn-sm" rel="next">›</a>
                    <?php endif; ?>

                </nav>
            <?php endif; ?>

        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/resources/views/catalog/index.blade.php ENDPATH**/ ?>