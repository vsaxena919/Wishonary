<?php
/**
 * Topics Loop
 *
 * @package bbPress
 * @subpackage Theme
 */

// @codingStandardsIgnoreFile
// Allow plugin text domain in theme.
?>

<?php do_action( 'bbp_template_before_topics_loop' ); ?>

<ul id="bbp-forum-<?php bbp_forum_id(); ?>" class="bbp-topics">

	<li class="bbp-header mt-2 mb-2 pl-4 pr-4">

		<ul class="forum-titles">
			<li class="bbp-topic-title"><?php esc_html_e( 'Topic', 'bbpress' ); ?></li>
			<li class="bbp-topic-voice-count"><?php esc_html_e( 'Voices', 'bbpress' ); ?></li>
			<li class="bbp-topic-reply-count"><?php bbp_show_lead_topic() ? esc_html_e( 'Replies', 'bbpress' ) : esc_html_e( 'Posts', 'bbpress' ); ?></li>
			<li class="bbp-topic-freshness d-none d-md-block"><?php esc_html_e( 'Freshness', 'bbpress' ); ?></li>
		</ul>

	</li>

	<li class="bbp-body">

		<?php while ( bbp_topics() ) : bbp_the_topic(); ?>

			<?php bbp_get_template_part( 'loop', 'single-topic' ); ?>

		<?php endwhile; ?>

	</li>

	<li class="bbp-footer">

		<div class="tr">
			<p>
				<span class="td colspan<?php echo ( bbp_is_user_home() && ( bbp_is_favorites() || bbp_is_subscriptions() ) ) ? '5' : '4'; ?>">&nbsp;</span>
			</p>
		</div><!-- .tr -->

	</li>

</ul><!-- #bbp-forum-<?php bbp_forum_id(); ?> -->

<?php do_action( 'bbp_template_after_topics_loop' ); ?>
