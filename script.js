(function($){
    var userMeta = {
        init : function(){

            $('#add-user-meta').bind('click',function(){
                userMeta.addRow([]);
            });

            for(var i in userMetas){
                this.addRow(userMetas[i]);
            }
        },
        addRow:function(field){
            var selectBoxOption='';
            for(var i=0;i<userMetasType.length;i++){
                selectBoxOption+='<option '+(userMetasType[i]==field['type']?'selected':'')+'>'+userMetasType[i]+'</option>';
            }

            row = '<tr>\
            <td><input type="text" name="name[]" placeholder="UserMeta name" value="'+(field['name']!=undefined?field['name']:'')+'"/></td>\
            <td><input type="text" name="description[]" placeholder="UserMeta description" value="'+(field['description']!=undefined?field['description']:'')+'"/></td>\
            <td><select name="type[]">'+selectBoxOption+'</select></td>\
            </tr>';
            $('#user-metas').append(row);
        }
    };
    $(document).ready(function(){
        userMeta.init();
    });
})(jQuery)
