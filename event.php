<?php
include('include/dbcon.php');
$id = 0;
if (isset($_GET['id'])) {
    $id = $myDB->escape_string(trim(addslashes($_GET['id'])));
    $sql = "SELECT * FROM rrsv_event WHERE id='$id'";
    $res = mysqli_query($myDB, $sql);
    $rows = mysqli_fetch_array($res);
}
?>
<html>

<head>
    <style>
        .event-name {
            font-size: 30px;
            color: #28a745;
            margin-top: 20px;
        }

        .event-desc {
            font-size: 20px;
            margin-top: 15px;
        }

        .button {
            background-color: #4CAF50;
            border: none;
            color: white;
            padding: 15px 32px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
        }

        .outer-border {
            width: 800px;
            height: 650px;
            padding: 20px;
            text-align: center;
            border: 10px solid #673AB7;
            margin-left: 21%;
        }

        .inner-dotted-border {
            width: 750px;
            height: 600px;
            padding: 20px;
            text-align: center;
            border: 5px solid #673AB7;
            border-style: dotted;
        }

        .certification {
            font-size: 18px;
            font-weight: bold;
            color: #663ab7;
        }

        .certify {
            font-size: 25px;
        }

        .name {
            font-size: 30px;
            color: green;
        }

        .fs-30 {
            font-size: 30px;
        }

        .fs-20 {
            font-size: 20px;
        }

        .fs-40 {
            font-size: 25px;
        }

        .img {
            width: 13%;
            float: left;
        }

        .preview-image {
            width: 300px;
            height: auto;
            margin: 10px 0;
            display: none;
            /* Hidden initially */
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            // Check if the doc_path is an image
            var docPath = '<?= $rows["doc_path"]; ?>';
            var imgExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];

            if (docPath) {
                var extension = docPath.split('.').pop().toLowerCase();
                if (imgExtensions.includes(extension)) {
                    $('#imagePreview').attr('src', docPath).show(); // Show the image
                }
            }
        });
    </script>
</head>

<body>
    <button type="button" id="backPageButton" class="button"><a style="color: #fff"
            href="manage_event.php">Back</a></button>
    <button type="button" onclick="generatePDF()" class="button" id="printPageButton">Print</button>

    <div class="outer-border">
        <div class="inner-dotted-border">
            <td><img src="libray/images/logo.jpeg" class="img" alt="image" /></td>
            <span class="certification"> RASULPUR RAMKRISHNA SARADA VIDYAPITH </b> <br>
                Reg. No. : SO196094, U-DISE Code : 19251610302, ESTD : 2012;<br>
                Baidyadanga, Rasulpur, Memari, Bardhaman, Pin -713151
            </span>
            <br><br>

            <!-- Image Preview Section -->
            <img id="imagePreview" class="preview-image" height="200" width="200" alt="Event Document Preview"><br>

            <span class="event-name fs-30"><?= $rows['event_name']; ?></span> <br /><br />
            <span class="event-desc fs-40"><?= $rows['event_desc']; ?></span> <br /><br />
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        function generatePDF() {
            window.print();
        }
        function cancel() {
            window.history.go(-1);
        }
    </script>
</body>

</html>