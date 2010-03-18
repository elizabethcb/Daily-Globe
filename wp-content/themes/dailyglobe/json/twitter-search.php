					<?php
					
						function makeURL($URL) {
	
							$URL = eregi_replace('(((f|ht){1}tp://)[-a-zA-Z0-9@:\+.~#?&//=]+)','<a href=\\1>\\1</a>', $URL);

							$URL = eregi_replace('([[:space:]()[{}])(www.[-a-zA-Z0-9@:\+.~#?&//=]+)','<a href=\\1>\\1</a>', $URL);
							$URL = eregi_replace('([_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,3})','<a href=\\1>\\1</a>', $URL);

							return $URL;
						}
						
						function time_since($your_timestamp) {
	
							$unix_timestamp = strtotime($your_timestamp);
	
							$seconds = time() - $unix_timestamp;
	
							$minutes = 0;
							$hours = 0;
							$days = 0;
							$weeks = 0;
	
							$months = 0;
							$years = 0;
	
							if ( $seconds == 0 ) $seconds = 1;
	
							if ( $seconds> 60 ) {
			
								$minutes =  $seconds/60;
	
							} else {
			
								return add_s($seconds,'second');
	
							}
							if ( $minutes >= 60 ) {
								$hours = $minutes/60;
	
							} else {
			
								return add_s($minutes,'minute');
	
							}

	
							if ( $hours >= 24) {
			
								$days = $hours/24;
	
							} else {
			
								return add_s($hours,'hour');
	
							}

	
							if ( $days >= 7 ) {
			
								$weeks = $days/7;
	
							} else {
			
								return add_s($days,'day');
	
							}

	
							if ( $weeks >= 4 ) {
			
								$months = $weeks/4;
	
							} else {
								return add_s($weeks,'week');
	
							}
							if ( $months>= 12 ) {
			
								$years = $months/12;
			
								return add_s($years,'year');
	
							} else {
			
								return add_s($months,'month');
	
							}


						}



						function add_s($num,$word) {
	
							$num = floor($num);
	
							if ( $num == 1 ) {
			
								return $num.' '.$word.' ago';
	
							} else {
			
								return $num.' '.$word.'s ago';
	
							}

						}
					
						$query = $_REQUEST['search'];
						$jsonurl = "http://search.twitter.com/search.json?q=" . urlencode($query);
						$json = file_get_contents($jsonurl,0,null,null);
						
					
							$json_output = json_decode($json);
							
							if (count($json_output->results) > 0 ) {
							
								foreach ( $json_output->results as $result ) {
									
									$time_since = time_since("{$result->created_at}");
							
									echo '<div class="tweet">';
									echo '<img width="48" height="48" src="' . "{$result->profile_image_url}" . '" class="left" />';
					
									echo '<p><a class="from_user" href="http://twitter.com/' . "{$result->from_user}" . '">' . $result->from_user . ": </a>". makeURL($result->text) . "</p>";
									echo "<small>" . $time_since . " | From: " . html_entity_decode($result->source) . "</small>";
									echo '</div>';
						
								} 
						
							} else {
							
								echo '<div class="tweet"><p>Sorry, no results found</p></div>';	
							
							}
						
					?>
