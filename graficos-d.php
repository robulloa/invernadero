<?php
$dataPoints = array();
$dataPoints2 = array();
$fecha = '2018-11-30';
//Best practice is to create a separate file for handling connection to database
try{
     // Creating a new connection.
    // Replace your-hostname, your-db, your-username, your-password according to your database
    $link = new \PDO(   'mysql:host=localhost;dbname=test;charset=utf8mb4', //'mysql:host=localhost;dbname=canvasjs_db;charset=utf8mb4',
                        'root', //'root',
                        '', //'',
                        array(
                            \PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                            \PDO::ATTR_PERSISTENT => false
                        )
                    );
	$fecha = '2018-11-30';
    $handle = $link->prepare("SELECT hour(inv_fec) AS ho, avg(inv_temp) as tt from inv where date(inv_fec)='$fecha' group by ho asc"); 
    $handle->execute(); 
    //SELECT concat_ws(':',HOUR(inv_hora), minute(inv_hora)) AS ho, inv_temp from inv where 1
    $result = $handle->fetchAll(\PDO::FETCH_OBJ);
		
    foreach($result as $row){
        array_push($dataPoints, array("x"=> $row->ho, "y"=> $row->tt));
    }

    $fecha2 = '2018-11-29';
    $handle2 = $link->prepare("SELECT hour(inv_fec) AS ho2, avg(inv_temp) as tt2 from inv where date(inv_fec)='$fecha2' group by ho2 asc"); 
    $handle2->execute(); 
    //SELECT concat_ws(':',HOUR(inv_hora), minute(inv_hora)) AS ho, inv_temp from inv where 1
    $result2 = $handle2->fetchAll(\PDO::FETCH_OBJ);
		
    foreach($result2 as $row){
        array_push($dataPoints2, array("x"=> $row->ho2, "y"=> $row->tt2));
    }

	$link = null;
}
catch(\PDOException $ex){
    print($ex->getMessage());
}
	
?>
<!DOCTYPE HTML>
<html>
<head>  
<script>
window.onload = function () {
 
var chart = new CanvasJS.Chart("chartContainer", {
	animationEnabled: true,
	exportEnabled: true,
	theme: "light1", // "light1", "light2", "dark1", "dark2"
	title:{
		text: "PHP Column Chart from Database"
	},
	axisX: {
		interval: 1,
		type: "dateTime",
	},
	data: [
	{
		name: <?php echo json_encode($fecha, JSON_NUMERIC_CHECK); ?>,
		showInLegend: true,	
		type: "line", //change type to bar, line, area, pie, etc  
		dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
	},
	{
		name: <?php echo json_encode($fecha2, JSON_NUMERIC_CHECK); ?>,
		showInLegend: true,
		type: "spline", //change type to bar, line, area, pie, etc  
		dataPoints: <?php echo json_encode($dataPoints2, JSON_NUMERIC_CHECK); ?>
	}]
});
chart.render();
 
}
</script>
</head>
<body>
<div id="chartContainer" style="height: 370px; width: 100%;"></div>
<script src="canvasjs.min.js"></script>
</body>
</html>   