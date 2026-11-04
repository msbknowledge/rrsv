<?php
  $dateOfBirth = "18-09-1984";
  $today = date("Y-m-d");
  $diff = date_diff(date_create($dateOfBirth), date_create($today));

  echo 'Your age is '.$diff->format('%Y-%m-%d');
?>

<span>
                        <strong>Date Of Birth*</strong><br />
                        <input type="date" id="DateOfBirth" name="DateOfBirth" value="" size="40" class="form-control" />
                    </span>

  <span>
                        <strong>Age*</strong><br />
                        <input type="text" id="Age" name="Age" value="" size="40" class="form-control" placeholder="Enter Age" disabled="disabled" />
                    </span>




<script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
<script>
    $(document).ready(function () 
{
  
 console.log($(document).width());           
     $('#datepicker').datepicker
    ({
        
        dateFormat: 'mm/dd/yy',
        changeMonth: true,
        changeYear: true,
        yearRange: "-100:+0",
     
        maxDate: new Date(),
        inline: true,

             onSelect: function() {
               var birthDay = document.getElementById("datepicker").value;
              
                var DOB = new Date(birthDay);
                
                var today = new Date();
           
          
                var age = today.getTime() - DOB.getTime();
                
                var elapsed = new Date(age);
                 
               
                var year = elapsed.getYear()-70;
                var month = elapsed.getMonth();
                var day = elapsed.getDay();
               
                var ageTotal = year + " Years," + month + " Months," + day + " Days";

                document.getElementById('agecal').innerText = ageTotal;

            }
     });  

});
</script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<div class="form-group">
  <label for="datepicker">Date of Birth: <span style="color:red">*</span></label>
  <input type="text" id="datepicker" name="date_of_birth" class="form-control" placeholder="Insert your Date of Birth...">
</div>
<div class="form-group" style="margin-top:10px;">
  <label for="agecal">Age as On(<?php echo date('d/m/Y'); ?>): <span id="agecal" style="background-color:#60ab59;padding: 3px 15px 3px 15px;color: white;font-weight: bold; border-radius: 5px;" >0 Years,0 Months,0 Days</span></label>
</div>
<?php
$bday = new DateTime('18.09.1984'); // Your date of birth
$today = new Datetime(date('m.d.y'));
$diff = $today->diff($bday);
printf(' Your age : %d years, %d month, %d days', $diff->y, $diff->m, $diff->d);
printf("\n");
?>