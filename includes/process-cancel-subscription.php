<?php 
// Include Modal on History Page
add_action('give_after_recurring_history', 'csfg_cancel_reason_modal' );

function csfg_cancel_reason_modal(){
    give_get_template_part('cancel', 'confirmation-modal' );
}
// Process Cancel Subscription
add_action('give_cancel_subscription_with_reason', 'csfg_process_cancellation');

function csfg_process_cancellation( $postdata ){
    // Return If Id Empty 
    if( empty( $postdata['subscription_id'] ) ){
        return;
    }
    /**
     *  Return If 
     * user Not logged In
     * user has no active donation session
     * email access is not enabled
     */  
    if( !is_user_logged_in() && Give_Recurring()->subscriber_has_email_access() == false  && ! give_get_purchase_session() ){
        return;
    }
    // Change to Absolute Integer Number 
    $postdata['subscription_id'] = absint( $postdata['subscription_id'] );

    // Get Reason Of Cancelling The Subscription
    $cancel_reason = $postdata['give_cancel_reason'];
    // Get Custom Reason
    $other_cancel_reason = '';
    if( $cancel_reason == 'other' ){
        $other_cancel_reason = $postdata['give_other_cancel_reason'];
    }
    // Verify Nonce for Security

    if ( ! wp_verify_nonce( $postdata['_wpnonce_cancel_subscription'], "cancel_subscription_with_reason" ) ) {
        wp_die( __( 'Nonce verification failed.', 'cancel-subscription-for-give' ), __( 'Error', 'cancel-subscription-for-give' ), [ 'response' => 403 ] );
    }

    // Access Give_Subscription Class
    $subscription = new Give_Subscription( $postdata['subscription_id'] );

    try {

        do_action( 'before_cancel_subscription',  $subscription );
        
        // Add Subscription Meta
        give_recurring()->subscription_meta->update_meta( $subscription->id, '__reason_for_cancelling', $cancel_reason );
        give_recurring()->subscription_meta->update_meta( $subscription->id, '__other_reason_for_cancelling', $other_cancel_reason );

        // Add Donation/payment meta
        Give()->payment_meta->update_meta( $subscription->id, '__reason_for_cancelling', $cancel_reason );
        Give()->payment_meta->update_meta( $subscription->id, '__other_reason_for_cancelling', $other_cancel_reason );

        //Cancal Subscription
		$subscription->cancel();

        // redirect to admin if admin panel 
        if ( is_admin() ) {

            wp_redirect( admin_url( 'edit.php?post_type=give_forms&page=give-subscriptions&give-message=cancelled&id=' . $subscription->id ) );
            exit;

        } else {

            $args = ! give_get_errors() ? [ 'give-message' => 'cancelled' ] : [];

            wp_redirect(
                remove_query_arg(
                    [
                        '_wpnonce',
                        'give_action',
                        'subscription_id',
                    ],
                    add_query_arg( $args )
                )
            );

            exit;

        }

    } catch ( Exception $e ) {
        wp_die( $e->getMessage(), __( 'Error', 'cancel-subscription-for-give' ), [ 'response' => 403 ] );
    }
}

// Add Email Tag For canceling Reason

add_filter('give_email_tags', 'csfg_reason_for_cancelling_tag' );

function csfg_reason_for_cancelling_tag( $email_tags ){

    $email_tags[] = [
        'tag'     => 'reason_for_cancelling',
        'desc'    => esc_html__( 'Cancel reason submitted when donation cancelled by donor.', 'cancel-subscription-for-give' ),
        'func'    => 'csfg_reason_for_cancelling',
        'context' => 'general',
    ];
    return $email_tags;
}
// Callback to replace tag with content
function csfg_reason_for_cancelling( $tag_args ){
    // Get Payment Id
    if ( !give_check_variable( $tag_args, 'isset', 0, 'payment_id' ) ){
        return __('N/A', 'cancel-subscription-for-give');
    }

    $payment_id = $tag_args['payment_id'];

    // Get Subscription ID from Payment_id
    $subscription_id = ( new Give_Subscriptions_DB() )->get_column_by('id', 'parent_payment_id', $payment_id );

    if( ! $subscription_id ){
        return __('N/A', 'cancel-subscription-for-give');
    }
    
    // Get Cancel Reason by id
    $cancel_reason = give_recurring()->subscription_meta->get_meta( $subscription_id, '__reason_for_cancelling', true );
    $other_cancel_reason = give_recurring()->subscription_meta->get_meta( $subscription_id, '__other_reason_for_cancelling', true );
    
    // Format Reason on the Basis of Other 
    $format_reason = $cancel_reason;
    if( trim( $cancel_reason ) == 'other' ){
        $format_reason = __( 'Others', 'cancel-subscription-for-give' ) . ' - ' . $other_cancel_reason;
    }

    // Return String 
    return $format_reason ;
    
}

// Add Cancel Subscription Reason on admin Subscription Details Page

add_action('give_recurring_add_subscription_detail', 'csfg_cancel_reason_on_subscription_details', 1, 1 );

function csfg_cancel_reason_on_subscription_details( $subs_id ){
    // Subscription Object
    $subscription = new Give_Subscription( $subs_id );

    // Get Cancel Reason by id
    $cancel_reason = give_recurring()->subscription_meta->get_meta( $subs_id, '__reason_for_cancelling', true );
    $other_cancel_reason = give_recurring()->subscription_meta->get_meta( $subs_id, '__other_reason_for_cancelling', true );

    if( $subscription->status == "cancelled" ){
        ?>
            <tr>
                <td class="row-title">
                    <label for="subscription_cancel_reason"><?php _e( 'Cancel Reason:', 'cancel-subscription-for-give' ); ?></label>
                </td>
                <td>
                    <?php
                        if( trim( $cancel_reason ) == 'other' ):
                            echo __('Others', 'cancel-subscription-for-give') . ' - ' . $other_cancel_reason;
                        else:
                            echo $cancel_reason;
                        endif;
                    ?>
                </td>
            </tr>
        <?php
    }

}

