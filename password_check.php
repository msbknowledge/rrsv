<?php
session_start();

// Get and validate dId
$dId = isset($_GET['dId']) ? trim($_GET['dId']) : '';

if (empty($dId)) {
    echo "<script>alert('Invalid or missing dId!'); window.history.back();</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validate Password</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Bootstrap for styling -->
</head>

<body class="container mt-4">

    <h2>Enter Password</h2>

    <form id="passwordForm">
        <input type="hidden" id="dId" name="dId" value="<?php echo htmlspecialchars($dId); ?>">

        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" class="form-control" placeholder="Enter Password">
            <span id="errorMessage" class="text-danger"></span>
        </div>

        <button type="submit" class="btn btn-primary">Submit</button>
        <button type="button" class="btn btn-secondary" onclick="window.history.back();">Back</button>
    </form>

    <script>
        $(document).ready(function () {
            $("#passwordForm").submit(function (event) {
                event.preventDefault(); // Prevent default form submission

                const enteredPassword = $("#password").val().trim();
                const correctPassword = "@#$5A&*Cr!F"; // Static password
                const dId = $("#dId").val(); // Get dId value

                if (enteredPassword === correctPassword) {
                    // Submit only dId to the next page
                    $("<form>", {
                        action: "manage_inquery.php",
                        method: "GET"
                    }).append(
                        $("<input>", { type: "hidden", name: "dId", value: dId })
                    ).appendTo("body").submit();
                } else {
                    $("#errorMessage").text("Incorrect password! Try again.");
                }
            });
        });
    </script>

</body>

</html>