<?php


class Blocks_Reformer {

	private $blocks;

	function __construct() {

	}

	/**
	 * The do_blocks function doesn't reform the blocks to be the same as the source.
	 * What method can we use to rebuild them?
	 *
	 * <!-- wp:cover {"url":"https://blocks.wp.a2z/wp-content/uploads/sites/10/2019/02/B7A4A34B-4393-4327-813B-C9CECF166F0D.jpeg","id":614,"className":"aligncenter"} -->
	<div class="wp-block-cover has-background-dim aligncenter" style="background-image:url(https://blocks.wp.a2z/wp-content/uploads/sites/10/2019/02/B7A4A34B-4393-4327-813B-C9CECF166F0D.jpeg)"><div class="wp-block-cover__inner-container"><!-- wp:paragraph {"align":"center","placeholder":"Write title…","fontSize":"large"} -->
	<p class="has-text-align-center has-large-font-size">WordPress core</p>
	<!-- /wp:paragraph --></div></div>
	<!-- /wp:cover -->
	 */
	function reform_blocks( $blocks=null ) {
		$blocks = $blocks ?? $this->blocks;
		$output = '';
		foreach ( $blocks as $block ) {
			$output .= $this->reform_block( $block );
		}
		return $output;
	}

	/**
	 * @TODO We need to have replaced block['innerContent'] with the adjusted HTML instead of $chunk
	 *
	 * @param null $block
	 *
	 * @return string
	 */
	function reform_block( $block=null ) {
		print_r( $block );
		$block_content = '';
		$index         = 0;
		$block_content .= $this->reform_html_comment( $block );

		if ( count( $block['innerContent'])) {
			foreach ( $block['innerContent'] as $chunk ) {
				$block_content .= is_string( $chunk ) ? $chunk : $this->reform_block( $block['innerBlocks'][ $index ++ ] );
				//$block_content .= PHP_EOL;
			}
			$block_content .= $this->end_html_comment( $block );
		}
		return $block_content;
	}

	function blockName( $block ) {
		$blockName = $block['blockName'];
		$blockName = str_replace( 'core/', '', $blockName );
		return $blockName;
	}

	function reform_html_comment( $block ) {
		$output = null;
		if ( isset( $block['blockName'])) {
			$output .= '<!-- wp:';
			$output .= $this->blockName( $block );
			if ( isset( $block['attrs'] ) && count( $block['attrs' ] ) ) {
				$output .= ' ';
				$output .= json_encode( $block['attrs'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );

			}
			if ( count( $block['innerContent']) ) {
				//$output .= ' {';
				//$output .= $this->reform_attrs( $block['attrs'] );
				$output .= ' -->';
			} else {
				$output .= ' /-->';
			}
		}
		return $output;
	}

	function end_html_comment( $block ) {
		$output = null;
		if ( isset( $block['blockName'])) {
			$output .= '<!-- /wp:';
			$output .= $this->blockName( $block );
			$output .= ' -->';
		}
		return $output;
	}


}