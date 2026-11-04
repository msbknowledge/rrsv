 <?php
                                 
                                 //result

if (@$_GET['q'] == 'result' && @$_GET['eid']) {
    $eid = @$_GET['eid'];
    $username = @$_GET['username'];
    $q = mysqli_query($con, "SELECT * FROM quiz WHERE eid='$eid' ") or die('Error157');
    while ($row = mysqli_fetch_array($q)) {
        $total = $row['total'];
    }
    $q = mysqli_query($con, "SELECT * FROM history WHERE eid='$eid' AND username='$username' ") or die('Error157');
    
    while ($row = mysqli_fetch_array($q)) {
        $s      = $row['score'];
        $w      = $row['wrong'];
        $r      = $row['correct'];
        $status = $row['status'];
    }
    if ($status == "finished") {
        echo '<div class="panel">

<center><p class="title" style="color:#669933"> ' . $username . '</p><center><br />
<table class="table table-striped title1" style="font-size:20px;font-weight:1000;">';
        echo '<tr style="color:darkblue"><td style="vertical-align:middle">Total Questions</td><td style="vertical-align:middle">' . $total . '</td></tr>
      <tr style="color:darkgreen"><td style="vertical-align:middle">Correct Answer&nbsp;<span class="glyphicon glyphicon-ok-arrow" aria-hidden="true"></span></td><td style="vertical-align:middle">' . $r . '</td></tr> 
    <tr style="color:red"><td style="vertical-align:middle">Wrong Answer&nbsp;<span class="glyphicon glyphicon-remove-arrow" aria-hidden="true"></span></td><td style="vertical-align:middle">' . $w . '</td></tr>
    <tr style="color:orange"><td style="vertical-align:middle">Unattempted&nbsp;<span class="glyphicon glyphicon-ban-arrow" aria-hidden="true"></span></td><td style="vertical-align:middle">' . ($total - $r - $w) . '</td></tr>
    <tr style="color:darkblue"><td style="vertical-align:middle">Score&nbsp;<span class="glyphicon glyphicon-star" aria-hidden="true"></span></td><td style="vertical-align:middle">' . $s . '</td></tr>';
        $q = mysqli_query($con, "SELECT * FROM rank WHERE  username='$username' ") or die('Error157');
        while ($row = mysqli_fetch_array($q)) {
            $s = $row['score'];
            echo '<tr style="color:#990000"><td style="vertical-align:middle">Overall Score&nbsp;<span class="glyphicon glyphicon-stats" aria-hidden="true"></span></td><td style="vertical-align:middle">' . $s . '</td></tr>';
        }
        echo '<tr></tr></table></div><div class="panel"><br /><h3 align="center" style="font-family:calibri">:: Detailed Analysis ::</h3><br /><ol style="font-size:20px;font-weight:bold;font-family:calibri;margin-top:20px">';
        $q = mysqli_query($con, "SELECT * FROM questions WHERE eid='$_GET[eid]'") or die('Error197');
        while ($row = mysqli_fetch_array($q)) {
            $question = $row['qns'];
            $qid      = $row['qid'];
            $q2 = mysqli_query($con, "SELECT * FROM user_answer WHERE eid='$_GET[eid]' AND qid='$qid' AND username='$username'") or die('Error197');
            if (mysqli_num_rows($q2) > 0) {
                $row1         = mysqli_fetch_array($q2);
                $ansid        = $row1['ans'];
                $correctansid = $row1['correctans'];
                $q3 = mysqli_query($con, "SELECT * FROM options WHERE optionid='$ansid'") or die('Error197');
                $q4 = mysqli_query($con, "SELECT * FROM options WHERE optionid='$correctansid'") or die('Error197');
                $row2       = mysqli_fetch_array($q3);
                $row3       = mysqli_fetch_array($q4);
                $ans        = $row2['option'];
                $correctans = $row3['option'];
            } else {
                $q3 = mysqli_query($con, "SELECT * FROM answer WHERE qid='$qid'") or die('Error197');
                $row1         = mysqli_fetch_array($q3);
                $correctansid = $row1['ansid'];
                $q4 = mysqli_query($con, "SELECT * FROM options WHERE optionid='$correctansid'") or die('Error197');
                $row2       = mysqli_fetch_array($q4);
                $correctans = $row2['option'];
                $ans        = "Unanswered";
            }
            if ($correctans == $ans && $ans != "Unanswered") {
                echo '<li><div style="font-size:16px;font-weight:bold;font-family:calibri;margin-top:20px;background-color:lightgreen;padding:10px;word-wrap:break-word;border:2px solid darkgreen;border-radius:10px;">' . $question . ' <span class="glyphicon glyphicon-ok" style="color:darkgreen"></span></div><br />';
                echo '<font style="font-size:14px;color:darkgreen"><b>Your Answer: </b></font><font style="font-size:14px;">' . $ans . '</font><br />';
                echo '<font style="font-size:14px;color:darkgreen"><b>Correct Answer: </b></font><font style="font-size:14px;">' . $correctans . '</font><br />';
            } 
            else if ($ans == "Unanswered") {
                echo '<li><div style="font-size:16px;font-weight:bold;font-family:calibri;margin-top:20px;background-color:#f7f576;padding:10px;word-wrap:break-word;border:2px solid #b75a0e;border-radius:10px;">' . $question . ' </div><br />';
                echo '<font style="font-size:14px;color:darkgreen"><b>Correct Answer: </b></font><font style="font-size:14px;">' . $correctans . '</font><br />';
            } 
            else {
                echo '<li><div style="font-size:16px;font-weight:bold;font-family:calibri;margin-top:20px;background-color:#f99595;padding:10px;word-wrap:break-word;border:2px solid darkred;border-radius:10px;">' . $question . ' <span class="glyphicon glyphicon-remove" style="color:red"></span></div><br />';
                echo '<font style="font-size:14px;color:darkgreen"><b>Your Answer: </b></font><font style="font-size:14px;">' . $ans . '</font><br />';
                echo '<font style="font-size:14px;color:red"><b>Correct Answer: </b></font><font style="font-size:14px;">' . $correctans . '</font><br />';
                
            }
            echo "<br /></li>";
        }
        echo '</ol>';
        echo "</div>";
    
        
    } else {
        die("Thats a 404 Error bro. You are trying to access a wrong page");
    }
   
}

	?>			
                 <?php
                         if (@$_GET['q'] == 2) {
    if(isset($_GET['show'])){
        $show = $_GET['show'];
        $showfrom = (($show-1)*10) + 1;
        $showtill = $showfrom + 9;
    }
    else{
        $show = 1;
        $showfrom = 1;
        $showtill = 10;
    }
    $q = mysqli_query($con, "SELECT * FROM rank") or die('Error223');
			
			echo '<table id="example1" class="table table-bordered table-striped" >
                <thead>
                <tr>
                  <th>S.NO</th>
				  
                  <th>Name</th>
				  <th>Branch</th>
                                  <th>Gender</th>
                                  <th>username</th>
                                  <th>Rollno</th>
                                  <th>Score</th>
                                  
                                  <th>Edit</th>
                                  <th>Delete</th>
                 
                </tr>
                </thead>
                <tbody>'; 
                $c = $showfrom-1;
    $total = mysqli_num_rows($q);
    if($total >= $showfrom){
        $q = mysqli_query($con, "SELECT * FROM rank ORDER BY score DESC, time ASC LIMIT ".($showfrom-1).",10") or die('Error223');
        while ($row = mysqli_fetch_array($q)) {
            
            $e = $row['username'];
            $s = $row['score'];
            $q12 = mysqli_query($con, "SELECT * FROM user WHERE username='$e' ") or die('Error231');
            while ($row = mysqli_fetch_array($q12)) {
                $name     = $row['name'];
                $branch   = $row['branch'];
                $username = $row['username'];
                $rollno     = $row['rollno'];
                $gender   = $row['gender'];
               // $pass     = $row['password'];
            }
            $result = mysqli_query($con, "SELECT * FROM history WHERE username='$e'") or die('Error98');
            while ($row = mysqli_fetch_array($result)) {
                  $eid     = $row['eid'];
               
            }

            $c++;
           
	 echo '      <tr>
		 <td>' . $c . '</td>
                  <td>' . $name . '</td>
		 <td>' . $branch . '</td>
                  <td>' .$gender . '</td>
                   <td>' . $username . '</td>
                    <td>' . $rollno . '</td>
                    <td>' .  $s . '</td>
                  
              <td style="vertical-align:middle">
  <a href="dash.php?q=result&eid=' . $eid . '&username=' . $username . '" class="btn" style="margin:0px;background:darkred;color:white">&nbsp;<span class="title1"><b>View Result</b></span></a> </td>
  <td style="vertical-align:middle"> <a title="Delete User" href="update.php?q=rmrah&username=' . $username . '" class="btn" style="margin:0px;background:skyblue;color:white"><b><span class="fa fa-trash" aria-hidden="true"></span></b></a>
  </td>
                </tr>';
                 }
    }
    echo '					</tbody>
				</table>';
	
        }?>
				 
               
			
             
             
              
              
              
              <div class="box-footer">
                
				 <!-- /.footer-->
				
                </div>
              <!-- /.box-footer -->
             
          </div>
          
          