window.wp = window.wp || {};

var BulkDistributionEdit;
( function( $, wp ) {
    
        BulkDistributionEdit = {

            doing_action : '',
            
            processing_list :   [],
            processing_length : 0,
            current_item_count: 1,
            
            ajax_params     :   '',
            
            
            canceled        :   false,
            
            init : function(){
                var t = this,  bulkDistributionRow = $('#bulk-distribution');

                t.type =  'post';
                t.what = '#post-';

             

                $( '.cancel', bulkDistributionRow ).click( function() {
                    
                    if(t.doing_action    ==  '')
                        {
                            BulkDistributionEdit.revert();
                        }
                        else
                        t.canceled   =   true;
                    
                });
                
                $( 'button.save', bulkDistributionRow ).click( function() {
                    
                    //if busy do nothing
                    if(t.doing_action   !=  '')
                        return;                    
                    
                    return BulkDistributionEdit.save();
                });

                $('#inline-edit .inline-edit-private input[value="private"]').click( function(){
                    var pw = $('input.inline-edit-password-input');
                    if ( $(this).prop('checked') ) {
                        pw.val('').prop('disabled', true);
                    } else {
                        pw.prop('disabled', false);
                    }
                });

        
                $('#doaction, #doaction2').click(function(e){
                    var n;

                    t.whichBulkButtonId = $( this ).attr( 'id' );
                    n = t.whichBulkButtonId.substr( 2 );

                    if ( 'bulk_distribution' === $( 'select[name="' + n + '"]' ).val() ) {
                        e.preventDefault();
                        setTimeout(function() {  t.setBulk();  }, 50);
                    } else if ( $('form#posts-filter tr.bd-inline-editor').length > 0 ) {
                        t.revert();
                    }
                });
                
                $('#ms_doaction').click(function(e){
                    var n;

                    t.whichBulkButtonId = $( this ).attr( 'id' );
                    n = t.whichBulkButtonId.substr( 5 );

                    if ( 'bulk_distribution' === $( 'select[name="' + n + '"]' ).val() ) {
                        e.preventDefault();
                        setTimeout(function() {  t.setBulk();  }, 50);
                    } else if ( $('form#posts-filter tr.bd-inline-editor').length > 0 ) {
                        t.revert();
                    }
                });
                
                
                $('#the-list').on( 'click', 'a.editinline', function( e ) {
                    t.revert();
                });
                
            },

            toggle : function(el){
                var t = this;
                $( t.what + t.getId( el ) ).css( 'display' ) === 'none' ? t.revert() : t.edit( el );
            },

            setBulk : function(){
                var te = '', c = true, blog_ids = [];
                this.revert();

                $( '#bulk-distribution td' ).attr( 'colspan', $( 'th:visible, td:visible', '.widefat:first thead' ).length );
                // Insert the editor at the top of the table with an empty row above to maintain zebra striping.
                $('table.widefat tbody').prepend( $('#bulk-distribution') ).prepend('<tr class="hidden"></tr>');
                $('#bulk-distribution').addClass('bd-inline-editor').show();

                $( 'tbody th.check-column input[type="checkbox"]' ).each( function() {
                    if ( $(this).prop('checked') ) {
                        c = false;
                        var id = $(this).val(), theTitle;

                        if(id.includes("_"))
                        {
                            var parts   =   id.split("_");
                            blog_id     =   parts[0];
                            id     =   parts[1];
                        }

                        if ( 'yes' === $('div._is_master_product', '#woocommerce_multistore_inline_' + id).text() ) {
                            theTitle = $('#inline_'+id+' .post_title').html() || inlineEditL10n.notitle;
                            te += '<div id="ttle'+id+'" data-id="'+ id +'"><a id="_'+id+'" class="ntdelbutton" title="'+inlineEditL10n.ntdeltitle+'">X</a>'+theTitle+'</div>';
                        } else {
                            $( this ).prop( 'checked', false );
                        }

                        $( 'div', '#woocommerce_multistore_inline_' + id ).each( function( index, element ) {
                            const name  = $(element).attr('class'),
                                  value = $(element).text();

                            if ( 'master_blog_id' === name ) {
                                $( 'p[data-group-id="' + value + '"]' ).hide();
                                blog_ids.push( value );
                            }
                        });
                    }
                });

                blog_ids = blog_ids.filter(function (x, i, a) {
                    return a.indexOf(x) === i;
                });
                if ( blog_ids.length > 1 ) {
                    alert("Selection contains parent products from multiple stores.\nYou can only distribute products that are parent products in the same store at the same time.");
                    return this.revert();
                }

                $('button.save', '#bulk-distribution').toggle( 0 !== te.length );
                // if ( 0 === te.length ) {
                //     alert('You will be able to Bulk Distribute only the products that are parent products of this store.')
                //     return this.revert();
                // }

                if ( c ) {
                    return this.revert();
                }

                $('#bulk-titles').html(te);
                $('#bulk-titles a').click(function(){
                    var id = $(this).attr('id').substr(1);

                    $('table.widefat input[value="' + id + '"]').prop('checked', false);
                    $('#ttle'+id).remove();
                });

   
                jQuery('#bulk-distribution #woonet_toggle_all_sites').change(function() {
                    if(jQuery(this).is(":checked")) {
                        
                        jQuery('#bulk-distribution .woonet_sites input[type="checkbox"]._woonet_publish_to').each(function() {
                            if(jQuery(this).prop('disabled')    ==  false)
                                {
                                    jQuery(this).attr('checked', 'checked');
                                    jQuery(this).trigger('change');
                                }    
                        })
         
                    }
                    else {
                        jQuery('#bulk-distribution .woonet_sites input[type="checkbox"]._woonet_publish_to').each(function() {
                            if(jQuery(this).prop('disabled')    ==  false)
                                {
                                    jQuery(this).attr('checked', false);
                                    jQuery(this).trigger('change');
                                }    
                        })     
                    }
                        
                }); 
   
                $('html, body').animate( { scrollTop: 0 }, 'fast' );
            },
           

            // Ajax saving is only for Quick Edit.
            save : function(id) {
                
                var t = this;
                
                this.doing_action  =   'save';
                
                $( 'table.widefat .spinner' ).addClass( 'is-active' );
                $( 'table.widefat .progress' ).addClass( 'is-active' );
                
                
                this.processing_list =   [];
                $( 'tbody th.check-column input[type="checkbox"]' ).each( function() {
                    if ( $(this).prop('checked') ) {
                        
                        var row =   jQuery(this).closest('tr.type-product');
                        t.processing_list.push ( jQuery(row).find('input.network-cb-select').val() );
                    }
                });
                
                this.processing_length  =   t.processing_list.length;
                if(this.processing_length < 1)
                    return;
                
                this.current_item_count =   0;
                
                this._update_progress();
                                        
                params = {
                    'action' : 'woosl-bulk-distribution-save',
                };

                fields = $('#bulk-distribution').find(':input').serialize();
                this.ajax_params = fields + '&' + $.param(params);
                
                this._do_call();
                                
                // Prevent submitting the form when pressing Enter on a focused field.
                return false;
            },
            
            _do_call    :   function()  {
             
                if(this.canceled    === true)
                    {
                        this.doing_action  =   '';
                        $( 'table.widefat .spinner' ).removeClass( 'is-active' );
                        $( 'table.widefat .progress' ).removeClass( 'is-active' );
                        this.processing_list    =   [];
                        
                        BulkDistributionEdit.revert();
                        
                        this.canceled   =   false;
                        
                        return;   
                    }
                
                if(this.processing_list.length  >   0)
                    {
                        var id  =   this.processing_list.slice(0, 1);
                        this.processing_list.shift();
                        
                        var params =    this.ajax_params + '&' + $.param( {'ids' : id });
                        
                        this.current_item_count++;
                        this._update_progress();
                        
                        this.do_ajax_call( params );
                        
                    }
                    else
                    {
                        //completed
                        this.doing_action  =   '';
                        $( 'table.widefat .spinner' ).removeClass( 'is-active' );   
                        $( 'table.widefat .progress' ).removeClass( 'is-active' );
                        
                        BulkDistributionEdit.revert();
                        
                        window.location.reload(false);
                    }                
            },
            
            do_ajax_call    :   function( params )  {
             
                var t = this;
                
                jQuery.ajax({
                      type: 'POST',
                      url: ajaxurl,
                      data: params,
                      cache: false,
                      dataType: "json",
                      success: function(data){
                        
                        t._do_call();
                        
                      },
                      error: function(html){

                          }
                    });                
            },

            _update_progress    :   function() {
                
                $( 'table.widefat .progress span' ).html(this.current_item_count + ' of ' + this.processing_length);
            },
            
            // Revert is for both Quick Edit and Bulk Edit.
            revert : function(){
                var $tableWideFat = $( '.widefat' ),
                    id = $( '.bd-inline-editor', $tableWideFat ).attr( 'id' );

                if ( id ) {
                    $( '.spinner', $tableWideFat ).removeClass( 'is-active' );
                    $( '.ac_results' ).hide();

                    $( '#bulk-distribution', $tableWideFat ).hide().siblings( '.hidden' ).remove();
                    $('#bulk-titles').empty();
                    $('#more-inlineedit').append( $('#bulk-distribution') );
                    
                }

                return false;
            },

            getId : function(o) {
                var id = $(o).closest('tr').attr('id'),
                    parts = id.split('-');
                return parts[parts.length - 1];
            }
        };

        $( document ).ready( function(){ BulkDistributionEdit.init(); } );

})( jQuery, window.wp );