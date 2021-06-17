<?php 
/**
 * Give - Cancel Reccuring Donation Modal Template
 * @package give-recurring
 * @subpackage give-recurring-cancellation
 * @since 0.0.1
 */

// Modal Container Initialize
 ?>
 <div class="csfg-cancel-subscription-modal fade">
    <div class="csfg-modal-container">
        <div class="csfg-cancel-subscription-content">
            <span class="csfg-close-confirm-modal">&times;</span>
            <div class="csfg-cancel-subscription-head">
                <?php do_action('csfg_before_confirm_cancel_title_text'); ?>
                <h2 class="csfg-title"> <?php echo apply_filters( 'csfg_confirm_cancel_title_text', __('Please confirm your cancellation', 'cancel-subscription-for-give' ) ); ?></h2>
                <?php do_action('csfg_after_confirm_cancel_title_text'); ?>
            </div>
            <?php do_action('csfg_bofore_confirm_cancel_body'); ?>
            <div class="csfg-cancel-subscription-body">
                <p class="csfg-subtitle">
                    <?php 
                        $cancel_subtitle = __('Please tell us why you are cancelling', 'cancel-subscription-for-give');
                        echo apply_filters('csfg_confirm_cancel_subtitle_text', $cancel_subtitle );
                    ?>
                </p>
                <?php 
                    $cancel_reasons = array(
                        __('My financial circumstances have changed', 'cancel-subscription-for-give'),
                        __('I\'ve changed my giving options', 'cancel-subscription-for-give' ),
                        __('The OM worker/project no longer needs my support', 'cancel-subscription-for-give'),
                        __('I\'m changing the amount', 'cancel-subscription-for-give')             
                    );
                    $cancel_reasons = apply_filters('give_cancel_subscription_reasons', $cancel_reasons );
                ?>
                <form action="<?php the_permalink() ?>" method="post" class="csfg-cancel-subscription-form">
                    <input type="hidden" name="subscription_id" value="0" id="csfg-subscription-id"/>
                    <input type="hidden" name="give_action" value="cancel_subscription_with_reason" />
                    <?php wp_nonce_field('cancel_subscription_with_reason', '_wpnonce_cancel_subscription'); ?>
                    <div class="csfg-form-group">
                        <select name="give_cancel_reason" class="give_cancel_reasons csfg-form-field">
                            <?php 
                                foreach( $cancel_reasons as $cancel_reason ):
                                    ?>
                                        <option value="<?php echo $cancel_reason ?>"><?php echo $cancel_reason ?></option>
                                    <?php 
                                endforeach;
                            ?>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="csfg-form-group csfg-other-reason csfg-hide">
                        <textarea rows="3" columns="10" placeholder="Write here..." class="csfg-form-field csfg-other-reason" name="give_other_cancel_reason"></textarea> 
                    </div>
                    <div class="csfg-form-group">
                        <?php do_action('csfg_before_confirm_cancel_subscription_button'); ?>
                        <button class="give-confirm-cancel-subscription give-form-button csfg-button"><?php echo apply_filters('cancel_subscription_confirm_button_text', __('Confirm Cancellation', 'cancel-subscription-for-give') );?></button>      
                        <?php do_action('csfg_after_confirm_cancel_subscription_button'); ?>
                    </div>
                </form>
            </div>
            <?php do_action('csfg_after_confirm_cancel_body'); ?>
        </div>
    </div>
 </div>
