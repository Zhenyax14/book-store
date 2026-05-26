<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['book']));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['book']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $colors = ['orange', 'blue', 'green', 'red', 'purple', 'teal', 'dark'];
    $color  = $colors[crc32($book['slug']) % count($colors)];
    $price  = $book['is_free'] ? 'Free' : ($book['price']['formatted'] ?? '—');
    $isSubscription = ($book['access_type'] ?? '') === 'subscription';
?>

<a href="/books/<?php echo e($book['id']); ?>" class="product-card h-100 d-block text-decoration-none">

    <div class="product-card__img">
        <?php if(!empty($book['cover_url'])): ?>
            <img src="<?php echo e($book['cover_url']); ?>"
                 alt="<?php echo e($book['title']); ?>"
                 style="height:160px; width:auto; object-fit:cover; border-radius:4px;"
                 loading="lazy">
        <?php else: ?>
            <div class="book-thumb book-thumb--<?php echo e($color); ?> mx-auto">
                <span><?php echo e($book['title']); ?></span>
                <?php if(!empty($book['publisher'])): ?>
                    <small><?php echo e($book['publisher']); ?></small>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="product-card__overlay">
            <?php if($isSubscription): ?>
                <button class="product-card__btn product-card__btn--cart">⭐ Subscribe</button>
            <?php else: ?>
                <button class="product-card__btn product-card__btn--cart">📖 Read</button>
            <?php endif; ?>
        </div>
    </div>

    <div class="product-card__info">
        <h3 class="product-card__title"><?php echo e($book['title']); ?></h3>
        <?php if(!empty($book['publisher'])): ?>
            <p class="product-card__author"><?php echo e($book['publisher']); ?></p>
        <?php endif; ?>
        <div class="product-card__price"><?php echo e($price); ?></div>
    </div>

</a>
<?php /**PATH /var/www/html/resources/views/components/book/book-card.blade.php ENDPATH**/ ?>