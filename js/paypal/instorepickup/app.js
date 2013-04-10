/**
 * Instore Pickup Admin
 *
 * @package      :  Paypal_Instorepickupadmin
 * @version      :  0.1.0
 * @since        :  Magento 1.7
 * @author       :  Paypal - http://www.paypal.com
 * @copyright    :  Copyright (C) 2013 Powered by Paypal
 * @license      :  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @Creation Date:  01/03/2013
 *
 */
 var app = {
    // init application
    init : function(settings){
        this.settings = settings;
        var This = this;
    },
    // render order detail
    showOrderDetail: function(orderId){
        var This = this;
        var data = {'order_id':orderId};
        data = this.buildAjaxParams(data);
        $.post(this.settings.urls.orderDetail, data, function(response){
            This.processDataResponse(response);
        },'json');
    },
    // save order information
    refundOrder: function(message){
        if(confirm(message)){
            $('#order_refund').submit();
        }
    },
    increaseQtyDeliver : function(itemId){
        var orderedQty = parseInt($('#items_qty_ordered_'+itemId).val());
        var remainQty = parseInt($('#items_qty_remain_'+itemId).val());
        var orgRemainQty = parseInt($('#items_qty_remain_'+itemId).attr('org_value'));
        var deliverQty = parseInt($('#items_qty_deliver_'+itemId).val());
        var orgDeliverQty = parseInt($('#items_qty_deliver_'+itemId).attr('org_value'));

        var nextDeliverQty = deliverQty + 1;
        var nextRemainQty = remainQty - 1;
        var maxDeliverQty = parseInt($('#items_qty_deliver_'+itemId).attr('max_deliver_qty'));
        var minDeliverQty = parseInt($('#items_qty_deliver_'+itemId).attr('min_deliver_qty'));
        if(nextDeliverQty <= maxDeliverQty){
            $('#items_qty_deliver_'+itemId).val(nextDeliverQty);
            var nextDeliverQtyLabel = nextDeliverQty;
            if(nextDeliverQty == 0){
                nextDeliverQtyLabel = '-';
            }
            $('#label_items_qty_deliver_'+itemId).html(nextDeliverQtyLabel);

            $('#items_qty_remain_'+itemId).val(nextRemainQty);
            $('#label_items_qty_remain_'+itemId).html(nextRemainQty);
        }

    },
    decreaseQtyDeliver : function(itemId){
        var orderedQty = parseInt($('#items_qty_ordered_'+itemId).val());
        var remainQty = parseInt($('#items_qty_remain_'+itemId).val());
        var orgRemainQty = parseInt($('#items_qty_remain_'+itemId).attr('org_value'));
        var deliverQty = parseInt($('#items_qty_deliver_'+itemId).val());
        var orgDeliverQty = parseInt($('#items_qty_deliver_'+itemId).attr('org_value'));

        var maxDeliverQty = parseInt($('#items_qty_deliver_'+itemId).attr('max_deliver_qty'));
        var minDeliverQty = parseInt($('#items_qty_deliver_'+itemId).attr('min_deliver_qty'));

        var nextDeliverQty = deliverQty - 1;
        var nextRemainQty = remainQty +1;

        if(nextDeliverQty >= minDeliverQty){
            $('#items_qty_deliver_'+itemId).val(nextDeliverQty);
            var nextDeliverQtyLabel = nextDeliverQty;
            if(nextDeliverQty == 0){
                nextDeliverQtyLabel = '-';
            }
            $('#label_items_qty_deliver_'+itemId).html(nextDeliverQtyLabel);

            $('#items_qty_remain_'+itemId).val(nextRemainQty);
            $('#label_items_qty_remain_'+itemId).html(nextRemainQty);
        }

    },
    deliverAll : function(){
        $('input.items_qty_deliver').each(function(){
            var itemId = $(this).attr('item_id');
            var deliverQty = parseInt($(this).val());
            var remainQty = parseInt($('#items_qty_remain_'+itemId).attr('org_value'));
            var orderedQty = parseInt($('#items_qty_ordered_'+itemId).val());

            var nextDeliverQty = remainQty;
            var nextRemainQty = remainQty - nextDeliverQty;
            var nextDeliverQtyLabel = nextDeliverQty;
            if(nextDeliverQty == 0){
                nextDeliverQtyLabel = '-';
            }
            $('#items_qty_deliver_'+itemId).val(nextDeliverQty);
            $('#label_items_qty_deliver_'+itemId).html(nextDeliverQtyLabel);

            $('#items_qty_remain_'+itemId).val(nextRemainQty);
            $('#label_items_qty_remain_'+itemId).html(nextRemainQty);
        });
    },
    // add security parameter for ajax request
    buildAjaxParams : function(params){
        if(typeof(params['form_key']) == 'undefined'){
            params['form_key'] = this.settings.formKey;
        }

        if(typeof(params['isAjax']) == 'undefined'){
            params['isAjax'] = 1;
        }
        return params;
    }
}