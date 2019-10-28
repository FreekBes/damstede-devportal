<?PHP
    session_start();
    if (!isset($_SESSION["zermelo_access_token"]) || empty($_SESSION["zermelo_access_token"])) {
        header("Location: link.php", 301);
        exit();
    }
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <title>Damstede Device Portaal</title>
    <link rel="stylesheet" href="opmaak.css" />
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
	<link rel="icon" type="image/ico" href="favicon.ico" />
</head>
<body>
    <header>
        <h1 id="pagetitle">Damstede Device Portaal</h1>
    </header>
    <div id="content">
        <p>Test</p>
    </div>
</body>
</html>