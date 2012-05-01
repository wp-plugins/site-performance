<div class="wrap">
	<h2>Site Performance</h2>
	
	<p>To generate statistics you need to open your pages. <a href="<?php echo home_url(); ?>">Go to home page and start browsing.</a> Come back after opened some pages.</p>
		
	<div id="column_num_queries" style="width: 900px; height: 300px;"></div>
	
	<div id="column_mem_usage" style="width: 900px; height: 300px;"></div>
	
	<div id="column_render_time" style="width: 900px; height: 300px;"></div>	
</div><!-- .wrap -->


<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
  google.load("visualization", "1", {packages:["corechart"]});
  google.setOnLoadCallback(drawChart);
  function drawChart() {
  	/* column chart num queries */
    var data = google.visualization.arrayToDataTable([
      ['Page', 'Queries'],
      
      <?php $rows = ''; foreach ( $stats_num_queries as $s ) { ?>
				<?php $rows .= "['$s->page', $s->num_queries],"; ?>
			<?php } 
			$rows = trim($rows, ',');
			echo $rows;
			?>
    ]);

    var options = {
      title: 'Number of queries per page',
      hAxis: {title: 'Page', titleTextStyle: {color: 'red'}}
    };

    var chart = new google.visualization.ColumnChart(document.getElementById('column_num_queries'));
    chart.draw(data, options);
    
    /* column chart memory usage */
    var data = google.visualization.arrayToDataTable([
      ['Page', 'Memory usage'],
      
      <?php $rows = ''; foreach ( $stats_mem_usage as $s ) { ?>
				<?php $rows .= "['$s->page', $s->memory_usage],"; ?>
			<?php } 
			$rows = trim($rows, ',');
			echo $rows;
			?>
    ]);

    var options = {
      title: 'Memory usage per page',
      hAxis: {title: 'Page', titleTextStyle: {color: 'red'}}
    };

    var chart = new google.visualization.ColumnChart(document.getElementById('column_mem_usage'));
    chart.draw(data, options);
    
    /* column chart render time */
    var data = google.visualization.arrayToDataTable([
      ['Page', 'Render time'],
      
      <?php $rows = ''; foreach ( $stats_render_time as $s ) { ?>
				<?php $rows .= "['$s->page', $s->render_time],"; ?>
			<?php } 
			$rows = trim($rows, ',');
			echo $rows;
			?>
    ]);

    var options = {
      title: 'Render time per page',
      hAxis: {title: 'Page', titleTextStyle: {color: 'red'}}
    };

    var chart = new google.visualization.ColumnChart(document.getElementById('column_render_time'));
    chart.draw(data, options);    
  }
</script>