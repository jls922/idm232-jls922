<?php

    require_once 'db.php';

    /*beef chicken fish pork turkey vegetarian */
    $recipes = [];
    $search = $_GET['search'] ?? '';
    $filter = $_GET['filter'] ?? '';
    /*
    $filterBeef = isset($_GET['beef']);
    $filterChicken = isset($_GET['chicken']);
    $filterFish = isset($_GET['fish']); 
    $filterPork = isset($_GET['pork']);
    $filterTurkey = isset($_GET['turkey']);
    $filterVeg = isset($_GET['vegetarian']); 

    $searchQuery = $_GET['search'] ?? '';
    $proteinFilter = $_GET['protein'] ?? 'all';
*/
    $sql = "
        SELECT id, heading, subheading, img_folder, hero_lg, hero_sm, protein
        FROM recipes
        WHERE 1
    ";

    $params = [];
    $types = "";
/*
    if (!empty($search)) {
        $sql .= "
            AND (heading LIKE ? 
            OR subheading LIKE ? 
            OR ingredients LIKE ? 
            OR steps LIKE ?)
        ";

        $term = "%$search%";
        $params = [$term, $term, $term, $term];
        $types .= "ssss"; /*
    }
    /*beef chicken fish pork turkey vegetarian */
    /*
    if ($filterBeef) {
        $sql .= " AND protein = 'beef'";
    }
    if ($filterChicken) {
        $sql .= " AND protein = 'chicken'";
    }
    if ($filterFish) {
        $sql .= " AND protein = 'fish'";
    }
    if ($filterPork) {
        $sql .= " AND protein = 'pork'";
    }
    if ($filterTurkey) {
        $sql .= " AND protein = 'turkey'";
    }
    if ($filterVeg) {
        $sql .= " AND protein = 'vegetarian'";
    }

    $stmt = $connection->prepare($sql);

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    } */

    // Prepare a SQL query with a WHERE clause for filtering
    if (!empty($search) && !empty($filter)) {
        $stmt = $connection->prepare(
            'SELECT * FROM recipes 
            WHERE (heading LIKE ? 
                OR subheading LIKE ? 
                OR protein LIKE ?) 
            AND protein = ?'
        );
        $searchParam = '%' . $search . '%';
        $stmt->bind_param('ssss', $searchParam, $searchParam, $searchParam, $filter);

    } elseif (!empty($search)) {

        $stmt = $connection->prepare(
            'SELECT * FROM recipes 
            WHERE heading LIKE ? 
                OR subheading LIKE ? 
                OR protein LIKE ?'
        );
        $searchParam = '%' . $search . '%';
        $stmt->bind_param('sss', $searchParam, $searchParam, $searchParam);

    } elseif (!empty($filter)) {

        $stmt = $connection->prepare(
            'SELECT * FROM recipes WHERE protein = ?'
        );
        $stmt->bind_param('s', $filter);

    } else {

        $stmt = $connection->prepare('SELECT * FROM recipes');
    }

    $stmt->execute();
    $recipes = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/home.css">
    <title>Home</title>
</head>
<body>
    <div class="title-container">
        <h1>
            <a href="index.php">Hungry Phil's</a>
        </h1>
    </div>
    
    <h1 class="page-title">Recipes</h1>

    <div class="search">
        <div class="search-bar">
            <form action="index.php" method="get" class="search-form">
                <input type="text" name="search" placeholder="What recipe are you looking for?" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            </form>
        </div>
    </div>

    <div class="sidebar">
        <h2>Filter</h2>

        <div class="filter-button">
        <form action="index.php" method="get">
            <input type="hidden" name="filter" value="Beef">
            <button type="submit">Beef</button>
        </form>
        <form action="index.php" method="get">
            <input type="hidden" name="filter" value="Chicken">
            <button type="submit">Chicken</button>
        </form>
        <form action="index.php" method="get">
            <input type="hidden" name="filter" value="Fish">
            <button type="submit">Fish</button>
        </form>
        
        <form action="index.php" method="get">
            <input type="hidden" name="filter" value="Turkey">
            <button type="submit">Turkey</button>
        </form>
        <form action="index.php" method="get">
            <input type="hidden" name="filter" value="Vegetarian">
            <button type="submit">Vegetarian</button>
        </form>

        <form action="index.php" method="get">
            <button type="submit" class="reset-button">Reset Filters</button>
        </form>
    </div>

    <div class="content">
        <div class="flex-container">
            <?php 
            if ($recipes->num_rows === 0): ?>
                <p>No recipes found.</p>
            <?php endif; ?>

            <?php while ($row = $recipes->fetch_assoc()): ?>

                <a class="card-container" href="recipe.php?id=<?= $row['id'] ?>">

                    <picture>
                        <source media="(min-width:980px)" 
                                srcset="images/<?= $row['img_folder'] ?>/<?= $row['hero_lg'] ?>"> <!-- FIXED -->

                        <img src="images/<?= $row['img_folder'] ?>/<?= $row['hero_sm'] ?>"> <!-- FIXED -->
                    </picture>

                    <h2><?= $row['heading'] ?></h2>
                    <h3><?= $row['subheading'] ?></h3>

                </a>

            <?php endwhile; ?>
            
        </div>
    </div>
</body>
</html>