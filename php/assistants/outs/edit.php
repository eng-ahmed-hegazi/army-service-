<?php
// start the session
 session_start();
// disable notification & warning
 error_reporting(E_ALL & ~E_NOTICE && ~E_WARNING); 
 // if the user not logged in
if(!$_SESSION['loggedInUser']){

  //send them to the login page
  header("Location: ../../../index.php");
}
########################################
# get the ID send by the GET collection 
########################################
$assistantID = $_GET['id'];

// connect to database
include ("../../../includes/controls/db/connection.php");
// include functions.php
include ("../../../includes/controls/controls/functions.php");

//query the data using the reseved clientID
$query = "SELECT * FROM  assistantout WHERE id='$assistantID'";
$result = mysqli_query($conn, $query);

// if the result is re4turned
if(mysqli_num_rows($result) > 0){
    while ($row = mysqli_fetch_assoc($result)) {
        $assistant_id    = $row['assistant_id'];
        $destination    = $row['destination'];
        $type    = $row['type'];
        $start_date    = $row['start_date'];
        $end_date    = $row['end_date'];
    }
    
}else{
    // no result returned
    $alertMessage = "<div class='alert alert-success'>لا توجد نتيجة<a href='' data-dismiss='alert' class='close pull-left'>&times;</a></div>";
}

// if the update buttom is pressed
if( isset( $_POST['update'] ) ){

    $assistant_id = validateFormData($_POST['assistant_id']); 
    $assistant = validateFormData($_POST['assistant_id']); 
    $destination   = validateFormData($_POST['destination']);
    $type   = validateFormData($_POST['type']);
    $start_date   = validateFormData($_POST['start_date']);
    $end_date   = validateFormData($_POST['end_date']);
    
	if($assistant==$assistant_id ){
       // new database query & result
       $query = "UPDATE assistantout SET assistant_id = '$assistant_id',"
       ."destination = '$destination',type='$type',start_date='$start_date',end_date='$end_date' WHERE id = $assistantID";
   }else{
		$sql="UPDATE assistant SET availability=1 WHERE id='$assistant'";
		$backstate = mysqli_query($conn,$sql);
		$sql2="UPDATE assistant SET availability=0 WHERE id='$assistant_id'";
		$changestate = mysqli_query($conn,$sql2);
		 // new database query & result
       $query = "UPDATE assistantout SET assistant_id = '$assistant_id',"
       ."destination = '$destination',type='$type',start_date='$start_date',end_date='$end_date' WHERE id = $assistantID";
   }
       // new database query & result
       
       
    if(mysqli_query($conn,$query)){
        header("Location: assistants.php");
    }else{
        echo mysqli_error($conn);
    }
    //header("Location: soldiers.php");
}
	
// if the update buttom is pressed
if( isset($_POST['delete'] ) ){
$alertMessage = "<div class='alert alert-danger'>
        <h5>هل أنت متأكد من حذف العنصر ...</h5>
        <form action='".htmlspecialchars($_SERVER["PHP_SELF"])."?id=$assistantID' method='post'>
            <input type='submit' class='btn btn-danger' name='confirm-delete' value='مسح '>
            <a type='button' class='btn btn-primary' data-dismiss='alert' name='no-thanks'>لا شكرا</a>
        </form>
    </div>";

    
}
if(isset( $_POST['confirm-delete'] )){
			$query1 = "SELECT * FROM assistantout WHERE id='$assistantID' LIMIT 1";
            $result1 = mysqli_query($conn,$query1);
			$soldierId = '';
			if( mysqli_num_rows($result1) > 0){
				$i=0;
				while ($row = mysqli_fetch_assoc($result1)) {
					$soldierId = $row['assistant_id'];
				}
			}
           // new database query & result
            $query = "DELETE FROM assistantout WHERE id='$assistantID'";
            $result = mysqli_query($conn,$query);
			$query1 = "UPDATE assistant SET availability=1 WHERE id='$soldierId'";
            $result1 = mysqli_query($conn,$query1);
            if($result){
                header("Location: assistants.php");
            }else{
                echo "<div class='alert alert-danger'>خطــــــأ </div>";
            }
          //header("Location: clients.php");
      }
