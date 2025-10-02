<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400..700&family=Kadwa:wght@400;700&display=swap" rel="stylesheet">
  <link rel="website icon" type="png" href="image/Logo.png" />
  <title>Inventory Management</title>
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: "Kadwa", serif;
      font-weight: 400;
      font-style: normal;
    }

    body {
      background-color: #f9e4d8;
      font-family: Arial, sans-serif;
    }

    .container {
      text-align: center;
      padding: 20px;
    }

    img {
      width: 20%;
      max-width: 500px;
      height: auto;
      margin-top: 20px;
      margin-bottom: -5px;
    }

    .header h1 {
      font-size: 50px;
      color: black;
    }

    .header h2 {
      font-size: 20px;
      color: black;
      margin-top: -10px;
    }

    h3 {
      font-size: 65px;
      font-weight: bold;
      color: black;
      margin: 0px 0;
    }

    .get-started {
      display: inline-block;
      padding: 5px 35px;
      background-color: transparent;
      border: 2px solid #ff6900;
      color: #ff6900;
      font-size: 18px;
      text-decoration: none;
      border-radius: 25px;
      transition: all 0.3s ease;
    }

    .get-started:hover {
      background-color: #ff6900;
      color: white;
    }


    .brushstroke {
      position: absolute;
      width: 167%;
      opacity: 0.7;
      z-index: -1;
    }

    .brushstroke.top-left {
      top: -50px;
      left: 10px;
    }

    .brushstroke.bottom-right {
      bottom: -6px;
      right: 10px;
    }

    /* Desktop Large (>1200px) */
    @media (min-width: 1201px) {
      img {
        width: 20%;
      }
      .header h1 {
        font-size: 50px;
      }
      .header h2 {
        font-size: 20px;
      }
      h3 {
        font-size: 65px;
      }
    }

    /* Laptop (1024px - 1200px) */
    @media (max-width: 1200px) {
      img {
        width: 25%;
      }
      .header h1 {
        font-size: 45px;
      }
      .header h2 {
        font-size: 18px;
      }
      h3 {
        font-size: 55px;
      }
    }

    /* Tablet (768px - 1024px) */
    @media (max-width: 1024px) {
      img {
        width: 30%;
      }
      .header h1 {
        font-size: 40px;
      }
      .header h2 {
        font-size: 17px;
      }
      h3 {
        font-size: 45px;
      }
      .get-started {
        padding: 5px 30px;
        font-size: 17px;
      }
    }

    /* Mobile Large (480px - 768px) */
    @media (max-width: 768px) {
      img {
        width: 35%;
      }
      .header h1 {
        font-size: 35px;
      }
      .header h2 {
        font-size: 16px;
      }
      h3 {
        font-size: 35px;
      }
      .get-started {
        padding: 5px 25px;
        font-size: 16px;
      }
    }

    /* Mobile Small (<480px) */
    @media (max-width: 480px) {
      img {
        width: 40%;
      }
      .header h1 {
        font-size: 30px;
      }
      .header h2 {
        font-size: 15px;
      }
      h3 {
        font-size: 25px;
      }
      .get-started {
        padding: 5px 20px;
        font-size: 15px;
      }
      .brushstroke {
        width: 150%;
      }
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="brushstroke top-left"><img src="image/orange color.png" alt=""></div>
    <div class="brushstroke bottom-right"><img src="image/orange color 2.png" alt=""></div>
    <div class="header">
      <img src="image/Logo2.png" alt="Logo">
    </div>
    <h3>INVENTORY MANAGEMENT</h3>
    <a href="Login.php" class="get-started">Get Started</a>
  </div>
</body>

</html>