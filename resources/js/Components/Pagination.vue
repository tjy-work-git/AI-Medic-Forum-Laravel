<template>
    <form method="get" class="pagination-form">
        <?php if ($page > 1): ?>
            <a href="?page=<?= $page - 1 ?>" class="btn btn-secondary">Previous</a>
        <?php endif; ?>

        Page <input type="number" name="page" value="<?= $page ?>" min="1" max="<?= $totalPages ?>" style="width:60px; text-align:center;">
        of <?= $totalPages ?>
        <button type="submit" class="btn btn-primary">Go</button>

        <?php if ($page < $totalPages): ?>
            <a href="?page=<?= $page + 1 ?>" class="btn btn-secondary">Next</a>
        <?php endif; ?>
    </form>
</template>

<script setup>

</script>