if(isset( $_POST['no-thanks'] )){
	unset($_POST['delete']);
}

 // close the connection

 // include the header
 include ("../../../includes/header/header/header.php");
 
    ?>
<div class="container">

        <h2 class="page-header">
		تعديل نوبتجية ضابط
			<?php
				echo $usedBefore;
				echo $requiredError;
				echo $invalidEmail;
				
			?>
			<?php echo $alertMessage; ?>
			<label for="date" class="col-sm-1 control-label">
			<?php
			$query = "SELECT * FROM  assistant WHERE id=assistant_id";
					$result = mysqli_query($conn , $query);
					if( mysqli_num_rows($result) > 0){
					$i=0;
					while ($row = mysqli_fetch_assoc($result)) {
						echo $row['degree'];
					}
				}
			?>
			</label>
		</h2>
        <form action="<?php htmlspecialchars($_SERVER["PHP_SELF"])?>" method="post" class="form-horizontal">
			  <div class="form-group">
				<div class="col-sm-2"></div>
			    <div class="col-sm-8">
					<select class="form-control" name="assistant_id" id="assistant_id">
						<option value="">الإسم</option>
						<?php
						$query = "SELECT * FROM  assistant";
						$result = mysqli_query($conn , $query);
						if( mysqli_num_rows($result) > 0){
						$i=0;
						while ($row = mysqli_fetch_assoc($result)) {
						if($row['crew']!=0){
								if($assistant_id==$row['id'] && $row['availability']==0){
									echo "<option value=".$row['id']." selected>".$row['name']."</option>";
								}else if($row['availability']==1){
									echo "<option value=".$row['id'].">".$row['name']."</option>";
								}
							}
						}
						} else {
							// if no entires
							echo "<div class='alert alert-warning'>لا يوجد بيانات<a class='close' data-dismiss='alert'>&times;</a></div>";
						  }				
						?>
					</select>  
				</div>
				<label for="assistant_id" class="col-sm-1 control-label">الأسم</label>
				</div>
			  <div class="form-group">
			    <div class="col-sm-2"></div>
			    <div class="col-sm-8"> 
			    <select class="form-control" name="type" id="type">
			      	<option value="">أختار النوع</option>
			      	<option value="1" <?php if ($type==1) echo "selected" ?>>مأمورية</option>
			      	<option value="2" <?php if ($type==2) echo "selected" ?>>فرقة</option>
			      	<option value="3" <?php if ($type==3) echo "selected" ?>>أختبارات</option>
			      	<option value="4" <?php if ($type==4) echo "selected" ?>>مستشفى</option>
			      </select>  
					</div>
					<label for="type" class="col-sm-1 control-label">النوع</label>
			  </div>
			  <div class="form-group">
				<div class="col-sm-2"></div>
					<div class="col-sm-8">
					  <input type="text" class="form-control1" id="destination" name="destination" value="<?php echo $destination ?>">
					</div>
					<label for="destination" class="col-sm-1 control-label" >الجهة</label>
			  </div>
			  <div class="form-group">
				<div class="col-sm-2"></div>
					<div class="col-sm-8">
					  <input type="date" class="form-control1" id="start_date" name="start_date" value="<?php echo $start_date ?>">
					</div>
					<label for="start_date" class="col-sm-1 control-label" >تاريخ القيام</label>
			  </div>
			  <div class="form-group">
				<div class="col-sm-2"></div>
					<div class="col-sm-8">
					  <input type="date" class="form-control1" id="end_date" name="end_date" value="<?php echo $end_date ?>">
					</div>
					<label for="end_date" class="col-sm-1 control-label" >تاريخ العودة</label>
			  </div>
			  
				
			   <div class="form-group">        
				  <div class="col-sm-10">
                    <a href="assistants.php" type="button" class="btn btn-default">إلغاء</a>
                    <button type="submit" class="btn btn-success " name="update">تعديل</button>
					<button type="submit" class="btn btn-danger" name="delete" >حذف</button>
                </div>
				</div>
            <div class="col-md-push-1 col-md-9">
			</div>
            </div>
        </form>
     </div>
		  <?php
				include "../../../includes/footer/footer/footer.php";
			?>
  </body>
</html>