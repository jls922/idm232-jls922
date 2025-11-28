<?php
    require_once 'db.php';

    $recipeId = $_GET['id'] ?? '';

    if (empty($recipeId)) {
        echo "Invalid Recipe ID.";
        exit;
    }

    $stmt = $connection->prepare("SELECT * FROM recipes WHERE id = ?");
    $stmt->bind_param("i", $recipeId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "Recipe not found.";
        exit;
    }

    $recipe = $result->fetch_assoc();
    $stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/recipe.css">
    <title>Recipe</title>
    <!--The recipe class-->
</head>
<body>
    <!--header-->
    <div class="title-container">
        <h1>
            <a href="index.php">Hungry Phil's</a>
        </h1>
    </div>
    

    <h1 class="page-title"><?php echo $recipe['heading']; ?></h1>
    <h2 class="page-title"><?php echo $recipe['subheading']; ?></h2>

    <div class="content">
        <picture>
            <source media="(min-width:980px)" srcset="images/<?= $recipe['img_folder'] ?>/<?= $recipe['hero_lg'] ?>">
            <img src="images/<?= $recipe['img_folder'] ?>/<?= $recipe['hero_sm'] ?>">
        </picture>
    
        <p class="center-text"><?php echo $recipe['description']; ?></p>

        <!--Split screen section for ingredients-->
        <div class="two-column">
            <div class="column">
                <h2>Ingredients</h2>
                <?php
                    $ingredients = explode("*", $recipe['ingredients']);
                    foreach ($ingredients as $ingredient) {
                        $cleanedIngredient = str_replace(',', '', $ingredient);
                        echo '<li> ' . htmlspecialchars($cleanedIngredient) . '</li>';
                    }
                ?>
            </div>
            <img class="column" src="images/<?= $recipe['img_folder'] ?>/ingredients.png" alt="image of ingredients">
        </div>

        <h2>Directions</h2>

        <?php
            $steps = explode("*", $recipe['steps']);
            $counter = 0;
            foreach ($steps as $step) {
                $counter++;
                $cleanedStep = str_replace(',', '', $step);
                echo '<h3>Step ' . $counter . ':</h3>' . '<p> ' . htmlspecialchars($cleanedStep) . '</p>';
            }
        ?>
    </div>

</body>
</html>