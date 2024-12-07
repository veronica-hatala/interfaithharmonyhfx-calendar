<?php
/*
 Event Block
*/
?>
<div <?php echo get_block_wrapper_attributes(); ?>>
	<div class="event_list">	
		<?php 
		$events_query = new WP_Query([
			'post_type'      => 'event',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
		]);
		
		$events = $events_query->posts;
		usort($events, function ($a, $b) {
			$timestamp_a = strtotime(get_post_meta($a->ID, 'datetime', true));
			$timestamp_b = strtotime(get_post_meta($b->ID, 'datetime', true));
			return $timestamp_a <=> $timestamp_b;
		});
		$events_query->posts = $events;
		
		if ($events_query->have_posts()) {
			while ($events_query->have_posts()) {
				$events_query->the_post();
				$event_id = get_the_ID();
				$event_timestamp = strtotime(get_post_meta($event_id, 'datetime', true));
				$date = date('F j, Y, g:i a', $event_timestamp);
		?>
				<div class="event">
					<div class="event_name"><?php echo get_the_title(); ?></div>
					<?php if (get_post_meta($event_id, 'online', true) == 1) { ?><div class="event_online">Online</div><?php } ?>
					<div class="event_datetime"><?php echo $date ?></div>
					<div class="event_address"><?php echo get_post_meta($event_id, 'address', true); ?></div>
					<div class="event_description"><?php echo get_post_meta($event_id, 'description', true); ?></div>
				</div>
			<?php 
			}
			wp_reset_postdata();
		}
		?>
	</div>
</div>
