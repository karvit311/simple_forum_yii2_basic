$(document).ready(function(){
    var msg = $('#form');
    $("#form").ajaxForm({
        method: 'POST',
        success: function (response) {
            console.log(response);
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) { 
                alert("Status: " + textStatus); alert("Error: " + errorThrown); 
        }     
    });
});
window.onload= function() {
    document.getElementById('toggler').onclick = function() {
        openbox('box', this);
        window.scrollTo(0, 1700);
        $(this).addClass('move');
        return false;
    };
};

function openbox(id, toggler) {
    var div = document.getElementById(id);
    if(div.style.display == 'block') {
        div.style.display = 'none';
        toggler.innerHTML = 'Add a comment';
    } else {
        div.style.display = 'block';
        toggler.innerHTML = 'Close';
        // toggler.innerHTML = '<button href="#" type="button" id="closir" class="close">&times;</button>';
    }
}
$(document).ready(function(){

    if($(".wrap").height() < 700){ $("#scrollUp").hide(); } 
});
function scrollDown() {
    window.scrollTo(0, 1500);
}
/////////////MYCOMMENT-TABLE//////////////////
$(document).ready(function(){
 	$('#mycomment').DataTable({
        "bInfo" : false
    });
});
////END////MYCOMMENT-TABLE////////END////
/////////////MESSAGE-INBOX//////////////////
$(document).ready(function(){
    $("#messageTable").dataTable({ 
    	"bInfo" : false
    });
});
///END////MESSAGE_INBOX /////END//////////
/////////////////////////SCROLL//////////
function scrollWin() {
        window.scrollTo(0, 20);
}
function scrollDown() {
    window.scrollTo(0, 1500);
}
$(document).ready(function(){
    $('.likegood').click(function() {     
        var tid = $(this).attr("tid");
        $.ajax({
            type: "POST",
            url: "/forum/forum/like",
            data: "id="+tid,
            dataType: "html",
            cache: false,
            success: function(data) {
                var res = parseInt(data);
                if (res == '-1')
                {
                    alert('Вы уже проголосовали!');
                } else {
                    $("#likegoodcount"+tid).html(res);
                }
            }
        });
});
    $('.delete').click(function(){
        
        var rel = $(this).attr("rel");
        
        $.confirm({
            'title'     : 'Подтверждение удаления',
            'message'   : 'После удаления восстановление будет невозможно! Продолжить?',
            'buttons'   : {
                'Да'    : {
                    'class' : 'blue',
                    'action': function(){
                        location.href = rel;
                    }
                },
                'Нет'   : {
                    'class' : 'gray',
                    'action': function(){}
                }
            }
        });
        
    });
    $('.delete-cat').click(function(){
        var selectid = $("#cat_type option:selected").val();
        if (!selectid)
        {
            $("#cat_type").css("borderColor","#F5A4A4");
        }else{
            $.ajax({
                type: "POST",
                url: "/admin/admin/delete-category",
                data: "id="+selectid,
                dataType: "html",
                cache: false,
                success: function(data) {
                    var data_parse = parseInt(data);
                    if (data_parse == 1)
                    {
                        $("#cat_type option:selected").remove();
                    }
                }
            });       
        }
                  
    });
    $('.delete-subcat').click(function(){
        var selectid = $("#subcat_type option:selected").val();
        if (!selectid)
        {
            $("#subcat_type").css("borderColor","#F5A4A4");
        }else{
            $.ajax({
                type: "POST",
                url: "/admin/admin/delete-subcategory",
                data: "id="+selectid,
                dataType: "html",
                cache: false,
                success: function(data) {
                    var data_parse = parseInt(data);
                    if (data_parse == 1)
                    {
                        $("#subcat_type option:selected").remove();
                    }
                }
            });       
        }
                  
    });
    $('#client-links-block').click(function(){
        // var selectid = $("#client-links-block").val();
        var id = $(this).attr("iid");
        alert(id);
        // if (!selectid)
        // {
        //     $(".delete-block").css("borderColor","#F5A4A4");
        // }else{
            $.ajax({
                type: "POST",
                url: "/admin/admin/clients-block",
                data: "id="+id,
                dataType: "html",
                cache: false,
                success: function(data) {
                    var data_parse = parseInt(data);
                    alert(data_parse);
                    // if (data_parse == 1)
                    // {
                        $("#block-clients").remove();
                        $("#client-links-block").remove();
                    // }
                }
            });       
        // }
                  
    });
    //  $('.block-clients').click(function(){
    //     // var selectid = $("#client-links-block").val();
    //     var id = $(this).attr("iid");
    //     alert(id);
    //     // if (!selectid)
    //     // {
    //     //     $(".delete-block").css("borderColor","#F5A4A4");
    //     // }else{
    //         $.ajax({
    //             type: "POST",
    //             url: "/admin/admin/clients",
    //             data: "id="+id,
    //             dataType: "html",
    //             cache: false,
    //             success: function(data) {
    //                 var data_parse = parseInt(data);
    //                 alert(data_parse);
    //                 // if (data_parse == 1)
    //                 // {
    //                     // $(".block-clients").remove();
    //                     // $("#client-links-block").remove();
    //                 // }
    //             }
    //         });       
    //     // }
                  
    // });
    $('.delete-block').click(function(){
        
        var rel = $(this).attr("rel");
        
        $.confirm({
            'title'     : 'Подтверждение удаления',
            'message'   : 'После удаления восстановление будет невозможно! Продолжить?',
            'buttons'   : {
                'Да'    : {
                    'class' : 'blue',
                    'action': function(){
                        location.href = rel;
                    }
                },
                'Нет'   : {
                    'class' : 'gray',
                    'action': function(){}
                }
            }
        });
        
    });
    
   $('.block-clients').click(function(){

 $(this).find('ul').slideToggle(300);
   
 });

});





