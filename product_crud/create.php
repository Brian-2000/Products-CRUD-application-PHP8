<?php  

$pdo = new PDO('mysql:host=localhost;port=3306;dbname=products_crud', 'root', '');

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


$errors = [];

$title = '';
$price = '';
$description = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $date = date('Y-m-d H:i:s');

    if (!$title) {
      $errors[] = 'Product title is required';  
    }

    if (!$price) {
      $errors[] = 'Product price is required';  
    }

   if (!is_dir('images')) {
      mkdir('images');
    }

    if (empty($errors)) {
        $image = $_FILES['image'] ?? null;
        $imagePath = '';
        if ($image && $image['tmp_name']) {
          $imagePath = 'images/'.randomString(8).'/'.$image['name'];
          mkdir(dirname($imagePath));
  
          move_uploaded_file($image['tmp_name'], $imagePath);
        
        }

        $statement = $pdo->prepare("INSERT INTO products (title, image, description, price, create_date)
                VALUES(:title, :image, :description, :price, :date)");

        $statement->bindValue(':title', $title);
        $statement->bindValue(':image', $imagePath);
        $statement->bindValue(':description', $description);
        $statement->bindValue(':price', $price);
        $statement->bindValue(':date', $date);
        $statement->execute();

        header('Location: index.php');

    }
}

function randomString($n)
{
  $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
  $str = '';

  for ($i = 0; $i<$n; $i++) {
    $index = rand(0, strlen($characters) - 1);
    $str .= $characters[$index];
  }

  return $str;
}

?>

<!doctype html>
<html lang="ar" dir="rtl">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"  rel="stylesheet" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
     <link rel="stylesheet" href="app.css">
    <title>Products CRUD Application</title>
  </head>
  <body>
    <h1>Create a New Products</h1>

    <?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
      <?php foreach ($errors as $error): ?>
        <div><?php echo $error?></div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label>Products Image</label>
                <br>
                    <input type="file" name="image">
             </div>

             <div class="mb-3">
                <label>Products Title</label>
                    <input type="text" name="title" class="form-control" value="<?php echo $title ?>">
             </div>

             <div class="mb-3">
                <label>Products Description</label>
                    <textarea name="description" class="form-control" value="<?php echo $description ?>" ></textarea>
             </div>

             <div class="mb-3">
                <label>Products Price</label>
                    <input type="number" name="price" step="0.01" class="form-control" value="<?php echo $price ?>">
             </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>

  </body>
</html>
