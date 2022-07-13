<?php
function print_poet_template( $quill_image_url, $work_id, $display_date ) {
	ob_start()
	?>
	<div class = "poet-container">
		<a href="https://explorer-mainnet.poetnetwork.net/works/<?php echo esc_html( $work_id ); ?>" target="_blank">
			<div class = "poet-inner">
				<img src="<?php echo esc_attr( $quill_image_url ); ?>" class = "poet-image" >
				<div>
					<p title="<?php echo esc_html( $work_id ); ?>" class = "poet-title">
						Verified on Po.et</p>
					<p class = "poet-date">
						<?php
						echo esc_html( $display_date );
						?>
					</p>
				</div>
			</div>
		</a>
	</div>
	<?php
	return ob_get_clean();
}
