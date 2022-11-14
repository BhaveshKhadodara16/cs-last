(function( $ ) {
    $( window ).load( function() {
        jQuery( '.multiselect2' ).select2();
        jQuery( '.product_filter_select2' ).select2({ minimumInputLength: 3, placeholder: 'Please enter 3 characters' });
        jQuery('.product_filter_select2').select2(select2object('wdpad_pro_product_dpad_conditions_values_product'));
        $( '#dpad_settings_start_date' ).datepicker( {
            dateFormat: 'dd-mm-yy',
            minDate: '0',
            onSelect: function() {
                var dt = $( this ).datepicker( 'getDate' );
                dt.setDate( dt.getDate() + 1 );
                $( '#dpad_settings_end_date' ).datepicker( 'option', 'minDate', dt );
            }
        } );
        $( '#dpad_settings_end_date' ).datepicker( {
            dateFormat: 'dd-mm-yy',
            minDate: '0',
            onSelect: function() {
                var dt = $( this ).datepicker( 'getDate' );
                dt.setDate( dt.getDate() - 1 );
                $( '#dpad_settings_start_date' ).datepicker( 'option', 'maxDate', dt );
            }
        } );
        
        $('#fee_settings_end_date').datepicker({
            dateFormat: 'dd-mm-yy',
            minDate: '0',
            onSelect: function () {
                var dt = $(this).datepicker('getDate');
                dt.setDate(dt.getDate() - 1);
                $('#fee_settings_start_date').datepicker('option', 'maxDate', dt);
            }
        });

        /*
         * Timepicker
         * */
        var dpad_time_from = $('#dpad_time_from').val();
        var dpad_time_to = $('#dpad_time_to').val();
        
        $('#dpad_time_from').timepicker({
            timeFormat: 'h:mm p',
            interval: 60,
            minTime: '00:00AM',
            maxTime: '11:59PM',
            startTime: dpad_time_from,
            dynamic: true,
            dropdown: true,
            scrollbar: true
        });
        
        $('#dpad_time_to').timepicker({
            timeFormat: 'h:mm p',
            interval: 60,
            minTime: '00:00AM',
            maxTime: '11:59PM',
            startTime: dpad_time_to,
            dynamic: true,
            dropdown: true,
            scrollbar: true
        });
        
        var ele = $('#total_row').val();
        var count;
        if (ele > 2) {
            count = ele;
        } else {
            count = 2;
        }
        $('body').on('click', '#fee-add-field', function () {
            var fee_add_field=$('#tbl-product-fee tbody').get(0);
            
            var tr = document.createElement('tr');
            tr=setAllAttributes(tr,{'id':'row_'+count});
            fee_add_field.appendChild(tr);
            
            // generate th of condition
            var th = document.createElement('th');
            th=setAllAttributes(th,{
                'class':'titledesc th_product_dpad_conditions_condition',
            });
            tr.appendChild(th);
            var conditions = document.createElement('select');
            conditions=setAllAttributes(conditions,{
                'rel-id':count,
                'id':'product_dpad_conditions_condition_'+count,
                'name':'dpad[product_dpad_conditions_condition][]',
                'class':'product_dpad_conditions_condition'
            });
            conditions=insertOptions(conditions,get_all_condition());
            th.appendChild(conditions);
            // th ends
            
            // generate td for equal or no equal to
            td = document.createElement('td');
            td = setAllAttributes(td,{});
            tr.appendChild(td);
            var conditions_is = document.createElement('select');
            conditions_is=setAllAttributes(conditions_is,{
                'name':'dpad[product_dpad_conditions_is][]',
                'class':'product_dpad_conditions_is product_dpad_conditions_is_'+count
            });
            conditions_is=insertOptions(conditions_is,condition_types());
            td.appendChild(conditions_is);
            // td ends
            
            // td for condition values
            td = document.createElement('td');
            td = setAllAttributes(td,{'id': 'column_'+count, 'class': 'condition-value'});
            tr.appendChild(td);
            condition_values(jQuery('#product_dpad_conditions_condition_'+count));
            
            var condition_key = document.createElement('input');
            condition_key=setAllAttributes(condition_key,{
                'type':'hidden',
                'name':'condition_key[value_'+count+'][]',
                'value':'',
            });
            td.appendChild(condition_key);
            conditions_values_index=jQuery('.product_dpad_conditions_values_' + count).get(0);
            jQuery('.product_dpad_conditions_values_' + count).trigger('chosen:updated');
            jQuery( '.multiselect2' ).select2();
            // td ends
            
            // td for delete button
            td = document.createElement('td');
            tr.appendChild(td);
            delete_button = document.createElement('a');
            delete_button=setAllAttributes(delete_button,{
                'id': 'fee-delete-field',
                'rel-id': count,
                'title': 'Delete',
                'class': 'delete-row',
                'href': 'javascript:void(0);'
            });
            deleteicon=document.createElement('i');
            deleteicon=setAllAttributes(deleteicon,{
                'class': 'fa fa-trash'
            });
            delete_button.appendChild(deleteicon);
            td.appendChild(delete_button);
            // td ends
            numberValidateForAdvanceRules();
            count++;
        });
        
        function insertOptions(parentElement,options){
            for(var i=0;i<options.length;i++){
                if(options[i].type === 'optgroup'){
                    optgroup=document.createElement('optgroup');
                    optgroup=setAllAttributes(optgroup,options[i].attributes);
                    for(var j=0;j<options[i].options.length;j++){
                        option=document.createElement('option');
                        option=setAllAttributes(option,options[i].options[j].attributes);
                        option.textContent=options[i].options[j].name;
                        optgroup.appendChild(option);
                    }
                    parentElement.appendChild(optgroup);
                } else {
                    option=document.createElement('option');
                    option=setAllAttributes(option,options[i].attributes);
                    option.textContent=allowSpeicalCharacter(options[i].name);
                    parentElement.appendChild(option);
                }
                
            }
            return parentElement;
            
        }
        function allowSpeicalCharacter(str){
            return str.replace('&#8211;','–').replace('&gt;','>').replace('&lt;','<').replace('&#197;','Å');
        }
        
        
        
        function setAllAttributes(element,attributes){
            Object.keys(attributes).forEach(function (key) {
                element.setAttribute(key, attributes[key]);
                // use val
            });
            return element;
        }
        
        function get_all_condition(){
            return [
                {
                    'type': 'optgroup',
                    'attributes' : {'label' :'Location Specific'},
                    'options' :[
                        {'name': 'Country','attributes' : {'value':'country'} },
                        {'name': 'City (Available in PRO)','attributes' : {'value':'', 'disabled':'disabled'} },
                        {'name': 'State (Available in PRO)','attributes' : {'value':'', 'disabled':'disabled'} },
                        {'name': 'Postcode (Available in PRO)','attributes' : {'value':'', 'disabled':'disabled'} },
                        {'name': 'Zone (Available in PRO)','attributes' : {'value':'', 'disabled':'disabled'} },
                    ]
                },
                {
                    'type': 'optgroup',
                    'attributes' : {'label' :'Product Specific'},
                    'options' :[
                        {'name': 'Product','attributes' : {'value':'product'} },
                        {'name': 'Variable Product (Available in PRO)','attributes' : {'value':'', 'disabled':'disabled'} },
                        {'name': 'Category','attributes' : {'value':'category'} },
                        {'name': 'Tag (Available in PRO)','attributes' : {'value':'', 'disabled':'disabled'} },
                        {'name': 'Product\'s quantity (Available in PRO)', 'attributes': {'value' : '', 'disabled':'disabled'} },
                        {'name': 'Product\'s count', 'attributes': {'value' : 'product_count'} },
                    ]
                },
                {
                    'type': 'optgroup',
                    'attributes' : {'label' : 'User Specific'},
                    'options': [
                        {'name' : 'User', 'attributes': {'value' : 'user'}},
                        {'name' : 'User Role (Available in PRO)', 'attributes': {'value' : '', 'disabled':'disabled'}},
                        {'name' : 'User Email (Available in PRO)', 'attributes': {'value' : '', 'disabled':'disabled'}},
                    ]
                },
                {
                    'type': 'optgroup',
                    'attributes' : {'label' : 'Purchase History'},
                    'options': [
                        {'name' : 'Last order spent  (Available in PRO)', 'attributes': {'value' : '', 'disabled':'disabled'}},
                        {'name' : 'Total order spent (all time) (Available in PRO)', 'attributes': {'value' : '', 'disabled':'disabled'}},
                        {'name' : 'Number of orders (all time) (Available in PRO)', 'attributes': {'value' : '', 'disabled':'disabled'}},
                    ]
                },
                {
                    'type': 'optgroup',
                    'attributes' : {'label' : 'Cart Specific'},
                    'options': [
                        {'name' : 'Cart Subtotal', 'attributes': {'value' : 'cart_total'}},
                        {'name' : 'Cart Subtotal (After Discount) (Available in PRO)', 'attributes': {'value' : '', 'disabled':'disabled'}},
                        {'name' : 'Quantity', 'attributes': {'value' : 'quantity'}},
                        {'name' : 'Weight (kg) (Available in PRO)', 'attributes': {'value' : '', 'disabled':'disabled'}},
                        {'name' : 'Coupon (Available in PRO)', 'attributes': {'value' : '', 'disabled':'disabled'}},
                        {'name' : 'Shipping Class (Available in PRO)', 'attributes': {'value' : '', 'disabled':'disabled'}}
                    ]
                },
                {
                    'type': 'optgroup',
                    'attributes' : {'label' : 'Payment Specific'},
                    'options': [
                        {'name' : 'Payment Gateway (Available in PRO)', 'attributes': {'value' : '', 'disabled':'disabled'}},
                    ]
                },
                {
                    'type': 'optgroup',
                    'attributes' : {'label' : 'Shipping Specific'},
                    'options': [
                        {'name' : 'Shipping Method (Available in PRO)', 'attributes' : {'value' : '', 'disabled':'disabled'}},
                        {'name' : 'Shipping Total (Available in PRO)', 'attributes' : {'value' : '', 'disabled':'disabled'}},
                    ]
                }
            ];
        }
        
        $( 'body' ).on( 'click', '#fee-delete-field', function() {
            var deleId = $( this ).attr( 'rel-id' );
            $( '#row_' + deleId ).remove();
        } );
        $( 'body' ).on( 'change', '.product_dpad_conditions_condition', function() {
            condition_values(this);
        } );
        
        $('body').on('keyup', '#product_filter_chosen input', function (e) {
            if(e.keyCode === 27 || e.keyCode === 8) {
                return ;
            }
            var countId = $(this).closest('td').attr('id');
            $('#piroduct_filter_chosen ul li.no-results').html('Please enter 3 or more characters');
            var post_per_page = 3; // Post per page
            var page = 0; // What page we are on.
            var value = $(this).val();
            var valueLenght = value.replace(/\s+/g, '');
            var valueCount = valueLenght.length;
            var remainCount = 3 - valueCount;
            // var selproductvalue = $('#' + countId + ' #product-filter').chosen().val();
            if (valueCount >= 3) {
                var no_result=$('#product_filter_chosen ul li.no-results').get(0);
                loader_image=document.createElement('img');
                loader_image=setAllAttributes(loader_image,{
                    'src':coditional_vars.plugin_url+'images/ajax-loader.gif'
                });
                no_result.appendChild(loader_image);
                var data = {
                    'action': 'wdpad_pro_product_dpad_conditions_values_product_ajax',
                    'value': value,
                    'post_per_page': post_per_page,
                    'offset': (page * post_per_page),
                };
                $.ajaxSetup({
                    headers: {
                        'Accept': 'application/json; charset=utf-8'
                    }
                });
                $.post(ajaxurl, data, function (response) {
                    page++;
                    if (response.length !== 0) {
                        var product_filter=jQuery('#' + countId + ' #product-filter').get(0);
                        product_filter=insertOptions(product_filter,JSON.parse(response));
                    } else {
                        $('#product-filter option').not(':selected').remove();
                    }
                    $('#' + countId + ' #product-filter option').each(function () {
                        $(this).siblings('[value="' + this.value + '"]').remove();
                    });
                    jQuery('#' + countId + ' #product-filter').trigger('chosen:updated');
                    $('#product_filter_chosen .search-field input').val(value);
                    $('#' + countId + ' #product-filter').chosen().change(function () {
                        var productVal = $('#' + countId + ' #product-filter').chosen().val();
                        jQuery('#' + countId + ' #product-filter option').each(function () {
                            $(this).siblings('[value="' + this.value + '"]').remove();
                            if (jQuery.inArray(this.value, productVal) === -1) {
                                jQuery(this).remove();
                            }
                        });
                        jQuery('#' + countId + ' #product-filter').trigger('chosen:updated');
                    });
                    $('#product_filter_chosen ul li.no-results').empty();
                });
            } else {
                if (remainCount > 0) {
                    $('#product_filter_chosen ul li.no-results').text('Please enter ' + remainCount + ' or more characters');
                }
            }
        });
        
        
        function condition_values(element) {
            var condition = $(element).val();
            var count = $(element).attr('rel-id');
            var column=jQuery('#column_' + count).get(0);
            jQuery(column).empty();
            var loader=document.createElement('img');
            loader=setAllAttributes(loader,{'src':coditional_vars.plugin_url+'images/ajax-loader.gif'});
            column.appendChild(loader);
            var data = {
                'action': 'wdpad_pro_product_dpad_conditions_values_ajax',
                'wcpfc_pro_product_dpad_conditions_values_ajax': $('#wcpfc_pro_product_dpad_conditions_values_ajax').val(),
                'condition': condition,
                'count': count
            };
            jQuery.ajaxSetup({
                headers: {
                    'Accept': 'application/json; charset=utf-8'
                }
            });
            // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
            jQuery.post(ajaxurl, data, function (response) {
                jQuery('.product_dpad_conditions_is_' + count).empty();
                var column=jQuery('#column_' + count).get(0);
                var condition_is=jQuery('.product_dpad_conditions_is_' + count).get(0);
                
                if (condition === 'cart_total'
                    || condition === 'quantity'
                    || condition === 'product_count'
                ) {
                    condition_is=insertOptions(condition_is,condition_types(true));
                } else {
                    condition_is=insertOptions(condition_is,condition_types(false));
                }
                jQuery('.product_dpad_conditions_is_' + count).trigger('chosen:updated');
                jQuery(column).empty();
                
                var condition_values_id='';
                if(condition === 'product'){
                    condition_values_id = 'product-filter';
                }
                var condition_values;
                if(isJson(response)){
                    condition_values = document.createElement('select');
                    condition_values=setAllAttributes(condition_values,{
                        'name':  'dpad[product_dpad_conditions_values][value_'+count+'][]',
                        'class': 'product_dpad_conditions_values product_discount_select product_dpad_conditions_values_'+count+' multiselect2',
                        'multiple': 'multiple',
                        'id':condition_values_id+'-'+count,
                        'placeholder': 'please enter 3 characters'
                    });
                    column.appendChild(condition_values);
                    data=JSON.parse(response);
                    condition_values=insertOptions(condition_values,data);
                } else{
                    var input_extra_class;
                    if (condition === 'quantity' || condition === 'product_count') {
                        input_extra_class = ' qty-class';
                    }
                    if (condition === 'cart_total' ) {
                        input_extra_class = ' price-class';
                    }
                    
                    condition_values = document.createElement(response.trim());
                    condition_values=setAllAttributes(condition_values,{
                        'name':  'dpad[product_dpad_conditions_values][value_'+count+']',
                        'class': 'product_dpad_conditions_values' + input_extra_class,
                        'type': 'text',
                        'min':0,
                    });
                    column.appendChild(condition_values);
                }
                column = $('#column_' + count).get(0);
                var input_node = document.createElement('input');
                input_node = setAllAttributes(input_node,{
                    'type':'hidden',
                    'name':'condition_key[value_'+count+'][]',
                    'value':''
                });
                column.appendChild(input_node);
                if(condition_values_id === 'product-filter'){
                    jQuery( '.multiselect2' ).select2(select2object('wdpad_pro_product_dpad_conditions_values_product'));
                }
                else{
                    jQuery( '.multiselect2' ).select2();
        
                }
                if ( condition === 'product' ) {
                    $('#product_filter_chosen input, #var_product_filter_chosen input').val('Please enter 3 or more characters');
                    $('#product_filter_chosen input, #var_product_filter_chosen input').attr('placeholder','Please enter 3 or more characters');
                }
                numberValidateForAdvanceRules();
            });
        }
        
        
        function condition_types(text){
            if( text === true ){
                return [
                    {'name': 'Equal to ( = )','attributes' : {'value':'is_equal_to'} },
                    {'name': 'Less or Equal to ( <= )','attributes' : {'value':'less_equal_to'} },
                    {'name': 'Less then ( < )','attributes' : {'value':'less_then'} },
                    {'name': 'greater or Equal to ( >= )','attributes' : {'value':'greater_equal_to'} },
                    {'name': 'greater then ( > )','attributes' : {'value':'greater_then'} },
                    {'name': 'Not Equal to ( != )','attributes' : {'value':'not_in'} },
                ];
            } else {
                return  [
                    {'name': 'Equal to ( = )','attributes' : {'value':'is_equal_to'} },
                    {'name': 'Not Equal to ( != )','attributes' : {'value':'not_in'} },
                ];
                
            }
            
        }
        $('#extra_product_cost, .price-field').keypress(function (e) {
            var regex = new RegExp('^[0-9.]+$');
            var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
            if (regex.test(str)) {
                return true;
            }
            e.preventDefault();
            return false;
        });
        numberValidateForAdvanceRules();
        function numberValidateForAdvanceRules() {
            $('.number-field').keypress(function (e) {
                var regex = new RegExp('^[0-9-%.]+$');
                var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
                if (regex.test(str)) {
                    return true;
                }
                e.preventDefault();
                return false;
            });
            $('.qty-class').keypress(function (e) {
                var regex = new RegExp('^[0-9]+$');
                var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
                if (regex.test(str)) {
                    return true;
                }
                e.preventDefault();
                return false;
            });
            $('.weight-class, .price-class').keypress(function (e) {
                var regex = new RegExp('^[0-9.]+$');
                var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
                if (regex.test(str)) {
                    return true;
                }
                e.preventDefault();
                return false;
            });
        }
        
        function isJson(str) {
            try {
                JSON.parse(str);
            } catch (err) {
                return false;
            }
            return true;
        }
        function select2object(ajaxtype){
            return {
                minimumInputLength: 3,
                ajax: {
                    url: ajaxurl,
                    dataType: 'json',
                    data: function (params) {
                        var query = {
                            action: ajaxtype+'_ajax',
                            search: params.term,
                            placeholder: 'Select a state',
                            allowClear: true
                        };
                        
                        // Query parameters will be ?search=[term]&page=[page]
                        return query;
                    }
                    
                }
            };
        }
        
        $( '.condition-check-all' ).click( function() {
            $( 'input.multiple_delete_fee:checkbox' ).not( this ).prop( 'checked', this.checked );
        } );
        $( '#detete-conditional-fee' ).click( function() {
            if ( $( '.multiple_delete_fee:checkbox:checked' ).length === 0 ) {
                alert( 'Please select at least one checkbox' );
                return false;
            }
            if ( confirm( 'Are You Sure You Want to Delete?' ) ) {
                var allVals = [];
                $( '.multiple_delete_fee:checked' ).each( function() {
                    allVals.push( $( this ).val() );
                } );
                var data = {
                    'action': 'wdpad_pro_wc_multiple_delete_conditional_fee',
                    'allVals': allVals
                };
                // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
                jQuery.post( ajaxurl, data, function( response ) {
                    if ( response === 1 ) {
                        alert( 'Delete Successfully' );
                        $( '.multiple_delete_fee' ).prop( 'checked', false );
                        location.reload();
                    }
                } );
            }
        } );
        $('.submitFee').click(function(e){
            var price_cartqty_based = $('#price_cartqty_based').val();
            if (price_cartqty_based === 'qty_product_based') {
                var f = 0;
                $('.product_dpad_conditions_condition').each(function () {
        
                    if ($(this).val() === 'product' || $(this).val() === 'variableproduct') {
                        f = 1;
                    }
        
                });
                if ($('#dpad_chk_qty_price').is(':checked') && f === 0) {
                    e.preventDefault();
                    alert('please choose atleast one product or product variation condition');
                    return;
        
                }
            }
        });
        
        $('.disable-enable-conditional-fee').click(function () {
            if ($('.multiple_delete_fee:checkbox:checked').length === 0) {
                alert('Please select at least one checkbox');
                return false;
            }
            if (confirm('Are You Sure You Want To Change The Status?')) {
                var allVals = [];
                $('.multiple_delete_fee:checked').each(function () {
                    allVals.push($(this).val());
                });
                var data = {
                    'action': 'wdpad_pro_wc_disable_conditional_fee',
                    'multiple_disable_enable_conditional_fee': $('#multiple_disable_enable_conditional_fee').val(),
                    'do_action': $(this).attr('id'),
                    'allVals': allVals
                };
                // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
                jQuery.post(ajaxurl, data, function (response) {
                    if (response === 1) {
                        alert('Status Changed Successfully');
                        $('.multiple_delete_fee').prop('checked', false);
                        location.reload();
                    }
                });
            }
        });
        
        
        /* description toggle */
        $( 'span.woocommerce_conditional_product_dpad_checkout_tab_descirtion' ).click( function( event ) {
            event.preventDefault();
            // var data = $( this );
            $( this ).next( 'p.description' ).toggle();
            //$('span.advance_extra_flate_rate_disctiption_tab').next('p.description').toggle();
        } );
    } );
    
    
    jQuery( document ).ready( function( $ ) {
        
        /** tiptip js implementation */
		$( '.woocommerce-help-tip' ).tipTip( {
			'attribute': 'data-tip',
			'fadeIn': 50,
			'fadeOut': 50,
			'delay': 200,
			'keepAlive': true
		} );

        $( '.tablesorter' ).tablesorter( {
            headers: {
                0: {
                    sorter: false
                },
                4: {
                    sorter: false
                }
            }
        } );
        var fixHelperModified = function( e, tr ) {
            var $originals = tr.children();
            var $helper = tr.clone();
            $helper.children().each( function( index ) {
                $( this ).width( $originals.eq( index ).width() );
            } );
            return $helper;
        };
        //Make diagnosis table sortable
        if( jQuery('#the-list tr').length > 1 ) {
            $('.wdpad-main-table .wp-list-table tbody').sortable({
                helper: fixHelperModified,
                stop: function() {
                    var listing = [];
                    var paged = $('.current_paged').val();
                    jQuery('.ui-sortable-handle').each(function(){
                        listing.push(jQuery(this).find('input').val());
                    });
                    var data = {
                        'action': 'wdpad_pro_product_discount_conditions_sorting',
                        'sorting_conditional_fee': jQuery('#sorting_conditional_fee').val(),
                        'listing': listing,
                        'paged': paged
                    };
                    jQuery.ajaxSetup({
                        headers: {
                            'Accept': 'application/json; charset=utf-8'
                        }
                    });
                    // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
                    jQuery.post(ajaxurl, data, function (response){
                        var div_wrap = $('<div></div>').addClass('notice notice-success');
                        var p_text = $('<p></p>').text(response.data.message);
                        div_wrap.append(p_text);
                        $(div_wrap).insertAfter($('.search-box'));
                        setTimeout( function(){
                            div_wrap.remove();
                        }, 2000 );
                    });
                }
            });
            $( 'table#conditional-fee-listing tbody' ).disableSelection();
        }
        
        /* Apply per quantity conditions start */
        if ( $( '#dpad_chk_qty_price' ).is( ':checked' ) ) {
            $( '.wdpad-main-table .product_cost_right_div .applyperqty-boxtwo' ).show();
            $( '.wdpad-main-table .product_cost_right_div .applyperqty-boxthree' ).show();
            $( '#extra_product_cost' ).prop( 'required', true );
        } else {
            $( '.wdpad-main-table .product_cost_right_div .applyperqty-boxtwo' ).hide();
            $( '.wdpad-main-table .product_cost_right_div .applyperqty-boxthree' ).hide();
            $( '#extra_product_cost' ).prop( 'required', false );
        }
        $( document ).on( 'change', '#dpad_chk_qty_price', function() {
            if ( this.checked ) {
                $( '.wdpad-main-table .product_cost_right_div .applyperqty-boxtwo' ).show();
                $( '.wdpad-main-table .product_cost_right_div .applyperqty-boxthree' ).show();
                $( '#extra_product_cost' ).prop( 'required', true );
            } else {
                $( '.wdpad-main-table .product_cost_right_div .applyperqty-boxtwo' ).hide();
                $( '.wdpad-main-table .product_cost_right_div .applyperqty-boxthree' ).hide();
                $( '#extra_product_cost' ).prop( 'required', false );
            }
        } );
        /* Apply per quantity conditions end */
        /* Check price only digits allow */
        $( '#dpad_settings_product_cost' ).keypress( function( e ) {
            //if the letter is not digit then display error and don't type anything
            if ( e.which !== 46 && e.which !== 8 && e.which !== 0 && (e.which < 48 || e.which > 57) ) {
                //display error message
                
                return false;
            }
        } );
        $( document ).on( 'click', '#dpad_chk_discount_msg_selected_product', function() {
            if( $(this).prop('checked') === true ){
                $('.wdpad-selected-product-list').show();
            }else{
                $('.wdpad-selected-product-list').hide();
            }
        });

        // script for plugin rating
        jQuery(document).on('click', '.dotstore-sidebar-section .content_box .wcdrc-star-rating label', function(e){
            e.stopImmediatePropagation();
            var rurl = jQuery('#wcdrc-review-url').val();
            window.open( rurl, '_blank' );
        });
        var span_full = $('.toggleSidebar .dashicons');
        var show_sidebar = localStorage.getItem('fps-sidebar-display');
        if( ( null !== show_sidebar || undefined !== show_sidebar ) && ( 'hide' === show_sidebar ) ) {
            $('.all-pad').addClass('hide-sidebar');
            span_full.removeClass('dashicons-arrow-right-alt2').addClass('dashicons-arrow-left-alt2');
        } else {
            $('.all-pad').removeClass('hide-sidebar');
            span_full.removeClass('dashicons-arrow-left-alt2').addClass('dashicons-arrow-right-alt2');
        }
        $(document).on( 'click', '.toggleSidebar', function(){
            $('.all-pad').toggleClass('hide-sidebar');
            if( $('.all-pad').hasClass('hide-sidebar') ){
                localStorage.setItem('fps-sidebar-display', 'hide');
                span_full.removeClass('dashicons-arrow-right-alt2').addClass('dashicons-arrow-left-alt2');
                $('.all-pad .fps-section-right').css({'-webkit-transition': '.3s ease-in width', '-o-transition': '.3s ease-in width',  'transition': '.3s ease-in width'});
                $('.all-pad .fps-section-left').css({'-webkit-transition': '.3s ease-in width', '-o-transition': '.3s ease-in width',  'transition': '.3s ease-in width'});
                setTimeout(function() {
                    $('#dotsstoremain .dotstore_plugin_sidebar').css('display', 'none');
                }, 300);
            } else {
                localStorage.setItem('fps-sidebar-display', 'show');
                span_full.removeClass('dashicons-arrow-left-alt2').addClass('dashicons-arrow-right-alt2');
                $('.all-pad .fps-section-right').css({'-webkit-transition': '.3s ease-out width', '-o-transition': '.3s ease-out width',  'transition': '.3s ease-out width'});
                $('.all-pad .fps-section-left').css({'-webkit-transition': '.3s ease-out width', '-o-transition': '.3s ease-out width',  'transition': '.3s ease-out width'});
                $('#dotsstoremain .dotstore_plugin_sidebar').css('display', 'block');
            }
        });
    } );
})( jQuery );