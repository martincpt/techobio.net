<?php

/**
 * return number of published sticky posts
 */
function videopro_get_sticky_posts_count() {
	 global $wpdb;
	 $sticky_posts = array_map( 'absint', (array) get_option('sticky_posts') );
	 return count($sticky_posts) > 0 ? $wpdb->get_var($wpdb->prepare( "SELECT COUNT( 1 ) FROM $wpdb->posts WHERE post_type = 'post' AND post_status = 'publish' AND ID IN (%s)",implode(',', $sticky_posts)) ) : 0;
}

/**
 * Get related posts
 *
 * @params $post_id (optional). If not passed, it will try to get global $post
 */
if(!function_exists('videopro_get_related_posts')){
	function videopro_get_related_posts( $options = array() ) 
	{
		if($options['enable_yarpp_plugin'] == 'on' && function_exists('yarpp_get_related'))
		{
		    $related_posts =  yarpp_get_related(array('limit' => $options['related_post_limit'], 'post_type' => 'post'), $options['post_ID']);
		}
		else
		{
		    $args = array(
		        'post_type'             => 'post',
		        'posts_per_page'        => $options['related_post_limit'],
		        'orderby'               => $options['get_related_order_by'],
		        'post_status'           => 'publish',
		        'post__not_in'          => array($options['post_ID']),
		        'ignore_sticky_posts'   => true
		    );

		    if($options['get_related_post_by'] != '' && $options['get_related_post_by'] == 'cat')
		    {
		        //get categories of post
		        $categories =  get_the_category($options['post_ID']);
		        $cats       = array();
		        if(count($categories) > 0)
		        {
		            foreach($categories as $category)
		            {
		                $cats[] = $category->term_id;
		            }
		            $cats_str = implode(",",$cats);
		            $args['cat'] = $cats_str;
		        }
		    }
		    else if($options['get_related_post_by'] != '' && $options['get_related_post_by'] == 'tag')
		    {
		        $tags       = wp_get_post_tags($options['post_ID']);
		        $tags_arr   = array();
		        if(count($tags) > 0)
		        {
		            foreach($tags as $tag)
		            {
		                $tags_arr[] = $tag->term_id;
		            }
		            $args['tag__in'] = $tags_arr;
		        }
		    }
			$args = apply_filters( 'videopro_get_related_posts', $args );
		    $related_post_query = new WP_Query( $args );
		    $related_posts      = $related_post_query->posts;
		}
		return $related_posts;
	}
}
if(!function_exists('videopro_get_posts')){
	/*
	 * Get item for trending, popular
	 * $conditions : most by :view, comment, likes, latest, rand/random
	 * $number : Number of post
	 * $ids : List id
	 */
	function videopro_get_posts($post_type, $conditions, $tags, $number, $ids, $sort_by, $categories, $args = array(), $trending, $timerange, $paged){
		$use_network_data = function_exists('osp_get') ? osp_get('ct_video_settings', 'use_video_network_data') : 'off';
		$use_network_data = ($use_network_data == 'on') ? 1 : 0;
		
		$args = array_merge($args, array(
						'post_type' 			=> 'post',
						'posts_per_page' 		=> $number,
						'post_status' 			=> 'publish',
						'ignore_sticky_posts' 	=> true
					));
		
		if($trending == ''){
			$trending = 0;
		}
		
        if($trending == 1){
			
            if(isset($ids) && $ids == '')
            {
                if($conditions == 'most_liked'){
                    if($use_network_data){
                        $args = array_merge($args, array(
                                                    'order' => 'DESC',
                                                    'orderby' => 'meta_value_num',
                                                    'meta_key' => '_video_network_likes'
                                                ));
                    } else {
                        global $wpdb;
                        if($timerange == 'day'){$time_range = '1';}
                        else if($timerange == 'week'){$time_range = '7';}
                        else if($timerange == 'month'){$time_range = '1m';}
                        else if($timerange == 'year'){$time_range = '1y';}

                        $limit = $where ='';
                        
                        $prepare_values = array();

                        $show_excluded_posts = get_option('wti_like_post_show_on_widget');
                        $excluded_post_ids = explode(',', get_option('wti_like_post_excluded_posts'));

                        if(!$show_excluded_posts && count($excluded_post_ids) > 0) {
                            $where .= "AND post_id NOT IN (" . implode(',', array_fill(0, count($excluded_post_ids), '%d')) . ")";
                            $prepare_values = array_merge($prepare_values, $excluded_post_ids);
                        }

                        if($timerange != 'all') {
                            if(function_exists('GetWtiLastDate')){
                                $last_date = GetWtiLastDate($time_range);
                                $where .= " AND date_time >= %s";
                                array_push($prepare_values, $last_date);
                            }
                        }
                        
                        if($number > 0) {
                            $limit = "LIMIT %d";
                            array_push($prepare_values, $number);
                        }

                        $query = "SELECT post_id, SUM(value) AS like_count, post_title FROM `{$wpdb->prefix}wti_like_post` L, {$wpdb->prefix}posts P ";
                        $query .= "WHERE L.post_id = P.ID AND post_status = 'publish' AND value > 0 $where GROUP BY post_id ORDER BY like_count DESC, post_title $limit";
                        
                        if(count($prepare_values) > 0){
                            $posts = $wpdb->get_results($wpdb->prepare($query, $prepare_values));
                        } else {
                            $posts = $wpdb->get_results($query);
                        }
                        
                        $p_data = array();
                        if(count($posts) > 0) {
                            foreach ($posts as $post) {
                                $p_data[] = $post->post_id;
                            }
                        }

                        $args = array_merge($args, array(
                            'orderby'=> 'post__in',
                            'order' => 'ASC',
                            'post_status' => 'publish',
                            'tag' => $tags,
                            'post__in' =>  $p_data,
                        ));
                    }
                }elseif($conditions == 'most_viewed' || $conditions == ''){
                    
                    if($use_network_data){
                        $args = array_merge($args, array(
                                                'order' => 'DESC',
                                                'orderby' => 'meta_value_num',
                                                'meta_key' => '_video_network_views'
                                            ));
                    } else {

                        $ids = array();
                        if($timerange == 'day')
                        {
                            if(function_exists('videopro_get_tptn_pop_posts')){
                                $ids = videopro_get_tptn_pop_posts(array(
                                    'daily' => 1,
                                    'daily_range' => 1,
                                    'post_types' => 'post',
                                    'limit' => $number
                                ));
                            }
                        }elseif($timerange == 'week'){
                            if(function_exists('videopro_get_tptn_pop_posts')){
                                $ids = videopro_get_tptn_pop_posts(array(
                                    'daily' => 1,
                                    'daily_range' => 7,
                                    'post_types' => 'post',
                                    'limit' => $number
                                ));
                            }
                        }elseif($timerange == 'month'){
                            if(function_exists('videopro_get_tptn_pop_posts')){
                                $ids = videopro_get_tptn_pop_posts(array(
                                    'daily' => 1,
                                    'daily_range' => 30,
                                    'post_types' => 'post',
                                    'limit' => $number
                                ));
                            }
                        }elseif($timerange == 'year'){
                            if(function_exists('videopro_get_tptn_pop_posts')){
                                $ids = videopro_get_tptn_pop_posts(array(
                                    'daily' => 1,
                                    'daily_range' => 365,
                                    'post_types' => 'post',
                                    'limit' => $number
                                ));
                            }
                        }else{
                            if(function_exists('videopro_get_tptn_pop_posts')){
                                $ids = videopro_get_tptn_pop_posts(array(
                                    'daily' => 0,
                                    'post_types' => 'post',
                                    'limit' => $number
                                ));
                            }
                        }
                        
                        $args = array_merge($args, array(
                            'post__in'=> $ids,
                            'orderby'=> 'post__in'
                        ));
                    }
                } elseif($conditions == 'most_commented'){
                    wp_reset_postdata();
                    if($use_network_data){
                        $args = array_merge($args, array(
                            'orderby' => 'meta_value_num',
                            'order' => 'DESC',
                            'meta_key' => '_video_network_comments'
                            ));
                    } else {
                        if($timerange == 'all'){
                            $args = array_merge($args, array(
                                'orderby' => 'comment_count',
                                'order' => $sort_by,
                                'tag' => $tags
                            ));
                        }else{
                            if($timerange=='day'){
                                $some_comments = get_comments( array(
                                    'date_query' => array(
                                        array(
                                            'after' => '1 day ago',
                                        ),
                                    ),
                                ) );
                            }else
                            if($timerange=='week'){
                                $some_comments = get_comments( array(
                                    'date_query' => array(
                                        array(
                                            'after' => '1 week ago',
                                        ),
                                    ),
                                ) );
                            }else
                            if($timerange=='month'){
                                $some_comments = get_comments( array(
                                    'date_query' => array(
                                        array(
                                            'after' => '1 month ago',
                                        ),
                                    ),
                                ) );
                            }else
                            if($timerange=='year'){
                                $some_comments = get_comments( array(
                                    'date_query' => array(
                                        array(
                                            'after' => '1 year ago',
                                        ),
                                    ),
                                ) );
                            }
                            $arr_id= array();
                            foreach($some_comments as $comment){
                                $arr_id[] = $comment->comment_post_ID;
                            }
                            $arr_id = array_unique($arr_id, SORT_REGULAR);
                            $args = array_merge($args, array(
                                'order' => $sort_by,
                                'orderby' => 'post__in',
                                'post__in' =>  $arr_id
                            ));
                        }
                    }
                }else {
                    $args = array_merge($args, array(
                                'order' => $sort_by,
                                'tag' => $tags
                                ));
                        
                    if($timerange != 'all'){
						if($timerange == '') $timerange = 'day';
                        if($timerange == 'week'){
                            $number_day = '7';
                        }
                        elseif($timerange == 'day'){$number_day = '1';}
                        elseif($timerange == 'month'){$number_day = '30';}
                        elseif($timerange == 'year'){$number_day = '365';}
                        $limit_date =  date('Y-m-d', strtotime('-' . $number_day . ' day'));
                        $args['date_query'] = array(
                                'after'         => $limit_date
                        );
                    }
                }
                
                if($paged){
                    $args['paged'] = $paged;
                }

                if(!is_array($categories)) {
                    if(isset($categories) && $categories != ''){
                        $cats = explode(",",$categories);
                        if(is_numeric($cats[0])){
                            $args['category__in'] = $cats;
                        }else{
                            $args['category_name'] = $categories;
                        }
                    }
                }else if(count($categories) > 0){
                    $args += array('category__in' => $categories);
                }
            }
            else
            {
                $args = array_merge($args, array(
                    'orderby' 				=> 'post__in'
                ));
                
                $id_arr = explode(",",$ids);

                if(is_numeric($id_arr[0]))
                    $args['post__in'] = $id_arr;
            }

            $query = new WP_Query($args);

            if(!$query->have_posts())
            {
                unset($args['date_query']);
                $query = new WP_Query($args);
            }
            
            
            return $query;
        } else {
            if($conditions == 'most_viewed' && $ids == ''){
                if($use_network_data){
            
                    $args = array_merge($args, array(
                                            'order' => 'DESC',
                                            'orderby' => 'meta_value_num',
                                            'meta_key' => '_video_network_views'
                                        ));
                                        
                } else {
                    if(function_exists('videopro_get_tptn_pop_posts')){
                          $ids = videopro_get_tptn_pop_posts(array(
                              'daily' => 0,
                              'post_types' =>'post',
                              'limit' => $number
                          ));
                      }
                      $args = array_merge($args, array(
                          'post__in'=> $ids,
                          'orderby'=> 'post__in'
                      ));
                }
            }elseif($conditions == 'most_commented' && $ids == ''){
                if($use_network_data){
                    $args = array_merge($args, array(
                        'orderby' => 'meta_value_num',
                        'order' => 'DESC',
                        'meta_key' => '_video_network_comments'
                        ));
                } else {
                    $args = array_merge($args, array(
                                'orderby' => 'comment_count',
                                'order' => $sort_by
                            ));
                }
            }elseif($conditions == 'high_rated' && $ids == ''){
                $args = array_merge($args, array(
                            'meta_key' => 'taq_review_score',
                            'orderby' => 'meta_value_num',
                            'order' => $sort_by,
                            'tag' => $tags
                        ));
            } elseif($ids != ''){
                $ids = explode(",", $ids);
                $gc = array();
                $dem = 0;
                foreach ( $ids as $grid_cat ) {
                    $dem++;
                    array_push($gc, $grid_cat);
                }
                $args = array_merge($args, array(
                            'order' => 'post__in',
                            'tag' => $tags,
                            'post__in' =>  $gc
                        ));

            } elseif($ids=='' && $conditions=='latest'){
                $args = array_merge($args, array(
                            'order' => $sort_by,
                            'tag' => $tags,
                        ));

            } elseif($ids == '' && $conditions == 'most_liked'){
                if($use_network_data){
                    $args = array_merge($args, array(
                                                'order' => 'DESC',
                                                'orderby' => 'meta_value_num',
                                                'meta_key' => '_video_network_likes'
                                            ));
                } else {
                    global $wpdb;
                    $time_range = 'all';

                    $show_excluded_posts = get_option('wti_like_post_show_on_widget');
                    $excluded_post_ids = explode(',', get_option('wti_like_post_excluded_posts'));
                    
                    $prepare_values = array();
                    
                    if(!$show_excluded_posts && count($excluded_post_ids) > 0) {
                        $where = "AND post_id NOT IN (" . implode(',', array_fill(0, count($excluded_post_ids), '%d')) . ")";
                        $prepare_values = array_merge($prepare_values, $excluded_post_ids);
                    }
                    else{$where = '';}
                    
                    $query = "SELECT post_id, SUM(value) AS like_count, post_title FROM `{$wpdb->prefix}wti_like_post` L, {$wpdb->prefix}posts P ";
                    $query .= "WHERE L.post_id = P.ID AND post_status = 'publish' AND value > -1 $where GROUP BY post_id ORDER BY like_count DESC, post_title";
                    
                    if(count($prepare_values) > 0){
                        $posts = $wpdb->get_results($wpdb->prepare($query, $prepare_values));
                    } else {
                        $posts = $wpdb->get_results($query);
                    }
                    
                    $p_data = array();
                    if(count($posts) > 0) {
                        foreach ($posts as $post) {
                            $p_data[] = $post->post_id;
                        }
                    }

                    $args = array_merge($args, array(
                                'orderby'=> 'post__in',
                                'order' => 'ASC',
                                'tag' => $tags,
                                'post__in' =>  $p_data
                            ));
                }
            } else {
                if($conditions == 'random') $conditions = 'rand';
                $args = array_merge($args, array(
                                'order' => $sort_by,
                                'orderby' => $conditions, /* title or modified */
                                'tag' => $tags
                        ));
            }
            if(!is_array($categories)) {
                if(isset($categories) && $categories != ''){
                    $cats = explode(",",$categories);
                    if(is_numeric($cats[0])){
                        $args['category__in'] = $cats;
                    }else{
                        $args['category_name'] = $categories;
                    }
                }
            }else if(count($categories) > 0){
                $args += array('category__in' => $categories);
            }

            $query = new WP_Query($args);

            return $query;
        }
	}
}
/*
Get Most like
*/
if(!function_exists('videopro_get_most_like')){
	function videopro_get_most_like($offset = 0, $posts_per_page = -1){
		global $wpdb;
		$time_range = 'all';

		$show_excluded_posts = get_option('wti_like_post_show_on_widget');
		$excluded_post_ids = explode(',', get_option('wti_like_post_excluded_posts'));
		
		$prepare_values = array();
		
		if(!$show_excluded_posts && count($excluded_post_ids) > 0) {
			$where = "AND post_id NOT IN (" . implode(',', array_fill(0, count($excluded_post_ids), '%d')) . ")";
			$prepare_values = array_merge($prepare_values, $excluded_post_ids);
		}
		else{$where = '';}
		
		$query = "SELECT post_id, SUM(value) AS like_count, post_title FROM `{$wpdb->prefix}wti_like_post` L, {$wpdb->prefix}posts P ";
		$query .= "WHERE L.post_id = P.ID AND post_status = 'publish' AND value > -1 $where GROUP BY post_id ORDER BY like_count DESC, post_title";
		
		$limit_q = '';
		if($posts_per_page != -1){
			$limit_q = ' LIMIT %d OFFSET %d';
			array_push($prepare_values, $posts_per_page, $offset);
			$query .= $limit_q;
		}
		
		if(count($prepare_values) > 0){
			$posts = $wpdb->get_results($wpdb->prepare($query, $prepare_values));
		} else {
			$posts = $wpdb->get_results($query);
		}
		
		$p_data = array();
		if(count($posts) > 0) {
			foreach ($posts as $post) {
				$p_data[] = $post->post_id;
			}
		}
		return $p_data;
	}
}

