<style>
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
    .outer-border{
    width:800px; height:650px; padding:20px; text-align:center; border: 10px solid #673AB7;    margin-left: 21%;
}

.inner-dotted-border{
    width:750px; height:600px; padding:20px; text-align:center; border: 5px solid #673AB7;border-style: dotted;
}

.certification{
    font-size:50px; font-weight:bold;    color: #663ab7;
}

.certify{
    font-size:18px;
    margin-left: -28px;
}
.certify1{
    font-size:18px;
    margin-left: -29px;
}
.certify2{
    font-size:18px;
    margin-left: -25px;
}
.certify3{
    font-size:18px;
    margin-left: -21px;
}

.certify4{
    font-size:18px;
   margin-left: -112px;
}
.certify6{
 font-size:18px;
   margin-left: -75px;
}
.certify8{
    font-size:18px;
   margin-left: 94px;
}
.certify9{
    font-size:18px;
   margin-left: 0px;
}
.certify10{
    font-size:18px;
   margin-left: -179px;
}
.certify11{
    font-size:18px;
   margin-left: -468px;
}
.certify12{
        text-align: left;
}
.name{
    font-size:30px;    color: green;
}
.p1{
    
}
.fs-30{
    font-size:30px;
}

.fs-20{
    font-size:20px;
}
</style>
<style>
    @page {
      size: auto;  /* auto is the initial value */
      margin: 0mm; /* this affects the margin in the printer settings */
    }
    html {
      background-color: #FFFFFF;
      margin: 0px; /* this affects the margin on the HTML before sending to printer */
    }
    body {
      border: solid 1px #FFFFFF;
      margin: 10mm 15mm 10mm 15mm; /* margin you want for the content */
	}
	@media print {
            #printbtn {
                display :  none;
            }
		}
		@media print {
            #cancel {
                display :  none;
            }
        }
    
@media print {
    body{
        background-image: none;
    }
	@media print {
  #printPageButton {
    display: none;
  }
}
@media print {
  #backPageButton {
    display: none;
  }
}
}
</style>
<button type="button" id="backPageButton" class="button" ><a style="color: #fff" href="manage_event.php">Back</a></button>
	<button type="button" onclick = "generatePDF()" class="button" id="printPageButton">Print</button>    
<div class="outer-border">
<div class="inner-dotted-border">
       <span class="certification">Transfer Certificate</span>
       <br><br>

       <span class="certify">This is to certify that RASULPUR RAMKRISHNA SARADA VIDYAPITH</span>
       <br>
       <span class="certify1">Son/Daughter of_______________________________________________________________</span><br/>
       <span class="certify2">an inhabitant of________________________________________________________________</span> <br/>
       <span class="certify3">in the District of___________________left the school on______________________________</span> <br/><br>
       <span class="certify4">The date of his/her birth as recorded to the Admiddion Register,was________________________________________________________________ </span><br>
              <span class="certify6">His/Her age at the date,according to the Admission Register  </span> <br/>
              <span class="certify7"> Year____________________________months_________________________dats__________________. </span> <br/>
       <span class="certify8">He/She was reading Class ____________________in__________________Stream </span><br>
            <span class="certify9"> and Had/Had not passed the Annual Examnination for promotion to class_________________</span><br>
      
      <span class="certify10">All sums due by him/her have been paid viz:-<br>
      Character_____________________________________________________________________</span><br><br>
      
<span class="certify11">REASONS FOE LEAVING:-</span><br>
<div class="certify12">
<ol>
  <li>Unavoidable change o residence</li>
  <li>heaith(this reason shall only be given if it is,in the option o the Headmaster/Headmistress,well founded)</li>
  <li>Abolition or closure of school</li>
   <li>Completion o the school course</li>
  <li>Option of the guardian</li>
  <li>Minor or privte reasons (including all reasons other than the foregoing).If failure to secure permotion has been put orward as a reason or is considered by the Headmaster/Headmistress to be the real reason.the fact should be noted here</li>  
</ol>
</div>
</div>
</div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
	<script src="js/jquery-3.3.1.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script>
		function generatePDF() {
		window.print();
		}
		    function cancel() {
		        window.history.go(-1);
		    }
    </script>