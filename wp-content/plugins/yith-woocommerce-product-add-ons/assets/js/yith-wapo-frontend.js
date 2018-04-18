/**
 * Frontend
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Product Add-Ons
 * @version 1.0.0
 */
jQuery(document).ready( function($) {
    "use strict";

    if ( typeof yith_wapo_general === 'undefined' )
        return false;

    /**
     * @author Andrea Frascaspata
     */
    $.fn.init_yith_wapo_totals = function() {


        $(this).on( 'change', '.yith_wapo_groups_container input, .yith_wapo_groups_container select, .yith_wapo_groups_container textarea,  input.qty', function( e ) {

            var current_selected_element = $(this);
            $(this).trigger( 'yith-wapo-product-option-conditional' , current_selected_element );

            $(this).trigger( 'yith-wapo-product-option-update' );

        } );

        $(this).on( 'found_variation', function( event, variation ) {

            var $variation_form = $(this);

            var yith_wapo_group_total = $('.yith_wapo_group_total');

            var new_product_price = 0;

            if ( typeof( variation.display_price ) !== 'undefined' ) {

                new_product_price = variation.display_price;

            } else if ( $( variation.price_html ).find('.amount:last').size() ) {

                var $cart = $('.cart');

                new_product_price = $( variation.price_html ).find('.amount:last').text();
                new_product_price = getFormattedPrice( new_product_price );

            }

            yith_wapo_group_total.data( 'product-price', new_product_price );

            yith_wapo_update_variation_price( variation )

            $(this).trigger( 'yith-wapo-product-option-update' );

        } );


        /**
         *
         * @param variation
         */
        function yith_wapo_update_variation_price( variation ) {
            'use strcit';

            var master_group_container = $( '.yith_wapo_groups_container' );

            if( typeof master_group_container != 'undefined' ) {

                var group_container_list =  master_group_container.find('input.ywapo_input.ywapo_price_percentage, select.ywapo_input option.ywapo_price_percentage, textarea.ywapo_input.ywapo_price_percentage');

                var $i = 0;

                group_container_list.each(function(){

                    var current_option = $(this);

                    if( typeof current_option.data('pricetype') != 'undefined' && current_option.data('pricetype') != 'fixed' ) {

                        var current_container = current_option.closest('.ywapo_input_container');

                        $.ajax({
                            url: yith_wapo_general.wc_ajax_url.toString().replace( '%%endpoint%%', 'yith_wapo_update_variation_price' ),
                            type: 'POST',
                            data: {
                                //action: 'yith_wccl_add_to_cart',
                                variation_id : variation.variation_id,
                                variation_price : variation.display_price,
                                type_id : current_option.data( 'typeid' ),
                                option_index : current_option.data( 'index' )
                            },
                            beforeSend: function(){
                                if( $i == 0  ) {
                                    showLoader( master_group_container );
                                }

                            },
                            success: function( res ){

                                // redirect to product page if some error occurred
                                if ( res.error || res == '' ) {
                                 //   hideLoader( current_option , current_container );
                                    return;
                                } else{

                                    current_option.attr( 'data-price' , res );

                                    var formatted_price = getFormattedPrice( parseFloat( res ) );

                                    current_container.find('span.amount').html(formatted_price);
                                    // select option fix
                                    if( current_option.text() != '') {
                                        var temp_text = current_option.text().split('+');
                                        if( temp_text.length > 0 ) {
                                            temp_text = temp_text[0] + ' + ' + formatted_price;
                                            current_option.addClass('ywapo_option_price_chaged');

                                            /* select2 fix */
                                            var current_group_container = current_option.closest('.ywapo_group_container');
                                            var select_element = current_group_container.find('select');
                                            var sb_attribute = select_element.attr('sb');
                                            if( typeof sb_attribute != 'undefined' ) {
                                                var index = current_option.data( 'index' );
                                                var select2_element = $($('#sbOptions_'+sb_attribute).find('li').get(parseInt(index)+1)).find('a');
                                                select2_element.html(temp_text);
                                            }

                                        }

                                        current_option.html( temp_text );

                                    }

                                    $i++;

                                    if( $i== group_container_list.length  ) {
                                        hideLoader(master_group_container);
                                    }

                                }

                            }
                        });

                    }

                });

            }

        }

        $(this).on( 'yith-wapo-product-option-update', function() {

            var $cart = $(this);
            var yith_wapo_group_total = $('.yith_wapo_group_total');
            var yith_wapo_option_total_price = getOptionsTotal( $cart );

            if( yith_wapo_option_total_price > 0 ) {

                if ( $cart.find('input.qty').size() ) {
                    var qty = parseFloat( $cart.find('input.qty').val() );
                } else {
                    var qty = 1.0
                }

                yith_wapo_option_total_price =   yith_wapo_option_total_price  * qty;

                var is_product_bundle = $('.yith-wcpb-product-bundled-items');

                var single_variation = $cart.find('.single_variation');

                if( typeof single_variation != 'undefined' && is_product_bundle.length == 0) {
                    single_variation.after( yith_wapo_group_total )
                }

                var yith_wapo_option_total_price_formatted = getFormattedPrice( yith_wapo_option_total_price );
                var yith_wapo_final_total_price = 0.0;
                var yith_wapo_group_total = $( '.yith_wapo_group_total' ) ;
                var yith_wapo_product_price = parseFloat( yith_wapo_group_total.data( 'product-price' ) );
                var yith_wapo_group_option_total = yith_wapo_group_total.find( '.yith_wapo_group_option_total span.price' );
                var yith_wapo_group_final_total = yith_wapo_group_total.find( '.yith_wapo_group_final_total span.price' );



                yith_wapo_group_option_total.html( yith_wapo_option_total_price_formatted );

                yith_wapo_final_total_price =  ( yith_wapo_product_price * qty ) + yith_wapo_option_total_price ;

                var yith_wapo_total_price_formatted = getFormattedPrice( yith_wapo_final_total_price );

                yith_wapo_group_final_total.html( yith_wapo_total_price_formatted );

                yith_wapo_group_total.fadeIn();

                $(document).trigger( 'yith_wapo_product_price_updated', [ yith_wapo_product_price + yith_wapo_option_total_price ] );

            } else {

                yith_wapo_group_total.fadeOut();

            }

        } );


        $(this).on( 'click', '.ywapo_input_container.ywapo_input_container_labels', function( e ) {

            var current_selected_element = $(this).find('input[type="hidden"]');

            if( current_selected_element.val() != '' ) {
                current_selected_element.val('');
                $(this).removeClass('ywapo_selected');
            } else {
                var all_labels =  $('.ywapo_input_container.ywapo_input_container_labels');
                all_labels.removeClass('ywapo_selected');
                all_labels.find('input[type="hidden"]').val('');

                $(this).addClass('ywapo_selected');

                current_selected_element.val(current_selected_element.data('index'));

            }

            $(this).trigger( 'yith-wapo-product-option-conditional' , current_selected_element );

            $(this).trigger( 'yith-wapo-product-option-update' );

        } );


        /* dependencies */

        $(this).on( 'yith-wapo-product-option-conditional', function( e , data ) {
            'use strict';

            var current_group_container = $(data).closest('.ywapo_group_container');

            doConditionaLoop( $(this) , data , current_group_container );

        } );

        /* end dependencies*/


        /* required */

        $(this).on( 'click', '.single_add_to_cart_button' , function( e ) {

            var $cart = $(this).closest('form.cart');

            yith_wapo_general.do_submit = checkRequiredFields( $cart );

            return yith_wapo_general.do_submit;

        });

        /* request a quote */
        $(document).on( 'yith_ywraq_action_before', function() {

            $cart = $('form.cart');
            yith_wapo_general.do_submit = checkRequiredFields( $cart );
            return yith_wapo_general.do_submit;

        });

        function checkRequiredFields( $cart ) {

            var do_submit = true;

            $cart.find( '.ywapo_group_container' ).each( function() {

                var group_container = $(this);

                if( typeof group_container != 'undefined' && ! group_container.hasClass('ywapo_conditional_hidden') ) {

                    var type =  group_container.data('type');

                    var required = group_container.data('requested') == '1';

                    var selected = true;

                    switch( type ) {
                        case 'text' :
                        case 'textarea' :
                        case 'number' :
                        case 'file' :
                        case 'date' :
                        case 'range' :

                            group_container.find( 'input.ywapo_input, textarea.ywapo_input').each(function(){

                                if( $(this).val() == '' && $(this).attr('required') == 'required' ) {
                                    required = true;
                                    selected = false;
                                    return;
                                } else if( $(this).val() == '' )  {
                                    selected = false;
                                    return;
                                }

                            });

                            break;

                        case 'color' :

                            group_container.find( 'input.ywapo_input_color').each(function(){

                                if( $(this).val() == '' )  {
                                    selected = false;
                                    return;
                                }

                            });

                            break;

                        case 'select' :

                            selected = group_container.find( 'select.ywapo_input').val() != '';

                            break;

                        case 'labels' :

                            selected = group_container.find( '.ywapo_input_container_labels.ywapo_selected').length > 0;

                            break;

                        case 'checkbox' :

                            if( required ) {

                                var num_elements =  group_container.find( '.ywapo_input').length;
                                var num_elements_selected =  group_container.find( '.ywapo_input:checked').length;
                                selected = num_elements > 0 && num_elements == num_elements_selected ;


                            } else {

                                group_container.find( '.ywapo_input').each(function(){

                                    if( ! $(this).is(':checked') && $(this).attr('required') == 'required' ) {
                                        required = true;
                                        selected = false;
                                        return;
                                    }

                                });

                            }

                            break;

                        case 'radio' :

                            selected = false;

                            group_container.find( 'input.ywapo_input').each(function(){

                                if( $(this).is(':checked') )  {
                                    selected = true;
                                    return;
                                }
                                
                            });

                            break;

                        default :

                    }

                    if( required && ! selected ) {
                        do_submit = false;

                        group_container.addClass('ywapo_miss_required');

                        return;
                    } else {
                        group_container.removeClass('ywapo_miss_required');
                    }

                }

            } );


            if( !do_submit ) {
                $('html, body').animate({
                    scrollTop: $("#yith_wapo_groups_container").offset().top
                }, 2000);
            }


            return do_submit;

        }


        /* end required */


        /**
         *
         * @param $disable_element
         * @param $load_element
         */
        function showLoader( $load_element ) {
            'use strcit';

            $load_element.block({ message: '' ,   overlayCSS:  {
                backgroundColor: '#fff',
                opacity:         0.6,
                cursor:          'wait'
            } });

        }

        /**
         *
         * @param $disable_element
         * @param $load_element
         */
        function hideLoader( $load_element ) {
            'use strcit';

            $load_element.unblock();

        }

        /**
         *
         * @param data
         * @param current_group_container
         * @returns {boolean}
         */
        function isFieldSelected( data , current_group_container ){
            'use strict';

            var current_group_container_type = current_group_container.data('type');

            var selected = false;

            if( current_group_container_type == 'select' || current_group_container_type == 'radio' ) {
                if( data.val() != '' ) {
                    selected = true;
                } else {
                    selected = false;
                }
            } else {

                switch( current_group_container_type ) {
                    case 'checkbox':

                        current_group_container.find('input[type="checkbox"].ywapo_input').each(function(){

                            if( $(this).is(':checked') ){
                                selected = true;
                                return;
                            }

                        });

                        break;

                    case 'labels':
                        var count_val = 0;
                        current_group_container.find('input[type="hidden"].ywapo_input').each(function(){

                            if( $(this).val() != '' ){
                                count_val++;
                                return true;
                            }

                        });

                        selected = ( count_val > 0 );

                        break;

                    case 'text' :
                    case 'textarea' :
                    case 'number' :
                    case 'file' :
                    case 'color' :
                    case 'date' :
                    case 'range' :

                        current_group_container.find('input.ywapo_input').each(function(){

                            if( $(this).val() != '' ) {
                                selected = true;
                                return;
                            }

                        });

                        break;
                    case 'textarea' :

                        current_group_container.find('textarea.ywapo_input').each(function(){

                            if( $(this).val() != '' ) {
                                selected = true;
                                return;
                            }
                        });

                        break;
                }
            }

            if( selected ) {
                current_group_container.removeClass('ywapo_miss_required');
            }

            return selected;

        }
            'use strcit';

        /**
         *
         * @param $cart
         * @param data
         * @param current_group_container
         */
        function doConditionaLoop( $cart, data , current_group_container ){
            'use strcit';

            var current_group_container_id = current_group_container.data('id');

            if( typeof current_group_container_id != 'undefined' ) {

                var current_value = isFieldSelected( $(data) ,current_group_container );

                // verify dependend condition
                $cart.find( '.ywapo_group_container' ).each( function() {

                    var group_container = $(this).closest('.ywapo_group_container');

                    if( typeof group_container != 'undefined' ) {

                        var group_container_id = group_container.data('id');

                        if( ( current_group_container_id != group_container_id ) ) {

                            var group_container_dependecies = group_container.data('condition');

                            if( group_container_dependecies != '' ) {

                                group_container_dependecies = group_container_dependecies.toString().split(',');
                                var has_hided_dependecies = checkDependeciesList( group_container_dependecies );

                                if( has_hided_dependecies ) {

                                    if( ! group_container.hasClass( 'ywapo_conditional_hidden' ) ) {
                                        group_container.addClass( 'ywapo_conditional_hidden' );
                                        doFieldDisabled(group_container);
                                        return true;
                                    }

                                } else {

                                    var is_dependent = $.inArray( current_group_container_id.toString(), group_container_dependecies );

                                    if( is_dependent == 0 ) {

                                        if( current_value ) {
                                            group_container.removeClass('ywapo_conditional_hidden');
                                            doFieldChange(group_container);
                                        } else {
                                            group_container.addClass('ywapo_conditional_hidden');
                                            doFieldDisabled(group_container);
                                        }

                                    }

                                }
                            }

                        }

                    }

                } );

            }

        }

        function doFieldChange( group_container ) {
            'use strcit';

            group_container.find('input, select, textarea').each(function(){
                $(this).removeAttr( 'disabled')
                $(this).change();
            });
        }

        function doFieldDisabled( group_container ) {
            'use strcit';

            group_container.find('input, select, textarea').each(function(){
                $(this).attr( 'disabled' , 'disabled' )
            });
        }

        /**
         *
         * @param dependencies_list
         * @returns {*}
         */
        function checkDependeciesList( dependencies_list ){
            'use strict';

            var has_hided_dependecies = false;
            $('.yith_wapo_groups_container').find('.ywapo_group_container').each(function(){

                var id=$(this).data('id');

                if( ( $.inArray( id.toString(), dependencies_list ) ) == 0 && ( $(this).hasClass( 'ywapo_conditional_hidden' ) ) ) {
                    has_hided_dependecies = true;
                    return;
                }

            });

            return has_hided_dependecies;

        }

        /**
         *
         * @param $cart
         *
         * @author Andrea Frascaspata
         * @returns {number}
         */
        function getOptionsTotal( $cart ) {
            'use strict';
            var yith_wapo_final_total_price = 0.0;

            $cart.find( '.ywapo_input:checked, select.ywapo_input option:selected, input[type="text"].ywapo_input, input[type="number"].ywapo_input, input[type="file"].ywapo_input, input[type="color"].ywapo_input, input[type="date"].ywapo_input,input[type="hidden"].ywapo_input ,textarea.ywapo_input' ).each( function() {

                var group_container = $(this).closest('.ywapo_group_container');

                if( typeof group_container != 'undefined' && ! group_container.hasClass('ywapo_conditional_hidden') ) {

                    var type =  group_container.data('type');

                    var add_price = false;

                    switch( type ) {
                        case 'text' :
                        case 'textarea' :
                        case 'number' :
                        case 'file' :
                        case 'color' :
                        case 'date' :
                        case 'labels' :

                            if( $(this).val().trim() != '' )  {
                                add_price = true;
                            }

                            break;

                        default :
                            add_price = true;
                    }

                    if( add_price ) {
                        var price_attribute = $(this).data('price');

                        if( typeof price_attribute != 'undefined' && price_attribute >= 0 ) {
                            yith_wapo_final_total_price+= parseFloat( price_attribute );
                        }
                    }

                }

            } );

            return yith_wapo_final_total_price;
        }

        /**
         *
         * @param price
         * @author Andrea Frascaspata
         * @returns {*}
         */
        function getFormattedPrice( price ) {
            'use strict';

            var formatted_price = accounting.formatMoney( price , {
                symbol 		: yith_wapo_general.currency_format_symbol,
                decimal 	: yith_wapo_general.currency_format_decimal_sep,
                thousand	: yith_wapo_general.currency_format_thousand_sep,
                precision 	: yith_wapo_general.currency_format_num_decimals,
                format		: yith_wapo_general.currency_format
            } );

            return formatted_price;
        }

        var $cart = $(this);

        /* trigger change event (default value fix) */
        $cart.find('.yith_wapo_groups_container input, .yith_wapo_groups_container select, .yith_wapo_groups_container textarea').each(function(){

            $cart.trigger( 'yith-wapo-product-option-conditional' , $(this) );
        });

        $cart.trigger( 'yith-wapo-product-option-update' );

    }


    function ywapo_initialize() {
        'use strcit';

        // Initialize
        $( 'body' ).find( 'form:not(.in_loop).cart' ).each( function() {
            $(this).init_yith_wapo_totals();
            $(this).find( '.variations select' ).change();
        } );

        $( 'body' ).find( '.ywapo_option_description' ).each( function() {
            var tooltip = $(this).data('tooltip');
            if( tooltip ) {
                yith_wapo_tooltip( $(this) , tooltip );
            }
        } );

        /* external */

        $( '.ywapo_input_container_color .wp-color-picker' ).wpColorPicker({
            change: function(event, ui){

                var container = $(this).closest( '.ywapo_input_container_color' );
                var element  = container.find('input.ywapo_input_color');
                element.val( ui.color.toString() );
                element.change();
            },
            clear: function(){
                var container = $(this).closest( '.ywapo_input_container_color' );
                var element  = container.find('input.ywapo_input_color');
                element.val( '');
                element.change();
            }
        });

        $( '.ywapo_datepicker' ).each( function() {
            $(this).datepicker( );
        });
    }

    ywapo_initialize();

    function yith_wapo_tooltip ( opt, tooltip ){
        'use strcit';

        var tooltip_wrapper = $('<span class="yith_wccl_tooltip"></span>'),
            classes         = yith_wapo_general.tooltip_pos + ' ' + yith_wapo_general.tooltip_ani;

        tooltip_wrapper.addClass( classes );

        opt.append( tooltip_wrapper.html( '<span>' + tooltip + '</span>' ) );
    };

    /* yith quick view */

    $(document).on( 'qv_loader_stop yit_quick_view_loaded', function( ) {

        ywapo_initialize();

    } );


});