if(!function_exists('videopro_get_tptn_pop_posts')){
	/*
	$args = array(
		'daily' => true, -- false to get all
		'daily_range' => '', -- number date to get
		'limit' => '', -- number post to query
		'offset' => '', -- number of posts to ignore
		'post_types' => '',
	);
	*/
	function videopro_get_tptn_pop_posts( $args = array() ) {
		if(!function_exists('get_tptn_post_count_only')){
			return;
		}
		global $wpdb, $tptn_settings;
		if($tptn_settings==''){ $tptn_settings = array();}	
		// Initialise some variables
		if($tptn_settings)
		$fields = array();
        $where = isset($args['where']) ? $args['where'] : '';
        $join = isset($args['join']) ? $args['join'] : '';
		$groupby = '';
		$orderby = '';
		$limits = '';
	
		$defaults = array(
			'daily' => true,
			'strict_limit' => true,
			'posts_only' => false,
		);
	
		// Merge the $defaults array with the $tptn_settings array
		$defaults = array_merge( $defaults, $tptn_settings );

        $limit = isset($args['limit']) ? $args['limit'] : 0;

		// Parse incomming $args into an array and merge it with $defaults
		$args = wp_parse_args( $args, $defaults );
		if ( $args['daily'] ) {
			$table_name = $wpdb->base_prefix . 'top_ten_daily';
		} else {
			$table_name = $wpdb->base_prefix . 'top_ten';
		}
	
		// If post_types is empty or contains a query string then use parse_str else consider it comma-separated.
		if ( ! empty( $args['post_types'] ) && false === strpos( $args['post_types'], '=' ) ) {
			$post_types = explode( ',', $args['post_types'] );
		} else {
			parse_str( $args['post_types'], $post_types );	// Save post types in $post_types variable
		}
	
		// If post_types is empty or if we want all the post types
		if ( empty( $post_types ) || 'all' === $args['post_types'] ) {
			$post_types = get_post_types( array(
				'public'	=> true,
			) );
		}
	
		$blog_id = get_current_blog_id();
	
		$current_time = current_time( 'timestamp', 0 );
		$from_date = $current_time - ( $args['daily_range'] * DAY_IN_SECONDS );
		$from_date = gmdate( 'Y-m-d' , $from_date );
	
		/**
		 *
		 * We're going to create a mySQL query that is fully extendable which would look something like this:
		 * "SELECT $fields FROM $wpdb->posts $join WHERE 1=1 $where $groupby $orderby $limits"
		 */
	
		// Fields to return
        $fields = array();
		$fields[] = 'ID';
		$fields[] = 'postnumber';
		$fields[] = ( $args['daily'] ) ? 'SUM(cntaccess) as sumCount' : 'cntaccess as sumCount';
	
		$fields = implode( ', ', $fields );
	
		// Create the JOIN clause
		$join .= " INNER JOIN {$wpdb->posts} ON postnumber=ID ";
	
		// Create the base WHERE clause
		$where .= $wpdb->prepare( ' AND blog_id = %d ', $blog_id );				// Posts need to be from the current blog only
		$where .= " AND $wpdb->posts.post_status = 'publish' ";					// Only show published posts
	
		if ( $args['daily'] ) {
			$where .= $wpdb->prepare( " AND dp_date >= '%s' ", $from_date );	// Only fetch posts that are tracked after this date
		}
	
		// Convert exclude post IDs string to array so it can be filtered
		$exclude_post_ids = explode( ',', $args['exclude_post_ids'] );
	
		/**
		 * Filter exclude post IDs array.
		 *
		 * @param array   $exclude_post_ids  Array of post IDs.
		 */
		$exclude_post_ids = apply_filters( 'tptn_exclude_post_ids', $exclude_post_ids );
	
		// Convert it back to string
		$exclude_post_ids = implode( ',', array_filter( $exclude_post_ids ) );
	
		if ( '' != $exclude_post_ids ) {
			$where .= " AND $wpdb->posts.ID NOT IN ({$exclude_post_ids}) ";
		}

		$where .= " AND $wpdb->posts.post_type IN ('" . join( "', '", $post_types ) . "') ";	// Array of post types
	
		// How old should the posts be?
		if ( $args['how_old'] ) {
			$where .= $wpdb->prepare( " AND $wpdb->posts.post_date > '%s' ", gmdate( 'Y-m-d H:m:s', $current_time - ( $args['how_old'] * DAY_IN_SECONDS ) ) );
		}
	
		// Create the base GROUP BY clause
		if ( $args['daily'] ) {
			$groupby = ' postnumber ';
		}
	
		// Create the base ORDER BY clause
		$orderby = ' sumCount DESC ';
	
		// Create the base LIMITS clause
		$limits .= $limit ? $wpdb->prepare( ' LIMIT %d ', $limit ): '';	
		$limits .= $limits ? $wpdb->prepare('OFFSET %d', isset($args['offset']) ? $args['offset'] : 0) : '';
	
		if ( ! empty( $groupby ) ) {
			$groupby = " GROUP BY {$groupby} ";
		}
		if ( ! empty( $orderby ) ) {
			$orderby = " ORDER BY {$orderby} ";
		}
	
		$sql = "SELECT DISTINCT $fields FROM {$table_name} $join WHERE 1=1 $where $groupby $orderby $limits";
		$results = $wpdb->get_results( $sql );
		$ids = array();
		foreach ( $results as $result ) {
			$ids[] = $result->ID;
		}
		return $ids;
	}
}

/**
 * get terms by its first letters
 *
 * @params
		$letter - string - a letter or '0-9'
		$taxonomy - string - name of taxonomy, ex: 'category' or 'post_tag'
   @return
		array - list of terms
 */
function videopro_get_terms_by_first_letter($letter, $taxonomy = 'category'){
	global $wpdb;
	
	if($letter == '0-9'){
		$where = array();
		for($i = 0; $i <= 9; $i++){
			array_push($where, "t1.`name` LIKE '$i%%'");
		}
		$where = implode(' OR ' , $where);
		$terms = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->terms AS t1, $wpdb->term_taxonomy AS t2 
										WHERE t1.`term_id` = t2.`term_id` AND t2.`taxonomy` = %s
												AND ($where)", $taxonomy));
	} else {
		$terms = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->terms AS t1, $wpdb->term_taxonomy AS t2 
										WHERE t1.`term_id` = t2.`term_id` AND t2.`taxonomy` = %s
												AND t1.`name` LIKE %s", $taxonomy, $letter . '%'));
	}
	return $terms;
}