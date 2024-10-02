<?php

require __DIR__ . '/inc/functions.php';
require __DIR__ . '/inc/db-connect.php';

// Prepare and execute the SQL statement
$perPage = 3;
$page = (int) ($_GET['page'] ?? 1);
if ($page < 1) $page = 1;

//$page = 1, $offset => 0
//$page = 2, $offset => $perPage
//$page = 3, $offset => $perPage * 2

$offset = ($page - 1) * $perPage;

$stmtCount = $pdo->prepare('SELECT COUNT(*) AS `count` FROM `entries`');
$stmtCount->execute();
$count = $stmtCount->fetch(PDO::FETCH_ASSOC)['count'];
//var_dump($count);

$numPages = ceil($count / $perPage);

$stmt = $pdo->prepare('SELECT * FROM `entries` ORDER BY `created_at` DESC, `id` DESC LIMIT :perPage OFFSET :offset');
$stmt-> bindValue('perPage', (int) $perPage, PDO::PARAM_INT);
$stmt->bindValue('offset', (int) $offset, PDO::PARAM_INT);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Optionally, print the results
//var_dump($results);

?>
<?php require __DIR__ . '/views/header.php'; ?>

<h1 class="main-heading">ENTRIES</h1>

<?php foreach ($results AS $result): ?>
    
    <div class="card">
        <?php if (!empty($result['image'])): ?>
            <div class="card__image-container">
                <img class="card__image" src="uploads/<?php echo e($result['image']); ?>" alt=""/>
            </div>
        <?php endif; ?>


        <div class="card__desc-container">
            <div class="card__desc-time"><?php echo e($result['created_at']); ?></div>
            <h2 class="card__heading"><?php echo e($result['title']); ?></h2>
            <p class="card__paragrph">
                <?php echo nl2br (e($result['message'])); ?>
            </p>
        </div>
        
        <!-- Delete button with confirmation -->
        <div class="form-delete">
            <form method="POST" action="delete-entry.php" onsubmit="return confirmDelete();">
                <input type="hidden" name="id" value="<?php echo $result['id']; ?>" />
                <button type="submit" class="button">Delete</button>
            </form>
        </div>
    </div>
<?php endforeach; ?>           

<?php if ($numPages > 1): ?>
<ul class="pagination">
    <?php if ($page > 1): ?>
        <li class="pagination__li">
            <a class="pagination__a" href="index.php?<?php echo http_build_query(['page' => $page - 1]); ?>">◀</a>
        </li>
    <?php endif; ?>
    <?php for($x = 1; $x <= $numPages; $x++): ?>
        <li class="pagination__li">
            <a 
                class="pagination__a <?php if ($page === $x): ?>pagination__li--active<?php endif; ?>" 
                href="index.php?<?php echo http_build_query(['page' => $x]); ?>">
                <?php echo e($x); ?>
            </a>
        </li>
    <?php endfor; ?>    
    
    <?php if ($page < $numPages): ?>
        <li class="pagination__li">
            <a 
                class="pagination__a" 
                href="index.php?<?php echo http_build_query(['page' => $page + 1]); ?>">▶</a>
        </li>
    <?php endif; ?>
</ul>
<?php endif; ?>

<!-- Confirmation delete popup script -->
<script>
function confirmDelete() {
    return confirm('Are you sure you want to delete this entry?');
}
</script>

<?php require __DIR__ .'/views/footer.php'; ?> 
