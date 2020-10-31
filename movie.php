<?php
//If not logged in redirect to login page
    session_start();
    if (!isset($_SESSION["UID"]))
  	{
  			header("Location: login.php");
  	}
    //test
    // Initialize PDO Object
    $pdo = new PDO("sqlite:MMDataBase.db");


    $userId = intval($_SESSION["UID"]);//Get userID which is unique to user
    $sqlWatchedMovies = "SELECT MovieId FROM Watchlist WHERE UserId=$userId"; //Get all movies in watch list that match current user ID
    $sqlFilteredMovies = "SELECT * FROM Movies WHERE MID NOT IN ($sqlWatchedMovies)"; //Select all movies from movie table that are not already in current users watchlist
    $stmtFill = $pdo->query($sqlFilteredMovies);
	  $moviesArr = $stmtFill->fetchall();
	?>
	<?php

    // This chuck of PHP code adds a movie to the users watchlist if the MovieId is set (if it is clicked)
	// since "$userId" is defined in the first PHP code chunk, can we omit this variable declaration (below)?
	function AddCategories($userId, $catArr) {
		if (count($catArr) > 0) {
      $pdo = new PDO("sqlite:MMDataBase.db");
			$sqlCatExists = "SELECT CategoryName FROM Scores WHERE UserId=:uid AND CategoryName=:category"; // Get all records containing scores for categories specific to current user
			$sqlInsertCat = "INSERT INTO Scores (UserId, CategoryName, Score) VALUES (:uid,:category,100)"; // Query to add a category if a record does not already exist
			foreach ($catArr as $cat) {
				$category = trim($cat); // remove spaces
				$stmtCatExists = $pdo->prepare($sqlCatExists);
				$stmtCatExists->bindParam(':uid', $userId);
				$stmtCatExists->bindParam(':category', $category);
				$stmtCatExists->execute();
				$catExists = $stmtCatExists->fetchColumn();
				if ($catExists == false) { //If recode does not already exist insert it into table with initial score of 100
					$stmtInsertCat = $pdo->prepare($sqlInsertCat);
					$stmtInsertCat->bindParam(':uid', $userId);
					$stmtInsertCat->bindParam(':category', $category);
					$stmtInsertCat->execute();
				}
				// else do nothing, category exists
			}
		} else {
			echo "Error, category array is empty";
		}
	}

  //The following function adjusts the score of a category based on whether a user passes on movie of that category or adds to watchlist
	function AdjustScore($amt, $userId, $catArr) {
		if (count($catArr) > 0) {
      $pdo = new PDO("sqlite:MMDataBase.db");
			$sqlCatExists = "SELECT Score FROM Scores WHERE UserId=:uid AND CategoryName=:name";
			$sqlUpdateScore = "UPDATE Scores SET Score=:score WHERE UserId=:uid AND CategoryName=:name";
			foreach ($catArr as $cat) {
				// assert the category exists
        $category = trim($cat);
				$stmtCatExists = $pdo->prepare($sqlCatExists);
        $stmtCatExists->bindParam(':uid', $userId);
        $stmtCatExists->bindParam(':name', $category);
				$stmtCatExists->execute();
				$catExists = $stmtCatExists->fetchColumn();
				if ($catExists != false) {
					// category exists, update the score
					$newScore = $catExists + $amt;
					$stmtUpdateScore = $pdo->prepare($sqlUpdateScore);
          $stmtUpdateScore->bindParam(':score', $newScore);
          $stmtUpdateScore->bindParam(':uid', $userId);
          $stmtUpdateScore->bindParam(':name', $category);
					$stmtUpdateScore->execute();
				}
				// else do nothing, category exists
			}
		} else {
			echo "Error, category array is empty";
		}
	}

	$userId = $_SESSION["UID"];
	if(isset($_POST['MovieId'])){
		// implement scoring functionality
		$pdo = new PDO("sqlite:MMDataBase.db");
		$categories = $_POST['Category'];
		$catArr = explode(',', $categories);
    if (count($catArr) < 1) {
      header("Location: login.php");
    }
		AddCategories($userId, $catArr);
		AdjustScore(5,$userId, $catArr);

		// add movie to user's watchlist
		$movieId = $_POST['MovieId'];
		$sql = "INSERT INTO Watchlist VALUES(?, ?, 0)";
		$insertStmt = $pdo->prepare($sql);
    $insertStmt->execute([$userId, $movieId]);

    $stmtFill = $pdo->query($sqlFilteredMovies);
    $all = $stmtFill->fetchall();
	}

  if(isset($_POST['NotCategory'])) {
    // decrement score for passed movie
    $pdo = new PDO("sqlite:MMDataBase.db");
		$categories = $_POST['NotCategory'];
		$catArr = explode(',', $categories);
    if (count($catArr) < 1) {
      header("Location: login.php");
    }
		AddCategories($userId, $catArr);
		AdjustScore(-5,$userId, $catArr);
  }
?>
<html>
	<head>
		<SCRIPT SRC="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></SCRIPT>
		<title>Swipe</title>
		<link rel="stylesheet" href="css/stylesheet.css">
    <link rel="icon" href="assets/favicon/favicon.ico">
	</head>
	<body>
		<?php include('components/header.php'); ?>
		<div class= container>
			<div class=overlay>
        <br><br>
				<div class="movieinfo">
					<h1 id="title">Movie Title</h1>
				</div>
				<div class="swipe">

					<button class="button" id="pass" onclick="pass()">Pass</button>
				</div>
				<div class="movieposter">
					<img id="poster" src="assets/popcorn.jpg" >
				</div>
				<div class="swipe">
					<button class="button" id="watch" onclick="watchmovie()">Watch</button>
				</div>
				<div class="movieinfo">
					<h2 id="dir">Director</h2>
					<h2 id="year">Release Year</h2>
					<h2 id="act">Actors</h2>
				</div>
			</div>
		</div>
		<?php include('components/footer.php'); ?>
	</body>
</html>

<script>
	var movies = <?php echo json_encode($moviesArr)?>;
	var moviecount = 0;
	populatemovie();

	function populatemovie(){
		document.getElementById("poster").src=movies[moviecount]["Poster"];
		document.getElementById("title").innerHTML = movies[moviecount]["Title"];
		document.getElementById("dir").innerHTML = "Director: " + movies[moviecount]["Director"];
		document.getElementById("year").innerHTML ="Release Year: " + movies[moviecount]["ReleaseYear"];
		document.getElementById("act").innerHTML = "Actors: " +movies[moviecount]["Actors"];
	}

  function pass() {
    var movietitle = movies[moviecount]["Title"];
		var movieCategories = movies[moviecount]["Category"];
		// console.log(movieCategories + " " + movieIdentity);
    const data = {
      NotCategory: movieCategories
    };
		$.ajax({
		type: 'POST',
		url: 'movie.php',
		data,
		success: function(data)
		{
			alert(movietitle + " disliked");
			nextmovie();
		}
		});

  }
	function nextmovie(){
		moviecount = moviecount + 1;
		populatemovie();
	}

	function watchmovie(){
		var movietitle = movies[moviecount]["Title"];
		var movieIdentity = movies[moviecount]["MID"];
		var movieCategories = movies[moviecount]["Category"];
		// console.log(movieCategories + " " + movieIdentity);
    const data = {
      MovieId: movieIdentity,
      Category: movieCategories
    };
		$.ajax({
		type: 'POST',
		url: 'movie.php',
		data,
		success: function(data)
		{
			alert(movietitle + " added to watchlist");
			nextmovie();
		}
		});

	}

</script>
