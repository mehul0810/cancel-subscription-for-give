"use strict";

let csfg_modal = {
    open: function(){
        let modal = document.querySelector('.csfg-cancel-subscription-modal');
        modal.classList.add('in');
    },
    close: function(){
        let modal = document.querySelector('.csfg-cancel-subscription-modal');
        modal.classList.remove('in');
    }
}

// Init Modal Event Handlers
document.addEventListener('readystatechange', e => {
    if( document.readyState == 'complete' ){
       let el = document.querySelectorAll( '.give-cancel-subscription-reason' );
        el.forEach( selector => {
            selector.addEventListener('click', function( e ){
                e.preventDefault();
                let currentElem  = e.target;
                let subsId = currentElem.getAttribute('cancel-subscription');
                csfg_modal.open();
                document.querySelector('#csfg-subscription-id').value = subsId;
            });
        });
        // Close Modal On Click
        document.querySelectorAll('.csfg-close-confirm-modal').forEach( el => {
            el.addEventListener( 'click', event => {
                event.preventDefault();
                csfg_modal.close();
            });
        });

        // Hide and show textarea if Reason seleted Other
        let rs = document.querySelector('.give_cancel_reasons');
        let confirmCancelButton = document.querySelector('.give-confirm-cancel-subscription');

        // Don't proceed with `cancel reasons` elements doesn't exists.
        if ( ! rs ) {
            return;
        }
        
        rs.addEventListener('change', event => {
            let cValue = event.target.value;
            let otherReasonField = document.querySelector('.csfg-other-reason'); 
            if( cValue == 'other' ){
                confirmCancelButton.setAttribute( 'disabled', 'disabled' );
                otherReasonField.classList.remove('csfg-hide');
            }
            else{
                confirmCancelButton.removeAttribute( 'disabled' );
                otherReasonField.classList.add('csfg-hide');
            }
        });
        // Enable Button On Keyup in Other Reason Textarea
        let csfg_reason_text = document.querySelector('.csfg-other-reason');
        csfg_reason_text.addEventListener('keyup', event => {
            let val = event.target.value;
            val = val.trim()
            .replace( /  +/g, ' ' )       
            .replace( /\s\s+/g, ' ' )     
            .replace( / {2,}/g, ' ' )     
            .replace( / +/g, ' ' )        
            .replace( / +(?= )/g, ' ').split(' ');
            if( val.length > 2 ){
                confirmCancelButton.removeAttribute( 'disabled' );
            }
            else{
                confirmCancelButton.setAttribute( 'disabled', 'disabled' );
            }
        });
    }
});