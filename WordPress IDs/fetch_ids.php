<?php

function get_post_id($url) {

	$post_id = false;

	printf("Loading link: %s\n", $url); 

	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, 0);

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	$v = curl_exec($ch);
	$info = curl_getinfo($ch);

	if ($info['http_code'] !== 200) {
		// improper status code
		throw new Exception('Post not found');
	}

	if (preg_match('#[<]link rel=[\'"]shortlink[\'"].+href=[\'"]([^\'"]+)[\'"]#i', $v, $matches)) {
		$shortlink = $matches[1];

		// get the ID from it
		$url = parse_url($shortlink);

		$query = $url['query'];
		$queries = explode('&', $query);

		foreach ($queries as $value) {
			$the_query = explode('=', $value);
			list($param, $param_value) = $the_query;

			if ($param == 'p') {
				$post_id = $param_value;
				break;
			}
		}

	} else {
		throw new Exception('Shortlink not set');
	}

	return $post_id;

}

function load_file($filename, &$csv_data, &$index) {
	$row = 1;
	
	if (($handle = fopen($filename, "r")) !== FALSE) {
	    while (($data = fgetcsv($handle)) !== FALSE) {

	    	if ($row == 1) {
	    		if ($index) continue;

		        foreach($data as $k => $v) {
		        	if ($v == 'link') $index = $k;
		        	continue;
		        }

		        array_unshift($data, 'id');
	    		$csv_data[] = $data;
		    }

		    $row ++;

		    // Only non header rows

		    $link = $data[$index ]; 
		    try {
			    $post_id = get_post_id($link);
			} catch (Exception $e) {
				$post_id = false;
			}

			array_unshift($data, $post_id);

			$csv_data[] = $data;

	    }
	    fclose($handle);
	}

	return $csv_data;
}

$files = array(
	'blog.1.csv',
	'blog.2.csv',
	'blog.3.csv'
);

$index = false;

$csv_data = array();

foreach( $files as $csv_file ) {
	load_file($csv_file, $csv_data, $index);
}

$fp = fopen('blog.csv', 'w');

foreach ($csv_data as $fields) {
    fputcsv($fp, $fields);
}

fclose($fp);



