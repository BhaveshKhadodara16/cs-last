(function( $ ) {
    $( window ).load( function() {
        jQuery( '.multiselect2' ).select2();
        jQuery('#dpad_select_day_of_week').select2({
			placeholder: 'Select days of the week'
		});
        jQuery( '.product_filter_select2' ).select2();
        jQuery('.product_filter_select2').select2(select2object('wdpad_pro_product_dpad_conditions_values_product'));
        /* <fs_premium_only> */
        jQuery( '.product_var_filter_select2' ).select2({ minimumInputLength: 3,placeholder: 'Please enter 3 characters' });
        jQuery('.product_var_filter_select2').select2(select2object('wdpad_pro_product_dpad_conditions_varible_values_product'));
        /* </fs_premium_only> */
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
        $( '#fee_settings_end_date' ).datepicker({
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
            
            // generate td of condition
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
                'title':'Delete',
                'class':'delete-row',
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
                if(options[i].type==='optgroup'){
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
                        /* <fs_premium_only> */
                        {'name': 'City','attributes' : {'value':'city'} },
                        {'name': 'State','attributes' : {'value':'state'} },
                        {'name': 'Postcode','attributes' : {'value':'postcode'} },
                        {'name': 'Zone','attributes' : {'value':'zone'} },
                        /* </fs_premium_only> */
                    ]
                },
                {
                    'type': 'optgroup',
                    'attributes' : {'label' :'Product Specific'},
                    'options' :[
                        {'name': 'Product','attributes' : {'value':'product'} },
                        /* <fs_premium_only> */
                        {'name': 'Variable Product','attributes' : {'value':'variableproduct'} },
                        /* </fs_premium_only> */
                        {'name': 'Category','attributes' : {'value':'category'} },
                        /* <fs_premium_only> */
                        {'name': 'Tag','attributes' : {'value':'tag'} },
                        {'name': 'Product\'s quantity', 'attributes': {'value' : 'product_qty'} },
                        /* </fs_premium_only> */
                        {'name': 'Product\'s count', 'attributes': {'value' : 'product_count'} },
                    ]
                },
                {
                    'type': 'optgroup',
                    'attributes' : {'label' : 'User Specific'},
                    'options': [
                        {'name' : 'User', 'attributes': {'value' : 'user'}},
                        /* <fs_premium_only> */
                        {'name' : 'User Role', 'attributes': {'value' : 'user_role'}},
                        {'name' : 'User Email', 'attributes': {'value' : 'user_mail'}},
                        /* </fs_premium_only> */
                    ]
                },
                /* <fs_premium_only> */
                {
                    'type': 'optgroup',
                    'attributes' : {'label' : 'Purchase History'},
                    'options': [
                        {'name' : 'Last order spent', 'attributes': {'value' : 'last_spent_order'}},
                        {'name' : 'Total order spent (all time)', 'attributes': {'value' : 'total_spent_order'}},
                        {'name' : 'Number of orders (all time)', 'attributes': {'value' : 'spent_order_count'}},
                    ]
                },
                /* </fs_premium_only> */
                {
                    'type': 'optgroup',
                    'attributes' : {'label' : 'Cart Specific'},
                    'options': [
                        {'name' : 'Cart Subtotal (Before Discount)', 'attributes': {'value' : 'cart_total'}},
                        /* <fs_premium_only> */
                        {'name' : 'Cart Subtotal (After Discount)', 'attributes': {'value' : 'cart_totalafter'}},
                        /* </fs_premium_only> */
                        {'name' : 'Quantity', 'attributes': {'value' : 'quantity'}},
                        /* <fs_premium_only> */
                        {'name' : 'Weight (kg)', 'attributes': {'value' : 'weight'}},
                        {'name' : 'Coupon', 'attributes': {'value' : 'coupon'}},
                        {'name' : 'Shipping Class', 'attributes': {'value' : 'shipping_class'}}
                        /* </fs_premium_only> */
                    ]
                },
                /* <fs_premium_only> */
                {
                    'type': 'optgroup',
                    'attributes' : {'label' : 'Payment Specific'},
                    'options': [
                        {'name' : 'Payment Gateway', 'attributes': {'value' : 'payment'}},
                    ]
                },
                {
                    'type': 'optgroup',
                    'attributes' : {'label' : 'Shipping Specific'},
                    'options': [
                        {'name' : 'Shipping Method', 'attributes' : {'value' : 'shipping_method'}},
                        {'name' : 'Shipping Total', 'attributes' : {'value' : 'shipping_total'}},
                    ]
                }
                /* </fs_premium_only> */
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
                    /* <fs_premium_only> */
                    || condition === 'total_spent_order'
                    || condition === 'spent_order_count'
                    || condition === 'last_spent_order'
                    || condition === 'cart_totalafter'
                    || condition === 'weight'
                    || condition === 'shipping_total'
                    || condition === 'product_qty'
                    /* </fs_premium_only> */
                ) {
                    condition_is=insertOptions(condition_is,condition_types('number'));
                } else if( condition === 'user_mail' ) {
                    condition_is=insertOptions(condition_is,condition_types('mail'));
                } else {
                    condition_is=insertOptions(condition_is,condition_types('string'));
                }
                jQuery('.product_dpad_conditions_is_' + count).trigger('chosen:updated');
                jQuery(column).empty();
                
                var condition_values_id='';
                if(condition === 'product'){
                    condition_values_id='product-filter';
                }
                /* <fs_premium_only> */
                if(condition === 'variableproduct'){
                    condition_values_id='var-product-filter';
                }
                var condition_values = document.createElement('select');
                /* </fs_premium_only> */
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
                    var input_extra_class = '';
                    if (condition === 'quantity'
                    || condition === 'spent_order_count'
                    || condition === 'product_count'
                    /* <fs_premium_only> */
                    || condition === 'product_qty'
                    /* </fs_premium_only> */
                    ) {
                        input_extra_class = ' qty-class';
                    }
                    /* <fs_premium_only> */
                    if (condition === 'weight') {
                        input_extra_class = ' weight-class';
                    }
                    /* </fs_premium_only> */
                    if (condition === 'cart_total'
                        /* <fs_premium_only> */
                        || condition === 'total_spent_order'
                        || condition === 'last_spent_order'
                        || condition === 'cart_totalafter'
                        || condition === 'shipping_total'
                        /* </fs_premium_only> */
                    ) {
                        input_extra_class = ' price-class';
                    }
                    /* <fs_premium_only> */
                    if (condition === 'user_mail') {
                        input_extra_class = ' user-mail-class';
                    }
                    /* </fs_premium_only> */
                    
                    condition_values = document.createElement(response.trim());
                    condition_values=setAllAttributes(condition_values,{
                        'name':  'dpad[product_dpad_conditions_values][value_'+count+']',
                        'class': 'product_dpad_conditions_values' + input_extra_class,
                        'type': 'text',
                        'min':0,
                    });
                    column.appendChild(condition_values);
                    /* <fs_premium_only> */
                    if (condition === 'user_mail') {
                        var condition_note = document.createElement('p');
                        var condition_strong_note = document.createElement('strong');

                        condition_note=setAllAttributes(condition_note, {
                            'class': 'dpad_conditions_notes',
                        });
                        condition_note.textContent = 'E.g., john.doe@gmail.com where, user name is "john.doe", domain is "gmail.com"';
                        
                        condition_strong_note.textContent = 'Note: ';
                        condition_note.prepend(condition_strong_note);

                        column.appendChild(condition_note);
                    }
                    /* </fs_premium_only> */
                }
                column = $('#column_' + count).get(0);
                var input_node=document.createElement('input');
                input_node=setAllAttributes(input_node,{
                    'type':'hidden',
                    'name':'condition_key[value_'+count+'][]',
                    'value':''
                });
                column.appendChild(input_node);
                if(condition_values_id === 'product-filter'){
                    jQuery( '.multiselect2' ).select2(select2object('wdpad_pro_product_dpad_conditions_values_product'));
                }
                /* <fs_premium_only> */
                else if(condition_values_id === 'var-product-filter'){
                    jQuery('.multiselect2').select2(select2object('wdpad_pro_product_dpad_conditions_varible_values_product'));
                }
                /* </fs_premium_only> */
                else{
                    jQuery( '.multiselect2' ).select2();
        
                }
                if (condition === 'product'
                    /* <fs_premium_only> */
                    || condition === 'variableproduct'
                /* </fs_premium_only> */
                ) {
                    $('#product_filter_chosen input, #var_product_filter_chosen input').val('Please enter 3 or more characters');
                    $('#product_filter_chosen input, #var_product_filter_chosen input').attr('placeholder','Please enter 3 or more characters');
                }

                var p_node = document.createElement( 'p' );
                var b_node = document.createElement( 'b' );
                var b_text_node = document.createTextNode( coditional_vars.note );
                var text_node;
                /* <fs_premium_only> */
                if ( condition === 'product_qty' ) {
                    b_node = setAllAttributes( b_node, {
                        'style': 'color: red;',
                    } );
                    b_node.appendChild( b_text_node );
                    if ( condition === 'product_qty' ) {
                        text_node = document.createTextNode( coditional_vars.product_qty_msg );
                    }

                    p_node.appendChild( b_node );
                    p_node.appendChild( text_node );
                    column.appendChild( p_node );
                }
                /* </fs_premium_only> */

                if ( condition === 'product_count' ) {
                    b_node = setAllAttributes( b_node, {
                        'style': 'color: red;',
                    } );
                    b_node.appendChild( b_text_node );
                    text_node = document.createTextNode( coditional_vars.product_count_msg );
                    p_node.appendChild( b_node );
                    p_node.appendChild( text_node );
                    column.appendChild( p_node );
                }
                getProductListBasedOnThreeCharAfterUpdate();
                numberValidateForAdvanceRules();
            });
        }
        
        
        function condition_types( text ){
            if( 'number' === text ){
                return [
                    {'name': 'Equal to ( = )','attributes' : {'value':'is_equal_to'} },
                    {'name': 'Less or Equal to ( <= )','attributes' : {'value':'less_equal_to'} },
                    {'name': 'Less then ( < )','attributes' : {'value':'less_then'} },
                    {'name': 'Greater or Equal to ( >= )','attributes' : {'value':'greater_equal_to'} },
                    {'name': 'Greater then ( > )','attributes' : {'value':'greater_then'} },
                    {'name': 'Not Equal to ( != )','attributes' : {'value':'not_in'} },
                ];
            } else if( 'mail' === text ){
                return [
                    {'name': 'User Name ( john.doe )','attributes' : {'value':'user_name'} },
                    {'name': 'Domain ( @gmail.com )','attributes' : {'value':'domain_name'} },
                    {'name': 'Email Address','attributes' : {'value':'full_mail'} },
                ];
            } else if( 'time' === text ){
                return [
                    {'name': 'With in','attributes' : {'value':'within'} },
                    {'name': 'Before','attributes' : {'value':'before'} },
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
        getProductListBasedOnThreeCharAfterUpdate();
        numberValidateForAdvanceRules();
        
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
        /* <fs_premium_only> */
        $('body').on('keyup', '#var_product_filter_chosen input', function () {
            if(e.keyCode === 27 || e.keyCode === 8) {
                return ;
            }
            countId = $(this).closest('td').attr('id');
            $('#var_product_filter_chosen ul li.no-results').html('Please enter 3 or more characters');
            var value = $(this).val();
            var valueLenght = value.replace(/\s+/g, '');
            var valueCount = valueLenght.length;
            var remainCount = 3 - valueCount;
            // var selproductvalue = $('#' + countId + ' #var-product-filter').chosen().val();
            if (valueCount >= 3) {
                var no_result=$('#var_product_filter_chosen ul li.no-results').get(0);
                var loader_image=document.createElement('img');
                loader_image=setAllAttributes(loader_image,{
                    'src':coditional_vars.plugin_url+'images/ajax-loader.gif'
                });
                if(no_result){
                    no_result.appendChild(loader_image);
                }
                var data = {
                    'action': 'wdpad_pro_product_dpad_conditions_varible_values_product_ajax',
                    'value': value,
                };
                jQuery.ajaxSetup({
                    headers: {
                        'Accept': 'application/json; charset=utf-8'
                    }
                });
                jQuery.post(ajaxurl, data, function (response) {
                    if (response.length !== 0) {
                        var var_product_filter=jQuery('#' + countId + ' #var-product-filter').get(0);
                        var_product_filter=insertOptions(var_product_filter,JSON.parse(response));
                    } else {
                        $('#var-product-filter option').not(':selected').remove();
                    }
                    $('#' + countId + ' #var-product-filter option').each(function () {
                        $(this).siblings('[value="' + this.value + '"]').remove();
                    });
                    jQuery('#' + countId + ' #var-product-filter').trigger('chosen:updated');
                    $('#var_product_filter_chosen .search-field input').val(value);
                    $('#' + countId + ' #var-product-filter').chosen().change(function () {
                        var productVal = $('#' + countId + ' #var-product-filter').chosen().val();
                        jQuery('#' + countId + ' #var-product-filter option').each(function () {
                            $(this).siblings('[value="' + this.value + '"]').remove();
                            if (jQuery.inArray(this.value, productVal) === -1) {
                                jQuery(this).remove();
                            }
                        });
                        jQuery('#' + countId + ' #var-product-filter').trigger('chosen:updated');
                    });
                    $('#var_product_filter_chosen ul li.no-results').empty();
                });
            } else {
                if (remainCount > 0) {
                    $('#var_product_filter_chosen ul li.no-results').text('Please enter ' + remainCount + ' or more characters');
                }
            }
        });
        /* </fs_premium_only> */
        
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

            // jQuery(".condition-value").each( function(){ 
            //     var condition_value = jQuery(this).find('select, input').val();
            //     if( "" == condition_value ) { 
            //         // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
            //         if ( $( '#warning_msg_6' ).length < 1 ) {
            //             var div = document.createElement( 'div' );
            //             div = setAllAttributes( div, {
            //                 'class': 'warning_msg',
            //                 'id': 'warning_msg_6'
            //             } );
            //             div.textContent = coditional_vars.error_msg;
            //             $( '.wdpad-main-table' ).prepend( div );
            //         }
            //         if ( $( '#warning_msg_6' ).length ) {
            //             $( 'html, body' ).animate( { scrollTop: 0 }, 'slow' );
            //             setTimeout( function() {
            //                 $( '#warning_msg_6' ).remove();
            //             }, 7000 );
            //         }
            //         e.preventDefault();
            //         return false;
            //     } 
            // });

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
            /* Checking product qty validation start */
			var product_qty_fees_conditions_conditions = $('select[name="dpad[product_dpad_conditions_condition][]"]').map(function () {
                return $(this).val();
            }).get();

            if ( -1 !== product_qty_fees_conditions_conditions.indexOf('product_qty') ) {
                if ( product_qty_fees_conditions_conditions.indexOf('product') === -1
                    && product_qty_fees_conditions_conditions.indexOf('variableproduct') === -1
                    && product_qty_fees_conditions_conditions.indexOf('category') === -1
                    && product_qty_fees_conditions_conditions.indexOf('tag') === -1 ) {
                    // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
                    if ( $( '#warning_msg_6' ).length < 1 ) {
                        var div = document.createElement( 'div' );
                        div = setAllAttributes( div, {
                            'class': 'warning_msg',
                            'id': 'warning_msg_6'
                        } );
                        div.textContent = coditional_vars.warning_msg6;
                        $( '.wdpad-main-table' ).prepend( div );
                    }
                    if ( $( '#warning_msg_6' ).length ) {
                        $( 'html, body' ).animate( { scrollTop: 0 }, 'slow' );
                        setTimeout( function() {
                            $( '#warning_msg_6' ).remove();
                        }, 7000 );
                    }
                    e.preventDefault();
                    return false;
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

        function createAdvancePricingRulesField( field_type, qty_or_weight, field_title, field_count, field_title2, category_list_option ) {
			var label_text, min_input_placeholder, max_input_placeholder, inpt_class, inpt_type;
			if ( qty_or_weight === 'qty' ) {
				label_text = coditional_vars.cart_qty;
			} else if ( qty_or_weight === 'weight' ) {
				label_text = coditional_vars.cart_weight;
			} else if ( qty_or_weight === 'subtotal' ) {
				label_text = coditional_vars.cart_subtotal;
			}

			if ( qty_or_weight === 'qty' ) {
				min_input_placeholder = coditional_vars.min_quantity;
			} else if ( qty_or_weight === 'weight' ) {
				min_input_placeholder = coditional_vars.min_weight;
			} else if ( qty_or_weight === 'subtotal' ) {
				min_input_placeholder = coditional_vars.min_subtotal;
			}

			if ( qty_or_weight === 'qty' ) {
				max_input_placeholder = coditional_vars.max_quantity;
			} else if ( qty_or_weight === 'weight' ) {
				max_input_placeholder = coditional_vars.max_weight;
			} else if ( qty_or_weight === 'subtotal' ) {
				max_input_placeholder = coditional_vars.max_subtotal;
			}

			if ( qty_or_weight === 'qty' ) {
				inpt_class = 'qty-class';
				inpt_type = 'number';
			} else if ( qty_or_weight === 'weight' ) {
				inpt_class = 'weight-class';
				inpt_type = 'text';
			} else if ( qty_or_weight === 'subtotal' ) {
				inpt_class = 'price-class';
				inpt_type = 'text';
			}
			var tr = document.createElement( 'tr' );
			tr = setAllAttributes( tr, {
				'class': 'ap_' + field_title + '_row_tr',
				'id': 'ap_' + field_title + '_row_' + field_count,
			} );

			var product_td = document.createElement( 'td' );
			if ( field_type === 'select' ) {
				var product_select = document.createElement( 'select' );
				product_select = setAllAttributes( product_select, {
					'rel-id': field_count,
					'id': 'ap_' + field_title + '_fees_conditions_condition_' + field_count,
					'name': 'dpad[ap_' + field_title + '_fees_conditions_condition][' + field_count + '][]',
					'class': 'wdpad_select ap_' + field_title + ' product_fees_conditions_values multiselect2',
					'multiple': 'multiple',
				} );

				product_td.appendChild( product_select );
                var all_category_option, option, category_option;
				if ( category_list_option === 'category_list' ) {
					all_category_option = JSON.parse( $( '#all_category_list' ).html() );
					for ( i = 0; i < all_category_option.length; i ++ ) {
						option = document.createElement( 'option' );
						category_option = all_category_option[ i ];
						option.value = category_option.attributes.value;
						option.text = allowSpeicalCharacter( category_option.name );
						product_select.appendChild( option );
					}
				}
				if ( category_list_option === 'shipping_class_list' ) {
				    all_category_option = JSON.parse( $( '#all_shipping_class_list' ).html() );
					for ( i = 0; i < all_category_option.length; i ++ ) {
						option = document.createElement( 'option' );
						category_option = all_category_option[ i ];
						option.value = category_option.attributes.value;
						option.text = allowSpeicalCharacter( category_option.name );
						product_select.appendChild( option );
					}
				}
			}
			if ( field_type === 'label' ) {
				var product_label = document.createElement( 'label' );
				var product_label_text = document.createTextNode( label_text );
				product_label = setAllAttributes( product_label, {
					'for': label_text.toLowerCase(),
				} );
				product_label.appendChild( product_label_text );
				product_td.appendChild( product_label );

				var input_hidden = document.createElement( 'input' );
				input_hidden = setAllAttributes( input_hidden, {
					'id': 'ap_' + field_title + '_fees_conditions_condition_' + field_count,
					'type': 'hidden',
					'name': 'dpad[ap_' + field_title + '_fees_conditions_condition][' + field_count + '][]',
				} );
				product_td.appendChild( input_hidden );
			}
			tr.appendChild( product_td );

			var min_qty_td = document.createElement( 'td' );
			min_qty_td = setAllAttributes( min_qty_td, {
				'class': 'column_' + field_count + ' condition-value',
			} );
			var min_qty_input = document.createElement( 'input' );
			if ( qty_or_weight === 'qty' ) {
				min_qty_input = setAllAttributes( min_qty_input, {
					'type': inpt_type,
					'id': 'ap_fees_ap_' + field_title2 + '_min_' + qty_or_weight + '[]',
					'name': 'dpad[ap_fees_ap_' + field_title2 + '_min_' + qty_or_weight + '][]',
					'class': 'text-class ' + inpt_class,
					'placeholder': min_input_placeholder,
					'value': '',
					'min': '1',
					'required': '1',
				} );
			} else {
				min_qty_input = setAllAttributes( min_qty_input, {
					'type': inpt_type,
					'id': 'ap_fees_ap_' + field_title2 + '_min_' + qty_or_weight + '[]',
					'name': 'dpad[ap_fees_ap_' + field_title2 + '_min_' + qty_or_weight + '][]',
					'class': 'text-class ' + inpt_class,
					'placeholder': min_input_placeholder,
					'value': '',
					'required': '1',
				} );
			}
			min_qty_td.appendChild( min_qty_input );
			tr.appendChild( min_qty_td );

			var max_qty_td = document.createElement( 'td' );
			max_qty_td = setAllAttributes( max_qty_td, {
				'class': 'column_' + field_count + ' condition-value',
			} );
			var max_qty_input = document.createElement( 'input' );
			if ( qty_or_weight === 'qty' ) {
				max_qty_input = setAllAttributes( max_qty_input, {
					'type': inpt_type,
					'id': 'ap_fees_ap_' + field_title2 + '_max_' + qty_or_weight + '[]',
					'name': 'dpad[ap_fees_ap_' + field_title2 + '_max_' + qty_or_weight + '][]',
					'class': 'text-class ' + inpt_class,
					'placeholder': max_input_placeholder,
					'value': '',
					'min': '1',
				} );
			} else {
				max_qty_input = setAllAttributes( max_qty_input, {
					'type': inpt_type,
					'id': 'ap_fees_ap_' + field_title2 + '_max_' + qty_or_weight + '[]',
					'name': 'dpad[ap_fees_ap_' + field_title2 + '_max_' + qty_or_weight + '][]',
					'class': 'text-class ' + inpt_class,
					'placeholder': max_input_placeholder,
					'value': '',
				} );
			}
			max_qty_td.appendChild( max_qty_input );
			tr.appendChild( max_qty_td );

			var price_td = document.createElement( 'td' );
			var price_input = document.createElement( 'input' );
			price_input = setAllAttributes( price_input, {
				'type': 'text',
				'id': 'ap_fees_ap_price_' + field_title + '[]',
				'name': 'dpad[ap_fees_ap_price_' + field_title + '][]',
				'class': 'text-class number-field',
				'placeholder': coditional_vars.amount,
				'value': '',
			} );
			price_td.appendChild( price_input );

			// if( 'product' == field_title ) {
			// 	var per_product_label = document.createElement( 'label' );
			// 	var per_product_label_text = document.createTextNode( coditional_vars.per_product );
			// 	per_product_label = setAllAttributes( per_product_label, {
			// 		'class': 'ap-label-checkbox',
			// 	} );
				
			// 	var per_product_checkbox = document.createElement( 'input' );
			// 	per_product_checkbox = setAllAttributes( per_product_checkbox, {
			// 		'type': 'checkbox',
			// 		'id': 'ap_fees_ap_per_' + field_title + '[]',
			// 		'name': 'dpad[ap_fees_ap_per_' + field_title + '][]',
			// 		'value': 'yes',
			// 	} );
			// 	per_product_label.appendChild( per_product_checkbox );
			// 	per_product_label.appendChild( per_product_label_text );
			// 	price_td.appendChild( per_product_label );
			// }
			tr.appendChild( price_td );

			var delete_td = document.createElement( 'td' );
			var delete_a = document.createElement( 'a' );
			delete_a = setAllAttributes( delete_a, {
				'id': 'ap_' + field_title + '_delete_field',
				'rel-id': field_count,
				'title': coditional_vars.delete,
				'class': 'delete-row',
				'href': 'javascript:void(0);'
			} );
			var delete_i = document.createElement( 'i' );
			delete_i = setAllAttributes( delete_i, {
				'class': 'fa fa-trash'
			} );
			delete_a.appendChild( delete_i );
			delete_td.appendChild( delete_a );

			tr.appendChild( delete_td );

			$( '#tbl_ap_' + field_title + '_method tbody tr' ).last().after( tr );
		}

        function getProductListBasedOnThreeCharAfterUpdate() {
            $( '.fees_pricing_rules .ap_product' ).each( function() {
                $( '.fees_pricing_rules .ap_product' ).select2( {
                    ajax: {
                        url: coditional_vars.ajaxurl,
                        dataType: 'json',
                        delay: 250,
                        data: function( params ) {
                            return {
                                value: params.term,
                                action: 'wdpad_pro_simple_and_variation_product_list_ajax'
                            };
                        },
                        processResults: function( data ) {
                            var options = [];
                            if ( data ) {
                                $.each( data, function( index, text ) {
                                    options.push( { id: text[ 0 ], text: allowSpeicalCharacter( text[ 1 ] ) } );
                                } );
    
                            }
                            return {
                                results: options
                            };
                        },
                        cache: true
                    },
                    minimumInputLength: 3
                } );
            } );
        }

        function numberValidateForAdvanceRules() {
            $( '.number-field' ).keypress( function( e ) {
                var regex = new RegExp( '^[0-9-%.]+$' );
                var str = String.fromCharCode( ! e.charCode ? e.which : e.charCode );
                if ( regex.test( str ) ) {
                    return true;
                }
                e.preventDefault();
                return false;
            } );
            $( '.qty-class' ).keypress( function( e ) {
                var regex = new RegExp( '^[0-9]+$' );
                var str = String.fromCharCode( ! e.charCode ? e.which : e.charCode );
                if ( regex.test( str ) ) {
                    return true;
                }
                e.preventDefault();
                return false;
            } );
            $( '.weight-class, .price-class' ).keypress( function( e ) {
                var regex = new RegExp( '^[0-9.]+$' );
                var str = String.fromCharCode( ! e.charCode ? e.which : e.charCode );
                if ( regex.test( str ) ) {
                    return true;
                }
                e.preventDefault();
                return false;
            } );
        }

        /* Defines AP Rules validate functions */
		function is_percent_valid() {
			//check amount only contains number or percentage
			$( '.percent_only' ).blur( function() {

				//regular expression for the valid amount enter like 20 or 20% or 50.0 or 50.55% etc.. is valid
				var is_valid_percent = /^[-]{0,1}((100)|(\d{1,2}(\.\d{1,2})?))[%]{0,1}$/;
				var percent_val = $( this ).val();
				//check that entered amount for the advanced price is valid or not like 20 or 20% or 50.0 or 50.55% etc.. is valid
				if ( ! is_valid_percent.test( percent_val ) ) {
					$( this ).val( '' );//if percent not in proper format than it will blank the textbox
				}
				//display note that if admin add - price than how message display in shipping method
				var first_char = percent_val.charAt( 0 );
				if ( first_char === '-' ) {
					//remove old notice message if exist
					$( this ).next().remove( 'p' );
					$( this ).after( coditional_vars.warning_msg1 );
				} else {
					//remove notice message if value is possitive
					$( this ).next().remove( 'p' );
				}
			} );
		}
        
        /* Add AP Product functionality start */
		//get total count row from hidden field
		var row_product_ele = $( '#total_row_product' ).val();
		var count_product;
		if ( row_product_ele > 2 ) {
			count_product = row_product_ele;
		} else {
			count_product = 2;
		}

		//on click add rule create new method row
		$( 'body' ).on( 'click', '#ap-product-add-field', function() {
			//design new format of advanced pricing method row html
			createAdvancePricingRulesField( 'select', 'qty', 'product', count_product, 'prd', '' );
			getProductListBasedOnThreeCharAfterUpdate();
			numberValidateForAdvanceRules();
			is_percent_valid();//bind percent on blur event for checking the amount is proper format or not
			count_product ++;
		} );
		/* Add AP Product functionality end here */

        /* Add AP Category functionality start here */
		//get total count row from hidden field
		var row_category_ele = $( '#total_row_category' ).val();
		var row_category_count;
		if ( row_category_ele > 2 ) {
			row_category_count = row_category_ele;
		} else {
			row_category_count = 2;
		}
		//on click add rule create new method row
		$( 'body' ).on( 'click', '#ap-category-add-field', function() {
			createAdvancePricingRulesField( 'select', 'qty', 'category', row_category_count, 'cat', 'category_list' );
			jQuery( '.ap_category' ).select2();
			numberValidateForAdvanceRules();
			//set default category list to newly added category dropdown
			//rebide the new row with validation
			is_percent_valid();//bind percent on blur event for checking the amount is proper format or not
			row_category_count ++;
		} );
        /* Add AP Category functionality end here */

        $( 'ul.tabs li' ).click( function() {
			var tab_id = $( this ).attr( 'data-tab' );

			$( 'ul.tabs li' ).removeClass( 'current' );
			$( '.tab-content' ).removeClass( 'current' );

			$( this ).addClass( 'current' );
			$( '#' + tab_id ).addClass( 'current' );
		} );
        if ( jQuery( window ).width() <= 980 ) {
            jQuery( '.fees-pricing-rules .fees_pricing_rules .tab-content' ).click( function() {
                var acc_id = jQuery( this ).attr( 'id' );
                jQuery( '.fees-pricing-rules .fees_pricing_rules .tab-content' ).removeClass( 'current' );
                jQuery( '#' + acc_id ).addClass( 'current' );
            } );
        }
        //remove tr on delete icon click
		$( 'body' ).on( 'click', '.delete-row', function() {
			$( this ).parent().parent().remove();
		} );

        function hideShowPricingRulesBasedOnPricingRuleStatus( elem ) {
            if ( jQuery( elem ).prop( 'checked' ) === true ) {
				jQuery( '.fees_pricing_rules' ).show();
				jQuery( '.multiselect2' ).select2();
			} else if ( $( elem ).prop( 'checked' ) === false ) {
				jQuery( '.fees_pricing_rules' ).hide();
			}
        }
        hideShowPricingRulesBasedOnPricingRuleStatus( 'input[name="ap_rule_status"]' );

        jQuery( 'body' ).on( 'click', 'input[name="ap_rule_status"]', function() {
			hideShowPricingRulesBasedOnPricingRuleStatus( this );
            getProductListBasedOnThreeCharAfterUpdate();
		} );
        getProductListBasedOnThreeCharAfterUpdate();
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
            $( '.wdpad-main-table .wp-list-table tbod' ).disableSelection();
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
        $('#dpad_chk_discount_msg').change(function(){
            show_hide_discount_textbox('#'+$(this).attr('id'));
        });
        show_hide_discount_textbox('#dpad_chk_discount_msg');
        function show_hide_discount_textbox(elem){
            if( $(elem).prop('checked') ){
                $('.display_discount_message_text').show();
            } else {
                $('.display_discount_message_text').hide();
            }
        }
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