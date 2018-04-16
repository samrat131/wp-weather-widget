<div class="bs23-weather-widget" style="width: <?php echo $width ?>px; height: <?php echo $height ?>px;">
	
	<div id="bs23-menu" class="menu">
		<img src="<?php echo $bs23PluginsURI.'/images/more-128.png' ?>" alt="">
	</div>
	<ul id="menu">
		<?php foreach ($cities as $city) { ?>
		<li><a href="?location=<?php echo $city ?>"><?php echo $city ?></a></li>
		<?php } ?>
	</ul>

	<span class="location"><?php echo $location ?></span><br>

	<span class="status"><?php echo $data[0]['weather'][0]['main'] ?></span><br>

	<p>
		<img width="80" height="80" src="<?php echo bs23_getIconUrl($data[0]['weather'][0]['icon']) ?>" alt="icon">

		<span class="temp"><?php echo $data[0]['main']['temp'] ?><sup class="symbol"><?php echo $symbol[$unit] ?></sup></span>
	</p>

	<hr>
	
	<table>
		<tr>
		<?php foreach ($data as $key => $item) { ?>
			<td>
				<p><?php echo date('D', $item['dt']) ?></p>

				<img width="32" height="32" src="<?php echo bs23_getIconUrl($item['weather'][0]['icon']) ?>" alt="icon">

				<p>
					<span class="max"><?php echo $item['main']['temp_max'] ?><sup class="symbol"><?php echo $symbol[$unit] ?></sup></span>
					<span class="min"><?php echo $item['main']['temp_min'] ?><sup class="symbol"><?php echo $symbol[$unit] ?></sup></span> 
				</p>

				
			</td>
		<?php } ?>
		</tr>
	</table>
</div>